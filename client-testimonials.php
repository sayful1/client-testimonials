<?php
/*!
 * Plugin Name: 	Client Testimonials
 * Plugin URI: 		http://wordpress.org/plugins/client-testimonials/
 * Description: 	Manage and display client testimonials for your WordPress site.
 * Version: 		3.1.0
 * Author: 			Sayful Islam
 * Author URI: 		https://sayfulislam.com
 * Text Domain: 	client-testimonials
 * License: 		GPLv2 or later
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Client_Testimonials' ) ) {

	class Client_Testimonials {

		/**
		 * The single instance of the class.
		 */
		protected static $instance = null;

		/**
		 * Plugin slug
		 *
		 * @var string
		 */
		protected $plugin_name = 'client-testimonials';

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		protected $version = '3.1.0';

		/**
		 * Main Client_Testimonials Instance.
		 * Ensures only one instance of the class is loaded or can be loaded.
		 *
		 * @return Client_Testimonials - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();

				// define constants
				self::$instance->define_constants();

				// include files
				self::$instance->include_files();

				add_action( 'plugin_loaded', [ self::$instance, 'init_classes' ] );

				add_action( 'wp_enqueue_scripts', array( self::$instance, 'enqueue_scripts' ) );

				// Load plugin textdomain
				add_action( 'plugins_loaded', array( self::$instance, 'load_plugin_textdomain' ) );
			}

			return self::$instance;
		}

		/**
		 * Define plugin constants
		 */
		private function define_constants() {
			define( 'CLIENT_TESTIMONIALS_VERSION', '3.1.0' );
			define( 'CLIENT_TESTIMONIALS_FILE', __FILE__ );
			define( 'CLIENT_TESTIMONIALS_PATH', dirname( CLIENT_TESTIMONIALS_FILE ) );
			define( 'CLIENT_TESTIMONIALS_INCLUDES', CLIENT_TESTIMONIALS_PATH . '/includes' );
			define( 'CLIENT_TESTIMONIALS_TEMPLATES', CLIENT_TESTIMONIALS_PATH . '/templates' );
			define( 'CLIENT_TESTIMONIALS_URL', plugins_url( '', CLIENT_TESTIMONIALS_FILE ) );
			define( 'CLIENT_TESTIMONIALS_ASSETS', CLIENT_TESTIMONIALS_URL . '/assets' );
		}

		/**
		 * Include plugin files
		 */
		private function include_files() {
			include_once CLIENT_TESTIMONIALS_INCLUDES . '/class-client-testimonials-post-type.php';
			include_once CLIENT_TESTIMONIALS_INCLUDES . '/class-client-testimonial-object.php';
			include_once CLIENT_TESTIMONIALS_INCLUDES . '/class-client-testimonials-frontend.php';
			include_once CLIENT_TESTIMONIALS_INCLUDES . '/class-client-testimonials-rest-controller.php';
			include_once CLIENT_TESTIMONIALS_INCLUDES . '/class-client-testimonials-widget.php';
		}

		/**
		 * Init plugin classes
		 */
		public function init_classes() {
			Client_Testimonials_Post_Type::init();
			Client_Testimonials_Frontend::init();
			Client_Testimonials_REST_Controller::init();

			add_action( 'widgets_init', array( 'Client_Testimonials_Widget', 'register' ) );
		}

		/**
		 * Enqueue scripts
		 */
		public function enqueue_scripts() {
			if ( ! $this->should_load_scripts() ) {
				return;
			}
			wp_enqueue_script( 'flickity', CLIENT_TESTIMONIALS_ASSETS . '/libs/flickity/flickity.pkgd.min.js',
				array(), '2.2.1', true );

			wp_enqueue_style( 'client-testimonials', CLIENT_TESTIMONIALS_ASSETS . '/css/frontend.css',
				array(), $this->get_version(), 'all' );
		}

		/**
		 * Check if it should load frontend scripts
		 *
		 * @return mixed|void
		 */
		private function should_load_scripts() {
			global $post;
			$load_scripts = is_active_widget( false, false, 'widget_client_testimonials', true ) || ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'client-testimonials' ) );

			return apply_filters( 'client_testimonials_load_scripts', $load_scripts );
		}

		/**
		 * Get plugin version number
		 *
		 * @return string
		 */
		public function get_version() {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				return $this->version . '-' . time();
			}

			return $this->version;
		}

		/**
		 * Load plugin textdomain
		 */
		public function load_plugin_textdomain() {
			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), $this->plugin_name );
			$mofile = sprintf( '%1$s-%2$s.mo', $this->plugin_name, $locale );

			// Setup paths to current locale file
			$mofile_global = WP_LANG_DIR . '/' . $this->plugin_name . '/' . $mofile;

			// Look in global /wp-content/languages/dialog-contact-form folder
			if ( file_exists( $mofile_global ) ) {
				load_textdomain( $this->plugin_name, $mofile_global );
			}
		}
	}
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
Client_Testimonials::instance();
