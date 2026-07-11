<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class IABC_Rate_Limit {
	const MAX_ATTEMPTS = 5;
	const WINDOW       = 900;

	/** @return string */
	public static function client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return (string) apply_filters( 'iabc_client_ip', $ip );
	}

	/** @param string $ip @return string */
	public static function hash_ip( $ip ) {
		return hash_hmac( 'sha256', (string) $ip, wp_salt( 'nonce' ) );
	}

	/** @param string $ip @return bool */
	public static function consume( $ip ) {
		$key   = 'iabc_rate_' . substr( self::hash_ip( $ip ), 0, 32 );
		$count = (int) get_transient( $key );
		if ( $count >= self::MAX_ATTEMPTS ) {
			return false;
		}

		set_transient( $key, $count + 1, self::WINDOW );
		return true;
	}
}
