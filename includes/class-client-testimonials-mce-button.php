<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Client_Testimonials_MCE_Button' ) ) {

	class Client_Testimonials_MCE_Button {
		/**
		 * The single instance of the class.
		 */
		protected static $_instance = null;

		/**
		 * Ensures only one instance of Client_Testimonials_MCE_Button is loaded.
		 *
		 * @return Client_Testimonials_MCE_Button - Main instance.
		 */
		public static function init() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();

				add_action( 'admin_init', array( self::$_instance, 'admin_init' ) );
			}

			return self::$_instance;
		}

		public function admin_init() {
			// check user permissions
			if ( ! current_user_can( 'edit_pages' ) ) {
				return;
			}

			// check if WYSIWYG is enabled
			if ( 'true' != get_user_option( 'rich_editing' ) ) {
				return;
			}

			add_action( 'admin_head', array( $this, 'admin_head' ) );
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_mce_button' ) );
		}

		// Hooks your functions into the correct filters
		public function admin_head() {
			?>
            <style type="text/css">
                i.mce-i-client-testimonials:before {
                    font-family: "dashicons";
                    content: "\f473";
                }
            </style>
			<?php
		}

		// Declare script for new button
		public function add_tinymce_plugin( $plugin_array ) {
			$plugin_array['testimonials_mce_button'] = CLIENT_TESTIMONIALS_ASSETS . '/js/admin.js';

			return $plugin_array;
		}

		// Register new button in the editor
		public function register_mce_button( $buttons ) {
			array_push( $buttons, 'testimonials_mce_button' );

			return $buttons;
		}
	}
}
