<?php
/**
 * Usage: wp eval-file tools/seed.php (after activating the theme).
 */

if ( PHP_SAPI !== 'cli' || ! defined( 'ABSPATH' ) || ! function_exists( 'Aberdeen\programme_types' ) ) {
	exit( "Run from WP-CLI with the Aberdeen theme active.\n" );
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$programmes = array(
	array( 'bachelor-computer-applications', 'Bachelor of Computer Applications (BCA)', 'undergraduate', '3 Years', 'campus', 'Study programming, software development and data structures through practical projects.' ),
	array( 'bachelor-engineering', 'Bachelor of Engineering (B.E.)', 'undergraduate', '4 Years', 'engineering', 'Develop technical and practical engineering skills to solve real-world problems.' ),
	array( 'bsc-data-science', 'BSc Data Science', 'undergraduate', '3 Years', 'computing', 'Use statistics, programming and visualisation to answer questions with data.' ),
	array( 'ba-business-management', 'BA Business Management', 'undergraduate', '3 Years', 'business', 'Explore how organisations make decisions about people, finances and operations.' ),
	array( 'bsc-environmental-science', 'BSc Environmental Science', 'undergraduate', '3 Years', 'mumbai', 'Study environmental systems and investigate how communities can respond to change.' ),
	array( 'master-computer-applications', 'Master of Computer Applications (MCA)', 'postgraduate', '2 Years', 'campus', 'Develop advanced expertise in software development, programming and emerging technologies.' ),
	array( 'master-engineering', 'Master of Engineering (M.E.)', 'postgraduate', '2 Years', 'engineering', 'Deepen your technical expertise through specialist engineering projects and research.' ),
	array( 'msc-artificial-intelligence', 'MSc Artificial Intelligence', 'postgraduate', '1 Year', 'computing', 'Study machine learning, intelligent systems and the responsible application of AI.' ),
	array( 'msc-international-business', 'MSc International Business', 'postgraduate', '1 Year', 'business', 'Examine how organisations operate across markets and cultures.' ),
	array( 'msc-sustainable-development', 'MSc Sustainable Development', 'postgraduate', '1 Year', 'careers', 'Explore policy and practice for addressing social and environmental challenges.' ),
);
$image_alts = array(
	'campus' => 'Trees and stone columns on campus',
	'engineering' => 'Two students working at a computer',
	'computing' => 'Laptop with an artificial intelligence interface',
	'business' => 'Presenter speaking to a group of colleagues',
	'mumbai' => 'Mumbai skyline',
	'careers' => 'Students talking outside a university building',
);

foreach ( $programmes as $row ) {
	list( $slug, $title, $type, $duration, $image, $description ) = $row;
	if ( get_page_by_path( $slug, OBJECT, 'program' ) ) {
		continue;
	}
	$id = wp_insert_post( array(
		'post_type' => 'program', 'post_status' => 'publish', 'post_name' => $slug,
		'post_title' => $title, 'post_content' => '<p>' . esc_html( $description ) . '</p>',
	), true );
	if ( is_wp_error( $id ) ) {
		throw new RuntimeException( $id->get_error_message() );
	}
	$source = get_template_directory() . '/assets/images/' . $image . '.jpg';
	$temp = wp_tempnam( $source );
	if ( ! $temp || ! copy( $source, $temp ) ) {
		throw new RuntimeException( 'Could not prepare sample image.' );
	}
	$attachment = media_handle_sideload( array( 'name' => $slug . '.jpg', 'tmp_name' => $temp ), $id, $image_alts[ $image ] );
	if ( is_wp_error( $attachment ) ) {
		wp_delete_file( $temp );
		wp_delete_post( $id, true );
		throw new RuntimeException( $attachment->get_error_message() );
	}
	update_post_meta( $attachment, '_wp_attachment_image_alt', $image_alts[ $image ] );
	set_post_thumbnail( $id, $attachment );
	wp_set_object_terms( $id, $type, 'program-type' );
	update_post_meta( $id, '_academic_year', '2025–26' );
	update_post_meta( $id, '_duration', $duration );
	update_post_meta( $id, '_content_sections', array(
		array( 'title' => 'What you will study', 'description' => $description, 'image' => 0 ),
		array( 'title' => 'Career opportunities', 'description' => 'Build a portfolio of practical work and use career guidance to plan your next steps.', 'image' => $attachment ),
	) );
}
