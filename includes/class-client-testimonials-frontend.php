<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Client_Testimonials_Frontend' ) ) {

	class Client_Testimonials_Frontend {

		/**
		 * The single instance of the class.
		 */
		protected static $_instance = null;

		/**
		 * Ensures only one instance of Client_Testimonials_Shortcode is loaded.
		 *
		 * @return Client_Testimonials_Frontend - Main instance.
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
				'tablet'     => 1,
				'desktop'    => 1,
				'widescreen' => 1,
				'fullhd'     => 1,
				'autoplay'   => 'no',
				'loop'       => 'yes',
				'nav'        => 'yes',
				'theme'      => 'one',
				'limit'      => 10,
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
			$items = Client_Testimonial_Object::get_testimonials( [
				'posts_per_page' => intval( $attributes['limit'] )
			] );
			$data  = [
				"autoPlay"        => self::is_checked( $attributes['autoplay'] ),
				"prevNextButtons" => self::is_checked( $attributes['nav'] ),
				"wrapAround"      => self::is_checked( $attributes['loop'] ),
				"pageDots"        => false,
				"cellAlign"       => 'left',
			];

			$tablet     = intval( $attributes['tablet'] );
			$desktop    = intval( $attributes['desktop'] ) > $tablet ? intval( $attributes['desktop'] ) : $tablet;
			$widescreen = intval( $attributes['widescreen'] ) > $desktop ? intval( $attributes['widescreen'] ) : $desktop;
			$fullhd     = intval( $attributes['fullhd'] ) > $desktop ? intval( $attributes['fullhd'] ) : $widescreen;
			$item_class = [
				'testimonial-carousel-item',
				'is-' . $tablet . '-tablet',
				'is-' . $desktop . '-desktop',
				'is-' . $widescreen . '-widescreen',
				'is-' . $fullhd . '-fullhd',
			];

			$themes = [ 'one', 'two' ];
			$theme  = in_array( $attributes['theme'], $themes ) ? $attributes['theme'] : 'one';

			$wrapper_classes   = [ 'client-testimonials' ];
			$wrapper_classes[] = 'client-testimonials--theme-' . $theme;

			$html = "<div class='" . implode( ' ', $wrapper_classes ) . "' data-flickity='" . wp_json_encode( $data ) . "'>";
			foreach ( $items as $item ) {
				$item_class[] = 'testimonial-' . $item->get_post()->ID;
				$html         .= '<div class="' . implode( ' ', $item_class ) . '">';
				$html         .= self::get_testimonial_item( $item->get_post() );
				$html         .= '</div>';
			}
			$html .= '</div>';

			return $html;
		}

		/**
		 * Get testimonial item html
		 *
		 * @param WP_Post $post
		 *
		 * @return string
		 */
		public static function get_testimonial_item( $post ) {
			$testimonial = new Client_Testimonial_Object( $post );
			ob_start();
			?>
            <div class="client-testimonial">
                <div class="client-testimonial__author">
                    <div class="client-testimonial__avatar">
						<?php if ( $testimonial->has_avatar() ): ?>
                            <span class="client-testimonial__avatar-thumb">
                                <?php echo $testimonial->get_client_avatar_image( array( 60, 60 ) ); ?>
                            </span>
						<?php else: ?>
                            <span class="client-testimonial__avatar-placeholder">
                                <?php echo $testimonial->get_client_avatar_placeholder(); ?>
                            </span>
						<?php endif; ?>
                    </div>
                    <div class="client-testimonial__vcard">
                        <div class="client-testimonial__client-name">
                            <span class="text-primary"><?php echo $testimonial->get_client_name() ?></span><br>
                        </div>
                        <div class="client-testimonial__client-company">
                            <a href="<?php echo $testimonial->get_client_website(); ?>" rel="nofollow"
                               target="_blank" class="text-secondary color-secondary">
								<?php echo $testimonial->get_client_company(); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="client-testimonial__content">
                    <div class="client-testimonial__message">
						<?php echo $testimonial->get_content() ?>
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
