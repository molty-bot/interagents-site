<?php

/**
 * Pure-PHP slot rules. This file intentionally has no WordPress dependency so
 * tests can exercise the production algorithm deterministically.
 */
final class IABC_Slot_Engine {
	/**
	 * @param DateTimeImmutable            $day              Any time on the requested local day.
	 * @param DateTimeImmutable            $now              Deterministic current time.
	 * @param array<string,mixed>          $settings         Validated plugin settings.
	 * @param array<int,array<string,int>> $booked_intervals UTC timestamp intervals.
	 * @return array<int,array<string,mixed>>
	 */
	public static function generate( DateTimeImmutable $day, DateTimeImmutable $now, array $settings, array $booked_intervals = array() ) {
		$timezone = new DateTimeZone( isset( $settings['timezone'] ) ? (string) $settings['timezone'] : 'Europe/Warsaw' );
		$day      = $day->setTimezone( $timezone )->setTime( 0, 0, 0 );
		$now      = $now->setTimezone( $timezone );
		$weekdays = isset( $settings['weekdays'] ) && is_array( $settings['weekdays'] ) ? array_map( 'intval', $settings['weekdays'] ) : array( 1, 2, 3, 4, 5 );

		if ( ! in_array( (int) $day->format( 'N' ), $weekdays, true ) ) {
			return array();
		}

		$today   = $now->setTime( 0, 0, 0 );
		$minimum_day = $today->modify( '+' . max( 0, (int) ( isset( $settings['min_booking_days'] ) ? $settings['min_booking_days'] : 1 ) ) . ' days' );
		$horizon = $today->modify( '+' . max( 1, (int) $settings['horizon_days'] ) . ' days' );
		if ( $day < $minimum_day || $day > $horizon ) {
			return array();
		}

		$open_parts  = self::time_parts( isset( $settings['work_start'] ) ? (string) $settings['work_start'] : '10:00', array( 10, 0 ) );
		$close_parts = self::time_parts( isset( $settings['work_end'] ) ? (string) $settings['work_end'] : '15:00', array( 15, 0 ) );
		$open        = $day->setTime( $open_parts[0], $open_parts[1], 0 );
		$close       = $day->setTime( $close_parts[0], $close_parts[1], 0 );
		$duration    = max( 5, (int) $settings['duration_min'] );
		$step        = max( 5, (int) $settings['step_min'] );

		if ( $close <= $open || $open->modify( '+' . $duration . ' minutes' ) > $close ) {
			return array();
		}

		$minimum_ts = $now->getTimestamp() + ( max( 0, (int) $settings['notice_hours'] ) * 3600 );
		$slots      = array();
		$cursor     = $open;

		while ( $cursor->modify( '+' . $duration . ' minutes' ) <= $close ) {
			$end       = $cursor->modify( '+' . $duration . ' minutes' );
			$start_ts  = $cursor->getTimestamp();
			$end_ts    = $end->getTimestamp();
			$available = $start_ts >= $minimum_ts;

			if ( $available ) {
				foreach ( $booked_intervals as $interval ) {
					$busy_start = isset( $interval['start_ts'] ) ? (int) $interval['start_ts'] : 0;
					$busy_end   = isset( $interval['end_ts'] ) ? (int) $interval['end_ts'] : 0;
					if ( self::overlaps( $start_ts, $end_ts, $busy_start, $busy_end ) ) {
						$available = false;
						break;
					}
				}
			}

			$slots[] = array(
				'start'     => $cursor->format( DateTimeInterface::ATOM ),
				'end'       => $end->format( DateTimeInterface::ATOM ),
				'start_ts'  => $start_ts,
				'end_ts'    => $end_ts,
				'label'     => $cursor->format( 'H:i' ) . '–' . $end->format( 'H:i' ),
				'available' => $available,
			);

			$cursor = $cursor->modify( '+' . $step . ' minutes' );
		}

		return $slots;
	}

	/** @return bool */
	public static function overlaps( $a_start, $a_end, $b_start, $b_end ) {
		return (int) $a_start < (int) $b_end && (int) $a_end > (int) $b_start;
	}

	/** @param string $value @param array<int,int> $fallback @return array<int,int> */
	private static function time_parts( $value, array $fallback ) {
		if ( ! preg_match( '/^(\\d{2}):(\\d{2})$/', $value, $matches ) ) {
			return $fallback;
		}

		$hour   = (int) $matches[1];
		$minute = (int) $matches[2];
		if ( $hour > 23 || $minute > 59 ) {
			return $fallback;
		}

		return array( $hour, $minute );
	}
}
