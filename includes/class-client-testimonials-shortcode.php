<?php

if( ! class_exists('Client_Testimonials_Shortcode')):

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
		}
		return self::$_instance;
	}

	public function __construct()
	{
		add_shortcode( 'client-testimonials', array( $this, 'testimonials' ) );
	}

	/**
	 * A shortcode for rendering the client testimonials slide.
	 *
	 * @param  array   $atts  		Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function testimonials( $atts, $content = null ){
		$defaults = array(
            'mobile' 			=> 1,
            'tablet' 			=> 2,
            'desktop' 			=> 3,
            'widescreen' 		=> 4,
            'fullhd' 			=> 5,
            'loop' 				=> 'true',
            'autoplay' 			=> 'true',
            'nav' 				=> 'false',
            'posts_per_page' 	=> 20,
            'orderby' 			=> 'none'
	    );

		$atts['mobile'] = $this->_parse_atts($atts, 'mobile', 'items_mobile', 1);
		$atts['tablet'] = $this->_parse_atts($atts, 'tablet', 'items_tablet_small', 2);
		$atts['desktop'] = $this->_parse_atts($atts, 'desktop', 'items_tablet', 3);
		$atts['widescreen'] = $this->_parse_atts($atts, 'widescreen', 'items_desktop', 4);
		$atts['fullhd'] = $this->_parse_atts($atts, 'fullhd', 'items_desktop', 4);

	    $atts = wp_parse_args( $atts, $defaults );

		extract( shortcode_atts( $defaults, $atts ) );

		ob_start();
	    require CT_TEMPLATES . '/client-testimonials.php';
	    $html = ob_get_contents();
	    ob_end_clean();
	    return apply_filters( 'client_testimonials', $html );
	}

	private function _parse_atts( $atts, $new_attr, $old_attr, $default = null )
	{
		if ( isset( $atts[$new_attr] ) ) {
			return $atts[$new_attr];
		}

		if ( isset( $atts[$old_attr] ) ) {
			return $atts[$old_attr];
		}

		return $default;
	}
}

endif;

Client_Testimonials_Shortcode::init();