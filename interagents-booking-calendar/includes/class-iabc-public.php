<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class IABC_Public {
	/** @return void */
	public function init() {
		add_shortcode( 'interagents_booking_calendar', array( $this, 'shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Load assets before wp_head so hard-coded template shortcodes are styled
	 * reliably. The files are small and the script exits immediately when no
	 * booking widget is present.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'iabc-booking', IABC_PLUGIN_URL . 'assets/css/interagents-booking-calendar.css', array(), IABC_VERSION );
		wp_enqueue_script( 'iabc-booking', IABC_PLUGIN_URL . 'assets/js/interagents-booking-calendar.js', array(), IABC_VERSION, true );
	}

	/** @param array<string,mixed> $attributes @return string */
	public function shortcode( $attributes = array() ) {
		$attributes = shortcode_atts(
			array(
				'lang'     => '',
				'embedded' => '0',
			),
			$attributes,
			'interagents_booking_calendar'
		);
		$lang       = self::detect_language( $attributes['lang'] );
		$embedded   = in_array( strtolower( trim( (string) $attributes['embedded'] ) ), array( '1', 'true', 'yes', 'on' ), true );
		$settings   = IABC_Plugin::settings();
		$duration   = (int) $settings['duration_min'];
		$dates      = self::calendar_dates( $settings );
		$privacy    = get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' );
		$privacy    = add_query_arg( 'lang', $lang, $privacy );
		$widget_id  = wp_unique_id( 'iabc-booking-' );

		$config = array(
			'slotsUrl'      => rest_url( IABC_REST::NAMESPACE . '/slots' ),
			'bookUrl'       => rest_url( IABC_REST::NAMESPACE . '/book' ),
			'nonce'         => wp_create_nonce( 'iabc_public_booking' ),
			'lang'          => $lang,
			'weekdays'      => array_values( array_map( 'absint', (array) $settings['weekdays'] ) ),
			'minDate'       => $dates['min_date'],
			'maxDate'       => $dates['max_date'],
			'suggestedDate' => $dates['suggested_date'],
			'strings'       => self::strings( $lang ),
		);

		ob_start();
		?>
		<section class="iabc-booking<?php echo $embedded ? ' iabc-booking--embedded' : ''; ?>" id="<?php echo esc_attr( $widget_id ); ?>" data-iabc-booking <?php if ( $embedded ) : ?>aria-label="<?php echo esc_attr( 'pl' === $lang ? 'Kalendarz spotkań interagents' : 'interagents meeting calendar' ); ?>"<?php else : ?>aria-labelledby="<?php echo esc_attr( $widget_id ); ?>-title"<?php endif; ?>>
			<script type="application/json" class="iabc-booking__config"><?php echo wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
			<?php if ( ! $embedded ) : ?>
				<div class="iabc-booking__header">
					<p class="iabc-booking__eyebrow"><?php echo esc_html( 'pl' === $lang ? sprintf( 'BEZPŁATNA ROZMOWA · %d MINUT', $duration ) : sprintf( 'FREE WORKFLOW CALL · %d MINUTES', $duration ) ); ?></p>
					<h2 id="<?php echo esc_attr( $widget_id ); ?>-title"><?php echo esc_html( 'pl' === $lang ? 'Przynieś jeden proces, który co tydzień zabiera Ci czas.' : 'Bring us one workflow that wastes your week.' ); ?></h2>
					<p><?php echo esc_html( 'pl' === $lang ? sprintf( 'W %d minut powiemy, czy wystarczy interagents, proces potrzebuje intercore, czy AI nie jest odpowiedzią.', $duration ) : sprintf( 'In %d minutes, we’ll tell you whether interagents is enough, the workflow needs intercore, or AI is not the answer.', $duration ) ); ?></p>
				</div>
			<?php endif; ?>

			<div class="iabc-booking__grid">
				<div class="iabc-booking__schedule">
					<div class="iabc-booking__step-heading"><span aria-hidden="true">01</span><h3><?php echo esc_html( 'pl' === $lang ? 'Wybierz dzień' : 'Choose a day' ); ?></h3></div>
					<div class="iabc-booking__calendar" data-iabc-calendar hidden></div>
					<div class="iabc-booking__date-fallback" data-iabc-date-fallback>
						<label class="iabc-booking__label" for="<?php echo esc_attr( $widget_id ); ?>-date"><?php echo esc_html( 'pl' === $lang ? 'Data spotkania' : 'Meeting date' ); ?></label>
						<input class="iabc-booking__date" id="<?php echo esc_attr( $widget_id ); ?>-date" type="date" min="<?php echo esc_attr( $config['minDate'] ); ?>" max="<?php echo esc_attr( $config['maxDate'] ); ?>" value="<?php echo esc_attr( $dates['suggested_date'] ); ?>">
					</div>
					<p class="iabc-booking__hint"><?php echo esc_html( self::availability_hint( $lang, $settings ) ); ?></p>

					<div class="iabc-booking__step-heading iabc-booking__step-heading--slots"><span aria-hidden="true">02</span><h3><?php echo esc_html( 'pl' === $lang ? 'Wybierz godzinę' : 'Choose a time' ); ?></h3></div>
					<div class="iabc-booking__slots" role="group" aria-label="<?php echo esc_attr( 'pl' === $lang ? 'Dostępne godziny' : 'Available times' ); ?>"></div>
					<div class="iabc-booking__status iabc-booking__status--slots" role="status" aria-live="polite" tabindex="-1"></div>
				</div>

				<form class="iabc-booking__form" novalidate>
					<div class="iabc-booking__step-heading"><span aria-hidden="true">03</span><h3><?php echo esc_html( 'pl' === $lang ? 'Powiedz nam, z kim rozmawiamy' : 'Tell us who we’re meeting' ); ?></h3></div>
					<input type="hidden" name="start" value="">
					<input type="hidden" name="lang" value="<?php echo esc_attr( $lang ); ?>">

					<div class="iabc-booking__field">
						<label for="<?php echo esc_attr( $widget_id ); ?>-name"><?php echo esc_html( 'pl' === $lang ? 'Imię i nazwisko' : 'Full name' ); ?> <span aria-hidden="true">*</span></label>
						<input id="<?php echo esc_attr( $widget_id ); ?>-name" name="name" type="text" maxlength="120" autocomplete="name" required>
					</div>
					<div class="iabc-booking__field">
						<label for="<?php echo esc_attr( $widget_id ); ?>-email"><?php echo esc_html( 'pl' === $lang ? 'E-mail służbowy' : 'Business email' ); ?> <span aria-hidden="true">*</span></label>
						<input id="<?php echo esc_attr( $widget_id ); ?>-email" name="email" type="email" maxlength="190" autocomplete="email" inputmode="email" required>
					</div>
					<div class="iabc-booking__field-row">
						<div class="iabc-booking__field">
							<label for="<?php echo esc_attr( $widget_id ); ?>-company"><?php echo esc_html( 'pl' === $lang ? 'Firma' : 'Company' ); ?> <small><?php echo esc_html( 'pl' === $lang ? '(opcjonalnie)' : '(optional)' ); ?></small></label>
							<input id="<?php echo esc_attr( $widget_id ); ?>-company" name="company" type="text" maxlength="190" autocomplete="organization">
						</div>
						<div class="iabc-booking__field">
							<label for="<?php echo esc_attr( $widget_id ); ?>-phone"><?php echo esc_html( 'pl' === $lang ? 'Telefon' : 'Phone' ); ?> <span aria-hidden="true">*</span></label>
							<input id="<?php echo esc_attr( $widget_id ); ?>-phone" name="phone" type="tel" minlength="6" maxlength="50" autocomplete="tel" inputmode="tel" required>
						</div>
					</div>
					<div class="iabc-booking__field">
						<label for="<?php echo esc_attr( $widget_id ); ?>-bottleneck"><?php echo esc_html( 'pl' === $lang ? 'Wiadomość' : 'Message' ); ?> <small><?php echo esc_html( 'pl' === $lang ? '(opcjonalnie)' : '(optional)' ); ?></small></label>
						<textarea id="<?php echo esc_attr( $widget_id ); ?>-bottleneck" name="bottleneck" maxlength="3000" rows="3"></textarea>
					</div>

					<div class="iabc-booking__honeypot" aria-hidden="true">
						<label for="<?php echo esc_attr( $widget_id ); ?>-website">Website</label>
						<input id="<?php echo esc_attr( $widget_id ); ?>-website" name="website" type="text" tabindex="-1" autocomplete="off">
					</div>

					<label class="iabc-booking__consent">
						<input name="privacy_acknowledged" type="checkbox" value="1" required>
						<span><?php echo esc_html( 'pl' === $lang ? 'Zapoznałem/am się z polityką prywatności i rozumiem, jak dane zostaną użyte do obsługi spotkania.' : 'I have read the privacy policy and understand how my data will be used to arrange this meeting.' ); ?> <a href="<?php echo esc_url( $privacy ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( 'pl' === $lang ? 'Polityka prywatności' : 'Privacy policy' ); ?></a>.</span>
					</label>

					<button class="iabc-booking__submit" type="submit"><?php echo esc_html( 'pl' === $lang ? 'Zarezerwuj bezpłatną rozmowę' : 'Book the free workflow call' ); ?></button>
					<div class="iabc-booking__status iabc-booking__status--form" role="status" aria-live="polite" tabindex="-1"></div>
					<p class="iabc-booking__fineprint"><?php echo esc_html( 'pl' === $lang ? 'Bez konta. Bez płatności. Szczegóły spotkania otrzymasz e-mailem.' : 'No account. No payment. Meeting details arrive by email.' ); ?></p>
				</form>
			</div>

			<div class="iabc-booking__success" hidden tabindex="-1">
				<h3><?php echo esc_html( 'pl' === $lang ? 'Termin zarezerwowany.' : 'You’re booked.' ); ?></h3>
				<p class="iabc-booking__success-when"></p>
				<p><?php echo esc_html( 'pl' === $lang ? 'Termin jest zarezerwowany. Potwierdzenie, link do spotkania i szczegóły dołączenia wyślemy osobno e-mailem.' : 'Your time is reserved. We’ll email the confirmation, meeting link and joining details separately.' ); ?></p>
				<a class="iabc-booking__ics" href="#"><?php echo esc_html( 'pl' === $lang ? 'Pobierz plik kalendarza (.ics)' : 'Download calendar file (.ics)' ); ?></a>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}

	/** @param mixed $explicit @return string */
	private static function detect_language( $explicit ) {
		$explicit = sanitize_key( (string) $explicit );
		if ( in_array( $explicit, array( 'en', 'pl' ), true ) ) {
			return $explicit;
		}
		if ( function_exists( 'ia_get_lang' ) ) {
			return 'pl' === ia_get_lang() ? 'pl' : 'en';
		}
		if ( isset( $_GET['lang'] ) && 'pl' === sanitize_key( wp_unslash( $_GET['lang'] ) ) ) {
			return 'pl';
		}
		if ( isset( $_COOKIE['ia_lang'] ) && 'pl' === sanitize_key( wp_unslash( $_COOKIE['ia_lang'] ) ) ) {
			return 'pl';
		}
		return 0 === strpos( strtolower( determine_locale() ), 'pl' ) ? 'pl' : 'en';
	}

	/** @param array<string,mixed> $settings @return array<string,string> */
	public static function calendar_dates( array $settings ) {
		$timezone    = new DateTimeZone( 'Europe/Warsaw' );
		$today       = new DateTimeImmutable( 'today', $timezone );
		$minimum_day = $today->modify( '+' . max( 0, (int) $settings['min_booking_days'] ) . ' days' );

		return array(
			'min_date'       => $minimum_day->format( 'Y-m-d' ),
			'max_date'       => $today->modify( '+' . (int) $settings['horizon_days'] . ' days' )->format( 'Y-m-d' ),
			'suggested_date' => self::suggested_date( $today, $settings ),
		);
	}

	/** @param DateTimeImmutable $today @param array<string,mixed> $settings @return string */
	private static function suggested_date( DateTimeImmutable $today, array $settings ) {
		$now          = new DateTimeImmutable( 'now', new DateTimeZone( 'Europe/Warsaw' ) );
		$horizon_days = (int) $settings['horizon_days'];
		$minimum_days = max( 0, (int) $settings['min_booking_days'] );
		$first_day    = $today->modify( '+' . $minimum_days . ' days' );
		$range_end    = $today->modify( '+' . ( $horizon_days + 1 ) . ' days' );
		$busy         = IABC_Bookings::overlaps_for_range( $first_day->getTimestamp(), $range_end->getTimestamp() );
		for ( $offset = $minimum_days; $offset <= $horizon_days; $offset++ ) {
			$day   = $today->modify( '+' . $offset . ' days' );
			$day_end = $day->modify( '+1 day' );
			$day_busy = array_values(
				array_filter(
					$busy,
					static function ( $interval ) use ( $day, $day_end ) {
						return (int) $interval['start_ts'] < $day_end->getTimestamp() && (int) $interval['end_ts'] > $day->getTimestamp();
					}
				)
			);
			$slots = IABC_Slot_Engine::generate( $day, $now, $settings, $day_busy );
			foreach ( $slots as $slot ) {
				if ( ! empty( $slot['available'] ) ) {
					return $day->format( 'Y-m-d' );
				}
			}
		}
		return $first_day->format( 'Y-m-d' );
	}

	/** @param string $lang @param array<string,mixed> $settings @return string */
	private static function availability_hint( $lang, array $settings ) {
		$minimum_days = max( 0, (int) $settings['min_booking_days'] );
		if ( 'pl' === $lang ) {
			$notice = 1 === $minimum_days
				? 'najwcześniej od następnego dnia'
				: sprintf( 'minimum %d dni kalendarzowych wcześniej', $minimum_days );
			return sprintf( 'Dostępne dni robocze, %s–%s · strefa Europe/Warsaw · %s', $settings['work_start'], $settings['work_end'], $notice );
		}

		$notice = 1 === $minimum_days
			? 'from the next calendar day'
			: sprintf( 'at least %d calendar days ahead', $minimum_days );
		return sprintf( 'Available working days, %s–%s · Europe/Warsaw · %s', $settings['work_start'], $settings['work_end'], $notice );
	}

	/** @param string $lang @return array<string,string> */
	private static function strings( $lang ) {
		if ( 'pl' === $lang ) {
			return array(
				'calendarLabel'       => 'Wybierz datę spotkania',
				'previousMonth'       => 'Poprzedni miesiąc',
				'nextMonth'           => 'Następny miesiąc',
				'previousShort'       => 'Wstecz',
				'nextShort'           => 'Dalej',
				'loading'             => 'Sprawdzamy dostępne godziny…',
				'noSlots'             => 'Tego dnia nie ma wolnych terminów. Wybierz inny dzień roboczy.',
				'chooseSlot'          => 'Najpierw wybierz godzinę spotkania.',
				'loadError'           => 'Nie udało się pobrać terminów. Spróbuj ponownie.',
				'submitting'          => 'Rezerwujemy termin…',
				'genericError'        => 'Coś poszło nie tak. Spróbuj ponownie.',
				'slotSelected'        => 'Wybrano godzinę',
				'replacementSelected' => 'Wybraliśmy kolejny dostępny termin',
				'invalidFields'       => 'Uzupełnij wymagane pola i potwierdź zapoznanie się z polityką prywatności.',
				'invalidPhone'        => 'Podaj numer telefonu zawierający co najmniej 6 cyfr.',
			);
		}
		return array(
			'calendarLabel'       => 'Choose a meeting date',
			'previousMonth'       => 'Previous month',
			'nextMonth'           => 'Next month',
			'previousShort'       => 'Previous',
			'nextShort'           => 'Next',
			'loading'             => 'Checking available times…',
			'noSlots'             => 'No times are available that day. Choose another working day.',
			'chooseSlot'          => 'Choose a meeting time first.',
			'loadError'           => 'Could not load available times. Please try again.',
			'submitting'          => 'Booking your time…',
			'genericError'        => 'Something went wrong. Please try again.',
			'slotSelected'        => 'Selected time',
			'replacementSelected' => 'We selected the next available time',
			'invalidFields'       => 'Complete the required fields and confirm that you have read the privacy policy.',
			'invalidPhone'        => 'Enter a phone number containing at least 6 digits.',
		);
	}
}
