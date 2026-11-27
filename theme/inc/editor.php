<?php
/**
 * Programme editor panel.
 */

namespace Aberdeen;

add_filter( 'use_block_editor_for_post_type', function ( $use_block_editor, $post_type ) {
	return 'program' === $post_type ? false : $use_block_editor;
}, 10, 2 );

add_action( 'add_meta_boxes_program', function () {
	add_meta_box( 'aberdeen-programme', 'Programme information', __NAMESPACE__ . '\editor_panel', 'program', 'normal', 'high' );
	remove_meta_box( 'postcustom', 'program', 'normal' );
} );

function editor_panel( $post ) {
	wp_nonce_field( 'aberdeen_edit_programme', 'aberdeen_programme_nonce' );
	$sections = sanitize_sections( get_post_meta( $post->ID, '_content_sections', true ) );
	$data = array(
		'year' => get_post_meta( $post->ID, '_academic_year', true ),
		'duration' => get_post_meta( $post->ID, '_duration', true ),
		'sections' => array_map( function ( $row ) {
			$row['thumbnail'] = wp_get_attachment_image_url( $row['image'] ?? 0, 'thumbnail' ) ?: '';
			return $row;
		}, $sections ),
	);
	echo '<div id="aberdeen-programme-editor" data-initial="' . esc_attr( wp_json_encode( $data ) ) . '"></div>';
}

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	$screen = get_current_screen();
	$manifest = get_template_directory() . '/build/index.asset.php';
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'program' !== ( $screen->post_type ?? '' ) || ! is_readable( $manifest ) ) {
		return;
	}
	$asset = require $manifest;
	wp_enqueue_media();
	wp_enqueue_script( 'aberdeen-editor', get_template_directory_uri() . '/build/index.js', $asset['dependencies'], $asset['version'], true );
	wp_enqueue_style( 'aberdeen-editor', asset( 'editor.css' ), array(), $asset['version'] );
} );

add_action( 'save_post_program', function ( $post_id ) {
	$nonce = $_POST['aberdeen_programme_nonce'] ?? '';
	$raw = $_POST['aberdeen_programme_fields'] ?? '';
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! is_string( $nonce ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $nonce ) ), 'aberdeen_edit_programme' ) ||
		! current_user_can( 'edit_post', $post_id ) || ! is_string( $raw ) || strlen( $raw ) > 160000 ) {
		return;
	}
	$data = json_decode( wp_unslash( $raw ), true );
	if ( ! is_array( $data ) || ! is_string( $data['year'] ?? null ) || ! is_string( $data['duration'] ?? null ) || ! is_array( $data['sections'] ?? null ) ) {
		return;
	}
	update_post_meta( $post_id, '_academic_year', wp_slash( substr( $data['year'], 0, 80 ) ) );
	update_post_meta( $post_id, '_duration', wp_slash( substr( $data['duration'], 0, 80 ) ) );
	update_post_meta( $post_id, '_content_sections', wp_slash( sanitize_sections( $data['sections'] ) ) );
} );
