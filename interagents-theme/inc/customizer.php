<?php
/**
 * InterAgents Customizer
 *
 * @package InterAgents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function interagents_customize_register( $wp_customize ) {

	// Hero Section
	$wp_customize->add_section( 'interagents_hero', array(
		'title'    => __( 'Sekcja Hero', 'interagents' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'hero_title', array(
		'default'           => 'Tworzymy inteligentne rozwiązania AI',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'hero_title', array(
		'label'   => __( 'Tytuł Hero', 'interagents' ),
		'section' => 'interagents_hero',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hero_subtitle', array(
		'default'           => 'Projektujemy agentów AI i budujemy platformy łączące Twoje systemy w jedną, spójną całość.',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'hero_subtitle', array(
		'label'   => __( 'Podtytuł Hero', 'interagents' ),
		'section' => 'interagents_hero',
		'type'    => 'textarea',
	) );

	$wp_customize->add_setting( 'hero_cta_text', array(
		'default'           => 'Porozmawiajmy',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'hero_cta_text', array(
		'label'   => __( 'Tekst CTA', 'interagents' ),
		'section' => 'interagents_hero',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hero_cta_url', array(
		'default'           => '#kontakt',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'hero_cta_url', array(
		'label'   => __( 'Link CTA', 'interagents' ),
		'section' => 'interagents_hero',
		'type'    => 'url',
	) );

	// Contact Section
	$wp_customize->add_section( 'interagents_contact', array(
		'title'    => __( 'Kontakt', 'interagents' ),
		'priority' => 40,
	) );

	$wp_customize->add_setting( 'contact_email', array(
		'default'           => 'hello@interagents.ai',
		'sanitize_callback' => 'sanitize_email',
	) );
	$wp_customize->add_control( 'contact_email', array(
		'label'   => __( 'Email kontaktowy', 'interagents' ),
		'section' => 'interagents_contact',
		'type'    => 'email',
	) );
}
add_action( 'customize_register', 'interagents_customize_register' );
