<?php
$ia_home_url    = ia_localized_url( '/' );
$ia_booking_url = ia_localized_url( '/', 'book' );
?>
<!DOCTYPE html>
<html lang="<?php echo esc_attr( ia_get_lang() ); ?>" dir="ltr">
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
		<a href="<?php echo esc_url( $ia_home_url ); ?>" class="brand-logo-link" rel="home">
			<img class="brand-logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/interagents-logo-transparent.png' ); ?>" width="1991" height="349" alt="interagents.ai">
		</a>

		<nav class="site-nav" role="navigation" aria-label="<?php echo esc_attr( ia_t( 'Menu główne', 'Main menu' ) ); ?>">
			<button type="button" class="lang-toggle" id="lang-toggle" aria-label="<?php echo esc_attr( ia_t( 'Przełącz na angielski', 'Switch to Polish' ) ); ?>" title="<?php echo esc_attr( ia_t( 'English', 'Polski' ) ); ?>">
				<?php echo esc_html( ia_get_lang() === 'pl' ? 'EN' : 'PL' ); ?>
			</button>
			<button type="button" class="nav-toggle" aria-expanded="false" aria-controls="primary-menu" aria-label="<?php echo esc_attr( ia_t( 'Menu', 'Menu' ) ); ?>">
				<span class="nav-toggle__label"><?php echo esc_html( ia_t( 'Menu', 'Menu' ) ); ?></span>
			</button>
			<ul id="primary-menu" class="menu">
				<li><a href="<?php echo esc_url( ia_localized_url( '/', 'offer' ) ); ?>"><?php echo esc_html( ia_t( 'Rozwiązania', 'Solutions' ) ); ?></a></li>
				<li><a href="<?php echo esc_url( ia_localized_url( '/', 'jak-dzialamy' ) ); ?>"><?php echo esc_html( ia_t( 'Jak działamy', 'How we work' ) ); ?></a></li>
				<li><a href="<?php echo esc_url( ia_localized_url( '/', 'dlaczego-my' ) ); ?>"><?php echo esc_html( ia_t( 'Efekty', 'Outcomes' ) ); ?></a></li>
				<li><a href="<?php echo esc_url( ia_localized_url( '/offer/' ) ); ?>"><?php echo esc_html( ia_t( 'Pełna oferta', 'Full offer' ) ); ?></a></li>
				<li><a href="<?php echo esc_url( $ia_booking_url ); ?>" class="btn btn--primary" data-booking-cta="header"><?php echo esc_html( ia_t( 'Umów rozmowę', 'Book a meeting' ) ); ?></a></li>
			</ul>
		</nav>
	</div>
</header>

<main id="main" role="main">
