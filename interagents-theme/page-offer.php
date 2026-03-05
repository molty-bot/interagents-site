<?php
/**
 * Template Name: Offer Builder
 * Template Post Type: page
 *
 * Redirects to homepage #offer section (offer is now embedded in front-page.php)
 *
 * @package InterAgents
 */

// Preserve any query params (lang, product, hosting, etc.)
$params = $_GET;
unset( $params['page_id'] ); // Remove WP internals if present

$redirect_url = home_url( '/' );
if ( ! empty( $params ) ) {
	$redirect_url .= '?' . http_build_query( $params ) . '#offer';
} else {
	$redirect_url .= '#offer';
}

wp_redirect( $redirect_url, 301 );
exit;
