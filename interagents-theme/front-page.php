<?php
/**
 * Front Page Template
 *
 * @package InterAgents
 */

get_header();

$lang     = ia_get_lang();
$is_pl    = 'pl' === $lang;
$currency = $is_pl ? 'PLN' : 'EUR';
$rate     = 4.3;

$pricing = array(
	'openclaw'  => $is_pl ? 6000 : round( 6000 / $rate / 50 ) * 50,
	'intercore' => $is_pl ? 15000 : round( 15000 / $rate / 100 ) * 100,
);

$booking_duration = class_exists( 'IABC_Plugin' ) ? (int) IABC_Plugin::settings()['duration_min'] : 20;

$offer_url = ia_localized_url( '/offer/' );
?>

<!-- Hero -->
<section class="hero" id="hero">
	<div class="container hero-inner">
		<p class="hero-tagline reveal"><?php echo esc_html( ia_t(
			'Agenci AI do pracy · Systemy dla ludzi i AI',
			'Custom AI workers · Human + AI business systems'
		) ); ?></p>
		<h1 class="reveal" style="--delay: 60ms"><?php echo esc_html( ia_t(
			'Przestań kupować narzędzia AI. Zacznij zlecać pracę.',
			'Stop buying AI tools. Start assigning work.'
		) ); ?></h1>
		<p class="hero-lead reveal" style="--delay: 120ms"><?php echo esc_html( ia_t(
			'Interagents daje Ci agentów AI szytych pod konkretne zadania — zarządzasz nimi z komputera lub telefonu. Intercore buduje środowisko, w którym ludzie, agenci, dane i procesy działają jako jeden system.',
			'Interagents gives you custom AI workers built around real business tasks and managed from desktop or mobile. Intercore creates the tailor-made workspace where your people, agents, data and processes work as one system.'
		) ); ?></p>
		<div class="hero-actions reveal" style="--delay: 200ms">
			<a href="#book" class="btn btn--primary" data-booking-cta="hero"><?php echo esc_html( ia_t( 'Umów bezpłatną rozmowę', 'Book a free meeting' ) ); ?></a>
			<a href="#offer" class="btn"><?php echo esc_html( ia_t( 'Porównaj Interagents i Intercore', 'Compare Interagents and Intercore' ) ); ?></a>
		</div>
		<p class="hero-note reveal" style="--delay: 240ms"><?php echo esc_html( ia_t(
			'Jeden problem. Jedna konkretna następna decyzja. Bez wykładu o AI.',
			'One bottleneck. One concrete next step. No AI lecture.'
		) ); ?></p>
	</div>
</section>

<!-- Brave transition -->
<section class="section section--manifesto" aria-labelledby="manifesto-title">
	<div class="container manifesto-inner reveal">
		<p class="section-kicker"><?php echo esc_html( ia_t( 'Brutalnie prosto', 'Brutally simple' ) ); ?></p>
		<h2 id="manifesto-title"><?php echo esc_html( ia_t(
			'Problemem nie są Twoi ludzie. Problemem jest system, w którym pracują.',
			'Your people are not the problem. The system around them is.'
		) ); ?></h2>
		<p><?php echo esc_html( ia_t(
			'Kolejny abonament na AI nie naprawi pracy uwięzionej między skrzynkami, arkuszami i akceptacjami.',
			'Another AI subscription will not fix work trapped between inboxes, spreadsheets and approvals.'
		) ); ?></p>
	</div>
</section>

<!-- Two paths -->
<section class="section section--offer" id="offer" aria-labelledby="offer-title">
	<div class="container">
		<p class="section-kicker reveal"><?php echo esc_html( ia_t( 'Dwie różne potrzeby', 'Two different needs' ) ); ?></p>
		<h2 class="section-title reveal" id="offer-title"><?php echo esc_html( ia_t(
			'Zatrudnij cyfrowego pracownika. Albo przebuduj miejsce pracy.',
			'Hire the digital worker. Or redesign the workplace.'
		) ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php echo esc_html( ia_t(
			'Nie zaczynamy od technologii. Zaczynamy od tego, gdzie dziś zatrzymuje się praca.',
			'We do not start with technology. We start where the work gets stuck today.'
		) ); ?></p>

		<div class="offer-products offer-products--homepage" id="offer-products">
			<a class="offer-product-card offer-product-card--link reveal" href="<?php echo esc_url( add_query_arg( 'product', 'openclaw', $offer_url ) ); ?>">
				<div class="offer-product-topline">
					<div class="offer-product-icon" aria-hidden="true">
						<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="20" cy="14" r="6" stroke="currentColor" stroke-width="2"/>
							<circle cx="10" cy="28" r="4" stroke="currentColor" stroke-width="2"/>
							<circle cx="30" cy="28" r="4" stroke="currentColor" stroke-width="2"/>
							<line x1="17" y1="19" x2="12" y2="25" stroke="currentColor" stroke-width="2"/>
							<line x1="23" y1="19" x2="28" y2="25" stroke="currentColor" stroke-width="2"/>
						</svg>
					</div>
					<span class="offer-product-powered"><?php echo esc_html( ia_t( 'Oparte na OpenClaw', 'Powered by OpenClaw' ) ); ?></span>
				</div>
				<h3>Interagents</h3>
				<p class="offer-product-label"><?php echo esc_html( ia_t( 'Agenci AI pod Twoją kontrolą', 'Custom AI workers under your control' ) ); ?></p>
				<p class="offer-product-desc"><?php echo esc_html( ia_t(
					'Projektujemy agentów pod konkretne zadania w Twojej firmie. Zlecaj pracę, śledź postęp i sprawdzaj wyniki z komputera lub telefonu.',
					'We design agents around specific jobs in your business. Assign work, follow progress and review results from desktop or mobile.'
				) ); ?></p>
				<p class="offer-product-fit"><strong><?php echo esc_html( ia_t( 'Najlepsze, gdy:', 'Best when:' ) ); ?></strong> <?php echo esc_html( ia_t(
					'praca jest powtarzalna, a odpowiedzialność jasna.',
					'the work is repeatable and responsibility is clear.'
				) ); ?></p>
				<div class="offer-product-from">
					<?php echo esc_html( ia_t( 'od', 'from' ) ); ?>
					<strong><?php echo esc_html( number_format( $pricing['openclaw'], 0, '', $is_pl ? ' ' : ',' ) ); ?> <?php echo esc_html( $currency ); ?></strong>
					<?php echo esc_html( ia_t( 'wdrożenie', 'setup' ) ); ?>
				</div>
				<span class="offer-product-arrow"><?php echo esc_html( ia_t( 'Zobacz Interagents →', 'Explore Interagents →' ) ); ?></span>
			</a>

			<a class="offer-product-card offer-product-card--link reveal" style="--delay: 80ms" href="<?php echo esc_url( add_query_arg( 'product', 'intercore', $offer_url ) ); ?>">
				<div class="offer-product-topline">
					<div class="offer-product-icon" aria-hidden="true">
						<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect x="4" y="8" width="12" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
							<rect x="24" y="22" width="12" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
							<path d="M16 13H24C26.2 13 28 14.8 28 17V22" stroke="currentColor" stroke-width="2"/>
							<path d="M24 27H16C13.8 27 12 25.2 12 23V18" stroke="currentColor" stroke-width="2"/>
						</svg>
					</div>
					<span class="offer-product-powered"><?php echo esc_html( ia_t( 'Ekosystem szyty na miarę', 'Tailor-made ecosystem' ) ); ?></span>
				</div>
				<h3>Intercore</h3>
				<p class="offer-product-label"><?php echo esc_html( ia_t( 'Środowisko pracy dla ludzi i AI', 'A workspace for humans and AI' ) ); ?></p>
				<p class="offer-product-desc"><?php echo esc_html( ia_t(
					'Łączymy ludzi, agentów, narzędzia, dane i akceptacje w jeden system zaprojektowany wokół sposobu działania Twojej firmy.',
					'We connect people, agents, tools, data and approvals in one workspace designed around how your company should operate.'
				) ); ?></p>
				<p class="offer-product-fit"><strong><?php echo esc_html( ia_t( 'Najlepsze, gdy:', 'Best when:' ) ); ?></strong> <?php echo esc_html( ia_t(
					'praca przechodzi między zespołami, systemami i decyzjami.',
					'work crosses teams, systems and decision points.'
				) ); ?></p>
				<div class="offer-product-from">
					<?php echo esc_html( ia_t( 'od', 'from' ) ); ?>
					<strong><?php echo esc_html( number_format( $pricing['intercore'], 0, '', $is_pl ? ' ' : ',' ) ); ?> <?php echo esc_html( $currency ); ?></strong>
					<?php echo esc_html( ia_t( 'wdrożenie', 'setup' ) ); ?>
				</div>
				<span class="offer-product-arrow"><?php echo esc_html( ia_t( 'Zobacz Intercore →', 'Explore Intercore →' ) ); ?></span>
			</a>
		</div>

		<a class="offer-bundle-strip reveal" href="<?php echo esc_url( add_query_arg( 'product', 'complete', $offer_url ) ); ?>">
			<span class="offer-bundle-copy">
				<strong><?php echo esc_html( ia_t( 'Potrzebujesz obu?', 'Need both?' ) ); ?></strong>
				<span><?php echo esc_html( ia_t(
					'Łączymy Interagents i Intercore wtedy, gdy uzasadnia to Twój proces — nie nasz cennik.',
					'We combine Interagents and Intercore when your business case earns it — not because our price list says so.'
				) ); ?></span>
			</span>
			<span class="offer-bundle-arrow" aria-hidden="true">→</span>
		</a>
	</div>
</section>

<!-- Booking: immediately follows the two-path section -->
<section class="section section--booking" id="book" aria-labelledby="booking-title">
	<div class="container booking-layout">
		<div class="booking-intro reveal">
			<p class="section-kicker"><?php echo esc_html( sprintf( ia_t( 'Bezpłatna rozmowa · %d minut', 'Free workflow call · %d minutes' ), $booking_duration ) ); ?></p>
			<h2 class="section-title" id="booking-title"><?php echo esc_html( ia_t(
				'Przynieś jeden proces, który co tydzień zabiera Ci czas.',
				'Bring us one workflow that wastes your week.'
			) ); ?></h2>
			<p><?php echo esc_html( ia_t(
				'Powiemy wprost, czy potrzebujesz Interagents, Intercore — czy żadnego z nich. Wybierz dogodny termin; rozmowa jest bezpłatna.',
				'We will tell you plainly whether you need Interagents, Intercore — or neither. Choose a time that works; the meeting is free.'
			) ); ?></p>
			<ul class="booking-proof" aria-label="<?php echo esc_attr( ia_t( 'Informacje o rozmowie', 'Meeting details' ) ); ?>">
				<li><?php echo esc_html( ia_t( 'Bez prezentacji sprzedażowej', 'No sales deck' ) ); ?></li>
				<li><?php echo esc_html( ia_t( 'Konkretny następny krok', 'A concrete next step' ) ); ?></li>
				<li><?php echo esc_html( ia_t( 'Spotkanie online', 'Online meeting' ) ); ?></li>
			</ul>
		</div>
		<div class="booking-calendar-wrap reveal" style="--delay: 80ms" id="booking-calendar" data-booking-calendar>
			<?php echo do_shortcode( '[interagents_booking_calendar]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="booking-email reveal">
			<span><?php echo esc_html( ia_t( 'Wolisz e-mail?', 'Prefer email?' ) ); ?></span>
			<button type="button" class="text-button" data-open-contact-form aria-haspopup="dialog" aria-controls="contact-modal">
				<?php echo esc_html( ia_t( 'Wyślij krótką wiadomość', 'Send a short note' ) ); ?>
			</button>
		</div>
	</div>
</section>

<!-- Services -->
<section class="section section--services" id="uslugi">
	<div class="container">
		<h2 class="section-title reveal"><?php echo esc_html( ia_t( 'Co naprawdę budujemy', 'What we actually build' ) ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php echo esc_html( ia_t(
			'Mniej demonstracji AI. Więcej działających procesów z jasną odpowiedzialnością.',
			'Fewer AI demos. More working processes with clear ownership.'
		) ); ?></p>
		<div class="cards-grid">
			<article class="card reveal">
				<div class="card-icon" aria-hidden="true">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="20" cy="14" r="6" stroke="currentColor" stroke-width="2"/>
						<circle cx="10" cy="28" r="4" stroke="currentColor" stroke-width="2"/>
						<circle cx="30" cy="28" r="4" stroke="currentColor" stroke-width="2"/>
						<line x1="17" y1="19" x2="12" y2="25" stroke="currentColor" stroke-width="2"/>
						<line x1="23" y1="19" x2="28" y2="25" stroke="currentColor" stroke-width="2"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Agenci AI', 'AI workers' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Agenci dopasowani do konkretnych zadań, źródeł danych i zasad działania. Ty zachowujesz kontrolę nad decyzjami i wynikiem.',
					'Agents tailored to specific tasks, data sources and operating rules. You keep control over decisions and results.'
				) ); ?></p>
			</article>
			<article class="card reveal">
				<div class="card-icon" aria-hidden="true">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="4" y="8" width="12" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
						<rect x="24" y="22" width="12" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
						<path d="M16 13H24C26.2 13 28 14.8 28 17V22" stroke="currentColor" stroke-width="2"/>
						<path d="M24 27H16C13.8 27 12 25.2 12 23V18" stroke="currentColor" stroke-width="2"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Integracja systemów', 'System integration' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Łączymy narzędzia i dane tak, aby ograniczyć ręczne przekazywanie pracy, duplikowanie informacji i niejasne statusy.',
					'We connect tools and data to reduce manual handoffs, duplicated information and unclear status updates.'
				) ); ?></p>
			</article>
			<article class="card reveal">
				<div class="card-icon" aria-hidden="true">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M20 6L34 14V26L20 34L6 26V14L20 6Z" stroke="currentColor" stroke-width="2"/>
						<path d="M20 6V20M20 20L34 14M20 20L6 14" stroke="currentColor" stroke-width="2" opacity="0.5"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Rozwiązania na miarę', 'Tailor-made systems' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Projektujemy rozwiązanie wokół Twojego procesu, zespołu i ryzyka — nie wokół ograniczeń gotowego produktu.',
					'We design around your process, team and risk — not around the constraints of an off-the-shelf product.'
				) ); ?></p>
			</article>
		</div>
	</div>
</section>

<!-- How we work -->
<section class="section section--process" id="jak-dzialamy">
	<div class="container">
		<h2 class="section-title reveal"><?php echo esc_html( ia_t( 'Jak działamy', 'How we work' ) ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php echo esc_html( ia_t(
			'Od jednego problemu do działającego, mierzalnego rozwiązania.',
			'From one bottleneck to a working, measurable solution.'
		) ); ?></p>
		<div class="steps-grid">
			<div class="step reveal">
				<div class="step-number">01</div>
				<h3><?php echo esc_html( ia_t( 'Analiza', 'Analysis' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Wybieramy proces, który realnie kosztuje czas, pieniądze lub uwagę właściciela firmy.',
					'We identify a workflow that genuinely costs time, money or the owner’s attention.'
				) ); ?></p>
			</div>
			<div class="step reveal">
				<div class="step-number">02</div>
				<h3><?php echo esc_html( ia_t( 'Projekt', 'Design' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Budujemy i sprawdzamy rozwiązanie na prawdziwych scenariuszach, z jasnymi zasadami kontroli.',
					'We build and test the solution against real scenarios with clear control points.'
				) ); ?></p>
			</div>
			<div class="step reveal">
				<div class="step-number">03</div>
				<h3><?php echo esc_html( ia_t( 'Wdrożenie', 'Deployment' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Uruchamiamy, obserwujemy wyniki i poprawiamy system razem z Twoim zespołem.',
					'We launch, observe results and improve the system alongside your team.'
				) ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- Why us -->
<section class="section section--features" id="dlaczego-my">
	<div class="container">
		<h2 class="section-title reveal"><?php echo esc_html( ia_t( 'Dlaczego Interagents', 'Why Interagents' ) ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php echo esc_html( ia_t(
			'Użyteczny system AI musi pasować do firmy także po zakończeniu prezentacji.',
			'A useful AI system still has to fit the business after the demo ends.'
		) ); ?></p>
		<div class="features-grid">
			<div class="feature reveal">
				<h3><?php echo esc_html( ia_t( 'Zaczynamy od wyniku', 'We start with the outcome' ) ); ?></h3>
				<p><?php echo esc_html( ia_t( 'Najpierw definiujemy problem, właściciela procesu i miarę sukcesu.', 'First we define the problem, process owner and measure of success.' ) ); ?></p>
			</div>
			<div class="feature reveal">
				<h3><?php echo esc_html( ia_t( 'Dobieramy technologię', 'Technology follows the job' ) ); ?></h3>
				<p><?php echo esc_html( ia_t( 'Wybieramy narzędzia do zadania, danych i poziomu ryzyka — nie odwrotnie.', 'We choose tools for the task, data and risk level — not the other way around.' ) ); ?></p>
			</div>
			<div class="feature reveal">
				<h3><?php echo esc_html( ia_t( 'Budujemy na miarę', 'Built around your business' ) ); ?></h3>
				<p><?php echo esc_html( ia_t( 'Rozwiązanie uwzględnia Twój zespół, procesy, marże i sposób podejmowania decyzji.', 'The solution reflects your team, processes, margins and decision-making.' ) ); ?></p>
			</div>
			<div class="feature reveal">
				<h3><?php echo esc_html( ia_t( 'Zostajemy po starcie', 'Support after launch' ) ); ?></h3>
				<p><?php echo esc_html( ia_t( 'Monitorujemy, poprawiamy i skalujemy system, kiedy zmienia się Twoja firma.', 'We monitor, improve and scale the system as your business changes.' ) ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- Final CTA -->
<section class="section section--cta" id="kontakt">
	<div class="container">
		<div class="cta-box reveal">
			<p class="section-kicker"><?php echo esc_html( ia_t( 'Bez zobowiązań', 'No obligation' ) ); ?></p>
			<h2><?php echo esc_html( ia_t( 'AI musi zasłużyć na miejsce w Twojej firmie.', 'AI should earn its place in your business.' ) ); ?></h2>
			<p><?php echo esc_html( ia_t( 'Zacznijmy od jednego procesu i uczciwej rozmowy.', 'Let’s start with one workflow and an honest conversation.' ) ); ?></p>
			<div class="cta-actions">
				<a href="#book" class="btn btn--primary" data-booking-cta="final_cta"><?php echo esc_html( ia_t( 'Wybierz termin', 'Choose a time' ) ); ?></a>
				<button type="button" class="btn" data-open-contact-form aria-haspopup="dialog" aria-controls="contact-modal"><?php echo esc_html( ia_t( 'Wolę e-mail', 'Prefer email' ) ); ?></button>
			</div>
		</div>
	</div>
</section>

<div class="mobile-booking-bar" aria-label="<?php echo esc_attr( ia_t( 'Szybka rezerwacja', 'Quick booking' ) ); ?>">
	<a href="#book" class="btn btn--primary" data-booking-cta="mobile_sticky"><?php echo esc_html( ia_t( 'Umów bezpłatną rozmowę', 'Book a free meeting' ) ); ?></a>
</div>

<!-- Contact Form Modal -->
<div class="modal-overlay" id="contact-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="contact-modal-title" aria-describedby="contact-modal-description">
	<div class="modal-backdrop" aria-hidden="true"></div>
	<div class="modal-content" tabindex="-1">
		<button type="button" class="modal-close" aria-label="<?php echo esc_attr( ia_t( 'Zamknij formularz', 'Close contact form' ) ); ?>">&times;</button>
		<h2 class="modal-title" id="contact-modal-title"><?php echo esc_html( ia_t( 'Wolisz e-mail?', 'Prefer email?' ) ); ?></h2>
		<p class="modal-subtitle" id="contact-modal-description"><?php echo esc_html( ia_t(
			'Opisz krótko proces, który chcesz usprawnić. Odezwiemy się z konkretnym następnym krokiem.',
			'Tell us briefly which workflow you want to improve. We will reply with a concrete next step.'
		) ); ?></p>
		<div class="contact-form-wrap">
			<?php echo do_shortcode( '[wpforms id="85" title="false" description="false"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
