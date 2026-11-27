<?php
/**
 * Theme setup and asset loading.
 */

namespace Aberdeen;

defined( 'ABSPATH' ) || exit;

require __DIR__ . '/inc/programmes.php';
require __DIR__ . '/inc/editor.php';
require __DIR__ . '/inc/events.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'aberdeen', $uri . '/assets/site.css', array(), '1.0.0' );
	wp_enqueue_script( 'aberdeen-site', $uri . '/assets/site.js', array(), '1.0.0', true );
	wp_add_inline_script( 'aberdeen-site', 'window.aberdeen = ' . wp_json_encode(
		array( 'track' => rest_url( 'programmes/v1/track' ), 'lead' => rest_url( 'programmes/v1/lead' ) ),
		JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
	) . ';', 'before' );

	if ( is_post_type_archive( 'program' ) || is_tax( 'program-type' ) ) {
		wp_enqueue_script_module( 'aberdeen-filter', $uri . '/assets/filter.js', array( '@wordpress/interactivity' ), '1.0.0' );
	}
} );

function asset( $name ) {
	return get_template_directory_uri() . '/assets/' . $name;
}

function track_attr( $event, $label, $section, $component ) {
	$data = array(
		'type' => 'click',
		'event' => $event,
		$event => array( 'label' => sanitize_title( $label ) ),
		'content_data' => array( 'sectionName' => $section, 'uiComponent' => $component ),
	);
	return esc_attr( wp_json_encode( $data ) );
}
