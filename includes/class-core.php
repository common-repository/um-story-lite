<?php
/**
 * UM Story Lite Core.
 *
 * @since   1.0.0
 * @package UM_Story_Lite
 */

/**
 * UM Story Lite Core.
 *
 * @since 1.0.0
 */
class UMSL_Core {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   UM_Story_Lite
	 */
	protected $plugin = null;

	public $post_type = null;
	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  UM_Story_Lite $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin    = $plugin;
		$this->post_type = 'um_story';
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'init', array($this, 'register_post_type') );
	}

	/**
	 * Register Post Type
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Stories', 'post type general name', 'um-story-lite' ),
			'singular_name'      => _x( 'Story', 'post type singular name', 'um-story-lite' ),
			'menu_name'          => _x( 'Stories', 'admin menu', 'um-story-lite' ),
			'name_admin_bar'     => _x( 'Story', 'add new on admin bar', 'um-story-lite' ),
			'add_new'            => _x( 'Add New', 'story', 'um-story-lite' ),
			'add_new_item'       => __( 'Add New Story', 'um-story-lite' ),
			'new_item'           => __( 'New Story', 'um-story-lite' ),
			'edit_item'          => __( 'Edit Story', 'um-story-lite' ),
			'view_item'          => __( 'View Story', 'um-story-lite' ),
			'all_items'          => __( 'All Stories', 'um-story-lite' ),
			'search_items'       => __( 'Search stories', 'um-story-lite' ),
			'parent_item_colon'  => __( 'Parent stories:', 'um-story-lite' ),
			'not_found'          => __( 'No stories found.', 'um-story-lite' ),
			'not_found_in_trash' => __( 'No stories found in Trash.', 'um-story-lite' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Post written by profile users.', 'um-story-lite' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'um_story' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'comments' )
		);

		register_post_type( $this->post_type, apply_filters( 'um_story_register_post_type', $args ) );
	}
}
