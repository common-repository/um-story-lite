<?php
/**
 * Plugin Name: SuitePlugins - UM Story Lite
 * Plugin URI:  https://suiteplugins.com/um-story
 * Description: Allows users to submit articles via the front end on Ultimate Member profile tabs.
 * Version:     1.0.2.2
 * Author:      SuitePlugins
 * Author URI:  https://suiteplugins.com/
 * Donate link: https://suiteplugins.com
 * License:     GPLv2
 * Text Domain: um-story-lite
 * Domain Path: /languages
 *
 * @link    https://suiteplugins.com
 *
 * @package UM_Story_Lite
 * @version 1.0.0
 *
 */

/**
 * Copyright (c) 2018 SuitePlugins (email : info@suiteplugins.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 * Autoloads files with classes when needed.
 *
 * @since  1.0.0
 * @param  string $class_name Name of the class being requested.
 */
function um_story_lite_autoload_classes( $class_name ) {

	// If our class doesn't have our prefix, don't load it.
	if ( 0 !== strpos( $class_name, 'UMSL_' ) ) {
		return;
	}

	// Set up our filename.
	$filename = strtolower( str_replace( '_', '-', substr( $class_name, strlen( 'UMSL_' ) ) ) );

	// Include our file.
	UM_Story_Lite::include_file( 'includes/class-' . $filename );
}
spl_autoload_register( 'um_story_lite_autoload_classes' );

/**
 * Main initiation class.
 *
 * @since  1.0.0
 */
final class UM_Story_Lite {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	const VERSION = '1.0.2.1';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    UM_Story_Lite
	 * @since  1.0.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of UMSL_Core
	 *
	 * @since1.0.0
	 * @var UMSL_Core
	 */
	protected $core;

	/**
	 * Instance of UMSL_Template
	 *
	 * @since1.0.0
	 * @var UMSL_Template
	 */
	protected $template;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   1.0.0
	 * @return  UM_Story_Lite A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  1.0.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );

		$this->file           = __FILE__;
		$this->plugin_dir     = apply_filters( 'um_story_dir_path',  plugin_dir_path( $this->file ) );
		$this->plugin_url     = apply_filters( 'um_story_dir_url',   plugin_dir_url( $this->file ) );

		$this->slug     = 'story';
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  1.0.0
	 */
	public function plugin_classes() {
		require_once( $this->plugin_dir . 'includes/helper-functions.php' );
		$this->core = new UMSL_Core( $this );
		$this->template = new UMSL_Template( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
	}

	public function add_scripts() {
		$min         = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		//wp_enqueue_style( 'um_story_jquery_ui', '//code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'um_story_public', $this->plugin_url . 'assets/css/um-story' . $min . '.css' );

		wp_register_script(
			'um_story',
			$this->plugin_url . 'assets/js/um-story' . $min . '.js',
			array(
				'jquery',
			)
		);

		wp_enqueue_script( 'um_story' );
	}
	/**
	 * Activate the plugin.
	 *
	 * @since  1.0.0
	 */
	public function _activate() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		$default_options = array(
			'profile_tab_story'         => 1,
			'profile_tab_story_privacy' => 0,
		);

		$options = get_option( 'um_options', array() );
		foreach ( $default_options as $key => $value ) {
			//set new options to default.
			if ( ! isset( $options[ $key ] ) ) {
				$options[ $key ] = $value;
			}
		}

		update_option( 'um_options', $options );

		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  1.0.0
	 */
	public function _deactivate() {
		// Add deactivation cleanup functionality here.
	}

	/**
	 * Init hooks
	 *
	 * @since  1.0.0
	 */
	public function init() {

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Load translated strings for plugin.
		load_plugin_textdomain( 'um-story-lite', false, dirname( $this->basename ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  1.0.0
	 *
	 * @return boolean True if requirements met, false if not.
	 */
	public function check_requirements() {

		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		// Didn't meet the requirements.
		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  1.0.0
	 */
	public function deactivate_me() {

		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since  1.0.0
	 *
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {

		// Do checks for required classes / functions or similar.
		// Add detailed messages to $this->activation_errors array.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  1.0.0
	 */
	public function requirements_not_met_notice() {

		// Compile default message.
		$default_message = sprintf( __( 'UM Story Lite is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'um-story-lite' ), admin_url( 'plugins.php' ) );

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( $this->activation_errors && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo wp_kses_post( $default_message ); ?></p>
			<?php echo wp_kses_post( $details ); ?>
		</div>
		<?php
	}

	public function um_query() {
		global $ultimatemember;
		if ( ! function_exists( 'UM' ) ) {
			$query = $ultimatemember->query;
		} else {
			$query = UM()->query();
		}
		return $query;
	}

	public function um_shortcodes() {
		global $ultimatemember;
		if ( ! function_exists( 'UM' ) ) {
			$shortcodes = $ultimatemember->shortcodes;
		} else {
			$shortcodes = UM()->shortcodes();
		}
		return $shortcodes;
	}
	/**
	 * Magic getter for our object.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'core':
			case 'template':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $filename Name of the file to be included.
	 * @return boolean          Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $path (optional) appended path.
	 * @return string       Directory and path.
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $path (optional) appended path.
	 * @return string       URL and path.
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the UM_Story_Lite object and return it.
 * Wrapper for UM_Story_Lite::get_instance().
 *
 * @since  1.0.0
 * @return UM_Story_Lite  Singleton instance of plugin class.
 */
if ( ! function_exists( 'um_stories' ) ) {
	function um_story_lite() {
		return UM_Story_Lite::get_instance();
	}

	// Kick it off.
	add_action( 'plugins_loaded', array( um_story_lite(), 'hooks' ) );

	// Activation and deactivation.
	register_activation_hook( __FILE__, array( um_story_lite(), '_activate' ) );
	register_deactivation_hook( __FILE__, array( um_story_lite(), '_deactivate' ) );
}
