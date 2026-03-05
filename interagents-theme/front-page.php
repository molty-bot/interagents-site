<?php
/**
 * Front Page Template
 * Includes offer builder section after hero
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

// Offer pricing setup
$lang     = ia_get_lang();
$is_pl    = $lang === 'pl';
$currency = $is_pl ? 'PLN' : 'EUR';
$rate     = 4.3;

$pricing = array(
	'openclaw' => array(
		'install'         => $is_pl ? 6000 : round( 6000 / $rate / 50 ) * 50,
		'managed_mac'     => $is_pl ? 2500 : round( 2500 / $rate / 10 ) * 10,
		'managed_vps'     => $is_pl ? 3000 : round( 3000 / $rate / 10 ) * 10,
		'managed_mini'    => $is_pl ? 3500 : round( 3500 / $rate / 10 ) * 10,
		'no_api_discount' => $is_pl ? 500 : round( 500 / $rate / 10 ) * 10,
	),
	'intercore' => array(
		'setup'   => $is_pl ? 15000 : round( 15000 / $rate / 100 ) * 100,
		'monthly' => $is_pl ? 8000 : round( 8000 / $rate / 50 ) * 50,
	),
	'complete' => array(
		'setup'   => $is_pl ? 15000 : round( 15000 / $rate / 100 ) * 100,
		'monthly' => $is_pl ? 10000 : round( 10000 / $rate / 50 ) * 50,
	),
);
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
			<a href="#offer" class="btn"><?php echo esc_html( ia_t( 'Zobacz ofertę', 'See our offer' ) ); ?></a>
		</div>
	</div>
</section>

<!-- Offer Builder -->
<section class="section section--offer" id="offer">
	<div class="container">
		<h2 class="section-title reveal"><?php echo esc_html( ia_t(
			'Zbuduj swój zespół AI',
			'Build your AI team'
		) ); ?></h2>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php echo esc_html( ia_t(
			'Wybierz. Skonfiguruj. Wdrażaj.',
			'Choose. Configure. Deploy.'
		) ); ?></p>

		<!-- Product Selector -->
		<div class="offer-products" id="offer-products">
			<!-- OpenClaw -->
			<div class="offer-product-card" data-product="openclaw" tabindex="0" role="button" aria-pressed="false">
				<div class="offer-product-icon">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="20" cy="14" r="6" stroke="currentColor" stroke-width="2"/>
						<circle cx="10" cy="28" r="4" stroke="currentColor" stroke-width="2"/>
						<circle cx="30" cy="28" r="4" stroke="currentColor" stroke-width="2"/>
						<line x1="17" y1="19" x2="12" y2="25" stroke="currentColor" stroke-width="2"/>
						<line x1="23" y1="19" x2="28" y2="25" stroke="currentColor" stroke-width="2"/>
					</svg>
				</div>
				<h3>OpenClaw</h3>
				<p class="offer-product-label"><?php echo esc_html( ia_t(
					'Twój osobisty zespół AI',
					'Your personal AI team'
				) ); ?></p>
				<p class="offer-product-desc"><?php echo esc_html( ia_t(
					'Wielu agentów AI na Twoim sprzęcie lub naszym. Aplikacja InterAgents na iOS i Androida.',
					'Multiple AI agents on your hardware or ours. InterAgents app on iOS and Android.'
				) ); ?></p>
				<div class="offer-product-from">
					<?php echo esc_html( ia_t( 'od', 'from' ) ); ?>
					<strong><?php echo number_format( $pricing['openclaw']['install'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?></strong>
				</div>
			</div>

			<!-- InterCore -->
			<div class="offer-product-card" data-product="intercore" tabindex="0" role="button" aria-pressed="false">
				<div class="offer-product-icon">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="4" y="8" width="12" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
						<rect x="24" y="22" width="12" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
						<path d="M16 13H24C26.2 13 28 14.8 28 17V22" stroke="currentColor" stroke-width="2"/>
						<path d="M24 27H16C13.8 27 12 25.2 12 23V18" stroke="currentColor" stroke-width="2"/>
					</svg>
				</div>
				<h3>InterCore</h3>
				<p class="offer-product-label"><?php echo esc_html( ia_t(
					'Platforma AI dla Twojej firmy',
					'AI platform for your business'
				) ); ?></p>
				<p class="offer-product-desc"><?php echo esc_html( ia_t(
					'Przyjeżdżamy, analizujemy Twoje procesy i budujemy zautomatyzowany system AI. Hosting, backupy, aktualizacje w cenie.',
					'We come to you, analyze your processes, and build an automated AI system. Hosting, backups, updates included.'
				) ); ?></p>
				<div class="offer-product-from">
					<?php echo esc_html( ia_t( 'od', 'from' ) ); ?>
					<strong><?php echo number_format( $pricing['intercore']['setup'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?></strong>
					<?php echo esc_html( ia_t( 'wdrożenie', 'setup' ) ); ?>
				</div>
			</div>

			<!-- Pełna Moc / Complete -->
			<div class="offer-product-card" data-product="complete" tabindex="0" role="button" aria-pressed="false">
				<div class="offer-product-icon">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M20 6L34 14V26L20 34L6 26V14L20 6Z" stroke="currentColor" stroke-width="2"/>
						<path d="M20 6V20M20 20L34 14M20 20L6 14" stroke="currentColor" stroke-width="2" opacity="0.5"/>
					</svg>
				</div>
				<h3><?php echo esc_html( ia_t( 'Pełna Moc', 'Complete' ) ); ?></h3>
				<p class="offer-product-label"><?php echo esc_html( ia_t(
					'InterCore + OpenClaw w pakiecie',
					'InterCore + OpenClaw bundle'
				) ); ?></p>
				<p class="offer-product-desc"><?php echo esc_html( ia_t(
					'Platforma AI + osobisty zespół agentów. Wdrożenie OpenClaw gratis. Wszystko w jednej fakturze.',
					'AI platform + personal agent team. OpenClaw setup free. Everything on one invoice.'
				) ); ?></p>
				<div class="offer-product-from">
					<?php echo esc_html( ia_t( 'od', 'from' ) ); ?>
					<strong><?php echo number_format( $pricing['complete']['setup'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?></strong>
					<?php echo esc_html( ia_t( 'wdrożenie', 'setup' ) ); ?>
				</div>
			</div>
		</div>

		<!-- OpenClaw Configurator -->
		<div class="offer-configurator" id="offer-configurator" style="display:none" aria-hidden="true">
			<h2 class="offer-config-title"><?php echo esc_html( ia_t(
				'Skonfiguruj OpenClaw',
				'Configure OpenClaw'
			) ); ?></h2>

			<!-- Hosting -->
			<div class="offer-config-group">
				<label class="offer-config-label"><?php echo esc_html( ia_t( 'Hosting', 'Hosting' ) ); ?></label>
				<div class="offer-toggle-group" data-config="hosting">
					<button class="offer-toggle active" data-value="mac" type="button">
						<span class="offer-toggle-icon">💻</span>
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Twój Mac', 'Your Mac' ) ); ?></span>
					</button>
					<button class="offer-toggle" data-value="vps" type="button">
						<span class="offer-toggle-icon">☁️</span>
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Twój VPS', 'Your VPS' ) ); ?></span>
					</button>
					<button class="offer-toggle" data-value="mini" type="button">
						<span class="offer-toggle-icon">🖥️</span>
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Nasz Mac Mini', 'Our Mac Mini' ) ); ?></span>
					</button>
				</div>
			</div>

			<!-- Support -->
			<div class="offer-config-group">
				<label class="offer-config-label"><?php echo esc_html( ia_t( 'Obsługa', 'Support' ) ); ?></label>
				<div class="offer-toggle-group" data-config="managed">
					<button class="offer-toggle" data-value="self" type="button">
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Sam/a się zajmuję', 'I\'ll handle it myself' ) ); ?></span>
						<span class="offer-toggle-price"><?php echo esc_html( ia_t( '0 PLN/mies.', '0 EUR/mo' ) ); ?></span>
					</button>
					<button class="offer-toggle active" data-value="managed" type="button">
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Zajmijcie się wszystkim', 'Take care of everything' ) ); ?></span>
						<span class="offer-toggle-badge"><?php echo esc_html( ia_t( 'Polecany', 'Recommended' ) ); ?></span>
					</button>
				</div>
			</div>

			<!-- AI Tokens -->
			<div class="offer-config-group" id="offer-api-group">
				<label class="offer-config-label"><?php echo esc_html( ia_t( 'Tokeny AI', 'AI Tokens' ) ); ?></label>
				<div class="offer-toggle-group" data-config="api">
					<button class="offer-toggle" data-value="own" type="button">
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Mam własny klucz API', 'I have my own API key' ) ); ?></span>
						<span class="offer-toggle-price">-<?php echo number_format( $pricing['openclaw']['no_api_discount'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?>/<?php echo esc_html( ia_t( 'mies.', 'mo' ) ); ?></span>
					</button>
					<button class="offer-toggle active" data-value="included" type="button">
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( '€200 tokenów Claude/OpenAI w cenie', '€200 Claude/OpenAI tokens included' ) ); ?></span>
						<span class="offer-toggle-badge"><?php echo esc_html( ia_t( 'Najlepsza wartość', 'Best value' ) ); ?></span>
					</button>
				</div>
			</div>

			<!-- Quote Summary -->
			<div class="offer-quote" id="offer-quote">
				<h3 class="offer-quote-title"><?php echo esc_html( ia_t( 'Twoja wycena', 'Your quote' ) ); ?></h3>
				<div class="offer-quote-lines">
					<div class="offer-quote-line">
						<span><?php echo esc_html( ia_t( 'Instalacja (jednorazowo)', 'Installation (one-time)' ) ); ?></span>
						<strong id="quote-setup"><?php echo number_format( $pricing['openclaw']['install'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?></strong>
					</div>
					<div class="offer-quote-line">
						<span><?php echo esc_html( ia_t( 'Miesięcznie', 'Monthly' ) ); ?></span>
						<strong id="quote-monthly">0 <?php echo $currency; ?></strong>
					</div>
					<div class="offer-quote-divider"></div>
					<div class="offer-quote-line offer-quote-total">
						<span><?php echo esc_html( ia_t( 'Łącznie', 'Total' ) ); ?></span>
						<strong id="quote-total"><?php echo number_format( $pricing['openclaw']['install'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?></strong>
					</div>
				</div>

				<div class="offer-includes">
					<h4><?php echo esc_html( ia_t( 'W cenie', 'Included' ) ); ?></h4>
					<ul id="offer-includes-list">
						<li><?php echo esc_html( ia_t(
							'Zespół agentów AI pracujących dla Ciebie (do 8 naraz)',
							'A team of AI agents working for you (up to 8 at once)'
						) ); ?></li>
						<li><?php echo esc_html( ia_t(
							'Agenty skonfigurowane pod Twoje potrzeby',
							'Agents configured for your needs'
						) ); ?></li>
						<li><?php echo esc_html( ia_t(
							'Aplikacja InterAgents na iOS i Androida — do 2 osób',
							'InterAgents app on iOS and Android — up to 2 people'
						) ); ?></li>
						<li><?php echo esc_html( ia_t(
							'Panel do zlecania i śledzenia zadań',
							'Dashboard to assign and track tasks'
						) ); ?></li>
						<li><?php echo esc_html( ia_t(
							'Sesja szkoleniowa na start',
							'Training session to get you started'
						) ); ?></li>
						<li id="offer-include-api" style="display:list-item"><?php echo esc_html( ia_t(
							'€200 tokenów Claude/OpenAI miesięcznie w cenie',
							'€200 Claude/OpenAI tokens included monthly'
						) ); ?></li>
						<li id="offer-include-managed" style="display:list-item"><?php echo esc_html( ia_t(
							'Aktualizacje, monitoring, wsparcie',
							'Updates, monitoring, support'
						) ); ?></li>
					</ul>
				</div>

				<div class="offer-actions">
					<button type="button" class="btn btn--primary offer-action-btn" id="offer-inquire">
						<?php echo esc_html( ia_t( 'Wyślij zapytanie', 'Send inquiry' ) ); ?>
					</button>
					<button type="button" class="btn offer-action-btn" id="offer-share">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="margin-right:6px;vertical-align:-2px">
							<path d="M11 1L15 5L11 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M15 5H6C3.2 5 1 7.2 1 10V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<?php echo esc_html( ia_t( 'Udostępnij link', 'Share link' ) ); ?>
					</button>
					<button type="button" class="btn offer-action-btn" id="offer-email">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="margin-right:6px;vertical-align:-2px">
							<rect x="1" y="3" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/>
							<path d="M1 5L8 9L15 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
						</svg>
						<?php echo esc_html( ia_t( 'Wyślij mailem', 'Email quote' ) ); ?>
					</button>
				</div>
			</div>
		</div>

		<!-- InterCore Info Card -->
		<div class="offer-info-card" id="offer-intercore-info" style="display:none" aria-hidden="true">
			<h2><?php echo esc_html( ia_t(
				'InterCore — Platforma AI dla Twojej firmy',
				'InterCore — AI Platform for Your Business'
			) ); ?></h2>
			<p class="offer-info-lead"><?php echo esc_html( ia_t(
				'Nie wdrażamy „narzędzia". Przebudowujemy sposób, w jaki Twoja firma myśli, decyduje i działa.',
				'We don\'t deploy a "tool." We rewire how your company thinks, decides, and executes.'
			) ); ?></p>

			<div class="offer-info-features">
				<div class="offer-info-feature">
					<strong>🤖 <?php echo esc_html( ia_t( 'Agenci AI dopasowani do Twoich ról', 'AI Agents tailored to your roles' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Sprzedaż, support, operacje, finanse — każdy agent zna Twoje dane i reguły.',
						'Sales, support, ops, finance — each agent knows your data and rules.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong>⚡ <?php echo esc_html( ia_t( 'Automatyzacja procesów', 'Automated workflows' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Koniec z ręcznym przekazywaniem zadań. Procesy działają same, od A do Z.',
						'No more manual handoffs. Processes run themselves, end to end.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong>🔗 <?php echo esc_html( ia_t( 'Wiele źródeł danych', 'Multiple data sources' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'CRM, ERP, email, arkusze, bazy danych — wszystko połączone w jeden inteligentny system.',
						'CRM, ERP, email, spreadsheets, databases — all wired into one intelligent system.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong>🔒 <?php echo esc_html( ia_t( 'Twoje dane, Twoje zasady', 'Your data, your rules' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Hosting, backupy, aktualizacje, tokeny AI — wszystko w cenie abonamentu.',
						'Hosting, backups, updates, AI tokens — all included in the subscription.'
					) ); ?></p>
				</div>
			</div>

			<div class="offer-info-pricing">
				<div class="offer-info-price-block">
					<span class="offer-info-price-label"><?php echo esc_html( ia_t( 'Wdrożenie', 'Setup' ) ); ?></span>
					<span class="offer-info-price-value"><?php echo esc_html( ia_t( 'od', 'from' ) ); ?> <?php echo number_format( $pricing['intercore']['setup'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?></span>
				</div>
				<div class="offer-info-price-block">
					<span class="offer-info-price-label"><?php echo esc_html( ia_t( 'Miesięcznie', 'Monthly' ) ); ?></span>
					<span class="offer-info-price-value"><?php echo esc_html( ia_t( 'od', 'from' ) ); ?> <?php echo number_format( $pricing['intercore']['monthly'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?>/<?php echo esc_html( ia_t( 'mies.', 'mo' ) ); ?></span>
				</div>
			</div>

			<div class="offer-info-process">
				<h3><?php echo esc_html( ia_t( 'Jak to działa', 'How it works' ) ); ?></h3>
				<div class="offer-info-steps">
					<div class="offer-info-step"><span>01</span> <?php echo esc_html( ia_t(
						'Przyjeżdżamy do Ciebie i analizujemy procesy',
						'We come to you and analyze your processes'
					) ); ?></div>
					<div class="offer-info-step"><span>02</span> <?php echo esc_html( ia_t(
						'Projektujemy system AI pod Twoje potrzeby',
						'We design an AI system for your needs'
					) ); ?></div>
					<div class="offer-info-step"><span>03</span> <?php echo esc_html( ia_t(
						'Wdrażamy, testujemy i uruchamiamy',
						'We deploy, test, and launch'
					) ); ?></div>
				</div>
			</div>

			<div class="offer-actions">
				<button type="button" class="btn btn--primary offer-action-btn" id="intercore-inquire">
					<?php echo esc_html( ia_t( 'Umów się na analizę', 'Book an analysis' ) ); ?>
				</button>
			</div>
		</div>

		<!-- Pełna Moc / Complete Info Card -->
		<div class="offer-info-card" id="offer-complete-info" style="display:none" aria-hidden="true">
			<h2><?php echo esc_html( ia_t(
				'Pełna Moc — Wszystko w jednym',
				'Complete — Everything in one package'
			) ); ?></h2>
			<p class="offer-info-lead"><?php echo esc_html( ia_t(
				'InterCore + OpenClaw w cenie wdrożenia InterCore. Instalacja OpenClaw gratis.',
				'InterCore + OpenClaw at the price of InterCore setup. OpenClaw installation free.'
			) ); ?></p>

			<div class="offer-info-features">
				<div class="offer-info-feature">
					<strong>🏢 <?php echo esc_html( ia_t( 'Platforma InterCore', 'InterCore Platform' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Pełna platforma AI z agentami systemowymi, automatyzacją procesów i integracją danych.',
						'Full AI platform with system agents, workflows, data integration.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong>🤖 <?php echo esc_html( ia_t( 'Zespół OpenClaw', 'OpenClaw Team' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Osobisty zespół agentów AI + aplikacja InterAgents na iOS i Androida.',
						'Personal AI agent team + InterAgents app on iOS and Android.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong>🚀 <?php echo esc_html( ia_t( 'Priorytetowe wdrożenie', 'Priority deployment' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Jeden spójny ekosystem. Dedykowane wsparcie techniczne. Jedna faktura za wszystko.',
						'One unified ecosystem. Dedicated technical support. One invoice for everything.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong>💰 <?php echo esc_html( ia_t( 'Oszczędność na wdrożeniu', 'Save on setup' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Instalacja OpenClaw gratis w pakiecie. Płacisz tylko za wdrożenie InterCore.',
						'OpenClaw installation free in the bundle. You only pay for InterCore setup.'
					) ); ?></p>
				</div>
			</div>

			<div class="offer-info-pricing">
				<div class="offer-info-price-block">
					<span class="offer-info-price-label"><?php echo esc_html( ia_t( 'Wdrożenie', 'Setup' ) ); ?></span>
					<span class="offer-info-price-value"><?php echo esc_html( ia_t( 'od', 'from' ) ); ?> <?php echo number_format( $pricing['complete']['setup'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?></span>
					<span class="offer-info-price-note"><?php echo esc_html( ia_t(
						'= cena wdrożenia InterCore. OpenClaw gratis.',
						'= InterCore setup price. OpenClaw free.'
					) ); ?></span>
				</div>
				<div class="offer-info-price-block">
					<span class="offer-info-price-label"><?php echo esc_html( ia_t( 'Miesięcznie', 'Monthly' ) ); ?></span>
					<span class="offer-info-price-value"><?php echo esc_html( ia_t( 'od', 'from' ) ); ?> <?php echo number_format( $pricing['complete']['monthly'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?>/<?php echo esc_html( ia_t( 'mies.', 'mo' ) ); ?></span>
				</div>
			</div>

			<div class="offer-actions">
				<button type="button" class="btn btn--primary offer-action-btn" id="complete-inquire">
					<?php echo esc_html( ia_t( 'Porozmawiajmy', 'Talk to us' ) ); ?>
				</button>
			</div>
		</div>

		<!-- Toast -->
		<div class="offer-toast" id="offer-toast" aria-live="polite"></div>
	</div>
</section>

<!-- Offer JS config -->
<script>
window.offerConfig = {
	lang: '<?php echo esc_js( $lang ); ?>',
	currency: '<?php echo esc_js( $currency ); ?>',
	thousandSep: '<?php echo $is_pl ? " " : ","; ?>',
	pricing: <?php echo wp_json_encode( $pricing ); ?>,
	labels: {
		setup: '<?php echo esc_js( ia_t( 'Instalacja (jednorazowo)', 'Installation (one-time)' ) ); ?>',
		monthly: '<?php echo esc_js( ia_t( 'Miesięcznie', 'Monthly' ) ); ?>',
		total: '<?php echo esc_js( ia_t( 'Łącznie', 'Total' ) ); ?>',
		perMonth: '<?php echo esc_js( ia_t( '/mies.', '/mo' ) ); ?>',
		plusPerMonth: '<?php echo esc_js( ia_t( '+ /mies.', '+ /mo' ) ); ?>',
		copied: '<?php echo esc_js( ia_t( 'Link skopiowany!', 'Link copied!' ) ); ?>',
		selfManaged: '<?php echo esc_js( ia_t( 'Sam/a się zajmuję', 'I\'ll handle it myself' ) ); ?>',
		managed: '<?php echo esc_js( ia_t( 'Zajmijcie się wszystkim', 'Take care of everything' ) ); ?>',
		hostingMac: '<?php echo esc_js( ia_t( 'Twój Mac', 'Your Mac' ) ); ?>',
		hostingVps: '<?php echo esc_js( ia_t( 'Twój VPS', 'Your VPS' ) ); ?>',
		hostingMini: '<?php echo esc_js( ia_t( 'Nasz Mac Mini', 'Our Mac Mini' ) ); ?>',
		apiOwn: '<?php echo esc_js( ia_t( 'Mam własny klucz API', 'I have my own API key' ) ); ?>',
		apiIncluded: '<?php echo esc_js( ia_t( '€200 tokenów Claude/OpenAI w cenie', '€200 Claude/OpenAI tokens included' ) ); ?>',
		emailSubject: '<?php echo esc_js( ia_t( 'Wycena OpenClaw — InterAgents.ai', 'OpenClaw Quote — InterAgents.ai' ) ); ?>',
		product: 'OpenClaw'
	}
};
</script>

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
