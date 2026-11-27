<?php
/**
 * Public click intake and registration form.
 */

namespace Aberdeen;

add_action( 'rest_api_init', function () {
	foreach ( array( 'track' => 'receive_click', 'lead' => 'receive_lead' ) as $route => $callback ) {
		register_rest_route( 'programmes/v1', '/' . $route, array(
			'methods' => 'POST', 'permission_callback' => '__return_true',
			'callback' => __NAMESPACE__ . '\\' . $callback,
		) );
	}
} );

function request_data( $request, $limit ) {
	if ( strlen( $request->get_body() ) > $limit ) {
		return new \WP_Error( 'body_too_large', 'Request is too large.', array( 'status' => 413 ) );
	}
	$data = json_decode( $request->get_body(), true );
	return is_array( $data ) ? $data : new \WP_Error( 'invalid_body', 'Expected a JSON object.', array( 'status' => 400 ) );
}

function receive_click( $request ) {
	$data = request_data( $request, 2048 );
	if ( is_wp_error( $data ) ) {
		return $data;
	}
	$type = $data['event'] ?? null;
	$label = is_string( $type ) && in_array( $type, array( 'link', 'button' ), true ) && is_array( $data[ $type ] ?? null ) ? ( $data[ $type ]['label'] ?? null ) : null;
	$content = $data['content_data'] ?? null;
	$section = is_array( $content ) ? ( $content['sectionName'] ?? null ) : null;
	$component = is_array( $content ) ? ( $content['uiComponent'] ?? null ) : null;
	$page = $data['page_url'] ?? null;
	if ( 'click' !== ( $data['type'] ?? null ) || ! is_string( $label ) || ! preg_match( '/^[a-z0-9][a-z0-9-]{0,79}$/D', $label ) ||
		! is_string( $section ) || ! preg_match( '/^[a-z][a-z0-9-]{0,59}$/D', $section ) ||
		! is_string( $component ) || ! preg_match( '/^component[A-Z][a-zA-Z]{0,39}$/D', $component ) ||
		! is_string( $page ) || ! preg_match( '~^/(?:[a-zA-Z0-9_%\-]+/)*[a-zA-Z0-9_%\-]*$~D', $page ) || strlen( $page ) > 240 ) {
		return new \WP_Error( 'invalid_event', 'Invalid click event.', array( 'status' => 400 ) );
	}

	$now = gmdate( DATE_ATOM );
	$entry = array(
		'timestamp' => $now, 'page_url' => $page,
		'payload' => array(
			'type' => 'click', 'event' => $type, $type => array( 'label' => sanitize_key( $label ) ),
			'content_data' => array( 'sectionName' => sanitize_key( $section ), 'uiComponent' => sanitize_text_field( $component ) ),
		),
	);
	$uploads = wp_get_upload_dir();
	$path = apply_filters( 'aberdeen_tracking_log_path', trailingslashit( $uploads['basedir'] ) . 'logs/data-track.log' );
	if ( ! append_event( $path, $entry ) ) {
		return new \WP_Error( 'tracking_unavailable', 'Tracking is temporarily unavailable.', array( 'status' => 503 ) );
	}
	return new \WP_REST_Response( null, 204 );
}

function append_event( $path, $entry ) {
	$directory = dirname( $path );
	if ( ! wp_mkdir_p( $directory ) ) {
		return false;
	}
	$protection = array(
		'.htaccess' => "Require all denied\n",
		'index.php' => "<?php http_response_code(403); exit;\n",
		'web.config' => '<configuration><system.webServer><security><authorization><remove users="*" roles="" verbs="" /><add accessType="Deny" users="*" /></authorization></security></system.webServer></configuration>',
	);
	foreach ( $protection as $file => $contents ) {
		$target = trailingslashit( $directory ) . $file;
		if ( ! file_exists( $target ) && false === @file_put_contents( $target, $contents, LOCK_EX ) ) {
			return false;
		}
	}
	$lock = @fopen( $path . '.lock', 'c' );
	if ( ! $lock ) {
		return false;
	}
	try {
		if ( ! flock( $lock, LOCK_EX ) ) {
			return false;
		}
		clearstatcache( true, $path );
		$modified = is_file( $path ) ? filemtime( $path ) : false;
		$today = substr( $entry['timestamp'], 0, 10 );
		if ( $modified && gmdate( 'Y-m-d', $modified ) !== $today ) {
			$dated = $directory . '/tracking-' . gmdate( 'Y-m-d', $modified ) . '.log';
			if ( file_exists( $dated ) || ! @rename( $path, $dated ) ) {
				return false;
			}
		}
		$line = wp_json_encode( $entry, JSON_UNESCAPED_SLASHES ) . "\n";
		return strlen( $line ) === @file_put_contents( $path, $line, FILE_APPEND | LOCK_EX );
	} finally {
		fclose( $lock );
	}
}

function lead_choices() {
	return array(
		'role' => array( 'prospective-student' => 'Prospective student', 'parent' => 'Parent or guardian', 'other' => 'Other' ),
		'level' => programme_types(),
		'country' => array( 'india' => 'India', 'uk' => 'United Kingdom', 'other' => 'Other' ),
		'city' => array( 'mumbai' => 'Mumbai', 'delhi' => 'Delhi', 'bengaluru' => 'Bengaluru', 'pune' => 'Pune', 'other' => 'Other' ),
	);
}

add_action( 'init', function () {
	register_post_type( 'programme_lead', array(
		'labels' => array( 'name' => 'Programme Enquiries', 'singular_name' => 'Programme Enquiry' ),
		'public' => false, 'show_ui' => true, 'show_in_rest' => false,
		'supports' => array( 'title', 'editor' ), 'capability_type' => 'post',
		'capabilities' => array_fill_keys(
			array( 'edit_post', 'read_post', 'delete_post', 'edit_posts', 'edit_others_posts', 'delete_posts', 'publish_posts', 'read_private_posts' ),
			'manage_options'
		),
	) );
} );

function receive_lead( $request ) {
	$data = request_data( $request, 4096 );
	if ( is_wp_error( $data ) ) {
		return $data;
	}
	if ( ! is_string( $data['nonce'] ?? null ) || ! wp_verify_nonce( $data['nonce'], 'aberdeen_lead' ) ||
		! empty( $data['website'] ) ) {
		return new \WP_Error( 'invalid_form', 'Refresh the page and try again.', array( 'status' => 403 ) );
	}
	$lead = array();
	foreach ( lead_choices() as $key => $options ) {
		if ( ! is_string( $data[ $key ] ?? null ) || ! isset( $options[ $data[ $key ] ] ) ) {
			return new \WP_Error( 'invalid_choice', 'Check the selected options and try again.', array( 'status' => 400 ) );
		}
		$lead[ $key ] = $options[ $data[ $key ] ];
	}
	$program = filter_var( $data['programme'] ?? null, FILTER_VALIDATE_INT );
	if ( ! $program || 'program' !== get_post_type( $program ) || 'publish' !== get_post_status( $program ) ||
		! has_term( $data['level'], 'program-type', $program ) ) {
		return new \WP_Error( 'invalid_programme', 'Choose a programme for the selected level.', array( 'status' => 400 ) );
	}
	foreach ( array( 'first_name', 'last_name', 'phone', 'email' ) as $key ) {
		$lead[ $key ] = is_string( $data[ $key ] ?? null ) ? sanitize_text_field( $data[ $key ] ) : '';
		if ( '' === $lead[ $key ] || strlen( $lead[ $key ] ) > 160 ) {
			return new \WP_Error( 'missing_fields', 'Complete all required fields.', array( 'status' => 400 ) );
		}
	}
	if ( ! is_email( $lead['email'] ) || ! preg_match( '/^\+?[0-9 ()-]{7,22}$/D', $lead['phone'] ) ) {
		return new \WP_Error( 'invalid_contact', 'Enter a valid email and phone number.', array( 'status' => 400 ) );
	}
	$lead['programme'] = get_the_title( $program );
	$lines = array();
	foreach ( $lead as $key => $value ) {
		$lines[] = ucwords( str_replace( '_', ' ', $key ) ) . ': ' . $value;
	}
	$id = wp_insert_post( wp_slash( array(
		'post_type' => 'programme_lead', 'post_status' => 'private',
		'post_title' => 'Programme enquiry', 'post_content' => implode( "\n", $lines ),
	) ), true );
	return is_wp_error( $id ) || ! $id
		? new \WP_Error( 'lead_unavailable', 'Could not save your enquiry. Try again.', array( 'status' => 503 ) )
		: new \WP_REST_Response( null, 201 );
}
