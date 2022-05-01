<?php
/**
 * The file that defines the core theme class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Torit
 * @subpackage Torit/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Torit
 * @subpackage Torit/includes
 * @author     Alvin Muthui <alvinmuthui@toric.co.ke>
 */
class Torit {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Torit_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ajax_API    $ajax_api    The current instance of Ajax API.
	 */
	protected $ajax_api;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TORIT_VERSION' ) ) {
			$this->version = TORIT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'torit';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->init_ajax_api();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Torit_Loader. Orchestrates the hooks of the plugin.
	 * - Torit_i18n. Defines internationalization functionality.
	 * - Torit_Admin. Defines all hooks for the admin area.
	 * - Torit_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-torit-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-torit-i18n.php';

		/**
		 * The class responsible for creating Public Accessible AJAX interface
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ajax-api.php';

		/**
		 * The class responsible for creating the AJAX
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/ajax/class-ajax.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-torit-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-torit-public.php';

		$this->loader = new Torit_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Torit_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Torit_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}


	/**
	 * Initialize the Ajax API
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function init_ajax_api() {

		$this->ajax_api = new Ajax_API();
	}

	/**
	 * Adds Ajax scripts and their associated actions and callback
	 *
	 * @param string $action The name of action.
	 * @param mixed  $php_callback PHP function to respond to ajax calls.
	 * @param string $script_path File path containing you Javascript file.
	 * @param string $mode Determines if script will be exposed to authenticated Ajax actions for logged-in users or non-authenticated Ajax actions for logged-out users or both.
	 * @param array  $ajax_variables Variables to be passed to be available for JS to utilize.
	 * @param string $nonce string used to create WP nonce for verification on PHP callback.
	 * @param string $ajax_object Name of object to be storing JS variables.
	 * @param string $ajax_handle Name of script.
	 * @return boolean Whether it is added or not.
	 *
	 * @since    1.0.0
	 */
	public function add_ajax(
		$action,
		$php_callback,
		$script_path,
		$mode,
		$ajax_variables,
		$nonce,
		$ajax_object,
		$ajax_handle
	) {
		return $this->ajax_api->add_ajax(
			$action,
			$php_callback,
			$script_path,
			$mode,
			$ajax_variables,
			$nonce,
			$ajax_object,
			$ajax_handle
		);

	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Torit_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Torit_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Torit_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}