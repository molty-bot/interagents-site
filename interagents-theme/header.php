<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
	<meta name="theme-color" content="#0a0a1a" media="(prefers-color-scheme: dark)">
	<meta name="theme-color" content="#0a0a1a" media="(prefers-color-scheme: light)">
	<meta name="theme-color" content="#0a0a1a">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-lang="<?php echo esc_attr( ia_get_lang() ); ?>">
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php echo esc_html( ia_t( 'Przejdź do treści', 'Skip to content' ) ); ?></a>

<header class="site-header" role="banner">
	<div class="container header-inner">
		<?php if ( has_custom_logo() ) : ?>
			<div class="site-logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title" rel="home">
				inter<span class="brand-accent">agents</span>.ai
			</a>
		<?php endif; ?>

		<nav class="site-nav" role="navigation" aria-label="<?php echo esc_attr( ia_t( 'Menu główne', 'Main menu' ) ); ?>">
			<button class="lang-toggle" id="lang-toggle" aria-label="<?php echo esc_attr( ia_t( 'Switch to English', 'Przełącz na polski' ) ); ?>" title="<?php echo esc_attr( ia_t( 'English', 'Polski' ) ); ?>">
				<?php echo ia_get_lang() === 'pl' ? '🇬🇧' : '🇵🇱'; ?>
			</button>
			<button class="nav-toggle" aria-expanded="false" aria-controls="primary-menu">
				<span class="nav-toggle__label"><?php esc_html_e( 'Menu', 'interagents' ); ?></span>
				<span class="nav-toggle__icon" aria-hidden="true"></span>
			</button>
			<ul id="primary-menu" class="menu">
				<li><a href="#uslugi"><?php echo esc_html( ia_t( 'Usługi', 'Services' ) ); ?></a></li>
				<li><a href="#jak-dzialamy"><?php echo esc_html( ia_t( 'Jak działamy', 'How we work' ) ); ?></a></li>
				<li><a href="#dlaczego-my"><?php echo esc_html( ia_t( 'Dlaczego my', 'Why us' ) ); ?></a></li>
				<li><a href="#kontakt" class="btn btn--primary"><?php echo esc_html( ia_t( 'Kontakt', 'Contact' ) ); ?></a></li>
			</ul>
		</nav>
	</div>
</header>

<main id="main" role="main">
