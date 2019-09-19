<?php

defined( 'ABSPATH' ) || exit;

class Client_Testimonials_REST_Controller extends WP_REST_Controller {

	/**
	 * HTTP status code.
	 *
	 * @var int
	 */
	protected $statusCode = 200;

	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	protected $namespace = 'client-testimonials/v1';

	/**
	 * MYSQL data format
	 *
	 * @var string
	 */
	protected static $mysql_date_format = 'Y-m-d';

	/**
	 * The instance of the class
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Only one instance of the class can be loaded.
	 *
	 * @return self
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self;

			add_action( 'rest_api_init', array( self::$instance, 'register_routes' ) );
		}

		return self::$instance;
	}

	/**
	 * Registers the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/testimonials', [
			[
				'methods'  => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_items' ],
				'args'     => $this->get_collection_params(),
			],
		] );
	}

	/**
	 * Retrieves a collection of portfolios.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$testimonials_args = array(
			'posts_per_page' => $request->get_param( 'per_page' ),
			'orderby'        => $request->get_param( 'orderby' ),
			'order'          => $request->get_param( 'order' ),
			'paged'          => $request->get_param( 'page' ),
		);

		$testimonials = Client_Testimonial_Object::get_testimonials( $testimonials_args );
		$response     = [
			'items' => $this->prepare_items_for_response( $testimonials, $request )
		];

		return $this->respondOK( $response );
	}

	/**
	 * @param Client_Testimonial_Object[] $testimonials
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public function prepare_items_for_response( $testimonials, $request ) {
		$items = [];
		foreach ( $testimonials as $testimonial ) {
			$items[] = $this->prepare_item_for_response( $testimonial, $request )->get_data();
		}

		return $items;
	}

	/**
	 * Prepares a single post output for response.
	 *
	 * @param Client_Testimonial_Object $testimonial Post object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $testimonial, $request ) {
		$fields = $request->get_param( 'fields' );
		$post   = $testimonial->get_post();

		// Base fields for every post.
		$data = array( 'id' => $post->ID );

		if ( in_array( 'title', $fields ) ) {
			$data['title'] = get_the_title( $post->ID );
		}

		if ( in_array( 'content', $fields ) ) {
			$data['content'] = apply_filters( 'the_content', $post->post_content );
		}

		if ( in_array( 'excerpt', $fields ) ) {
			$data['excerpt'] = apply_filters( 'the_excerpt', apply_filters( 'get_the_excerpt', $post->post_excerpt, $post ) );
		}

		if ( in_array( 'date', $fields ) ) {
			$data['date'] = mysql_to_rfc3339( $post->post_date );
		}

		if ( in_array( 'date_gmt', $fields ) ) {
			$data['date_gmt'] = mysql_to_rfc3339( $post->post_date_gmt );
		}

		if ( in_array( 'modified', $fields ) ) {
			$data['modified'] = mysql_to_rfc3339( $post->post_modified );
		}

		if ( in_array( 'modified_gmt', $fields ) ) {
			$data['modified_gmt'] = mysql_to_rfc3339( $post->post_modified_gmt );
		}

		if ( in_array( 'link', $fields ) ) {
			$data['link'] = get_permalink( $post->ID );
		}

		if ( in_array( 'client_name', $fields ) ) {
			$data['client_name'] = $testimonial->get_client_name();
		}

		if ( in_array( 'client_company', $fields ) ) {
			$data['client_company'] = $testimonial->get_client_company();
		}

		if ( in_array( 'client_website', $fields ) ) {
			$data['client_website'] = $testimonial->get_client_website();
		}

		if ( in_array( 'featured_media', $fields ) ) {
			$data['featured_media'] = [];
			if ( $testimonial->has_avatar() ) {
				$thumbnail_id           = get_post_thumbnail_id( $post->ID );
				$data['featured_media'] = [
					'id'       => intval( $thumbnail_id ),
					'title'    => get_the_title( $thumbnail_id ),
					'alt_text' => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ),
					'src'      => $testimonial->get_client_avatar_url(),
				];
			}
		}

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		$valid_fields = [
			'id',
			'title',
			'content',
			'excerpt',
			'date',
			'date_gmt',
			'modified',
			'modified_gmt',
			'link',
			'client_name',
			'client_company',
			'client_website',
			'featured_media'
		];

		$params = parent::get_collection_params();

		return array_merge( $params, array(
			'order'   => array(
				'description' => __( 'Order sort attribute ascending or descending.', 'client-testimonials' ),
				'type'        => 'string',
				'default'     => 'desc',
				'enum'        => array( 'asc', 'desc' ),
			),
			'orderby' => array(
				'description' => __( 'Sort collection by object attribute.', 'client-testimonials' ),
				'type'        => 'string',
				'default'     => 'date',
				'enum'        => array( 'id', 'title', 'date', ),
			),
			'fields'  => array(
				'description'       => __( 'List of fields to include in response. Available fields are ', 'client-testimonials' ) . implode( ', ', $valid_fields ),
				'type'              => 'array',
				'default'           => [
					'id',
					'content',
					'client_name',
					'client_company',
					'client_website',
					'featured_media'
				],
				'validate_callback' => 'rest_validate_request_arg',
			),
		) );
	}

	/**
	 * Get HTTP status code.
	 *
	 * @return integer
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * Set HTTP status code.
	 *
	 * @param int $statusCode
	 *
	 * @return self
	 */
	public function setStatusCode( $statusCode ) {
		$this->statusCode = $statusCode;

		return $this;
	}

	/**
	 * Respond.
	 *
	 * @param mixed $data Response data. Default null.
	 * @param int $status Optional. HTTP status code. Default 200.
	 * @param array $headers Optional. HTTP header map. Default empty array.
	 *
	 * @return WP_REST_Response
	 */
	public function respond( $data = null, $status = 200, $headers = array() ) {
		return new WP_REST_Response( $data, $status, $headers );
	}

	/**
	 * Response error message
	 *
	 * @param string $code
	 * @param string $message
	 * @param mixed $data
	 *
	 * @return WP_REST_Response
	 */
	public function respondWithError( $code = null, $message = null, $data = null ) {
		if ( 1 === func_num_args() && is_array( $code ) ) {
			list( $code, $message, $data ) = array( null, null, $code );
		}

		$status_code = $this->getStatusCode();
		$response    = [ 'success' => false ];

		if ( ! empty( $code ) && is_string( $code ) ) {
			$response['code'] = $code;
		}

		if ( ! empty( $message ) && is_string( $message ) ) {
			$response['message'] = $message;
		}

		if ( ! empty( $data ) ) {
			$response['errors'] = $data;
		}

		return $this->respond( $response, $status_code );
	}

	/**
	 * Response success message
	 *
	 * @param mixed $data
	 * @param string $message
	 * @param array $headers
	 *
	 * @return WP_REST_Response
	 */
	public function respondWithSuccess( $data = null, $message = null, $headers = array() ) {
		if ( 1 === func_num_args() && is_string( $data ) ) {
			list( $data, $message ) = array( null, $data );
		}

		$code     = $this->getStatusCode();
		$response = [ 'success' => true ];

		if ( ! empty( $message ) ) {
			$response['message'] = $message;
		}

		if ( ! empty( $data ) ) {
			$response['data'] = $data;
		}

		return $this->respond( $response, $code, $headers );
	}

	/**
	 * 200 (OK)
	 * The request has succeeded.
	 *
	 * Use cases:
	 * --> update/retrieve data
	 * --> bulk creation
	 * --> bulk update
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return WP_REST_Response
	 */
	public function respondOK( $data = null, $message = null ) {
		return $this->setStatusCode( 200 )->respondWithSuccess( $data, $message );
	}

	/**
	 * 201 (Created)
	 * The request has succeeded and a new resource has been created as a result of it.
	 * This is typically the response sent after a POST request, or after some PUT requests.
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return WP_REST_Response
	 */
	public function respondCreated( $data = null, $message = null ) {
		return $this->setStatusCode( 201 )->respondWithSuccess( $data, $message );
	}

	/**
	 * 202 (Accepted)
	 * The request has been received but not yet acted upon.
	 * The response should include the Location header with a link towards the location where
	 * the final response can be polled & later obtained.
	 *
	 * Use cases:
	 * --> asynchronous tasks (e.g., report generation)
	 * --> batch processing
	 * --> delete data that is NOT immediate
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return WP_REST_Response
	 */
	public function respondAccepted( $data = null, $message = null ) {
		return $this->setStatusCode( 202 )->respondWithSuccess( $data, $message );
	}

	/**
	 * 204 (No Content)
	 * There is no content to send for this request, but the headers may be useful.
	 *
	 * Use cases:
	 * --> deletion succeeded
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return WP_REST_Response
	 */
	public function respondNoContent( $data = null, $message = null ) {
		return $this->setStatusCode( 204 )->respondWithSuccess( $data, $message );
	}

	/**
	 * 400 (Bad request)
	 * Server could not understand the request due to invalid syntax.
	 *
	 * Use cases:
	 * --> invalid/incomplete request
	 * --> return multiple client errors at once
	 *
	 * @param string $code
	 * @param string $message
	 * @param mixed $data
	 *
	 * @return WP_REST_Response
	 */
	public function respondBadRequest( $code = null, $message = null, $data = null ) {
		return $this->setStatusCode( 400 )->respondWithError( $code, $message, $data );
	}

	/**
	 * 401 (Unauthorized)
	 * The request requires user authentication.
	 *
	 * @param string $code
	 * @param string $message
	 * @param mixed $data
	 *
	 * @return WP_REST_Response
	 */
	public function respondUnauthorized( $code = null, $message = null, $data = null ) {
		if ( empty( $code ) ) {
			$code = 'rest_forbidden_context';
		}

		if ( empty( $message ) ) {
			$message = 'Sorry, you are not allowed to access this resource.';
		}

		return $this->setStatusCode( 401 )->respondWithError( $code, $message, $data );
	}

	/**
	 * 403 (Forbidden)
	 * The client is authenticated but not authorized to perform the action.
	 *
	 * @param string $code
	 * @param string $message
	 * @param mixed $data
	 *
	 * @return WP_REST_Response
	 */
	public function respondForbidden( $code = null, $message = null, $data = null ) {
		if ( empty( $code ) ) {
			$code = 'rest_forbidden_context';
		}

		if ( empty( $message ) ) {
			$message = 'Sorry, you are not allowed to access this resource.';
		}

		return $this->setStatusCode( 403 )->respondWithError( $code, $message, $data );
	}

	/**
	 * 404 (Not Found)
	 * The server can not find requested resource. In an API, this can also mean that the endpoint is valid but
	 * the resource itself does not exist. Servers may also send this response instead of 403 to hide
	 * the existence of a resource from an unauthorized client.
	 *
	 * @param string $code
	 * @param string $message
	 * @param mixed $data
	 *
	 * @return WP_REST_Response
	 */
	public function respondNotFound( $code = null, $message = null, $data = null ) {
		if ( empty( $code ) ) {
			$code = 'rest_no_item_found';
		}

		if ( empty( $message ) ) {
			$message = 'Sorry, no item found.';
		}

		return $this->setStatusCode( 404 )->respondWithError( $code, $message, $data );
	}

	/**
	 * 422 (Unprocessable Entity)
	 * The request was well-formed but was unable to be followed due to semantic errors.
	 *
	 * @param string $code
	 * @param string $message
	 * @param mixed $data
	 *
	 * @return WP_REST_Response
	 */
	public function respondUnprocessableEntity( $code = null, $message = null, $data = null ) {
		return $this->setStatusCode( 422 )->respondWithError( $code, $message, $data );
	}

	/**
	 * 500 (Internal Server Error)
	 * The server has encountered a situation it doesn't know how to handle.
	 *
	 * @param string $code
	 * @param string $message
	 * @param mixed $data
	 *
	 * @return WP_REST_Response
	 */
	public function respondInternalServerError( $code = null, $message = null, $data = null ) {
		return $this->setStatusCode( 500 )->respondWithError( $code, $message, $data );
	}

	/**
	 * Format date for REST Response
	 *
	 * @param string|int|DateTime $date
	 * @param string $type
	 *
	 * @return DateTime|int|string
	 * @throws Exception
	 */
	public static function formatDate( $date, $type = 'iso' ) {
		if ( ! $date instanceof DateTime ) {
			$date = new DateTime( $date );

			$timezone = get_option( 'timezone_string' );
			if ( in_array( $timezone, DateTimeZone::listIdentifiers() ) ) {
				$date->setTimezone( new DateTimeZone( $timezone ) );
			}
		}

		// Format ISO 8601 date
		if ( 'iso' == $type ) {
			return $date->format( DateTime::ISO8601 );
		}

		if ( 'mysql' == $type ) {
			return $date->format( self::$mysql_date_format );
		}

		if ( 'timestamp' == $type ) {
			return $date->getTimestamp();
		}

		if ( 'view' == $type ) {
			$date_format = get_option( 'date_format' );

			return $date->format( $date_format );
		}

		if ( ! in_array( $type, [ 'raw', 'mysql', 'timestamp', 'view', 'iso' ] ) ) {
			return $date->format( $type );
		}

		return $date;
	}

	/**
	 * Generate pagination metadata
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function getPaginationMetadata( array $args ) {
		$data = wp_parse_args( $args, array(
			"totalCount"     => 0,
			"limit"          => 10,
			"currentPage"    => 1,
			"offset"         => 0,
			"previousOffset" => null,
			"nextOffset"     => null,
			"pageCount"      => 0,
		) );

		if ( ! isset( $args['currentPage'] ) && isset( $args['offset'] ) ) {
			$data['currentPage'] = ( $args['offset'] / $data['limit'] ) + 1;
		}

		if ( ! isset( $args['offset'] ) && isset( $args['currentPage'] ) ) {
			$offset         = ( $data['currentPage'] - 1 ) * $data['limit'];
			$data['offset'] = max( $offset, 0 );
		}

		$previousOffset         = ( $data['currentPage'] - 2 ) * $data['limit'];
		$nextOffset             = $data['currentPage'] * $data['limit'];
		$data['previousOffset'] = ( $previousOffset < 0 || $previousOffset > $data['totalCount'] ) ? null : $previousOffset;
		$data['nextOffset']     = ( $nextOffset < 0 || $nextOffset > $data['totalCount'] ) ? null : $nextOffset;
		$data['pageCount']      = ceil( $data['totalCount'] / $data['limit'] );

		return $data;
	}

	/**
	 * Get sorting metadata
	 *
	 * @param string $field
	 * @param string $order
	 *
	 * @return array
	 */
	public function getSortingMetadata( $field, $order ) {
		return array(
			array( "field" => $field, "order" => $order ),
		);
	}
}
