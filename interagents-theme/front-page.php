<?php
/**
 * Front Page Template
 *
 * @package InterAgents
 */

get_header();

$booking_settings = class_exists( 'IABC_Plugin' ) ? IABC_Plugin::settings() : array(
	'duration_min' => 20,
	'work_start'   => '10:00',
	'work_end'     => '16:00',
	'weekdays'     => array( 1, 2, 3, 4, 5 ),
);
$booking_duration = (int) $booking_settings['duration_min'];
$booking_weekdays = array_values( array_map( 'absint', (array) $booking_settings['weekdays'] ) );
$weekday_names    = 'pl' === ia_get_lang()
	? array( 1 => 'pon.', 2 => 'wt.', 3 => 'śr.', 4 => 'czw.', 5 => 'pt.', 6 => 'sob.', 7 => 'niedz.' )
	: array( 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun' );
$booking_day_label = array( 1, 2, 3, 4, 5 ) === $booking_weekdays
	? ia_t( 'pon.–pt.', 'Mon–Fri' )
	: implode( ', ', array_map( static function ( $day ) use ( $weekday_names ) { return isset( $weekday_names[ $day ] ) ? $weekday_names[ $day ] : ''; }, $booking_weekdays ) );
$booking_availability = sprintf( '%s, %s–%s', $booking_day_label, $booking_settings['work_start'], $booking_settings['work_end'] );
$offer_url            = ia_localized_url( '/offer/' );
?>

<section class="hero" id="hero">
	<div class="container hero-layout">
		<div class="hero-copy">
			<p class="section-kicker reveal"><?php echo esc_html( ia_t( 'System operacyjny firmy na miarę', 'A tailor-made operating system for your business' ) ); ?></p>
			<h1 class="reveal" style="--delay: 60ms">
				<span><?php echo esc_html( ia_t( 'AI, które pracuje.', 'AI that works.' ) ); ?></span>
				<span class="text-accent"><?php echo esc_html( ia_t( 'System, który dowozi.', 'A system that delivers.' ) ); ?></span>
			</h1>
			<p class="hero-lead reveal" style="--delay: 120ms"><?php echo esc_html( ia_t(
				'interagents to pracownicy AI szyci pod konkretne zadania. intercore zawsze zawiera interagents i dodaje środowisko, w którym ludzie, agenci, dane oraz procesy działają jako jeden system.',
				'interagents are AI workers built for specific jobs. intercore always includes interagents and adds the workspace where people, agents, data and processes operate as one system.'
			) ); ?></p>
			<div class="hero-actions reveal" style="--delay: 180ms">
				<a href="#book" class="btn btn--primary" data-booking-cta="hero"><?php echo esc_html( ia_t( 'Umów bezpłatną rozmowę', 'Book a free meeting' ) ); ?></a>
				<a href="#offer" class="btn"><?php echo esc_html( ia_t( 'Zobacz, jak to działa', 'See how it works' ) ); ?></a>
			</div>
		</div>

		<dl class="hero-facts reveal" style="--delay: 220ms" aria-label="<?php echo esc_attr( ia_t( 'Najważniejsze zasady', 'Core principles' ) ); ?>">
			<div>
				<dt><?php echo esc_html( ia_t( 'Dopasowane', 'Tailor-made' ) ); ?></dt>
				<dd><?php echo esc_html( ia_t( 'do procesów Twojej firmy', 'to your business processes' ) ); ?></dd>
			</div>
			<div>
				<dt><?php echo esc_html( ia_t( 'Pod kontrolą ludzi', 'Human-controlled' ) ); ?></dt>
				<dd><?php echo esc_html( ia_t( 'decyzje nadal należą do Ciebie', 'the decisions still belong to you' ) ); ?></dd>
			</div>
			<div>
				<dt><?php echo esc_html( ia_t( 'Zawsze pod ręką', 'Always within reach' ) ); ?></dt>
				<dd><?php echo esc_html( ia_t( 'zarządzanie z komputera lub telefonu', 'managed from desktop or mobile' ) ); ?></dd>
			</div>
		</dl>
	</div>
</section>

<section class="section section--architecture" id="offer" aria-labelledby="architecture-title">
	<div class="container">
		<header class="editorial-heading reveal">
			<p class="section-kicker"><?php echo esc_html( ia_t( 'Dwa poziomy wdrożenia', 'Two deployment levels' ) ); ?></p>
			<h2 class="section-title" id="architecture-title"><?php echo esc_html( ia_t(
				'interagents wykonuje pracę. intercore organizuje cały system.',
				'interagents does the work. intercore organizes the whole system.'
			) ); ?></h2>
		</header>

		<div class="architecture-grid">
			<article class="architecture-level architecture-level--worker reveal">
				<p class="architecture-index"><?php echo esc_html( ia_t( '01 · samodzielne wdrożenie', '01 · standalone deployment' ) ); ?></p>
				<h3 class="brand-word" aria-label="interagents"><span aria-hidden="true">inter</span><span class="brand-word__accent" aria-hidden="true">agents</span></h3>
				<p class="architecture-role"><?php echo esc_html( ia_t( 'Pracownicy AI do konkretnych zadań.', 'AI workers for specific jobs.' ) ); ?></p>
				<p><?php echo esc_html( ia_t(
					'Projektujemy role, dostęp do danych i zasady działania. Ty zlecasz pracę oraz sprawdzasz wynik z komputera lub telefonu.',
					'We design the roles, data access and operating rules. You assign the work and review the result from desktop or mobile.'
				) ); ?></p>
				<ul class="architecture-meta" aria-label="<?php echo esc_attr( ia_t( 'Zakres interagents', 'interagents scope' ) ); ?>">
					<li><?php echo esc_html( ia_t( 'Role szyte na miarę', 'Tailor-made roles' ) ); ?></li>
					<li><?php echo esc_html( ia_t( 'Oparte na OpenClaw', 'Powered by OpenClaw' ) ); ?></li>
				</ul>
				<a class="editorial-link" href="<?php echo esc_url( add_query_arg( 'product', 'openclaw', $offer_url ) ); ?>"><?php echo esc_html( ia_t( 'Zakres i cena interagents', 'interagents scope and price' ) ); ?></a>
			</article>

			<article class="architecture-level architecture-level--ecosystem reveal" style="--delay: 100ms">
				<div class="architecture-level__header">
					<div>
						<p class="architecture-index"><?php echo esc_html( ia_t( '02 · pełne środowisko', '02 · complete workspace' ) ); ?></p>
						<h3 class="brand-word" aria-label="intercore"><span aria-hidden="true">inter</span><span class="brand-word__accent" aria-hidden="true">core</span></h3>
					</div>
					<p class="architecture-includes"><?php echo esc_html( ia_t( 'zawsze zawiera interagents', 'always includes interagents' ) ); ?></p>
				</div>
				<p class="architecture-role"><?php echo esc_html( ia_t( 'Środowisko pracy dla ludzi i AI.', 'A workspace for humans and AI.' ) ); ?></p>
				<p><?php echo esc_html( ia_t(
					'Łączy ludzi, pracowników AI, narzędzia, dane i akceptacje w jeden system zaprojektowany wokół sposobu działania firmy.',
					'It connects people, AI workers, tools, data and approvals in one system designed around how the business operates.'
				) ); ?></p>

				<div class="included-layer">
					<span class="included-layer__label"><?php echo esc_html( ia_t( 'warstwa wykonawcza w środku', 'the execution layer inside' ) ); ?></span>
					<strong class="brand-word brand-word--small" aria-label="interagents"><span aria-hidden="true">inter</span><span class="brand-word__accent" aria-hidden="true">agents</span></strong>
					<span><?php echo esc_html( ia_t( 'wykonuje, raportuje i przekazuje decyzje ludziom', 'executes, reports and hands decisions to people' ) ); ?></span>
				</div>

				<ul class="ecosystem-parts" aria-label="<?php echo esc_attr( ia_t( 'Elementy intercore', 'intercore elements' ) ); ?>">
					<li><?php echo esc_html( ia_t( 'Ludzie', 'People' ) ); ?></li>
					<li><?php echo esc_html( ia_t( 'Narzędzia', 'Tools' ) ); ?></li>
					<li><?php echo esc_html( ia_t( 'Dane', 'Data' ) ); ?></li>
					<li><?php echo esc_html( ia_t( 'Zasady i decyzje', 'Rules and decisions' ) ); ?></li>
				</ul>
				<a class="editorial-link" href="<?php echo esc_url( add_query_arg( 'product', 'intercore', $offer_url ) ); ?>"><?php echo esc_html( ia_t( 'Zakres i cena intercore', 'intercore scope and price' ) ); ?></a>
			</article>
		</div>
	</div>
</section>

<section class="section section--manifesto" aria-labelledby="manifesto-title">
	<div class="container manifesto-grid reveal">
		<h2 id="manifesto-title"><?php echo esc_html( ia_t(
			'AI bez odpowiedzialności to tylko droższy chaos.',
			'AI without accountability is just more expensive chaos.'
		) ); ?></h2>
		<p><?php echo esc_html( ia_t(
			'Dlatego każde zadanie, dostęp i decyzja mają właściciela. AI wykonuje pracę. Ludzie zachowują kontrolę.',
			'That is why every task, permission and decision has an owner. AI does the work. People keep control.'
		) ); ?></p>
	</div>
</section>

<section class="section section--process" id="jak-dzialamy" aria-labelledby="process-title">
	<div class="container">
		<header class="editorial-heading reveal">
			<p class="section-kicker"><?php echo esc_html( ia_t( 'Jak wdrażamy', 'How we deliver' ) ); ?></p>
			<h2 class="section-title" id="process-title"><?php echo esc_html( ia_t( 'Od procesu do wyniku.', 'From workflow to outcome.' ) ); ?></h2>
		</header>

		<ol class="process-rail">
			<li class="process-step reveal">
				<span class="process-number" aria-hidden="true">01</span>
				<div>
					<h3><?php echo esc_html( ia_t( 'Analiza', 'Analysis' ) ); ?></h3>
					<p><?php echo esc_html( ia_t( 'Wybieramy proces, który kosztuje czas, pieniądze lub uwagę właściciela.', 'We select a workflow that costs time, money or the owner’s attention.' ) ); ?></p>
				</div>
			</li>
			<li class="process-step reveal" style="--delay: 80ms">
				<span class="process-number" aria-hidden="true">02</span>
				<div>
					<h3><?php echo esc_html( ia_t( 'Projekt', 'Design' ) ); ?></h3>
					<p><?php echo esc_html( ia_t( 'Budujemy rozwiązanie na prawdziwych scenariuszach i ustalamy zasady kontroli.', 'We build against real scenarios and define the control points.' ) ); ?></p>
				</div>
			</li>
			<li class="process-step reveal" style="--delay: 160ms">
				<span class="process-number" aria-hidden="true">03</span>
				<div>
					<h3><?php echo esc_html( ia_t( 'Wdrożenie', 'Deployment' ) ); ?></h3>
					<p><?php echo esc_html( ia_t( 'Uruchamiamy, mierzymy wynik i poprawiamy system razem z zespołem.', 'We launch, measure the outcome and improve the system with the team.' ) ); ?></p>
				</div>
			</li>
		</ol>
	</div>
</section>

<section class="section section--outcomes" id="dlaczego-my" aria-labelledby="outcomes-title">
	<div class="container outcomes-grid">
		<header class="outcomes-intro reveal">
			<p class="section-kicker"><?php echo esc_html( ia_t( 'Co zostaje w firmie', 'What stays in the business' ) ); ?></p>
			<h2 class="section-title" id="outcomes-title"><?php echo esc_html( ia_t( 'System, nie pokaz.', 'A system, not a demo.' ) ); ?></h2>
			<p><?php echo esc_html( ia_t( 'Budujemy operacyjny sposób pracy, który Twój zespół może kontrolować i rozwijać.', 'We build an operating way of working your team can control and develop.' ) ); ?></p>
		</header>

		<ol class="outcome-list">
			<li class="reveal">
				<span aria-hidden="true">01</span>
				<div><strong><?php echo esc_html( ia_t( 'Działający proces', 'A working workflow' ) ); ?></strong><p><?php echo esc_html( ia_t( 'Nie demo ani prezentacja.', 'Not a demo or presentation.' ) ); ?></p></div>
			</li>
			<li class="reveal" style="--delay: 60ms">
				<span aria-hidden="true">02</span>
				<div><strong><?php echo esc_html( ia_t( 'Kontrola', 'Control' ) ); ?></strong><p><?php echo esc_html( ia_t( 'Dane, dostępy i decyzje pozostają po Twojej stronie.', 'Data, access and decisions remain on your side.' ) ); ?></p></div>
			</li>
			<li class="reveal" style="--delay: 120ms">
				<span aria-hidden="true">03</span>
				<div><strong><?php echo esc_html( ia_t( 'Jeden kontekst', 'One shared context' ) ); ?></strong><p><?php echo esc_html( ia_t( 'Ludzie i AI pracują na wspólnych zasadach.', 'People and AI work under the same rules.' ) ); ?></p></div>
			</li>
			<li class="reveal" style="--delay: 180ms">
				<span aria-hidden="true">04</span>
				<div><strong><?php echo esc_html( ia_t( 'Możliwość zmiany', 'Room to change' ) ); ?></strong><p><?php echo esc_html( ia_t( 'System rośnie razem z firmą bez utraty odpowiedzialności.', 'The system grows with the business without losing accountability.' ) ); ?></p></div>
			</li>
		</ol>
	</div>
</section>

<section class="section section--booking" id="book" aria-labelledby="booking-title">
	<div class="container booking-layout">
		<div class="booking-intro reveal">
			<p class="section-kicker"><?php echo esc_html( sprintf( ia_t( 'Bezpłatnie · %d minut · online', 'Free · %d minutes · online' ), $booking_duration ) ); ?></p>
			<h2 class="section-title" id="booking-title"><?php echo esc_html( sprintf( ia_t( 'Zarezerwuj %d minut.', 'Book %d minutes.' ), $booking_duration ) ); ?></h2>
			<p><?php echo esc_html( ia_t(
				'Powiemy, czy wystarczy samodzielne wdrożenie interagents, proces powinien trafić do intercore, czy AI nie jest tu odpowiedzią.',
				'We will tell you whether standalone interagents is enough, the workflow belongs in intercore, or AI is not the answer.'
			) ); ?></p>
			<dl class="booking-facts">
				<div><dt><?php echo esc_html( ia_t( 'Dostępność', 'Availability' ) ); ?></dt><dd><?php echo esc_html( $booking_availability ); ?></dd></div>
				<div><dt><?php echo esc_html( ia_t( 'Forma', 'Format' ) ); ?></dt><dd><?php echo esc_html( ia_t( 'Spotkanie online', 'Online meeting' ) ); ?></dd></div>
			</dl>
		</div>

		<div class="booking-calendar-wrap reveal" style="--delay: 80ms" id="booking-calendar" data-booking-calendar>
			<?php echo do_shortcode( '[interagents_booking_calendar embedded="1"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<div class="booking-email reveal">
			<span><?php echo esc_html( ia_t( 'Wolisz e-mail?', 'Prefer email?' ) ); ?></span>
			<button type="button" class="text-button" data-open-contact-form aria-haspopup="dialog" aria-controls="contact-modal">
				<?php echo esc_html( ia_t( 'Wyślij krótką wiadomość', 'Send a short note' ) ); ?>
			</button>
		</div>
	</div>
</section>

<div class="mobile-booking-bar" aria-label="<?php echo esc_attr( ia_t( 'Szybka rezerwacja', 'Quick booking' ) ); ?>">
	<a href="#book" class="btn btn--primary" data-booking-cta="mobile_sticky"><?php echo esc_html( ia_t( 'Umów bezpłatną rozmowę', 'Book a free meeting' ) ); ?></a>
</div>

<div class="modal-overlay" id="contact-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="contact-modal-title" aria-describedby="contact-modal-description">
	<div class="modal-backdrop" aria-hidden="true"></div>
	<div class="modal-content" tabindex="-1">
		<button type="button" class="modal-close" aria-label="<?php echo esc_attr( ia_t( 'Zamknij formularz', 'Close contact form' ) ); ?>"><?php echo esc_html( ia_t( 'Zamknij', 'Close' ) ); ?></button>
		<h2 class="modal-title" id="contact-modal-title"><?php echo esc_html( ia_t( 'Wolisz e-mail?', 'Prefer email?' ) ); ?></h2>
		<p class="modal-subtitle" id="contact-modal-description"><?php echo esc_html( ia_t(
			'Opisz proces, który chcesz usprawnić. Wrócimy z konkretnym następnym krokiem.',
			'Tell us which workflow you want to improve. We will reply with a concrete next step.'
		) ); ?></p>
		<div class="contact-form-wrap">
			<?php echo do_shortcode( '[wpforms id="85" title="false" description="false"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
