<?php
/**
 * Front Page Template
 *
 * @package InterAgents
 */

get_header();

$booking_settings = class_exists( 'IABC_Plugin' ) ? IABC_Plugin::settings() : array(
	'duration_min' => 30,
	'work_start'   => '10:00',
	'work_end'     => '15:00',
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
			<h2 class="section-title architecture-title" id="architecture-title">
				<span class="architecture-title__line">
					<span class="brand-word brand-word--inline"><span>inter</span><span class="brand-word__accent">agents</span></span>
					<?php echo esc_html( ia_t( 'przejmuje zadania.', 'takes on the work.' ) ); ?>
				</span>
				<span class="architecture-title__line">
					<span class="brand-word brand-word--inline"><span>inter</span><span class="brand-word__accent">core</span></span>
					<?php echo esc_html( ia_t( 'łączy ludzi, AI i procesy.', 'connects people, AI and processes.' ) ); ?>
				</span>
			</h2>
		</header>

		<div class="architecture-grid">
			<article class="architecture-level architecture-level--worker reveal">
				<p class="architecture-index"><?php echo esc_html( ia_t( '01 · pracownik AI do konkretnego procesu', '01 · an AI worker for a specific workflow' ) ); ?></p>
				<h3 class="brand-word"><span>inter</span><span class="brand-word__accent">agents</span></h3>
				<p class="architecture-role"><?php echo esc_html( ia_t( 'Oddaje gotową pracę, nie kolejne narzędzie do obsługi.', 'It delivers completed work, not another tool to operate.' ) ); ?></p>
				<p><?php echo esc_html( ia_t(
					'Może przygotowywać oferty, porządkować dokumenty, analizować dane, obsługiwać zapytania klientów lub pilnować realizacji zadań. Zlecasz pracę i odbierasz wynik z komputera albo telefonu.',
					'It can prepare proposals, organize documents, analyze data, handle customer enquiries or track task delivery. You assign the work and review the result from desktop or mobile.'
				) ); ?></p>
				<ul class="architecture-meta" aria-label="<?php echo esc_attr( ia_t( 'Zakres interagents', 'interagents scope' ) ); ?>">
					<li><?php echo esc_html( ia_t( 'Rola dopasowana do konkretnego procesu', 'A role designed for a specific workflow' ) ); ?></li>
					<li><?php echo esc_html( ia_t( 'Dostęp tylko do potrzebnych danych', 'Access only to the data it needs' ) ); ?></li>
					<li><?php echo esc_html( ia_t( 'Decyzje i wyjątki trafiają do człowieka', 'Decisions and exceptions go to a person' ) ); ?></li>
				</ul>
				<a class="btn architecture-cta" href="<?php echo esc_url( add_query_arg( 'product', 'openclaw', $offer_url ) ); ?>"><?php echo esc_html( ia_t( 'Zobacz zakres i cenę interagents', 'See interagents scope and pricing' ) ); ?></a>
			</article>

			<article class="architecture-level architecture-level--ecosystem reveal" style="--delay: 100ms">
				<p class="architecture-index"><?php echo esc_html( ia_t( '02 · system pracy dla ludzi i AI', '02 · a work system for people and AI' ) ); ?></p>
				<h3 class="brand-word"><span>inter</span><span class="brand-word__accent">core</span></h3>
				<p class="architecture-role"><?php echo esc_html( ia_t( 'Porządkuje cały proces, gdy jeden agent to za mało.', 'It coordinates the whole workflow when one agent is not enough.' ) ); ?></p>
				<p><?php echo esc_html( ia_t(
					'intercore zawsze zawiera interagents. Łączy pracowników AI, zespół, narzędzia i dane w jeden przepływ: rozdziela zadania, przekazuje kontekst, zbiera wyniki i kieruje decyzje do właściwych osób.',
					'intercore always includes interagents. It connects AI workers, your team, tools and data in one workflow: assigning tasks, passing context, collecting results and routing decisions to the right people.'
				) ); ?></p>

				<div class="included-layer">
					<span class="included-layer__label"><?php echo esc_html( ia_t( 'W intercore pracują', 'Working inside intercore' ) ); ?></span>
					<strong class="brand-word brand-word--small"><span>inter</span><span class="brand-word__accent">agents</span></strong>
					<span><?php echo esc_html( ia_t( 'Wykonują zadania, a intercore przekazuje pracę, kontekst i decyzje między nimi a zespołem.', 'They execute tasks while intercore moves work, context and decisions between them and the team.' ) ); ?></span>
				</div>

				<ul class="ecosystem-parts" aria-label="<?php echo esc_attr( ia_t( 'Przepływ pracy intercore', 'intercore workflow' ) ); ?>">
					<li><?php echo esc_html( ia_t( 'Zlecenie i kontekst', 'Brief and context' ) ); ?></li>
					<li><?php echo esc_html( ia_t( 'Praca agenta', 'Agent execution' ) ); ?></li>
					<li><?php echo esc_html( ia_t( 'Akceptacja człowieka', 'Human approval' ) ); ?></li>
					<li><?php echo esc_html( ia_t( 'Wynik i raport', 'Result and report' ) ); ?></li>
				</ul>
				<a class="btn architecture-cta" href="<?php echo esc_url( add_query_arg( 'product', 'intercore', $offer_url ) ); ?>"><?php echo esc_html( ia_t( 'Zobacz zakres i cenę intercore', 'See intercore scope and pricing' ) ); ?></a>
			</article>
		</div>
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
			<p class="section-kicker"><?php echo esc_html( ia_t( 'Efekt w codziennej pracy', 'What changes day to day' ) ); ?></p>
			<h2 class="section-title outcomes-title" id="outcomes-title">
				<span><?php echo esc_html( ia_t( 'Mniej ręcznej pracy.', 'Less manual work.' ) ); ?></span>
				<span><?php echo esc_html( ia_t( 'Więcej kontroli.', 'More control.' ) ); ?></span>
			</h2>
			<p><?php echo esc_html( ia_t( 'Nie zostawiamy firmy z prezentacją. Uruchamiamy proces na realnych danych, mierzymy wynik i przekazujemy zespołowi jasne zasady obsługi.', 'We do not leave you with a presentation. We launch the workflow on real data, measure the result and give the team clear operating rules.' ) ); ?></p>
		</header>

		<ol class="outcome-list">
			<li class="reveal">
				<span aria-hidden="true">01</span>
				<div><strong><?php echo esc_html( ia_t( 'Czas wraca do zespołu', 'Time returns to the team' ) ); ?></strong><p><?php echo esc_html( ia_t( 'AI przejmuje powtarzalne kroki i przygotowuje rezultat gotowy do użycia.', 'AI handles repeatable steps and prepares a usable result.' ) ); ?></p></div>
			</li>
			<li class="reveal" style="--delay: 60ms">
				<span aria-hidden="true">02</span>
				<div><strong><?php echo esc_html( ia_t( 'Decyzje nie giną', 'Decisions do not get lost' ) ); ?></strong><p><?php echo esc_html( ia_t( 'Właściwa osoba dostaje kontekst, rekomendację i wyraźny punkt akceptacji.', 'The right person receives the context, recommendation and a clear approval point.' ) ); ?></p></div>
			</li>
			<li class="reveal" style="--delay: 120ms">
				<span aria-hidden="true">03</span>
				<div><strong><?php echo esc_html( ia_t( 'Wiadomo, co się dzieje', 'You can see what is happening' ) ); ?></strong><p><?php echo esc_html( ia_t( 'Każde zadanie ma status, historię i odpowiedzialnego człowieka.', 'Every task has a status, history and accountable owner.' ) ); ?></p></div>
			</li>
			<li class="reveal" style="--delay: 180ms">
				<span aria-hidden="true">04</span>
				<div><strong><?php echo esc_html( ia_t( 'System można rozwijać', 'The system can grow' ) ); ?></strong><p><?php echo esc_html( ia_t( 'Dodajesz kolejne procesy i agentów bez budowania chaosu od nowa.', 'Add more workflows and agents without rebuilding the chaos.' ) ); ?></p></div>
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
