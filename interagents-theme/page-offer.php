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
		'self_mini'       => $is_pl ? 500 : round( 500 / $rate / 10 ) * 10,
		'no_api_discount' => $is_pl ? 500 : round( 500 / $rate / 10 ) * 10,
	),
	'intercore' => array(
		'setup'   => $is_pl ? 15000 : round( 15000 / $rate / 100 ) * 100,
		'monthly' => $is_pl ? 10000 : round( 10000 / $rate / 50 ) * 50,
	),
);
?>

<!-- Offer Builder -->
<section class="section section--offer" id="offer">
	<div class="container">
		<h1 class="section-title reveal"><?php echo esc_html( ia_t(
			'Wybierz, co naprawdę trzeba naprawić.',
			'Choose what actually needs fixing.'
		) ); ?></h1>
		<p class="section-subtitle reveal" style="--delay: 60ms"><?php echo esc_html( ia_t(
			'Dwa poziomy: interagents do konkretnych zadań albo intercore, gdy potrzebujesz interagents wraz z całym środowiskiem pracy.',
			'Two levels: interagents for specific jobs, or intercore when you need interagents plus the full workspace around them.'
		) ); ?></p>

		<!-- Product Selector -->
		<div class="offer-products" id="offer-products">
			<!-- interagents, powered by OpenClaw -->
			<div class="offer-product-card" data-product="openclaw" tabindex="0" role="button" aria-pressed="false">
				<p class="offer-product-level"><?php echo esc_html( ia_t( 'Poziom 01 · samodzielni pracownicy AI', 'Level 01 · standalone AI workers' ) ); ?></p>
				<h3>interagents</h3>
				<p class="offer-product-label"><?php echo esc_html( ia_t(
					'Dedykowani pracownicy AI · zbudowani na OpenClaw',
					'Custom AI workers · powered by OpenClaw'
				) ); ?></p>
				<p class="offer-product-desc"><?php echo esc_html( ia_t(
					'Agenci szyci pod konkretne zadania. Zlecasz pracę i kontrolujesz wyniki z komputera lub telefonu.',
					'Agents tailored to specific jobs. Assign work and review results from desktop or mobile.'
				) ); ?></p>
				<div class="offer-product-from">
					<?php echo esc_html( ia_t( 'od', 'from' ) ); ?>
					<strong><?php echo number_format( $pricing['openclaw']['install'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?></strong>
				</div>
			</div>

			<!-- intercore -->
			<div class="offer-product-card" data-product="intercore" tabindex="0" role="button" aria-pressed="false">
				<p class="offer-product-level"><?php echo esc_html( ia_t( 'Poziom 02 · cały system pracy', 'Level 02 · the full operating system' ) ); ?></p>
				<h3>intercore</h3>
				<p class="offer-product-label"><?php echo esc_html( ia_t(
					'interagents i środowisko pracy dla ludzi oraz AI',
					'interagents plus a workspace for humans and AI'
				) ); ?></p>
				<p class="offer-product-desc"><?php echo esc_html( ia_t(
					'intercore zawsze zawiera interagents. Łączy ludzi, agentów, dane, narzędzia i akceptacje w jeden system zaprojektowany pod sposób działania firmy.',
					'intercore always includes interagents. It connects people, agents, data, tools and approvals in one system designed around how the business operates.'
				) ); ?></p>
				<div class="offer-product-from">
					<?php echo esc_html( ia_t( 'od', 'from' ) ); ?>
					<strong><?php echo number_format( $pricing['intercore']['setup'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?></strong>
					<?php echo esc_html( ia_t( 'wdrożenie', 'setup' ) ); ?>
				</div>
			</div>
		</div>

		<!-- interagents configurator -->
		<div class="offer-configurator" id="offer-configurator" style="display:none" aria-hidden="true">
			<h2 class="offer-config-title"><?php echo esc_html( ia_t(
				'Skonfiguruj interagents (OpenClaw)',
				'Configure interagents (OpenClaw)'
			) ); ?></h2>

			<!-- Hosting -->
			<div class="offer-config-group">
				<span class="offer-config-label" id="offer-hosting-label"><?php echo esc_html( ia_t( 'Hosting', 'Hosting' ) ); ?></span>
				<div class="offer-toggle-group" data-config="hosting" role="group" aria-labelledby="offer-hosting-label">
					<button class="offer-toggle active" data-value="mac" type="button" aria-pressed="true">
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Twój Mac', 'Your Mac' ) ); ?></span>
					</button>
					<button class="offer-toggle" data-value="vps" type="button" aria-pressed="false">
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Twój VPS', 'Your VPS' ) ); ?></span>
					</button>
					<button class="offer-toggle" data-value="mini" type="button" aria-pressed="false">
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Nasz Mac Mini', 'Our Mac Mini' ) ); ?></span>
					</button>
				</div>
			</div>

			<!-- Support -->
			<div class="offer-config-group">
				<span class="offer-config-label" id="offer-managed-label"><?php echo esc_html( ia_t( 'Obsługa', 'Support' ) ); ?></span>
				<div class="offer-toggle-group" data-config="managed" role="group" aria-labelledby="offer-managed-label">
					<button class="offer-toggle" data-value="self" type="button" aria-pressed="false">
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Sam/a się zajmuję', 'I\'ll handle it myself' ) ); ?></span>
					</button>
					<button class="offer-toggle active" data-value="managed" type="button" aria-pressed="true">
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Zajmijcie się wszystkim', 'Take care of everything' ) ); ?></span>
						<span class="offer-toggle-badge"><?php echo esc_html( ia_t( 'Polecany', 'Recommended' ) ); ?></span>
					</button>
				</div>
			</div>

			<!-- AI Tokens -->
			<div class="offer-config-group" id="offer-api-group">
				<span class="offer-config-label" id="offer-api-label"><?php echo esc_html( ia_t( 'Tokeny AI', 'AI Tokens' ) ); ?></span>
				<div class="offer-toggle-group" data-config="api" role="group" aria-labelledby="offer-api-label">
					<button class="offer-toggle" data-value="own" type="button" aria-pressed="false">
						<span class="offer-toggle-title"><?php echo esc_html( ia_t( 'Mam własny klucz API', 'I have my own API key' ) ); ?></span>
						<span class="offer-toggle-price">-<?php echo number_format( $pricing['openclaw']['no_api_discount'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?>/<?php echo esc_html( ia_t( 'mies.', 'mo' ) ); ?></span>
					</button>
					<button class="offer-toggle active" data-value="included" type="button" aria-pressed="true">
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
							'Agenci skonfigurowani pod Twoje potrzeby',
							'Agents configured for your needs'
						) ); ?></li>
						<li><?php echo esc_html( ia_t(
							'Aplikacja interagents na iOS i Androida — do 2 osób',
							'interagents app on iOS and Android — up to 2 people'
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
						<?php echo esc_html( ia_t( 'Udostępnij link', 'Share link' ) ); ?>
					</button>
					<button type="button" class="btn offer-action-btn" id="offer-email">
						<?php echo esc_html( ia_t( 'Wyślij mailem', 'Email quote' ) ); ?>
					</button>
				</div>
			</div>
		</div>

		<!-- intercore info card -->
		<div class="offer-info-card" id="offer-intercore-info" style="display:none" aria-hidden="true">
			<h2><?php echo esc_html( ia_t(
				'intercore — interagents i środowisko pracy dla ludzi oraz AI',
				'intercore — interagents plus a workspace for humans and AI'
			) ); ?></h2>
			<p class="offer-info-lead"><?php echo esc_html( ia_t(
				'intercore zawsze zawiera interagents. Dostajesz pracowników AI, aplikację do zarządzania nimi i środowisko, które porządkuje przepływ pracy, danych oraz decyzji w firmie.',
				'intercore always includes interagents. You get AI workers, the app to manage them, and the workspace that organizes work, data, and decisions across the business.'
			) ); ?></p>

			<div class="offer-info-features">
				<div class="offer-info-feature">
					<strong><?php echo esc_html( ia_t( 'interagents w standardzie', 'interagents included as standard' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Dedykowany zespół pracowników AI na OpenClaw oraz aplikacja interagents na iOS i Androida.',
						'A dedicated team of AI workers powered by OpenClaw, plus the interagents app for iOS and Android.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong><?php echo esc_html( ia_t( 'Procesy z jasną odpowiedzialnością', 'Workflows with clear ownership' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Mniej ręcznego przekazywania zadań. Każdy agent, człowiek i punkt akceptacji ma określoną rolę.',
						'Fewer manual handoffs. Every agent, person, and approval point has a defined role.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong><?php echo esc_html( ia_t( 'Dane i integracje', 'Data and integrations' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'CRM, ERP, e-mail, arkusze i bazy danych połączone w jeden kontrolowany system.',
						'CRM, ERP, email, spreadsheets, and databases connected in one controlled system.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong><?php echo esc_html( ia_t( 'Jedno wdrożenie, jedna odpowiedzialność', 'One deployment, one accountable partner' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Priorytetowe wdrożenie, hosting, kopie zapasowe, aktualizacje, wsparcie techniczne i jedna faktura.',
						'Priority deployment, hosting, backups, updates, technical support, and one invoice.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong><?php echo esc_html( ia_t( 'OpenClaw bez osobnej opłaty wdrożeniowej', 'OpenClaw with no separate setup fee' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Instalacja OpenClaw dla pracowników interagents jest częścią wdrożenia intercore.',
						'The OpenClaw installation for your interagents workers is part of the intercore setup.'
					) ); ?></p>
				</div>
				<div class="offer-info-feature">
					<strong><?php echo esc_html( ia_t( 'Twoje dane, Twoje zasady', 'Your data, your rules' ) ); ?></strong>
					<p><?php echo esc_html( ia_t(
						'Projektujemy dostęp, kontrolę i akceptacje pod poziom ryzyka w Twojej firmie.',
						'We design access, controls, and approvals around the risk level in your business.'
					) ); ?></p>
				</div>
			</div>

			<div class="offer-info-pricing">
				<div class="offer-info-price-block">
					<span class="offer-info-price-label"><?php echo esc_html( ia_t( 'Wdrożenie', 'Setup' ) ); ?></span>
					<span class="offer-info-price-value"><?php echo esc_html( ia_t( 'od', 'from' ) ); ?> <?php echo number_format( $pricing['intercore']['setup'], 0, '', $is_pl ? ' ' : ',' ); ?> <?php echo $currency; ?></span>
					<span class="offer-info-price-note"><?php echo esc_html( ia_t(
						'Obejmuje wdrożenie intercore i instalację OpenClaw dla interagents.',
						'Includes the intercore setup and OpenClaw installation for interagents.'
					) ); ?></span>
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
						'Mapujemy jeden proces, odpowiedzialność i punkty decyzyjne',
						'We map one workflow, its owners, and decision points'
					) ); ?></div>
					<div class="offer-info-step"><span>02</span> <?php echo esc_html( ia_t(
						'Projektujemy intercore z interagents, integracjami i zasadami kontroli',
						'We design intercore with interagents, integrations, and control rules'
					) ); ?></div>
					<div class="offer-info-step"><span>03</span> <?php echo esc_html( ia_t(
						'Uruchamiamy, mierzymy wynik i poprawiamy system z zespołem',
						'We launch, measure the outcome, and improve the system with the team'
					) ); ?></div>
				</div>
			</div>

			<div class="offer-actions">
				<button type="button" class="btn btn--primary offer-action-btn" id="intercore-inquire">
					<?php echo esc_html( ia_t( 'Omów intercore', 'Discuss intercore' ) ); ?>
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
		emailSubject: '<?php echo esc_js( ia_t( 'Wycena interagents (OpenClaw) — interagents.ai', 'interagents (OpenClaw) quote — interagents.ai' ) ); ?>',
		product: 'interagents (OpenClaw)'
	}
};
</script>

<!-- Contact Form Modal -->
<div class="modal-overlay" id="contact-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="contact-modal-title" aria-describedby="contact-modal-description">
	<div class="modal-backdrop" aria-hidden="true"></div>
	<div class="modal-content" tabindex="-1">
		<button type="button" class="modal-close" aria-label="<?php echo esc_attr( ia_t( 'Zamknij formularz', 'Close contact form' ) ); ?>">&times;</button>
		<h2 class="modal-title" id="contact-modal-title"><?php echo esc_html( ia_t( 'Napisz do nas', 'Get in touch' ) ); ?></h2>
		<p class="modal-subtitle" id="contact-modal-description"><?php echo esc_html( ia_t(
			'Wypełnij formularz, a odezwiemy się najszybciej jak to możliwe.',
			'Fill out the form and we\'ll get back to you as soon as possible.'
		) ); ?></p>
		<div class="contact-form-wrap">
			<?php echo do_shortcode( '[wpforms id="85" title="false" description="false"]' ); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
