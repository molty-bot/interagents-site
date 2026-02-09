<?php
/**
 * Front Page Template
 *
 * @package InterAgents
 */

get_header();

$hero_title    = get_theme_mod( 'hero_title', 'Tworzymy inteligentne rozwiązania AI' );
$hero_subtitle = get_theme_mod( 'hero_subtitle', 'Projektujemy agentów AI i budujemy platformy łączące Twoje systemy w jedną, spójną całość.' );
$hero_cta_text = get_theme_mod( 'hero_cta_text', 'Porozmawiajmy' );
$hero_cta_url  = get_theme_mod( 'hero_cta_url', '#kontakt' );
$contact_email = get_theme_mod( 'contact_email', 'hello@interagents.ai' );
?>

<!-- Hero -->
<section class="hero" id="hero">
	<div class="container hero-inner">
		<h1 class="reveal"><?php echo esc_html( $hero_title ); ?></h1>
		<p class="reveal" style="--delay: 80ms"><?php echo esc_html( $hero_subtitle ); ?></p>
		<div class="hero-actions reveal" style="--delay: 160ms">
			<a href="<?php echo esc_url( $hero_cta_url ); ?>" class="btn btn--primary"><?php echo esc_html( $hero_cta_text ); ?></a>
			<a href="#uslugi" class="btn"><?php esc_html_e( 'Poznaj nas', 'interagents' ); ?></a>
		</div>
	</div>
</section>

<!-- Usługi (Services) -->
<section class="section section--services" id="uslugi">
	<div class="container">
		<h2 class="section-title reveal"><?php esc_html_e( 'Nasze usługi', 'interagents' ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php esc_html_e( 'Kompleksowe rozwiązania AI dopasowane do Twoich potrzeb', 'interagents' ); ?></p>
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
				<h3><?php esc_html_e( 'Agenci AI', 'interagents' ); ?></h3>
				<p><?php esc_html_e( 'Projektujemy i wdrażamy inteligentnych agentów AI, którzy automatyzują procesy i wspierają Twój zespół.', 'interagents' ); ?></p>
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
				<h3><?php esc_html_e( 'Integracja Systemów', 'interagents' ); ?></h3>
				<p><?php esc_html_e( 'Łączymy Twoje narzędzia i systemy w jedną, wydajną platformę. API, automatyzacje, przepływ danych.', 'interagents' ); ?></p>
			</article>
			<article class="card reveal" style="--delay: 260ms">
				<div class="card-icon">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M20 6L34 14V26L20 34L6 26V14L20 6Z" stroke="currentColor" stroke-width="2"/>
						<path d="M20 6V20M20 20L34 14M20 20L6 14" stroke="currentColor" stroke-width="2" opacity="0.5"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'Rozwiązania na Miarę', 'interagents' ); ?></h3>
				<p><?php esc_html_e( 'Każdy biznes jest inny. Tworzymy dedykowane rozwiązania dopasowane do Twoich potrzeb.', 'interagents' ); ?></p>
			</article>
		</div>
	</div>
</section>

<!-- Jak Działamy (How We Work) -->
<section class="section section--process" id="jak-dzialamy">
	<div class="container">
		<h2 class="section-title reveal"><?php esc_html_e( 'Jak działamy', 'interagents' ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php esc_html_e( 'Trzy proste kroki do inteligentnego rozwiązania', 'interagents' ); ?></p>
		<div class="steps-grid">
			<div class="step reveal" style="--delay: 100ms">
				<div class="step-number">01</div>
				<h3><?php esc_html_e( 'Analiza', 'interagents' ); ?></h3>
				<p><?php esc_html_e( 'Poznajemy Twój biznes, procesy i potrzeby. Identyfikujemy obszary, gdzie AI przyniesie największą wartość.', 'interagents' ); ?></p>
			</div>
			<div class="step reveal" style="--delay: 200ms">
				<div class="step-number">02</div>
				<h3><?php esc_html_e( 'Projektowanie', 'interagents' ); ?></h3>
				<p><?php esc_html_e( 'Tworzymy architekturę i prototyp rozwiązania. Testujesz je zanim przejdziemy do wdrożenia.', 'interagents' ); ?></p>
			</div>
			<div class="step reveal" style="--delay: 300ms">
				<div class="step-number">03</div>
				<h3><?php esc_html_e( 'Wdrożenie', 'interagents' ); ?></h3>
				<p><?php esc_html_e( 'Implementujemy, testujemy i uruchamiamy. Zapewniamy wsparcie i optymalizację po starcie.', 'interagents' ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- Dlaczego My (Why Us) -->
<section class="section section--features" id="dlaczego-my">
	<div class="container">
		<h2 class="section-title reveal"><?php esc_html_e( 'Dlaczego my', 'interagents' ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php esc_html_e( 'Twój sukces to nasz priorytet', 'interagents' ); ?></p>
		<div class="features-grid">
			<div class="feature reveal" style="--delay: 100ms">
				<div class="feature-icon">
					<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="16" cy="16" r="10" stroke="currentColor" stroke-width="2"/>
						<path d="M16 10V16L20 20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'Doświadczenie w AI', 'interagents' ); ?></h3>
				<p><?php esc_html_e( 'Praktyczna wiedza w projektowaniu i wdrażaniu rozwiązań opartych na sztucznej inteligencji.', 'interagents' ); ?></p>
			</div>
			<div class="feature reveal" style="--delay: 180ms">
				<div class="feature-icon">
					<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="6" y="6" width="20" height="20" rx="4" stroke="currentColor" stroke-width="2"/>
						<path d="M12 16L15 19L21 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'Nowoczesne technologie', 'interagents' ); ?></h3>
				<p><?php esc_html_e( 'Korzystamy z najnowszych narzędzi i frameworków, by dostarczać rozwiązania na najwyższym poziomie.', 'interagents' ); ?></p>
			</div>
			<div class="feature reveal" style="--delay: 260ms">
				<div class="feature-icon">
					<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M16 6C10.5 6 6 10.5 6 16C6 21.5 10.5 26 16 26" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
						<path d="M16 6C21.5 6 26 10.5 26 16C26 21.5 21.5 26 16 26" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 3"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'Indywidualne podejście', 'interagents' ); ?></h3>
				<p><?php esc_html_e( 'Nie ma dwóch takich samych firm. Każde rozwiązanie projektujemy od podstaw, specjalnie dla Ciebie.', 'interagents' ); ?></p>
			</div>
			<div class="feature reveal" style="--delay: 340ms">
				<div class="feature-icon">
					<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M8 20L16 12L24 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M8 26L16 18L24 26" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.4"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'Wsparcie po wdrożeniu', 'interagents' ); ?></h3>
				<p><?php esc_html_e( 'Nie zostawiamy Cię samego. Monitorujemy, optymalizujemy i rozwijamy rozwiązanie razem z Tobą.', 'interagents' ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- CTA / Kontakt -->
<section class="section section--cta" id="kontakt">
	<div class="container">
		<div class="cta-box reveal">
			<h2><?php esc_html_e( 'Gotowy na transformację?', 'interagents' ); ?></h2>
			<p><?php esc_html_e( 'Porozmawiajmy o tym, jak AI może przyspieszyć Twój biznes.', 'interagents' ); ?></p>
			<div class="cta-actions">
				<a href="mailto:<?php echo esc_attr( $contact_email ); ?>" class="btn btn--primary"><?php esc_html_e( 'Napisz do nas', 'interagents' ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
