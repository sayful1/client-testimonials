<?php
if ( ! class_exists('Client_Testimonials_Post_Type' ) ):

class Client_Testimonials_Post_Type {
	/**
	 * The single instance of the class.
	 */
	protected static $_instance = null;

	/**
	 * Ensures only one instance of Client_Testimonials_Post_Type is loaded.
	 *
	 * @return Client_Testimonials_Post_Type - Main instance.
	 */
	public static function init() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action('init', array( $this, 'post_type' ) );
		add_action('save_post', array( $this, 'save_post' ));
		add_filter( 'manage_edit-testimonials_columns', array( $this, 'columns_title' ));
		add_action( 'manage_posts_custom_column', array( $this, 'columns_content'), 10, 2 );
	}

	/**
	 * Data validation and saving
	 *
	 * This functions is attached to the 'save_post' action hook.
	 */
	public function save_post( $post_id ){

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			return;
		}

		if ( ! isset( $_POST['testimonials'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['testimonials'], 'testimonials' ) ){
			return;
		}

		if ( ! current_user_can( 'edit_page', $post_id ) ){
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'testimonials' != get_post_type( $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['testimonial'] ) ) {
			return;
		}

		remove_action( 'save_post', array( $this, 'save_post') );

		wp_update_post( array(
			'ID' 			=> $post_id,
			'post_title' 	=> 'Testimonial - ' . $post_id
		) );

		add_action( 'save_post', array( $this, 'save_post') );

		$ct = $_POST['testimonial'];

		$data['client_name'] = empty($ct['client_name']) ? '' : sanitize_text_field( $ct['client_name'] );
		$data['source'] = empty($ct['source']) ? '' : sanitize_text_field($ct['source']);
		$data['link'] = empty($ct['link']) ? '' : esc_url($ct['link']);

		update_post_meta( $post_id, '_testimonial', $data );
	}

	/**
	 * Modifying the list view columns
	 *
	 * This functions is attached to the 'manage_edit-testimonials_columns' filter hook.
	 */
	public function columns_title( $columns ) {
		$columns = array(
			'cb' 			=> '<input type="checkbox">',
			'title' 		=> 'Title',
			'testimonial' 	=> 'Testimonial',
			'client-name' 	=> 'Client Name',
			'source' 		=> 'Business/Site',
			'link' 			=> 'Link',
			'avatar' 		=> 'Client Avatar'
		);

		return $columns;
	}

	/**
	 * Customizing the list view columns
	 *
	 * This functions is attached to the 'manage_posts_custom_column' action hook.
	 */
	public function columns_content( $column, $post_id ) {
		$data = get_post_meta( $post_id, '_testimonial', true );
		switch ( $column ) {
			case 'testimonial':
				$text = get_the_excerpt( $post_id );
				echo esc_attr( wp_trim_words( $text, 10, '...' ) );
				break;
			case 'client-name':
				if ( ! empty( $data['client_name'] ) )
					echo esc_attr( $data['client_name'] );
				break;
			case 'source':
				if ( ! empty( $data['source'] ) )
					echo esc_attr( $data['source'] );
				break;
			case 'link':
				if ( ! empty( $data['link'] ) )
					echo esc_url( $data['link'] );
				break;
			case 'avatar':
				if ( has_post_thumbnail() )
					echo get_the_post_thumbnail( get_the_ID(), array(64,64));
				break;
		}
	}

	/**
	 * Creating the custom post type
	 *
	 * This functions is attached to the 'init' action hook.
	 */
	public function post_type(){
		$labels = array(
			'name' 					=> 'Testimonials',
			'singular_name' 		=> 'Testimonial',
			'add_new' 				=> 'Add New',
			'add_new_item' 			=> 'Add New Testimonial',
			'edit_item' 			=> 'Edit Testimonial',
			'new_item' 				=> 'New Testimonial',
			'view_item' 			=> 'View Testimonial',
			'search_items' 			=> 'Search Testimonials',
			'not_found' 			=>  'No Testimonials found',
			'not_found_in_trash' 	=> 'No Testimonials in the trash',
			'parent_item_colon' 	=> '',
		);

		register_post_type( 'testimonials', array(
			'labels' 				=> $labels,
			'public' 				=> false,
			'publicly_queryable' 	=> false,
			'show_ui' 				=> true,
			'exclude_from_search' 	=> true,
			'query_var' 			=> true,
			'rewrite' 				=> false,
			'has_archive' 			=> false,
			'hierarchical' 			=> false,
			'capability_type' 		=> 'page',
			'menu_position' 		=> 10,
			'menu_icon' 			=> 'dashicons-testimonial',
			'supports' 				=> array( 'editor', 'thumbnail' ),
			'register_meta_box_cb' 	=> array( $this, 'add_meta_box'),
		) );

	}

	/**
	 * Adding the necessary metabox
	 */
	public function add_meta_box(){
		add_meta_box(
			'testimonials_form',
			'Testimonial Details',
			array( $this, 'meta_box_cb'),
			'testimonials',
			'normal',
			'high'
		);
	}

	/**
	 * Adding the necessary metabox
	 */
	public function meta_box_cb() {
		$post_id = get_the_ID();
		$testimonial_data = get_post_meta( $post_id, '_testimonial', true );
		$client_name = ( empty( $testimonial_data['client_name'] ) ) ? '' : $testimonial_data['client_name'];
		$source = ( empty( $testimonial_data['source'] ) ) ? '' : $testimonial_data['source'];
		$link = ( empty( $testimonial_data['link'] ) ) ? '' : $testimonial_data['link'];

		wp_nonce_field( 'testimonials', 'testimonials' );
		?>
		<table class="form-table">
			<tr valign="top">
	            <th scope="row">
	                <label for="client_name">
	                    <?php esc_html_e('Client\'s Name (optional)','shapla') ?>
	                </label>
	            </th>
				<td>
					<input type="text" class="widefat" id="client_name" name="testimonial[client_name]" value="<?php echo esc_attr( $client_name ); ?>">
				</td>
			</tr>
			<tr valign="top">
	            <th scope="row">
	                <label for="source">
	                    <?php esc_html_e('Business/Site Name (optional)','shapla') ?>
	                </label>
	            </th>
				<td>
					<input type="text" class="widefat" id="source" name="testimonial[source]" value="<?php echo esc_attr( $source ); ?>">
				</td>
			</tr>
			<tr valign="top">
	            <th scope="row">
	                <label for="link">
	                    <?php esc_html_e('Business/Site Link (optional)','shapla') ?>
	                </label>
	            </th>
				<td>
					<input type="text" class="widefat" id="link" name="testimonial[link]" value="<?php echo esc_attr( $link ); ?>">
				</td>
			</tr>
		</table>
		<?php
	}
}

endif;

Client_Testimonials_Post_Type::init();