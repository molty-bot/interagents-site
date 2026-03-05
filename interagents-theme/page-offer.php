<?php
/**
 * Template Name: Offer Builder
 * Template Post Type: page
 *
 * Full offer page with product cards, configurator, and info panels.
 *
 * @package InterAgents
 */

get_header();

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
