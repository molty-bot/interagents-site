<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class IABC_ICS {
	/** @return void */
	public function init() {
		add_action( 'admin_post_iabc_download_ics', array( $this, 'download' ) );
		add_action( 'admin_post_nopriv_iabc_download_ics', array( $this, 'download' ) );
	}

	/** @param string $token @return string */
	public static function url( $token ) {
		return add_query_arg(
			array(
				'action' => 'iabc_download_ics',
				'token'  => rawurlencode( (string) $token ),
			),
			admin_url( 'admin-post.php' )
		);
	}

	/** @return void */
	public function download() {
		$token   = isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : '';
		$booking = IABC_Bookings::find_by_token( $token );
		if ( ! $booking || 'confirmed' !== $booking['status'] ) {
			status_header( 404 );
			exit;
		}

		$start = strtotime( $booking['start_utc'] . ' UTC' );
		$end   = strtotime( $booking['end_utc'] . ' UTC' );
		$uid   = substr( $booking['token_hash'], 0, 32 ) . '@interagents.ai';
		$lines = array(
			'BEGIN:VCALENDAR',
			'VERSION:2.0',
			'PRODID:-//Interagents.ai//Booking Calendar//EN',
			'CALSCALE:GREGORIAN',
			'METHOD:PUBLISH',
			'BEGIN:VEVENT',
			'UID:' . self::escape( $uid ),
			'DTSTAMP:' . gmdate( 'Ymd\\THis\\Z' ),
			'DTSTART:' . gmdate( 'Ymd\\THis\\Z', $start ),
			'DTEND:' . gmdate( 'Ymd\\THis\\Z', $end ),
			'SUMMARY:' . self::escape( 'Interagents workflow call' ),
			'DESCRIPTION:' . self::escape( 'The meeting link and joining details will be sent separately by email.' ),
			'LOCATION:' . self::escape( 'Details will follow by email' ),
			'STATUS:CONFIRMED',
			'END:VEVENT',
			'END:VCALENDAR',
		);

		nocache_headers();
		header( 'X-Robots-Tag: noindex, nofollow', true );
		header( 'Content-Type: text/calendar; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="interagents-meeting-' . absint( $booking['id'] ) . '.ics"' );
		echo implode( "\r\n", $lines ) . "\r\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- RFC 5545 output is escaped above.
		exit;
	}

	/** @param string $value @return string */
	private static function escape( $value ) {
		$value = str_replace( '\\', '\\\\', (string) $value );
		$value = str_replace( array( "\r\n", "\r", "\n" ), '\\n', $value );
		return str_replace( array( ';', ',' ), array( '\\;', '\\,' ), $value );
	}
}
