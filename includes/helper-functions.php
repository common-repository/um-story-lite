<?php
/**
 * Get Story Post Type
 *
 */
function um_story_post_type() {
	return apply_filters( 'um_story_post_type', 'um_story' );
}
/**
 * Get Profile Permalink
 * @param  string $slug
 * @return string $profile_url
 */
function um_story_profile_permalink( $slug = '' ){

	$profile_url = um_user_profile_url();

	return ! empty( $profile_url ) ? strtolower( $profile_url ) : '';

}
/**
 *	Get the Edit Url for documents
 */
function um_get_story_link( $id = 0 ) {
	$url = um_user_profile_url();
	$slug = get_story_slug();
	$url = remove_query_arg( 'profiletab', $url );
	$url = add_query_arg( 'profiletab', $slug, $url );
	$url = add_query_arg( 'subnav', 'entries', $url );
	if ( $id ) {
		$url = add_query_arg( 'subnav', 'view_entry', $url );
		$url = add_query_arg( 'view',  $id, $url );
	}
	return $url;
}
/**
 * [um_get_story_author_link description]
 * @param  integer $user_id [description]
 * @return [type]           [description]
 */
function um_get_story_author_link($user_id = 0) {
	$slug = get_story_slug();
	$url = um_user_profile_url();
	$url = remove_query_arg('profiletab', $url);
	$url = remove_query_arg('subnav', $url);
	$url = add_query_arg( 'profiletab', $slug, $url );
	return $url;
}
/**
 * Get the Edit Url for documents
 * @param integer $id
 * @return string URL to Form page
 */
function um_get_story_edit_link( $id = 0 ) {
	$slug = get_story_slug();
	$url = um_user_profile_url();
	$url = remove_query_arg( 'profiletab', $url );
	$url = add_query_arg( 'profiletab', $slug, $url );
	$url = add_query_arg( 'subnav',  'edit_entry', $url );

	if ( $id ) {
		$url = add_query_arg( 'id',  $id, $url );
	}
	return $url;
}

function um_get_story_delete_link_full( $id = 0 ) {
	$link = '';
	if ( $id ) {
		$link = add_query_arg( 'id', $id, um_user_profile_url() );
		$link = add_query_arg( 'story_action', 'delete', $link );
		$link = wp_nonce_url( $link, 'story_delete_action', 'story_delete_' );
	}

	return $link;
}
/**
 * Delete Link
 */
function um_story_delete_link( $id='', $author='' ) {
	if ( empty( $id ) || empty( $author ) ) {
		return false;
	}
	if ( get_current_user_id() ==  $author ) {
		echo '<a href="' . um_get_story_delete_link_full($id) . '" data-id="' . absint( $id ) . '" class="um-delete-story"><i class="um-faicon-trash"></i> ' . __( 'Delete', 'um-story-lite' ) . '</a></>';
	}
}

/**
 * [um_story_edit_link description]
 * @param  string $id     [description]
 * @param  string $author [description]
 * @return [type]         [description]
 */
function um_story_edit_link($id='', $author='') {
	if (empty($id) || empty($author)) {
		return false;
	}
	if ( get_current_user_id() ==  $author ) {
		echo '<a href="' . um_get_story_edit_link($id) . '"  class="um-edit-story"><i class="um-faicon-pencil"></i> '.__('Edit', 'um-story-lite').'</a></>';
	}
}

/**
 * Get Story Slug
 * @return string
 */
function get_story_slug() {
	$slug = um_story_get_label( 'tab_slug', 'story' );
	return apply_filters( 'um_story_slug', $slug );
}

function um_story_can_moderate() {
	if ( is_admin() ) {
		return true;
	}

	//get profile ID.
	$profile_id = um_get_requested_user();

	// if we are not on profile, let's get logged in user ID for the sake of the admin bar menu.
	if ( ! $profile_id ) {
		$profile_id = get_current_user_id();
	}

	//return false
	return false;
}


/**
 * A custom sanitization function that will take the incoming input, and sanitize
 * the input before handing it back to WordPress to save to the database.
 *
 * @since    1.0.6
 *
 * @param    array    $input        The address input.
 * @return   array    $new_input    The sanitized input.
 */
function um_story_sanitize_array( $input ) {

	// Initialize the new array that will hold the sanitize values
	$new_input = array();

	// Loop through the input and sanitize each of the values
	foreach ( $input as $key => $val ) {
		$new_input[ $key ] = sanitize_text_field( $val );
	}

	return $new_input;

}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed		Option value
 */
function um_story_get_label( $key = '', $default = '' ) {
	$options = get_option( 'um_story' );
	$value = '';
	if ( ! empty( $options[ $key ] ) ) {
		if ( is_array( $options[ $key ] ) ) {
			$value = um_story_sanitize_array( $options[ $key ] );
		} else {
			$value = sanitize_text_field( $options[ $key ] );
		}
	}
	if ( empty( $value ) && ! empty( $default ) ) {
		$value = $default;
	}
	return $value;
}

function um_story_date_block() {
	$post_id      = get_the_ID();
	$db_time      = get_post_meta( $post_id, '_um_story_entry_time', true );
	$time         = date( 'H:ia', strtotime( $db_time ) );
	$day          = date( 'M', strtotime( $db_time ) );
	$date         = date( 'd', strtotime( $db_time ) );
	?>
	<div class="um-story-date-container">
		<div class="um-story-time"><?php echo esc_html( $time ); ?></div>
		<div class="um-story-date"><?php echo esc_html( $date ); ?></div>
		<div class="um-story-day"><?php echo esc_html( $day ); ?></div>
	</div>
	<?php
}

/**
 * Get Faceted Search template directory.
 *
 * @since  1.0.0
 *
 * @author SuitePlugins
 *
 * @return  string File path.
 */
function um_story_get_templates_dir() {
	return um_story_lite()->path . 'templates/story/';
}

/**
 * The ld_activity_get_template_part function.
 *
 * @access public
 * @param mixed $slug Template slug.
 * @param mixed $name Default: null.
 * @param bool  $load Default: true.
 * @return string
 */
function um_story_get_template_part( $slug, $name = null, $load = true ) {
	// Execute code for this part.
	do_action( 'get_template_part_' . $slug, $slug, $name );

	// Setup possible parts.
	$templates = array();
	if ( isset( $name ) ) {
		$templates[] = $slug . '-' . $name . '.php';
	}
	$templates[] = $slug . '.php';

	// Allow template parts to be filtered.
	$templates = apply_filters( 'um_story_get_template_part', $templates, $slug, $name );

	// Return the part that is found.
	return um_story_locate_template( $templates, $load, false );
}

/**
 * The ld_activity_locate_template function.
 *
 * @param mixed $template_names Template names.
 * @param bool  $load           Default: false.
 * @param bool  $require_once   Default: true.
 * @return string
 */
function um_story_locate_template( $template_names, $load = false, $require_once = true ) {
	// No file found yet.
	$located = false;

	// Try to find a template file.
	foreach ( (array) $template_names as $template_name ) {

		// Continue if template is empty.
		if ( empty( $template_name ) ) {
			continue;
		}

		// Trim off any slashes from the template name.
		$template_name = ltrim( $template_name, '/' );
		// Check child theme first.
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'story/' . $template_name ) ) {
			$located = trailingslashit( get_stylesheet_directory() ) . 'story/' . $template_name;
			break;

			// Check parent theme next.
		} elseif ( file_exists( trailingslashit( get_template_directory() ) . 'story/' . $template_name ) ) {
			$located = trailingslashit( get_template_directory() ) . 'story/' . $template_name;
			break;

			// Check theme compatibility last.
		} elseif ( file_exists( trailingslashit( um_story_get_templates_dir() ) . $template_name ) ) {
			$located = trailingslashit( um_story_get_templates_dir() ) . $template_name;
			break;
		}
	}
	if ( ( true == $load ) && ! empty( $located ) ) {
		load_template( $located, $require_once );
	}

	return $located;
}


/**
 * Gets and includes template files.
 *
 * @since 1.0.0
 * @param mixed  $template_name
 * @param array  $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function get_um_story_template( $template_name, $args = array(), $template_path = 'um_docs', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}
	include( locate_um_story_template( $template_name, $template_path, $default_path ) );
}

/**
 * Locates a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @since 1.0.0
 * @param string      $template_name
 * @param string      $template_path (default: 'um_docs')
 * @param string|bool $default_path (default: '') False to not load a default
 * @return string
 */
function locate_um_story_template( $template_name, $template_path = 'um_story', $default_path = '' ) {
	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template && $default_path !== false ) {
		$default_path = $default_path ? $default_path : um_story_lite()->path . 'templates/story';
		
		if ( file_exists( trailingslashit( $default_path ) . $template_name ) ) {
			$template = trailingslashit( $default_path ) . $template_name;
		}
	}
	// Return what we found
	return apply_filters( 'um_story_locate_template', $template, $template_name, $template_path );
}
