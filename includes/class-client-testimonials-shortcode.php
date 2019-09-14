<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Client_Testimonials_Shortcode' ) ) {

	class Client_Testimonials_Shortcode {

		/**
		 * The single instance of the class.
		 */
		protected static $_instance = null;

		/**
		 * Ensures only one instance of Client_Testimonials_Shortcode is loaded.
		 *
		 * @return Client_Testimonials_Shortcode - Main instance.
		 */
		public static function init() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();

				add_shortcode( 'client-testimonials', array( self::$_instance, 'testimonials' ) );
			}

			return self::$_instance;
		}

		/**
		 * A shortcode for rendering the client testimonials slide.
		 *
		 * @param array $attributes Shortcode attributes.
		 * @param string $content The text content for shortcode. Not used.
		 *
		 * @return string  The shortcode output
		 */
		public function testimonials( $attributes, $content = null ) {
			$defaults = array(
				'mobile'         => 1,
				'tablet'         => 1,
				'desktop'        => 1,
				'widescreen'     => 1,
				'fullhd'         => 1,
				'loop'           => 'true',
				'autoplay'       => 'true',
				'nav'            => 'false',
				'posts_per_page' => 20,
				'orderby'        => 'none'
			);

			$attributes = shortcode_atts( $defaults, $attributes, 'client-testimonials' );

			extract( $attributes );

			ob_start();
			require CLIENT_TESTIMONIALS_TEMPLATES . '/client-testimonials.php';
			$html = ob_get_contents();
			ob_end_clean();

			return apply_filters( 'client_testimonials', $html );
		}
	}
}
