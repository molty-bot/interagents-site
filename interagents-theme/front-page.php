<?php
/**
 * Front Page Template
 *
 * @package InterAgents
 */

get_header();

$hero_title    = ia_t(
	get_theme_mod( 'hero_title', 'AI, które zastępuje cały zespół' ),
	'AI that replaces your entire team'
);
$hero_subtitle = ia_t(
	get_theme_mod( 'hero_subtitle', 'Projektujemy agentów AI, którzy pracują 24/7 bez urlopów, bez błędów i za ułamek kosztów Twojego zespołu.' ),
	'We design AI agents that work 24/7. No vacations, no mistakes, at a fraction of your team\'s cost.'
);
$hero_cta_text = ia_t(
	get_theme_mod( 'hero_cta_text', 'Porozmawiajmy' ),
	'Let\'s talk'
);
$hero_cta_url  = get_theme_mod( 'hero_cta_url', '#kontakt' );
?>

<!-- Hero -->
<section class="hero" id="hero">
	<div class="container hero-inner">
		<p class="hero-tagline reveal"><?php echo esc_html( ia_t(
			'Agenci AI • Integracja systemów • Automatyzacja procesów',
			'AI Agents • System Integration • Process Automation'
		) ); ?></p>
		<h1 class="reveal" style="--delay: 60ms"><?php echo esc_html( $hero_title ); ?></h1>
		<p class="reveal" style="--delay: 120ms"><?php echo esc_html( $hero_subtitle ); ?></p>
		<div class="hero-actions reveal" style="--delay: 200ms">
			<a href="<?php echo esc_url( $hero_cta_url ); ?>" class="btn btn--primary"><?php echo esc_html( $hero_cta_text ); ?></a>
			<a href="#uslugi" class="btn"><?php echo esc_html( ia_t( 'Poznaj nas', 'Learn more' ) ); ?></a>
		</div>
	</div>
</section>

<!-- Usługi (Services) -->
<section class="section section--services" id="uslugi">
	<div class="container">
		<h2 class="section-title reveal"><?php echo esc_html( ia_t( 'Nasze usługi', 'Our services' ) ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php echo esc_html( ia_t(
			'Twoja konkurencja już to wdraża. Pytanie, czy zdążysz.',
			'Your competitors are already deploying this. The question is — will you keep up?'
		) ); ?></p>
		<div class="cards-grid">
			<article class="card reveal" style="--delay: 100ms">
				<div class="card-icon">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="20" cy="14" r="6" stroke="currentColor" stroke-width="2"/>
						<circle cx="10" cy="28" r="4" stroke="currentColor" stroke-width="2"/>
						<circle cx="30" cy="28" r="4" stroke="currentColor" stroke-width="2"/>
						<line x1="17" y1="19" x2="12" y2="25" stroke="currentColor" stroke-width="2"/>
						<line x1="23" y1="19" x2="28" y2="25" stroke="currentColor" stroke-width="2"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Agenci AI', 'AI Agents' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Projektujemy i wdrażamy inteligentnych agentów AI, którzy zastępują powtarzalne role w Twojej firmie. Bez zwolnień lekarskich, bez błędów, bez limitu godzin.',
					'We design and deploy intelligent AI agents that replace repetitive roles in your company. No sick leave, no errors, no hour limits.'
				) ); ?></p>
			</article>
			<article class="card reveal" style="--delay: 180ms">
				<div class="card-icon">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="4" y="8" width="12" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
						<rect x="24" y="22" width="12" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
						<path d="M16 13H24C26.2 13 28 14.8 28 17V22" stroke="currentColor" stroke-width="2"/>
						<path d="M24 27H16C13.8 27 12 25.2 12 23V18" stroke="currentColor" stroke-width="2"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Integracja Systemów', 'System Integration' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Łączymy Twoje narzędzia w jedną maszynę. Zero ręcznego przeklepywania danych, zero ludzkich pomyłek, pełna automatyzacja.',
					'We wire your tools into one machine. Zero manual data entry, zero human error, full automation.'
				) ); ?></p>
			</article>
			<article class="card reveal" style="--delay: 260ms">
				<div class="card-icon">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M20 6L34 14V26L20 34L6 26V14L20 6Z" stroke="currentColor" stroke-width="2"/>
						<path d="M20 6V20M20 20L34 14M20 20L6 14" stroke="currentColor" stroke-width="2" opacity="0.5"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Rozwiązania na Miarę', 'Custom Solutions' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Każdy biznes jest inny — ale każdy ma procesy, które pożerają czas i pieniądze. Budujemy AI, które je eliminuje.',
					'Every business is different — but every one has processes that devour time and money. We build AI that eliminates them.'
				) ); ?></p>
			</article>
		</div>
	</div>
</section>

<!-- Jak Działamy (How We Work) -->
<section class="section section--process" id="jak-dzialamy">
	<div class="container">
		<h2 class="section-title reveal"><?php echo esc_html( ia_t( 'Jak działamy', 'How we work' ) ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php echo esc_html( ia_t(
			'Od analizy do wdrożenia — szybciej niż rekrutacja jednego pracownika',
			'From analysis to deployment — faster than hiring a single employee'
		) ); ?></p>
		<div class="steps-grid">
			<div class="step reveal" style="--delay: 100ms">
				<div class="step-number">01</div>
				<h3><?php echo esc_html( ia_t( 'Analiza', 'Analysis' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Prześwietlamy Twój biznes i wskazujemy, gdzie tracisz pieniądze na zadania, które AI zrobi 10x szybciej.',
					'We x-ray your business and pinpoint where you\'re bleeding money on tasks AI does 10x faster.'
				) ); ?></p>
			</div>
			<div class="step reveal" style="--delay: 200ms">
				<div class="step-number">02</div>
				<h3><?php echo esc_html( ia_t( 'Projektowanie', 'Design' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Tworzymy działający prototyp, który od razu przynosi wartość. Płacisz za efekt, nie za obietnice.',
					'We build a working prototype that delivers value immediately. You pay for results, not promises.'
				) ); ?></p>
			</div>
			<div class="step reveal" style="--delay: 300ms">
				<div class="step-number">03</div>
				<h3><?php echo esc_html( ia_t( 'Wdrożenie', 'Deployment' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Uruchamiamy, testujemy i optymalizujemy. Twoi nowi „pracownicy" zaczynają od pierwszego dnia na 100%.',
					'We launch, test, and optimize. Your new "employees" operate at 100% from day one.'
				) ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- Dlaczego My (Why Us) -->
<section class="section section--features" id="dlaczego-my">
	<div class="container">
		<h2 class="section-title reveal"><?php echo esc_html( ia_t( 'Dlaczego my', 'Why us' ) ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php echo esc_html( ia_t(
			'Bo tani zespół to nadal zespół. AI to inna liga.',
			'Because a cheap team is still a team. AI is a different league.'
		) ); ?></p>
		<div class="features-grid">
			<div class="feature reveal" style="--delay: 100ms">
				<div class="feature-icon">
					<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="16" cy="16" r="10" stroke="currentColor" stroke-width="2"/>
						<path d="M16 10V16L20 20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Doświadczenie w AI', 'AI Expertise' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Nie uczymy się na Twoim budżecie. Wdrażamy sprawdzone rozwiązania, które już działają w innych firmach.',
					'We don\'t learn on your budget. We deploy proven solutions already running in other companies.'
				) ); ?></p>
			</div>
			<div class="feature reveal" style="--delay: 180ms">
				<div class="feature-icon">
					<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="6" y="6" width="20" height="20" rx="4" stroke="currentColor" stroke-width="2"/>
						<path d="M12 16L15 19L21 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Nowoczesne technologie', 'Cutting-edge tech' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'LLM, agenci autonomiczni, automatyzacja procesów — używamy tego, co za 2 lata będzie standardem. Ty masz to dzisiaj.',
					'LLMs, autonomous agents, process automation — we use what will be standard in 2 years. You get it today.'
				) ); ?></p>
			</div>
			<div class="feature reveal" style="--delay: 260ms">
				<div class="feature-icon">
					<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M16 6C10.5 6 6 10.5 6 16C6 21.5 10.5 26 16 26" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
						<path d="M16 6C21.5 6 26 10.5 26 16C26 21.5 21.5 26 16 26" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 3"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Indywidualne podejście', 'Tailored approach' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'Nie sprzedajemy pudełkowego softu. Każde rozwiązanie projektujemy pod Twój biznes, Twoje procesy, Twoje marże.',
					'We don\'t sell boxed software. Every solution is engineered for your business, your processes, your margins.'
				) ); ?></p>
			</div>
			<div class="feature reveal" style="--delay: 340ms">
				<div class="feature-icon">
					<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M8 20L16 12L24 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M8 26L16 18L24 26" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.4"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Wsparcie po wdrożeniu', 'Post-launch support' ) ); ?></h3>
				<p><?php echo esc_html( ia_t(
					'AI się nie zwalnia, ale wymaga opieki. Monitorujemy, optymalizujemy i skalujemy razem z Tobą.',
					'AI doesn\'t quit, but it needs care. We monitor, optimize, and scale alongside you.'
				) ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- CTA / Kontakt -->
<section class="section section--cta" id="kontakt">
	<div class="container">
		<div class="cta-box reveal">
			<h2><?php echo esc_html( ia_t(
				'Ile kosztuje Cię zespół, który AI może zastąpić?',
				'How much is that team costing you when AI can do it?'
			) ); ?></h2>
			<p><?php echo esc_html( ia_t(
				'Policz to z nami. Jedna rozmowa może zmienić Twój rachunek kosztów.',
				'Do the math with us. One conversation could slash your cost sheet.'
			) ); ?></p>
			<div class="cta-actions">
				<button type="button" class="btn btn--primary" id="open-contact-form"><?php echo esc_html( ia_t( 'Napisz do nas', 'Get in touch' ) ); ?></button>
			</div>
		</div>
	</div>
</section>

<!-- Contact Form Modal -->
<div class="modal-overlay" id="contact-modal" aria-hidden="true" role="dialog" aria-label="<?php echo esc_attr( ia_t( 'Formularz kontaktowy', 'Contact form' ) ); ?>">
	<div class="modal-backdrop"></div>
	<div class="modal-content">
		<button type="button" class="modal-close" aria-label="<?php echo esc_attr( ia_t( 'Zamknij', 'Close' ) ); ?>">&times;</button>
		<h3 class="modal-title"><?php echo esc_html( ia_t( 'Napisz do nas', 'Get in touch' ) ); ?></h3>
		<p class="modal-subtitle"><?php echo esc_html( ia_t(
			'Wypełnij formularz, a odezwiemy się najszybciej jak to możliwe.',
			'Fill out the form and we\'ll get back to you as soon as possible.'
		) ); ?></p>
		<div class="contact-form-wrap">
			<?php echo do_shortcode( '[wpforms id="85" title="false" description="false"]' ); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
