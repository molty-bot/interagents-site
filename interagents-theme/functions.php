<?php
/**
 * InterAgents.ai Theme Functions
 *
 * @package InterAgents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'INTERAGENTS_VERSION', '1.0.0' );

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
 * Load customizer
 */
require_once get_template_directory() . '/inc/customizer.php';
