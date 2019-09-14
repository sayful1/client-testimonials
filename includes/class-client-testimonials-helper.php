<?php

defined( 'ABSPATH' ) || exit;

class Client_Testimonials_Helper {

	/**
	 * Post type
	 *
	 * @var string
	 */
	const POST_TYPE = 'testimonials';

	/**
	 * Get list of testimonials
	 *
	 * @param array $args
	 *
	 * @return WP_Post[]
	 */
	public static function get_testimonials( $args = [] ) {
		$defaults = [
			'posts_per_page' => 10,
			'orderby'        => 'id',
			'order'          => 'DESC',
			'paged'          => 1,
			'post_status'    => 'publish',
		];
		$args     = wp_parse_args( $args, $defaults );

		$args['post_type'] = self::POST_TYPE;

		return get_posts( $args );
	}
}
