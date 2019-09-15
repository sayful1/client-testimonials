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
		 *
		 * @return string  The shortcode output
		 */
		public function testimonials( $attributes ) {
			$defaults = array(
				'mobile'     => 1,
				'tablet'     => 1,
				'desktop'    => 1,
				'widescreen' => 1,
				'fullhd'     => 1,
				'autoplay'   => 'no',
				'nav'        => 'yes',
				'limit'      => 20,
			);

			$attributes = shortcode_atts( $defaults, $attributes, 'client-testimonials' );

			$html = self::get_testimonial_items( $attributes );

			return apply_filters( 'client_testimonials', $html );
		}

		/**
		 * @param array $attributes
		 *
		 * @return string
		 */
		public static function get_testimonial_items( array $attributes ) {
			$items      = Client_Testimonial_Object::get_testimonials( [
				'posts_per_page' => intval( $attributes['limit'] )
			] );
			$data       = [
				"autoPlay"        => self::is_checked( $attributes['autoplay'] ),
				"prevNextButtons" => self::is_checked( $attributes['nav'] ),
				"adaptiveHeight"  => true,
				"pageDots"        => false,
			];
			$item_class = [
				'testimonial-carousel-item',
				'is-' . intval( $attributes['tablet'] ) . '-tablet',
				'is-' . intval( $attributes['desktop'] ) . '-desktop',
				'is-' . intval( $attributes['widescreen'] ) . '-widescreen',
				'is-' . intval( $attributes['fullhd'] ) . '-fullhd',
			];

			$html = "<div class='client-testimonials client-testimonials--default' data-flickity='" . wp_json_encode( $data ) . "'>";
			foreach ( $items as $item ) {
				$html .= '<div class="' . implode( ' ', $item_class ) . '">';
				$html .= self::get_testimonial_item( $item->get_post() );
				$html .= '</div>';
			}
			$html .= '</div>';

			return $html;
		}

		/**
		 * @param WP_Post $post
		 *
		 * @return string
		 */
		public static function get_testimonial_item( $post ) {
			$testimonial = new Client_Testimonial_Object( $post );
			ob_start();
			?>
            <div class="client-testimonial">
				<?php if ( $testimonial->has_avatar() ): ?>
                    <div class="client-testimonial__avatar">
						<?php echo $testimonial->get_client_avatar_image( array( 64, 64 ) ); ?>
                    </div>
				<?php endif; ?>
                <div class="client-testimonial__content">
                    <div class="client-testimonial__message">
						<?php echo $testimonial->get_content(); ?>
                    </div>
                </div>
                <div class="client-testimonial__client-info">
                    <div class="client-testimonial__client-name">
						<?php echo $testimonial->get_client_name(); ?>
                    </div>
                    <div class="client-testimonial__client-company">
                        <a href="<?php echo $testimonial->get_client_website(); ?>" rel="nofollow" target="_blank">
							<?php echo $testimonial->get_client_company(); ?>
                        </a>
                    </div>
                </div>
            </div>
			<?php
			$html = ob_get_clean();

			return apply_filters( 'client_testimonials_item', $html, $testimonial );
		}

		/**
		 * Check if checked
		 *
		 * @param string $value
		 *
		 * @return bool
		 */
		private static function is_checked( $value ) {
			return in_array( $value, [ 'yes', 'on', 'true', true, 1 ], true );
		}
	}
}
