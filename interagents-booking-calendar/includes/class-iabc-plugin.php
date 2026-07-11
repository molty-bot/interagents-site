<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class IABC_Plugin {
	const OPTION_SETTINGS = 'iabc_settings';
	const OPTION_VERSION  = 'iabc_db_version';

	/** @var IABC_Plugin|null */
	private static $instance = null;

	/** @return IABC_Plugin */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/** @return array<string,mixed> */
	public static function defaults() {
		return array(
			'notification_email' => 'hello@interagents.ai',
			'work_start'         => '10:00',
			'work_end'           => '16:00',
			'duration_min'       => 20,
			'step_min'           => 30,
			'notice_hours'       => 24,
			'horizon_days'       => 60,
			'retention_months'   => 12,
			'weekdays'           => array( 1, 2, 3, 4, 5 ),
			'timezone'           => 'Europe/Warsaw',
		);
	}

	/** @return array<string,mixed> */
	public static function settings() {
		$saved = get_option( self::OPTION_SETTINGS, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		$settings             = array_merge( self::defaults(), $saved );
		$settings['timezone'] = 'Europe/Warsaw';
		$settings['retention_months'] = 12;
		return $settings;
	}

	/** @param mixed $input @return array<string,mixed> */
	public static function sanitize_settings( $input ) {
		$defaults = self::defaults();
		$input    = is_array( $input ) ? $input : array();
		$email    = isset( $input['notification_email'] ) ? sanitize_email( $input['notification_email'] ) : '';
		$start    = self::sanitize_time( isset( $input['work_start'] ) ? $input['work_start'] : '', $defaults['work_start'] );
		$end      = self::sanitize_time( isset( $input['work_end'] ) ? $input['work_end'] : '', $defaults['work_end'] );

		if ( self::time_to_minutes( $end ) <= self::time_to_minutes( $start ) ) {
			$start = $defaults['work_start'];
			$end   = $defaults['work_end'];
			add_settings_error( self::OPTION_SETTINGS, 'iabc_hours', __( 'Closing time must be later than opening time. Safe defaults were restored.', 'interagents-booking-calendar' ) );
		}

		$weekdays = isset( $input['weekdays'] ) && is_array( $input['weekdays'] ) ? array_map( 'absint', $input['weekdays'] ) : $defaults['weekdays'];
		$weekdays = array_values( array_intersect( array( 1, 2, 3, 4, 5, 6, 7 ), array_unique( $weekdays ) ) );
		if ( empty( $weekdays ) ) {
			$weekdays = $defaults['weekdays'];
		}

		return array(
			'notification_email' => $email && is_email( $email ) ? $email : $defaults['notification_email'],
			'work_start'         => $start,
			'work_end'           => $end,
			'duration_min'       => max( 5, min( 240, absint( isset( $input['duration_min'] ) ? $input['duration_min'] : $defaults['duration_min'] ) ) ),
			'step_min'           => max( 5, min( 120, absint( isset( $input['step_min'] ) ? $input['step_min'] : $defaults['step_min'] ) ) ),
			'notice_hours'       => max( 0, min( 720, absint( isset( $input['notice_hours'] ) ? $input['notice_hours'] : $defaults['notice_hours'] ) ) ),
			'horizon_days'       => max( 1, min( 365, absint( isset( $input['horizon_days'] ) ? $input['horizon_days'] : $defaults['horizon_days'] ) ) ),
			'retention_months'   => 12,
			'weekdays'           => $weekdays,
			'timezone'           => 'Europe/Warsaw',
		);
	}

	/** @param mixed $value @param string $fallback @return string */
	private static function sanitize_time( $value, $fallback ) {
		$value = sanitize_text_field( (string) $value );
		if ( ! preg_match( '/^(?:[01]\\d|2[0-3]):[0-5]\\d$/', $value ) ) {
			return $fallback;
		}

		return $value;
	}

	/** @param string $time @return int */
	private static function time_to_minutes( $time ) {
		$parts = array_map( 'intval', explode( ':', $time ) );
		return ( $parts[0] * 60 ) + $parts[1];
	}

	/** @return string */
	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . 'iabc_bookings';
	}

	/** @return void */
	public static function activate() {
		global $wpdb;

		$current = get_option( self::OPTION_SETTINGS, array() );
		$current = is_array( $current ) ? $current : array();
		update_option( self::OPTION_SETTINGS, array_merge( self::defaults(), $current ) );

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table           = self::table_name();
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			status varchar(20) NOT NULL DEFAULT 'confirmed',
			lang char(2) NOT NULL DEFAULT 'en',
			customer_name varchar(120) NOT NULL,
			customer_email varchar(190) NOT NULL,
			company varchar(190) NOT NULL DEFAULT '',
			phone varchar(50) NOT NULL DEFAULT '',
			workflow_bottleneck text NULL,
			privacy_acknowledged_at_utc datetime NOT NULL,
			privacy_policy_url varchar(500) NOT NULL DEFAULT '',
			start_utc datetime NOT NULL,
			end_utc datetime NOT NULL,
			token_hash char(64) NOT NULL,
			customer_email_sent tinyint(1) NOT NULL DEFAULT 0,
			admin_email_sent tinyint(1) NOT NULL DEFAULT 0,
			created_at_utc datetime NOT NULL,
			cancelled_at_utc datetime NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY token_hash (token_hash),
			KEY status_start (status,start_utc),
			KEY customer_email (customer_email)
		) {$charset_collate};";

		dbDelta( $sql );
		update_option( self::OPTION_VERSION, IABC_VERSION );
		if ( ! wp_next_scheduled( 'iabc_cleanup_bookings' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'iabc_cleanup_bookings' );
		}
	}

	/** @return void */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'iabc_cleanup_bookings' );
	}

	/** @return void */
	public function init() {
		require_once IABC_PLUGIN_DIR . 'includes/class-iabc-slot-engine.php';
		require_once IABC_PLUGIN_DIR . 'includes/class-iabc-rate-limit.php';
		require_once IABC_PLUGIN_DIR . 'includes/class-iabc-bookings.php';
		require_once IABC_PLUGIN_DIR . 'includes/class-iabc-mailer.php';
		require_once IABC_PLUGIN_DIR . 'includes/class-iabc-rest.php';
		require_once IABC_PLUGIN_DIR . 'includes/class-iabc-public.php';
		require_once IABC_PLUGIN_DIR . 'includes/class-iabc-ics.php';
		require_once IABC_PLUGIN_DIR . 'includes/class-iabc-admin.php';

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_init', array( $this, 'maybe_upgrade' ), 1 );
		add_action( 'iabc_cleanup_bookings', array( 'IABC_Bookings', 'delete_expired' ) );
		( new IABC_REST() )->init();
		( new IABC_Public() )->init();
		( new IABC_ICS() )->init();
		( new IABC_Admin() )->init();
	}

	/** @return void */
	public function load_textdomain() {
		load_plugin_textdomain( 'interagents-booking-calendar', false, dirname( plugin_basename( IABC_PLUGIN_FILE ) ) . '/languages' );
	}

	/** @return void */
	public function maybe_upgrade() {
		if ( (string) get_option( self::OPTION_VERSION, '' ) !== IABC_VERSION ) {
			self::activate();
		}
	}
}
