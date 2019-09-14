<?php
/*!
 * Plugin Name: 	Client Testimonials
 * Plugin URI: 		http://wordpress.org/plugins/client-testimonials/
 * Description: 	Manage and display client testimonials for your WordPress site.
 * Version: 		3.0.0
 * Author: 			Sayful Islam
 * Author URI: 		https://profiles.wordpress.org/sayful/
 * Text Domain: 	client-testimonials
 * License: 		GPLv2 or later
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! class_exists( 'Client_Testimonials' ) ) {

	class Client_Testimonials {

		/**
		 * The single instance of the class.
		 */
		protected static $_instance = null;

		/**
		 * Main Client_Testimonials Instance.
		 * Ensures only one instance of the class is loaded or can be loaded.
		 *
		 * @return Client_Testimonials - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();

				// define constants
				self::$_instance->define_constants();

				// include files
				self::$_instance->include_files();

				add_action( 'plugin_loaded', [ self::$_instance, 'init_classes' ] );

				add_action( 'wp_enqueue_scripts', array( self::$_instance, 'enqueue_scripts' ) );
			}

			return self::$_instance;
		}

		private function define_constants() {
			define( 'CLIENT_TESTIMONIALS_VERSION', '3.0.0' );
			define( 'CLIENT_TESTIMONIALS_FILE', __FILE__ );
			define( 'CLIENT_TESTIMONIALS_PATH', dirname( CLIENT_TESTIMONIALS_FILE ) );
			define( 'CLIENT_TESTIMONIALS_INCLUDES', CLIENT_TESTIMONIALS_PATH . '/includes' );
			define( 'CLIENT_TESTIMONIALS_TEMPLATES', CLIENT_TESTIMONIALS_PATH . '/templates' );
			define( 'CLIENT_TESTIMONIALS_URL', plugins_url( '', CLIENT_TESTIMONIALS_FILE ) );
			define( 'CLIENT_TESTIMONIALS_ASSETS', CLIENT_TESTIMONIALS_URL . '/assets' );
		}

		private function include_files() {
			include_once CLIENT_TESTIMONIALS_INCLUDES . '/class-client-testimonials-post-type.php';
			include_once CLIENT_TESTIMONIALS_INCLUDES . '/class-client-testimonials-mce-button.php';
			include_once CLIENT_TESTIMONIALS_INCLUDES . '/class-client-testimonials-shortcode.php';
			include_once CLIENT_TESTIMONIALS_INCLUDES . '/class-client-testimonials-rest-controller.php';
			include_once CLIENT_TESTIMONIALS_INCLUDES . '/widgets/widget-client-testimonials.php';
		}

		public function init_classes() {
			Client_Testimonials_Post_Type::init();
			Client_Testimonials_MCE_Button::init();
			Client_Testimonials_Shortcode::init();
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

			wp_enqueue_style( 'client-testimonials', CLIENT_TESTIMONIALS_ASSETS . '/css/style.css',
				array(), CLIENT_TESTIMONIALS_VERSION, 'all' );
			wp_enqueue_script( 'owl-carousel', CLIENT_TESTIMONIALS_ASSETS . '/js/owl.carousel.min.js',
				array( 'jquery' ), '2.2.1', true );
			wp_enqueue_script( 'client-testimonials', CLIENT_TESTIMONIALS_ASSETS . '/js/scripts.js',
				array( 'jquery', 'owl-carousel' ), CLIENT_TESTIMONIALS_VERSION, true );
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
