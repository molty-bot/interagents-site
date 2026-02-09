<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Przejdź do treści', 'interagents' ); ?></a>

<header class="site-header" role="banner">
	<div class="container header-inner">
		<?php if ( has_custom_logo() ) : ?>
			<div class="site-logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title" rel="home">
				inter<span class="brand-accent">agents</span>.ai
			</a>
		<?php endif; ?>

		<nav class="site-nav" role="navigation" aria-label="<?php esc_attr_e( 'Menu główne', 'interagents' ); ?>">
			<button class="nav-toggle" aria-expanded="false" aria-controls="primary-menu">
				<span class="nav-toggle__label"><?php esc_html_e( 'Menu', 'interagents' ); ?></span>
				<span class="nav-toggle__icon" aria-hidden="true"></span>
			</button>
			<ul id="primary-menu" class="menu">
				<li><a href="#uslugi"><?php esc_html_e( 'Usługi', 'interagents' ); ?></a></li>
				<li><a href="#jak-dzialamy"><?php esc_html_e( 'Jak działamy', 'interagents' ); ?></a></li>
				<li><a href="#dlaczego-my"><?php esc_html_e( 'Dlaczego my', 'interagents' ); ?></a></li>
				<li><a href="#kontakt" class="btn btn--primary"><?php esc_html_e( 'Kontakt', 'interagents' ); ?></a></li>
			</ul>
		</nav>
	</div>
</header>

<main id="main" role="main">
