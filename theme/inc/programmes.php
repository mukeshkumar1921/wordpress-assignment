<?php
/**
 * Programme content and archive routes.
 */

namespace Aberdeen;

function programme_types() {
	return array( 'undergraduate' => 'Undergraduate', 'postgraduate' => 'Postgraduate' );
}

function register_programmes() {
	register_post_type( 'program', array(
		'labels' => array( 'name' => 'Programs', 'singular_name' => 'Program', 'add_new_item' => 'Add Program', 'edit_item' => 'Edit Program' ),
		'public' => true,
		'show_in_rest' => true,
		'has_archive' => 'programmes',
		'rewrite' => array( 'slug' => 'programmes', 'with_front' => false ),
		'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' ),
		'menu_icon' => 'dashicons-welcome-learn-more',
	) );
	register_taxonomy( 'program-type', 'program', array(
		'labels' => array( 'name' => 'Program Types', 'singular_name' => 'Program Type' ),
		'hierarchical' => true,
		'show_in_rest' => true,
		'show_admin_column' => true,
		'rewrite' => false,
	) );
	foreach ( programme_types() as $slug => $label ) {
		add_rewrite_rule( '^programmes/' . $slug . '/?$', 'index.php?program-type=' . $slug, 'top' );
	}

	$common = array( 'single' => true, 'auth_callback' => __NAMESPACE__ . '\can_edit_meta' );
	foreach ( array( '_academic_year', '_duration' ) as $field ) {
		register_post_meta( 'program', $field, $common + array(
			'type' => 'string', 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_text_field',
		) );
	}
	register_post_meta( 'program', '_content_sections', $common + array(
		'type' => 'array',
		'default' => array(),
		'sanitize_callback' => __NAMESPACE__ . '\sanitize_sections',
		'show_in_rest' => array( 'schema' => array(
			'type' => 'array',
			'items' => array(
				'type' => 'object',
				'additionalProperties' => false,
				'properties' => array(
					'title' => array( 'type' => 'string' ),
					'description' => array( 'type' => 'string' ),
					'image' => array( 'type' => 'integer', 'minimum' => 0 ),
				),
			),
		) ),
	) );
}
add_action( 'init', __NAMESPACE__ . '\register_programmes' );

function can_edit_meta( $allowed, $key, $post_id, $user_id ) {
	return user_can( $user_id, 'edit_post', $post_id );
}

function sanitize_sections( $value ) {
	$sections = array();
	foreach ( is_array( $value ) ? array_slice( $value, 0, 40 ) : array() as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$image = absint( $row['image'] ?? 0 );
		$sections[] = array(
			'title' => sanitize_text_field( $row['title'] ?? '' ),
			'description' => sanitize_textarea_field( $row['description'] ?? '' ),
			'image' => $image && wp_attachment_is_image( $image ) ? $image : 0,
		);
	}
	return $sections;
}

add_action( 'after_switch_theme', function () {
	register_programmes();
	foreach ( programme_types() as $slug => $name ) {
		if ( ! term_exists( $slug, 'program-type' ) ) {
			wp_insert_term( $name, 'program-type', array( 'slug' => $slug ) );
		}
	}
	flush_rewrite_rules( false );
} );

add_filter( 'wp_unique_post_slug_is_bad_flat_slug', function ( $bad, $slug, $type ) {
	return $bad || ( 'program' === $type && isset( programme_types()[ $slug ] ) );
}, 10, 3 );

add_filter( 'term_link', function ( $url, $term, $taxonomy ) {
	if ( 'program-type' === $taxonomy && isset( programme_types()[ $term->slug ] ) && get_option( 'permalink_structure' ) ) {
		return home_url( user_trailingslashit( 'programmes/' . $term->slug ) );
	}
	return $url;
}, 10, 3 );

function archive_url( $slug = '' ) {
	$term = $slug ? get_term_by( 'slug', $slug, 'program-type' ) : false;
	$link = $term ? get_term_link( $term ) : get_post_type_archive_link( 'program' );
	return is_wp_error( $link ) ? get_post_type_archive_link( 'program' ) : $link;
}

function catalogue() {
	return get_posts( array(
		'post_type' => 'program', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC',
		'update_post_term_cache' => true,
	) );
}

function render_programme_card( $post_id ) {
	get_template_part( 'template-parts/programme-card', null, array( 'post_id' => $post_id ) );
}
