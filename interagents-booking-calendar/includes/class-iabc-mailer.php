<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class IABC_Mailer {
	/** @param array<string,mixed> $booking @return array<string,bool> */
	public static function send_confirmation( array $booking ) {
		$lang       = 'pl' === $booking['lang'] ? 'pl' : 'en';
		$start      = ( new DateTimeImmutable( '@' . $booking['start_ts'] ) )->setTimezone( new DateTimeZone( 'Europe/Warsaw' ) );
		$end        = ( new DateTimeImmutable( '@' . $booking['end_ts'] ) )->setTimezone( new DateTimeZone( 'Europe/Warsaw' ) );
		$duration   = max( 1, (int) round( ( $booking['end_ts'] - $booking['start_ts'] ) / 60 ) );
		$when       = $start->format( 'Y-m-d H:i' ) . '–' . $end->format( 'H:i' ) . ' (Europe/Warsaw)';
		$ics_url    = IABC_ICS::url( $booking['token'] );
		$admin_mail = IABC_Plugin::settings()['notification_email'];
		$customer_headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		$admin_headers    = $customer_headers;
		$admin_headers[]  = 'Reply-To: ' . sanitize_text_field( $booking['customer_name'] ) . ' <' . sanitize_email( $booking['customer_email'] ) . '>';

		if ( 'pl' === $lang ) {
			$customer_subject = 'Twoje spotkanie z interagents zostało zarezerwowane';
			$customer_body    = "Cześć {$booking['customer_name']},\n\nTwoja bezpłatna, {$duration}-minutowa rozmowa została zarezerwowana.\nTermin: {$when}\n\nLink do spotkania i szczegóły dołączenia wyślemy osobno e-mailem. Nie musisz zakładać konta ani dokonywać płatności.\n\nDodaj termin do kalendarza (plik .ics):\n{$ics_url}\n\nDo zobaczenia,\ninteragents.ai";
			$admin_subject    = 'Nowa rezerwacja interagents: ' . $booking['customer_name'] . ' — ' . $start->format( 'Y-m-d H:i' );
			$admin_body       = self::admin_body( $booking, $when, 'pl' );
		} else {
			$customer_subject = 'Your interagents meeting is booked';
			$customer_body    = "Hi {$booking['customer_name']},\n\nYour free {$duration}-minute workflow call is booked.\nTime: {$when}\n\nWe’ll email the meeting link and joining details separately. No account or payment is required.\n\nAdd it to your calendar (.ics file):\n{$ics_url}\n\nSee you soon,\ninteragents.ai";
			$admin_subject    = 'New interagents booking: ' . $booking['customer_name'] . ' — ' . $start->format( 'Y-m-d H:i' );
			$admin_body       = self::admin_body( $booking, $when, 'en' );
		}

		$customer_sent = (bool) wp_mail( $booking['customer_email'], $customer_subject, $customer_body, $customer_headers );
		$admin_sent    = (bool) wp_mail( $admin_mail, $admin_subject, $admin_body, $admin_headers );
		IABC_Bookings::record_mail_results( $booking['id'], $customer_sent, $admin_sent );

		return array(
			'customer' => $customer_sent,
			'admin'    => $admin_sent,
		);
	}

	/** @param array<string,mixed> $booking @param string $when @param string $lang @return string */
	private static function admin_body( array $booking, $when, $lang ) {
		if ( 'pl' === $lang ) {
			return "Nowa rezerwacja spotkania interagents.\n\nTermin: {$when}\nImię i nazwisko: {$booking['customer_name']}\nE-mail służbowy: {$booking['customer_email']}\nFirma: {$booking['company']}\nTelefon: {$booking['phone']}\nWiadomość: {$booking['workflow_bottleneck']}\n\nLink do spotkania i szczegóły należy wysłać klientowi osobno.";
		}

		return "New interagents workflow-call booking.\n\nTime: {$when}\nName: {$booking['customer_name']}\nBusiness email: {$booking['customer_email']}\nCompany: {$booking['company']}\nPhone: {$booking['phone']}\nMessage: {$booking['workflow_bottleneck']}\n\nSend the meeting link and joining details to the customer separately.";
	}
}
