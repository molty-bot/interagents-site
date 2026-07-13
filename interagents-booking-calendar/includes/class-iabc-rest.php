<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class IABC_REST {
	const NAMESPACE = 'interagents-booking/v1';

	/** @return void */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/** @return void */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/slots',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'slots' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'date' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'lang' => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/book',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'book' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/** @return WP_REST_Response|WP_Error */
	public function slots( WP_REST_Request $request ) {
		$lang  = self::language( $request->get_param( 'lang' ) );
		$slots = IABC_Bookings::day_slots( (string) $request->get_param( 'date' ) );
		$dates = IABC_Public::calendar_dates( IABC_Plugin::settings() );
		if ( is_wp_error( $slots ) ) {
			return self::error( 'invalid_date', $lang, 400 );
		}

		$available = array();
		foreach ( $slots as $slot ) {
			if ( ! empty( $slot['available'] ) ) {
				$available[] = array(
					'start' => $slot['start'],
					'end'   => $slot['end'],
					'label' => $slot['label'],
				);
			}
		}

		$response = new WP_REST_Response(
			array(
				'date'           => (string) $request->get_param( 'date' ),
				'timezone'       => 'Europe/Warsaw',
				'slots'          => $available,
				'min_date'       => $dates['min_date'],
				'max_date'       => $dates['max_date'],
				'suggested_date' => $dates['suggested_date'],
				// Refresh the public form token through this uncached endpoint. This
				// avoids stale nonces from full-page caches and works for logged-in
				// administrators whose REST request is intentionally anonymous.
				'booking_nonce'  => wp_create_nonce( 'iabc_public_booking' ),
			),
			200
		);
		$response->header( 'Cache-Control', 'no-store, max-age=0' );
		return $response;
	}

	/** @return WP_REST_Response|WP_Error */
	public function book( WP_REST_Request $request ) {
		$params = $request->get_json_params();
		$params = is_array( $params ) ? $params : array();
		$lang   = self::language( isset( $params['lang'] ) ? $params['lang'] : 'en' );
		$ip     = IABC_Rate_Limit::client_ip();

		if ( ! IABC_Rate_Limit::consume( $ip ) ) {
			return self::error( 'rate_limited', $lang, 429 );
		}
		if ( empty( $params['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( (string) $params['nonce'] ), 'iabc_public_booking' ) ) {
			return self::error( 'session_expired', $lang, 403 );
		}
		if ( ! empty( $params['website'] ) ) {
			return self::error( 'invalid_request', $lang, 400 );
		}

		$name       = isset( $params['name'] ) ? sanitize_text_field( (string) $params['name'] ) : '';
		$email      = isset( $params['email'] ) ? sanitize_email( (string) $params['email'] ) : '';
		$company    = isset( $params['company'] ) ? sanitize_text_field( (string) $params['company'] ) : '';
		$phone      = isset( $params['phone'] ) ? sanitize_text_field( (string) $params['phone'] ) : '';
		$bottleneck = isset( $params['bottleneck'] ) ? sanitize_textarea_field( (string) $params['bottleneck'] ) : '';
		$acknowledged = ! empty( $params['privacy_acknowledged'] ) && in_array( $params['privacy_acknowledged'], array( true, 1, '1', 'on' ), true );

		if ( '' === $name ) {
			return self::error( 'name_required', $lang, 400 );
		}
		if ( ! $email || ! is_email( $email ) ) {
			return self::error( 'email_invalid', $lang, 400 );
		}
		$phone_digits = preg_replace( '/\D+/', '', $phone );
		if ( '' === $phone || ! is_string( $phone_digits ) || strlen( $phone_digits ) < 6 ) {
			return self::error( 'phone_required', $lang, 400 );
		}
		if ( ! $acknowledged ) {
			return self::error( 'consent_required', $lang, 400 );
		}

		$privacy_url = get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' );
		$privacy_url = add_query_arg( 'lang', $lang, $privacy_url );
		$data        = array(
			'start'       => isset( $params['start'] ) ? sanitize_text_field( (string) $params['start'] ) : '',
			'lang'        => $lang,
			'name'        => substr( $name, 0, 120 ),
			'email'       => substr( $email, 0, 190 ),
			'company'     => substr( $company, 0, 190 ),
			'phone'       => substr( $phone, 0, 50 ),
			'bottleneck'  => substr( $bottleneck, 0, 3000 ),
			'privacy_url' => $privacy_url,
		);

		$booking = IABC_Bookings::create( $data );
		if ( is_wp_error( $booking ) ) {
			$status = $booking->get_error_data();
			$status = is_array( $status ) && isset( $status['status'] ) ? (int) $status['status'] : 400;
			$code   = 'iabc_slot_taken' === $booking->get_error_code() ? 'slot_taken' : ( 'iabc_lock_unavailable' === $booking->get_error_code() ? 'busy' : 'booking_failed' );
			return self::error( $code, $lang, $status );
		}

		IABC_Mailer::send_confirmation( $booking );
		$start = ( new DateTimeImmutable( '@' . $booking['start_ts'] ) )->setTimezone( new DateTimeZone( 'Europe/Warsaw' ) );
		$end   = ( new DateTimeImmutable( '@' . $booking['end_ts'] ) )->setTimezone( new DateTimeZone( 'Europe/Warsaw' ) );
		return new WP_REST_Response(
			array(
				'success' => true,
				'when'    => $start->format( 'Y-m-d H:i' ) . '–' . $end->format( 'H:i' ) . ' (Europe/Warsaw)',
				'ics_url' => IABC_ICS::url( $booking['token'] ),
			),
			201
		);
	}

	/** @param mixed $candidate @return string */
	public static function language( $candidate = '' ) {
		$candidate = sanitize_key( (string) $candidate );
		return 'pl' === $candidate ? 'pl' : 'en';
	}

	/** @param string $code @param string $lang @param int $status @return WP_Error */
	private static function error( $code, $lang, $status ) {
		$messages = array(
			'invalid_date'     => array( 'en' => 'Choose a valid date.', 'pl' => 'Wybierz prawidłową datę.' ),
			'rate_limited'     => array( 'en' => 'Too many attempts. Try again in 15 minutes.', 'pl' => 'Zbyt wiele prób. Spróbuj ponownie za 15 minut.' ),
			'session_expired'  => array( 'en' => 'This form has expired. Refresh the page and try again.', 'pl' => 'Formularz wygasł. Odśwież stronę i spróbuj ponownie.' ),
			'invalid_request'  => array( 'en' => 'We could not process this request.', 'pl' => 'Nie mogliśmy przetworzyć tego zgłoszenia.' ),
			'name_required'    => array( 'en' => 'Enter your name.', 'pl' => 'Podaj imię i nazwisko.' ),
			'email_invalid'    => array( 'en' => 'Enter a valid business email.', 'pl' => 'Podaj prawidłowy e-mail służbowy.' ),
			'phone_required'   => array( 'en' => 'Enter a valid phone number.', 'pl' => 'Podaj prawidłowy numer telefonu.' ),
			'consent_required' => array( 'en' => 'Privacy policy acknowledgement is required.', 'pl' => 'Potwierdzenie zapoznania się z polityką prywatności jest wymagane.' ),
			'slot_taken'       => array( 'en' => 'That time was just booked.', 'pl' => 'Ten termin został właśnie zajęty.' ),
			'busy'             => array( 'en' => 'Booking is temporarily busy. Please try again.', 'pl' => 'Rezerwacja jest chwilowo zajęta. Spróbuj ponownie.' ),
			'booking_failed'   => array( 'en' => 'We could not save the booking. Please try again.', 'pl' => 'Nie udało się zapisać rezerwacji. Spróbuj ponownie.' ),
		);
		$message = isset( $messages[ $code ][ $lang ] ) ? $messages[ $code ][ $lang ] : $messages['booking_failed'][ $lang ];
		return new WP_Error( 'iabc_' . $code, $message, array( 'status' => $status ) );
	}
}
