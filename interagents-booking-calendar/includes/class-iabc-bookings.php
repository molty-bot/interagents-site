<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class IABC_Bookings {
	/** @param string $date @param DateTimeImmutable|null $now @return array<int,array<string,mixed>>|WP_Error */
	public static function day_slots( $date, $now = null ) {
		$settings = IABC_Plugin::settings();
		$timezone = new DateTimeZone( 'Europe/Warsaw' );
		$day      = DateTimeImmutable::createFromFormat( '!Y-m-d', (string) $date, $timezone );
		$errors   = DateTimeImmutable::getLastErrors();

		if ( false === $day || ( is_array( $errors ) && ( $errors['warning_count'] || $errors['error_count'] ) ) || $day->format( 'Y-m-d' ) !== (string) $date ) {
			return new WP_Error( 'iabc_invalid_date', 'Invalid date.' );
		}

		$now       = $now instanceof DateTimeImmutable ? $now : new DateTimeImmutable( 'now', $timezone );
		$day_start = $day->setTime( 0, 0, 0 );
		$day_end   = $day_start->modify( '+1 day' );
		$busy      = self::overlaps_for_range( $day_start->getTimestamp(), $day_end->getTimestamp() );

		return IABC_Slot_Engine::generate( $day, $now, $settings, $busy );
	}

	/** @param int $range_start_ts @param int $range_end_ts @return array<int,array<string,int>> */
	public static function overlaps_for_range( $range_start_ts, $range_end_ts ) {
		global $wpdb;
		$table = IABC_Plugin::table_name();
		$rows  = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT start_utc, end_utc FROM {$table} WHERE status = 'confirmed' AND start_utc < %s AND end_utc > %s",
				gmdate( 'Y-m-d H:i:s', $range_end_ts ),
				gmdate( 'Y-m-d H:i:s', $range_start_ts )
			),
			ARRAY_A
		);

		$intervals = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$start = DateTimeImmutable::createFromFormat( '!Y-m-d H:i:s', $row['start_utc'], new DateTimeZone( 'UTC' ) );
			$end   = DateTimeImmutable::createFromFormat( '!Y-m-d H:i:s', $row['end_utc'], new DateTimeZone( 'UTC' ) );
			if ( $start && $end ) {
				$intervals[] = array(
					'start_ts' => $start->getTimestamp(),
					'end_ts'   => $end->getTimestamp(),
				);
			}
		}

		return $intervals;
	}

	/**
	 * Validate again and insert while holding one site-wide database lock.
	 * Every booking writer in this plugin uses the same lock, making the overlap
	 * check and insert atomic even when two visitors choose a slot together. The
	 * lock uses WordPress' atomic option insert so it also works on SQLite-based
	 * development environments instead of relying on MySQL-only GET_LOCK().
	 *
	 * @param array<string,mixed> $data Sanitized booking fields.
	 * @return array<string,mixed>|WP_Error
	 */
	public static function create( array $data ) {
		global $wpdb;
		$settings = IABC_Plugin::settings();
		$timezone = new DateTimeZone( 'Europe/Warsaw' );

		try {
			$requested = new DateTimeImmutable( (string) $data['start'] );
		} catch ( Exception $exception ) {
			return new WP_Error( 'iabc_invalid_slot', 'Invalid time.' );
		}
		$requested = $requested->setTimezone( $timezone );

		$lock = self::acquire_booking_lock( 5 );
		if ( ! is_array( $lock ) ) {
			return new WP_Error( 'iabc_lock_unavailable', 'Booking is temporarily busy. Please try again.', array( 'status' => 503 ) );
		}

		try {
			$slots = self::day_slots( $requested->format( 'Y-m-d' ) );
			if ( is_wp_error( $slots ) ) {
				return $slots;
			}

			$chosen = null;
			foreach ( $slots as $slot ) {
				if ( (int) $slot['start_ts'] === $requested->getTimestamp() && ! empty( $slot['available'] ) ) {
					$chosen = $slot;
					break;
				}
			}

			if ( null === $chosen ) {
				return new WP_Error( 'iabc_slot_taken', 'The selected time is no longer available.', array( 'status' => 409 ) );
			}

			try {
				$token = bin2hex( random_bytes( 32 ) );
			} catch ( Exception $exception ) {
				return new WP_Error( 'iabc_token_error', 'Could not create a secure booking token.', array( 'status' => 500 ) );
			}

			$created = current_time( 'mysql', true );
			$record  = array(
				'status'                 => 'confirmed',
				'lang'                   => $data['lang'],
				'customer_name'          => $data['name'],
				'customer_email'         => $data['email'],
				'company'                => $data['company'],
				'phone'                  => $data['phone'],
				'workflow_bottleneck'    => $data['bottleneck'],
				'privacy_acknowledged_at_utc' => $created,
				'privacy_policy_url'      => $data['privacy_url'],
				'start_utc'               => gmdate( 'Y-m-d H:i:s', (int) $chosen['start_ts'] ),
				'end_utc'                 => gmdate( 'Y-m-d H:i:s', (int) $chosen['end_ts'] ),
				'token_hash'              => hash( 'sha256', $token ),
				'customer_email_sent'     => 0,
				'admin_email_sent'        => 0,
				'created_at_utc'          => $created,
			);

			$inserted = $wpdb->insert(
				IABC_Plugin::table_name(),
				$record,
				array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s' )
			);
			if ( false === $inserted ) {
				return new WP_Error( 'iabc_insert_failed', 'Could not save the booking.', array( 'status' => 500 ) );
			}

			$record['id']       = (int) $wpdb->insert_id;
			$record['token']    = $token;
			$record['start_ts'] = (int) $chosen['start_ts'];
			$record['end_ts']   = (int) $chosen['end_ts'];
			return $record;
		} finally {
			self::release_booking_lock( $lock );
		}
	}

	/**
	 * Acquire the short site-wide booking lock.
	 *
	 * add_option() ultimately uses a unique option_name insert, so exactly one
	 * request owns the lock. The expiry lets a later request recover if PHP exits
	 * before the finally block can release it.
	 *
	 * @param int $timeout_seconds Maximum time to wait.
	 * @return array{name:string,value:string}|null
	 */
	private static function acquire_booking_lock( $timeout_seconds ) {
		global $wpdb;

		$name     = '_iabc_booking_lock_' . substr( hash( 'sha256', $wpdb->prefix . ':' . get_current_blog_id() ), 0, 32 );
		$deadline = microtime( true ) + max( 1, (int) $timeout_seconds );
		try {
			$token = bin2hex( random_bytes( 16 ) );
		} catch ( Exception $exception ) {
			$token = wp_generate_password( 32, false, false );
		}

		do {
			$now   = time();
			$value = $token . '|' . ( $now + 15 );
			if ( add_option( $name, $value, '', false ) ) {
				return array(
					'name'  => $name,
					'value' => $value,
				);
			}

			$current = (string) get_option( $name, '' );
			$parts   = explode( '|', $current, 2 );
			if ( 2 === count( $parts ) && (int) $parts[1] < $now ) {
				$deleted = $wpdb->delete(
					$wpdb->options,
					array(
						'option_name'  => $name,
						'option_value' => $current,
					),
					array( '%s', '%s' )
				);
				if ( $deleted ) {
					wp_cache_delete( $name, 'options' );
					continue;
				}
			}

			if ( microtime( true ) < $deadline ) {
				usleep( 100000 );
			}
		} while ( microtime( true ) < $deadline );

		return null;
	}

	/** @param array{name:string,value:string} $lock @return void */
	private static function release_booking_lock( array $lock ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->options,
			array(
				'option_name'  => $lock['name'],
				'option_value' => $lock['value'],
			),
			array( '%s', '%s' )
		);
		wp_cache_delete( $lock['name'], 'options' );
	}

	/** @param string $token @return array<string,mixed>|null */
	public static function find_by_token( $token ) {
		global $wpdb;
		$token = strtolower( trim( (string) $token ) );
		if ( ! preg_match( '/^[a-f0-9]{64}$/', $token ) ) {
			return null;
		}

		$row = $wpdb->get_row(
			$wpdb->prepare( 'SELECT * FROM ' . IABC_Plugin::table_name() . ' WHERE token_hash = %s LIMIT 1', hash( 'sha256', $token ) ),
			ARRAY_A
		);

		return is_array( $row ) ? $row : null;
	}

	/** @param int $id @param bool $customer_sent @param bool $admin_sent @return void */
	public static function record_mail_results( $id, $customer_sent, $admin_sent ) {
		global $wpdb;
		$wpdb->update(
			IABC_Plugin::table_name(),
			array(
				'customer_email_sent' => $customer_sent ? 1 : 0,
				'admin_email_sent'    => $admin_sent ? 1 : 0,
			),
			array( 'id' => absint( $id ) ),
			array( '%d', '%d' ),
			array( '%d' )
		);
	}

	/** @param int $limit @param int $offset @return array<int,array<string,mixed>> */
	public static function list_bookings( $limit, $offset ) {
		global $wpdb;
		$sql = $wpdb->prepare( 'SELECT * FROM ' . IABC_Plugin::table_name() . ' ORDER BY start_utc DESC LIMIT %d OFFSET %d', absint( $limit ), absint( $offset ) );
		$rows = $wpdb->get_results( $sql, ARRAY_A );
		return is_array( $rows ) ? $rows : array();
	}

	/** @return int */
	public static function count_bookings() {
		global $wpdb;
		return (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . IABC_Plugin::table_name() );
	}

	/** @return int Number of booking records removed. */
	public static function delete_expired() {
		global $wpdb;
		$months = max( 1, (int) IABC_Plugin::settings()['retention_months'] );
		$cutoff = ( new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ) )
			->modify( '-' . $months . ' months' )
			->format( 'Y-m-d H:i:s' );
		$deleted = $wpdb->query(
			$wpdb->prepare(
				'DELETE FROM ' . IABC_Plugin::table_name() . ' WHERE created_at_utc < %s',
				$cutoff
			)
		);

		return false === $deleted ? 0 : (int) $deleted;
	}

	/** @param int $id @return bool */
	public static function cancel( $id ) {
		global $wpdb;
		$updated = $wpdb->update(
			IABC_Plugin::table_name(),
			array(
				'status'           => 'cancelled',
				'cancelled_at_utc' => current_time( 'mysql', true ),
			),
			array(
				'id'     => absint( $id ),
				'status' => 'confirmed',
			),
			array( '%s', '%s' ),
			array( '%d', '%s' )
		);
		return false !== $updated && $updated > 0;
	}
}
