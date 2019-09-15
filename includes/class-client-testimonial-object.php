<?php

defined( 'ABSPATH' ) || exit;

class Client_Testimonial_Object {

	/**
	 * Post type
	 *
	 * @var string
	 */
	const POST_TYPE = 'testimonials';

	/**
	 * WP_Post object
	 *
	 * @var WP_Post
	 */
	protected $post;

	/**
	 * Client name
	 *
	 * @var string
	 */
	protected $client_name;

	/**
	 * Client company
	 *
	 * @var string
	 */
	protected $client_company;

	/**
	 * Client website
	 *
	 * @var string
	 */
	protected $client_website;

	/**
	 * Client_Testimonial_Object constructor.
	 *
	 * @param null|WP_Post $post
	 */
	public function __construct( $post = null ) {
		$this->post = get_post( $post );
		$meta       = get_post_meta( $post->ID, '_testimonial', true );

		$this->client_name    = ! empty( $meta['client_name'] ) ? $meta['client_name'] : '';
		$this->client_company = ! empty( $meta['source'] ) ? $meta['source'] : '';
		$this->client_website = ! empty( $meta['link'] ) && filter_var( $meta['link'], FILTER_VALIDATE_URL ) ? $meta['link'] : '';
	}

	/**
	 * Get testimonial content
	 *
	 * @return string
	 */
	public function get_content() {
		return apply_filters( 'the_content', $this->post->post_content );
	}

	/**
	 * Get client name
	 *
	 * @return string
	 */
	public function get_client_name() {
		return $this->client_name;
	}

	/**
	 * Get client company
	 *
	 * @return string
	 */
	public function get_client_company() {
		return $this->client_company;
	}

	/**
	 * Get client website
	 *
	 * @return string
	 */
	public function get_client_website() {
		return $this->client_website;
	}

	/**
	 * Check if avatar exists
	 *
	 * @return bool
	 */
	public function has_avatar() {
		$thumbnail_id = get_post_thumbnail_id( $this->post->ID );

		return (bool) $thumbnail_id;
	}

	/**
	 * Get client avatar
	 *
	 * @param string|array $size
	 *
	 * @return string
	 */
	public function get_client_avatar_url( $size = 'thumbnail' ) {
		$thumbnail_id = get_post_thumbnail_id( $this->post->ID );
		$image_src    = wp_get_attachment_image_src( $thumbnail_id, $size );

		if ( isset( $image_src[0] ) && filter_var( $image_src[0], FILTER_VALIDATE_URL ) ) {
			return $image_src[0];
		}

		return '';
	}

	/**
	 * Get client avatar image
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public function get_client_avatar_image( $size = 'thumbnail' ) {
		$thumbnail_id = get_post_thumbnail_id( $this->post->ID );

		return wp_get_attachment_image( $thumbnail_id, $size );
	}

	/**
	 * Get post object
	 *
	 * @return WP_Post
	 */
	public function get_post() {
		return $this->post;
	}

	/**
	 * Get list of testimonials
	 *
	 * @param array $args
	 *
	 * @return self[]
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

		$posts = get_posts( $args );
		$items = [];
		foreach ( $posts as $post ) {
			$items[] = new self( $post );
		}

		return $items;
	}
}
