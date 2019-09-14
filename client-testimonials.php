<?php
/*
 * Plugin Name: 	Client Testimonials
 * Plugin URI: 		http://wordpress.org/plugins/client-testimonials/
 * Description: 	Manage and display client testimonials for your WordPress site.
 * Version: 		3.0.0
 * Author: 			Sayful Islam
 * Author URI: 		https://profiles.wordpress.org/sayful/
 * Text Domain: 	display-latest-tweets
 * License: 		GPLv2 or later
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! class_exists( 'Client_Testimonials' ) ):

class Client_Testimonials {

	/**
	 * The single instance of the class.
	 */
	protected static $_instance = null;

	/**
	 * Main Client_Testimonials Instance.
	 * Ensures only one instance of Client_Testimonials is loaded or can be loaded.
	 *
	 * @return Client_Testimonials - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct(){

		// define constants
		$this->define_constants();

		// include files
		$this->include_files();

		add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ));
	}

	private function define_constants()
	{
		$this->define( 'CT_VERSION', '3.0.0' );
		$this->define( 'CT_FILE', __FILE__ );
        $this->define( 'CT_PATH', dirname( CT_FILE ) );
        $this->define( 'CT_INCLUDES', CT_PATH . '/includes' );
        $this->define( 'CT_TEMPLATES', CT_PATH . '/templates' );
        $this->define( 'CT_URL', plugins_url( '', CT_FILE ) );
        $this->define( 'CT_ASSETS', CT_URL . '/assets' );
	}

	private function include_files() {
		include_once CT_INCLUDES . '/class-client-testimonials-post-type.php';
		include_once CT_INCLUDES . '/class-client-testimonials-mce-button.php';
		include_once CT_INCLUDES . '/class-client-testimonials-shortcode.php';
		include_once CT_INCLUDES . '/widgets/widget-client-testimonials.php';
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts(){
		if ( ! $this->should_load_scripts() ) return;

	    wp_enqueue_style( 'client-testimonials', CT_ASSETS . '/css/style.css', array(), CT_VERSION, 'all' );
	    wp_enqueue_script( 'owl-carousel', CT_ASSETS . '/js/owl.carousel.min.js', array( 'jquery' ), '2.2.1', true );
	    wp_enqueue_script( 'client-testimonials', CT_ASSETS . '/js/scripts.js', array( 'jquery', 'owl-carousel' ), CT_VERSION, true );
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

endif;

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
Client_Testimonials::instance();
