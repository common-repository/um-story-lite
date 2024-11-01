<?php
/**
 * UM Story Lite Template.
 *
 * @since   1.0.0
 * @package UM_Story_Lite
 */

/**
 * UM Story Lite Template.
 *
 * @since 1.0.0
 */
class UMSL_Template {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   UM_Story_Lite
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  UM_Story_Lite $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		//Hook for creating Menu Items : um_user_profile_tabs
		add_filter( 'um_profile_tabs', array( $this, 'setup_tabs' ), 12, 1 );
		//Hook: um_profile_content_$menu_key
		if ( empty( $_GET['subnav'] ) ) {
			add_action( 'um_profile_content_' . um_story_lite()->slug, array( $this, 'entries_content_page' ) );
		}
		add_action( 'um_profile_content_' . um_story_lite()->slug . '_entries', array( $this, 'entries_content_page' ) );
		add_action( 'um_profile_content_' . um_story_lite()->slug . '_edit_entry', array( $this, 'setup_subnav_entry' ) );
		add_action( 'um_profile_content_' . um_story_lite()->slug . '_new_entry', array( $this, 'setup_subnav_entry' ) );
		add_action( 'um_profile_content_' . um_story_lite()->slug . '_view_entry', array( $this, 'setup_subnav_view' ) );

		add_action( 'um_story_form_content', array( $this, 'um_story_form_title_field' ), 10, 2 );
		add_action( 'um_story_form_content', array( $this, 'um_story_form_date_field' ), 12, 1 );
		add_action( 'um_story_form_content', array( $this, 'um_story_form_thumb_field' ), 12, 1 );
		add_action( 'um_story_form_content', array( $this, 'um_story_form_content_field' ), 13, 2 );

		add_action( 'template_redirect', array( $this, 'story_actions' ) );
	}

	/**
	 * Post Title.
	 *
	 * @param  integer $id   Post ID
	 * @param  array   $post WP_POST Object
	 *
	 * @since  1.0.0
	 */
	public function um_story_form_title_field( $id = 0, $post = array() ) {
		$value = ! empty( $post ) ? $post->post_title : '';
		?>
		<div class="um-field">
		  <div class="um-field-label">
			<label for="story_title" class="documents_name"><?php echo __( 'Name', 'um-story-lite' ); ?></label>
			<div class="um-story-clear"></div>
		  </div>
		  <input type="text" name="title" id="story_title" class="form-control" required minlength="2" value="<?php echo esc_attr( $value ); ?>" placeholder="">
		</div>
	<?php
	}

	/**
	 * Dates field.
	 *
	 * @param  integer $id   Post ID
	 *
	 * @since  1.0.0
	 */
	public function um_story_form_date_field( $id = 0 ) {
		$value = ! empty( $id ) ? get_post_meta( $id, '', true ) : '';
		if ( $id ) {
			$time = get_post_meta( $id, '_um_story_entry_time', true );
		} else {
			$time = date( 'Y/m/d H:i' );
		}
		$value = date( 'Y/m/d H:i', strtotime( $time ) );
		?>
		<div class="um-field">
		  <div class="um-field-label">
			<label for="time" class="documents_name"><?php echo __( 'Entry Date', 'um-story-lite' ); ?>
			</label>
			<div class="um-story-clear"></div>
		  </div>
		  <input type="text" name="time" id="umDateTime" class="form-control" value="<?php echo esc_attr( $value ); ?>">
		</div>
		<?php
	}

	/**
	 * Dates field.
	 *
	 * @param  integer $id   Post ID
	 *
	 * @since  1.0.0
	 */
	public function um_story_form_thumb_field( $id = 0 ) {
		$file_id          = '';
		$attachment_title = '';
		$file_url         = '';
		if ( $id ) {
			$file_id          = get_post_meta( $id, '_thumbnail_id', true );
			$file_url         = wp_get_attachment_thumb_url( $file_id , 'thumbnail' );
			if ( $file_id ) {
				$attachment_title = basename( $file_url ) . '<br />';
			}
		}
		$value = $file_id;
		?>
		<div class="um-field">
		  <div class="um-field-label">
			<label for="main_image" class="documents_name"><?php echo __( 'Main Image', 'um-story-lite' ); ?></label>
			<div class="um-story-clear"></div>
		  </div>
		  <?php if ( $file_url ) { ?>
		  <img src="<?php echo esc_url( $file_url ); ?>" class="um-story-small-thumb" />
		  <?php } ?>
		  <?php echo $attachment_title; ?>
		  <input type="file" name="main_image" id="main_image" value="">
		</div>
		<?php
	}

	/**
	 * Post Content.
	 *
	 * @param  integer $id   Post ID
	 * @param  array   $post WP_POST Object
	 *
	 * @since  1.0.0
	 */
	public function um_story_form_content_field( $id = 0, $post = array() ) {
		 $value = ! empty( $post ) ? $post->post_content : '';
		 $settings = array(
			'wpautop' => false,
			'media_buttons' => false,
			'quicktags' => false,
		);
		?>
		<div class="um-field">
			<?php wp_editor( $value, 'storycontent', $settings ); ?> 
		<?php
	}

	/**
		 * This function will return an array of tabs. Use this function to rename or add a new tab array
		 * @param  array $tabs
		 * @return array
		 */
		public function setup_tabs( $tabs = array() ) {
			$title 				= um_story_get_label( 'tab_name', __( 'Journal', 'um-story-lite' ) );
			$tab_icon 			= um_story_get_label( 'tab_icon', 'um-faicon-book' );
			$tab_my_entries 	= um_story_get_label( 'tab_my_entries', __( 'My Entries','um-story-lite' ) );
			$tab_entries 		= um_story_get_label( 'tab_entries', __( 'Entries','um-story-lite' ) );
			$tab_new_entries 	= um_story_get_label( 'tab_new_entries', __( 'New Entry','um-story-lite' ) );
			$tab_entries_slug 	= um_story_get_label( 'tab_entries_slug', 'entries' );
			$tab_entries_slug 	= um_story_get_label( 'tab_entries_slug', 'entries' );
			//can this user role use this tab
			$tabs[ um_story_lite()->slug ] = array(
				'name' 				=> $title,
				'icon'				=> $tab_icon,
				'custom'            => true,
				'subnav_default'    => 0,
				);
			if ( $this->is_owner() ) {
				$tabs[ um_story_lite()->slug ]['subnav'] = array(
					$tab_entries_slug 	=> esc_html( $tab_entries ),
					'new_entry' 		=> esc_html( $tab_new_entries ),
				);
			} else {
				$tabs[ um_story_lite()->slug ]['subnav'] = array(
					$tab_entries_slug 	=> esc_html( $tab_entries ),
				);
			}
			$tabs[ um_story_lite()->slug ]['subnav_default'] = 'entries';
			return $tabs;
		}

		/**
		 * Entries tab
		 *
		 * @since 1.0.8
		 * @return void
		 */
		public function entries_content_page() {
			do_action( 'um_story_notice_bar' );
			get_um_story_template( 'list.php' );
		}

		public function get_thumbnail_id( $id = 0 ) {
			return get_post_thumbnail_id( $id );
		}
		public function get_thumbnail_src( $id = 0, $size = 'thumbnail' ){
			$attachment_id = $this->get_thumbnail_id( $id );
			$image_attributes = wp_get_attachment_image_src( $attachment_id, $size ); // returns an array
			if( $image_attributes ) {
				return $image_attributes[0];
			}
			return false;
		}

		public function setup_subnav_view() {
			$user_ID = um_profile_id();
			$id      = 0;
			$post    = array();
			$image   = '';
			
			if ( isset( $_GET['view'] ) ) {
				$id    = absint( $_GET['view'] );
				$post  = get_post( $id );
				$image = $this->get_thumbnail_src( $id, 'full' );
			}

			$template_args = array(
				'id'    => $id,
				'post'  => $post,
				'image' => $image,
			);

			add_filter( 'the_content', 'wpautop' );
			get_um_story_template( 'story-single.php', $template_args );
			remove_filter( 'the_content', 'wpautop' );
		}
		/**
		 * Content screen for subnav entry
		 *
		 * @since 1.0.8
		 * @return void
		 */
		public function setup_subnav_entry() {
			global $entry_id;
			$entry_id = 0;
			$post             = array();
			if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
				$entry_id = absint( $_GET['id'] );
			}

			if ( $entry_id ) {
				$post = get_post( $entry_id );
				if ( um_story_post_type() != $post->post_type ) {
					echo __( 'Access denied to this entry', 'um-story-lite' );
					return false;
				}
				if ( um_profile_id() != $post->post_author ) {
					echo __( 'Access denied to this entry', 'um-story-lite' );
					return false;
				}
			}

			$user_id          = um_profile_id();
			$id               = $entry_id;

			$template_args = array(
				'id'               => $id,
				'post'             => $post,
				'user_id'          => $user_id,
			);
			get_um_story_template( 'form.php', $template_args );
		}

		/**
		 * Check if user has access
		 *
		 * @return boolean [description]
		 */
		public function is_owner() {
			//logged in ID
			$my_id = get_current_user_id();
			//get profile ID
			$profile_id = um_get_requested_user();

			//if not logged in then return false
			if ( ! $my_id ) {
				return false;
			}
			if ( $profile_id == $my_id ) :
				return true;
			else :
				return false;
			endif;
		}

		/**
		 * Upload files to Media folder
		 * @param  string  $files_key         [description]
		 * @param  boolean $insert_attachment [description]
		 * @param  boolean $overwrite         [description]
		 * @return [type]                     [description]
		 */
		public function upload_media( $files_key='', $insert_attachment = true, $overwrite = true ){
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			// These files need to be included as dependencies when on the front end.
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			if ( empty( $files_key ) ) {
				return false;
			}
			if ( empty( $_FILES[ $files_key ] ) ) {
				return false;
			}
			$result = array();
			$upload_overrides = array( 'test_form' => false );
			$attachment_id = media_handle_upload( $files_key, $this->id);
			if ( ! is_wp_error( $attachment_id ) ) {
				if( $insert_attachment ){
					if( $overwrite ) {
						//$this->delete_post_media( $this->id );
					}
					//$movefile['extension'] = $file_type['ext'];
					//$movefile['attachment_id'] = $attach_id;
				}
				$result[] = $attachment_id;
			}else{
				$error_string = $attachment_id->get_error_message();
			}
			return $result;
		}
		public function story_actions() {
			// Check the nonce and if set then save/update story
			if ( 
				isset( $_POST['um_story_nonce'] ) 
				&& wp_verify_nonce( $_POST['um_story_nonce'], 'um_story_save' ) 
			) {
				
				$id       = ! empty( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';;
				$defaults = array (
					'post_type' 	=> 'um_story',
					'post_author'   => get_current_user_id(),
					'post_status' 	=> "publish",
					'post_title'    => '',
					'post_content'  => '',
				);

				$args = wp_parse_args( $args, $defaults );

				$args['post_title']   = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
				$args['post_content'] = ! empty( $_POST['storycontent'] ) ? wp_kses_post( $_POST['storycontent'] ) : '';

				if ( empty( $id ) ){
					$id 	= wp_insert_post( $args );
				} else {
					$args['ID'] = $id;
					$id 	= wp_update_post( $args );
				}

				if ( ! is_wp_error( $id ) ) {
					$file = $this->upload_media( 'main_image' );
					if ( ! empty( $file ) ) {
						update_post_meta( $id, '_thumbnail_id', $file[0] );
					}
					if ( ! empty( $_POST['time'] ) ) {
						$time = sanitize_text_field( $_POST['time'] );
						$time = date( 'Y-m-d H:i:s', strtotime( $time ) );
					} else {
						$time = date( 'Y-m-d H:i:s' );
					}
					update_post_meta( $id, '_um_story_entry_time', $time );
				}

				wp_redirect( um_get_story_link() );
				exit;
			}

			 if (
			 	isset( $_GET['story_delete_'] ) && 
			 	wp_verify_nonce( $_GET['story_delete_'], 'story_delete_action' ) 
			 	){
			 	if ( isset( $_GET['id'] ) ) {
			 		$id = absint( $_GET['id'] );
			 		$this->delete_story( $id );
			 	}
			 	
			 	wp_redirect( um_get_story_link() );
			 	exit;
			 }
		}

		public function delete_story( $id = 0 ) {
			// Check if user is the owner of the post.
			$post = get_post( $id );
			$post_author_id = $post->post_author;
			$post_type      = $post->post_type;
			if ( 'um_story' == $post_type && $post_author_id == get_current_user_id() ) {
				wp_delete_post( $id, false );
			}
		}
}
