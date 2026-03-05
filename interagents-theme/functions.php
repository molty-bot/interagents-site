<?php
/**
 * InterAgents.ai Theme Functions
 *
 * @package InterAgents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'INTERAGENTS_VERSION', '1.7.0' );

/**
 * Language detection: cookie > Accept-Language header
 * If browser language starts with 'pl', show Polish. Otherwise English.
 */
function ia_get_lang() {
	static $lang = null;
	if ( $lang !== null ) return $lang;

	// URL param override (cache-busting)
	if ( ! empty( $_GET['lang'] ) && in_array( $_GET['lang'], array( 'pl', 'en' ), true ) ) {
		$lang = $_GET['lang'];
		return $lang;
	}

	// Cookie override
	if ( ! empty( $_COOKIE['ia_lang'] ) && in_array( $_COOKIE['ia_lang'], array( 'pl', 'en' ), true ) ) {
		$lang = $_COOKIE['ia_lang'];
		return $lang;
	}

	// Accept-Language header
	$accept = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
	$lang = ( strpos( strtolower( $accept ), 'pl' ) === 0 ) ? 'pl' : 'en';
	return $lang;
}

/**
 * Bilingual text helper — returns Polish or English based on detected language.
 */
function ia_t( $pl, $en ) {
	return ia_get_lang() === 'pl' ? $pl : $en;
}

/**
 * Bypass page cache when user has explicitly chosen a language.
 * SpeedyCache (and most caching plugins) respect DONOTCACHEPAGE.
 */
if ( ! empty( $_COOKIE['ia_lang'] ) ) {
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
}

/**
 * Theme setup
 */
function interagents_setup() {
	load_theme_textdomain( 'interagents', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 300,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	register_nav_menus( array(
		'primary' => esc_html__( 'Menu główne', 'interagents' ),
	) );
}
add_action( 'after_setup_theme', 'interagents_setup' );

/**
 * Enqueue styles and scripts
 */
function interagents_scripts() {
	// Google Fonts - Inter
	wp_enqueue_style(
		'interagents-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	// Theme critical CSS
	wp_enqueue_style(
		'interagents-style',
		get_stylesheet_uri(),
		array( 'interagents-google-fonts' ),
		INTERAGENTS_VERSION
	);

	// Main CSS (sections, cards, footer)
	wp_enqueue_style(
		'interagents-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'interagents-style' ),
		INTERAGENTS_VERSION
	);

	// Main JS (nav, scroll reveals)
	wp_enqueue_script(
		'interagents-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		INTERAGENTS_VERSION,
		true
	);

	// Offer Builder (only on pages using the offer template)
	if ( is_page_template( 'page-offer.php' ) ) {
		wp_enqueue_style(
			'interagents-offer',
			get_template_directory_uri() . '/assets/css/offer.css',
			array( 'interagents-main' ),
			INTERAGENTS_VERSION
		);
		wp_enqueue_script(
			'interagents-offer',
			get_template_directory_uri() . '/assets/js/offer.js',
			array(),
			INTERAGENTS_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'interagents_scripts' );

/**
 * Add defer to main script
 */
function interagents_defer_scripts( $tag, $handle, $src ) {
	if ( 'interagents-main' === $handle ) {
		return str_replace( ' src=', ' defer src=', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'interagents_defer_scripts', 10, 3 );

/**
 * Add preconnect for Google Fonts
 */
function interagents_preconnect( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'interagents_preconnect', 10, 2 );

/**
 * Schema.org JSON-LD structured data
 */
function ia_schema_jsonld() {
	if ( ! is_front_page() ) return;
	$lang = ia_get_lang();
	$desc_pl = 'Agenci AI i aplikacje, które automatyzują, łączą i ulepszają dane Twojej firmy. Integracja systemów, automatyzacja procesów, niestandardowe rozwiązania AI.';
	$desc_en = 'AI agents and apps that automate, connect, and enhance your business data. System integration, process automation, custom AI solutions.';
	$desc = $lang === 'pl' ? $desc_pl : $desc_en;
	?>
	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "Organization",
		"name": "InterAgents.ai",
		"url": "https://interagents.ai",
		"logo": "https://interagents.ai/wp-content/themes/interagents-theme/assets/img/interagents-logo-transparent.png",
		"description": "<?php echo esc_js( $desc ); ?>",
		"email": "hello@interagents.ai",
		"sameAs": [],
		"contactPoint": {
			"@type": "ContactPoint",
			"email": "hello@interagents.ai",
			"contactType": "sales",
			"availableLanguage": ["Polish", "English", "Norwegian"]
		},
		"address": [
			{
				"@type": "PostalAddress",
				"addressCountry": "PL",
				"addressLocality": "Poland"
			},
			{
				"@type": "PostalAddress",
				"addressCountry": "NO",
				"addressLocality": "Norway"
			}
		],
		"foundingDate": "2025",
		"numberOfEmployees": {
			"@type": "QuantitativeValue",
			"minValue": 2,
			"maxValue": 10
		},
		"knowsAbout": [
			"Artificial Intelligence",
			"AI Agents",
			"System Integration",
			"Process Automation",
			"Large Language Models",
			"Business Intelligence"
		]
	}
	</script>
	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "WebSite",
		"name": "InterAgents.ai",
		"url": "https://interagents.ai",
		"potentialAction": {
			"@type": "SearchAction",
			"target": "https://interagents.ai/?s={search_term_string}",
			"query-input": "required name=search_term_string"
		}
	}
	</script>
	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "ProfessionalService",
		"name": "InterAgents.ai",
		"url": "https://interagents.ai",
		"description": "<?php echo esc_js( $desc ); ?>",
		"priceRange": "$$",
		"areaServed": ["PL", "NO", "EU"],
		"serviceType": [
			"AI Agent Development",
			"System Integration",
			"Process Automation",
			"Custom AI Solutions",
			"Business Intelligence"
		]
	}
	</script>
	<?php
}
add_action( 'wp_head', 'ia_schema_jsonld', 5 );

/**
 * Open Graph & Twitter Card meta tags
 */
function ia_og_meta() {
	if ( ! is_front_page() ) return;
	$lang = ia_get_lang();
	$title = 'InterAgents.ai — ' . ( $lang === 'pl'
		? 'Agenci AI • Integracja systemów • Automatyzacja'
		: 'AI Agents • System Integration • Automation' );
	$desc = $lang === 'pl'
		? 'Tworzymy agentów AI i aplikacje, które automatyzują, łączą i ulepszają dane Twojej firmy. Szybciej, taniej, bez ludzkich błędów.'
		: 'We build AI agents and apps that automate, connect, and enhance your business data. Faster, cheaper, without human errors.';
	$url = 'https://interagents.ai/';
	$img = 'https://interagents.ai/wp-content/themes/interagents-theme/assets/img/interagents-og.png';
	?>
	<meta property="og:type" content="website" />
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>" />
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>" />
	<meta property="og:image" content="<?php echo esc_url( $img ); ?>" />
	<meta property="og:locale" content="<?php echo $lang === 'pl' ? 'pl_PL' : 'en_US'; ?>" />
	<meta property="og:site_name" content="InterAgents.ai" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta name="twitter:description" content="<?php echo esc_attr( $desc ); ?>" />
	<meta name="twitter:image" content="<?php echo esc_url( $img ); ?>" />
	<meta name="description" content="<?php echo esc_attr( $desc ); ?>" />
	<link rel="canonical" href="<?php echo esc_url( $url ); ?>" />
	<?php
}
add_action( 'wp_head', 'ia_og_meta', 4 );

/**
 * Security headers
 */
function ia_security_headers() {
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'ia_security_headers' );

/**
 * Remove WordPress version from head for security
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * Disable XML-RPC for security
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Consent Mode v2 defaults — MUST run before any gtag config (before Site Kit).
 * Uses dataLayer.push so it works even before gtag.js loads.
 */
function ia_consent_defaults() {
	if ( is_admin() ) return;
	?>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	// Consent defaults: denied for EEA, granted elsewhere
	gtag('consent', 'default', {
		'analytics_storage': 'denied',
		'ad_storage': 'denied',
		'ad_user_data': 'denied',
		'ad_personalization': 'denied',
		'wait_for_update': 500,
		'region': ['BE','BG','CZ','DK','DE','EE','IE','EL','ES','FR','HR','IT','CY','LV','LT','LU','HU','MT','NL','AT','PL','PT','RO','SI','SK','FI','SE','IS','LI','NO','CH','GB']
	});
	gtag('consent', 'default', {
		'analytics_storage': 'granted',
		'ad_storage': 'granted',
		'ad_user_data': 'granted',
		'ad_personalization': 'granted'
	});
	</script>
	<?php
}
add_action( 'wp_head', 'ia_consent_defaults', 1 );

/**
 * Fix GA4: Site Kit only configures GT-5NTGF3JS but never adds the GA4 measurement ID.
 * Runs AFTER Site Kit loads gtag.js (priority 99).
 */
function ia_ga4_config() {
	if ( is_admin() ) return;
	$lang = ia_get_lang();
	?>
	<script>
	// Configure GA4 measurement ID (missing from Site Kit's GT tag)
	gtag('config', 'G-96DLWCDZJE', {
		'custom_map': {
			'dimension1': 'language',
			'dimension2': 'user_type',
			'dimension3': 'form_step'
		},
		'language': '<?php echo esc_js( $lang ); ?>',
		'content_group': 'homepage'
	});
	// CookieAdmin integration: read cookieadmin_consent cookie
	(function() {
		var m = document.cookie.match(/cookieadmin_consent=([^;]+)/);
		if (m) {
			try {
				var c = JSON.parse(decodeURIComponent(m[1]));
				if (c.accept === 'true' || c.accept === true) {
					// User clicked "Accept All"
					gtag('consent', 'update', {
						'analytics_storage': 'granted',
						'ad_storage': 'granted',
						'ad_user_data': 'granted',
						'ad_personalization': 'granted'
					});
				} else {
					// Check individual categories
					if (c.analytics === 'true' || c.analytics === true) {
						gtag('consent', 'update', { 'analytics_storage': 'granted' });
					}
					if (c.marketing === 'true' || c.marketing === true) {
						gtag('consent', 'update', { 'ad_storage': 'granted', 'ad_user_data': 'granted', 'ad_personalization': 'granted' });
					}
				}
			} catch(e) {}
		}
		// Also watch for clicks on Accept buttons (for same-page consent)
		document.addEventListener('click', function(e) {
			if (e.target && e.target.classList && e.target.classList.contains('cookieadmin_accept_btn')) {
				setTimeout(function() {
					gtag('consent', 'update', {
						'analytics_storage': 'granted',
						'ad_storage': 'granted',
						'ad_user_data': 'granted',
						'ad_personalization': 'granted'
					});
				}, 100);
			}
		});
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'ia_ga4_config', 99 );

/**
 * Load customizer
 */
require_once get_template_directory() . '/inc/customizer.php';
