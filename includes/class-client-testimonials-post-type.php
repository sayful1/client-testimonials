<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Client_Testimonials_Post_Type' ) ) {

	class Client_Testimonials_Post_Type {

		/**
		 * The single instance of the class.
		 */
		protected static $_instance = null;

		/**
		 * Ensures only one instance of the class is loaded.
		 *
		 * @return self - Main instance.
		 */
		public static function init() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();

				add_action( 'init', array( self::$_instance, 'post_type' ) );
				add_action( 'save_post', array( self::$_instance, 'save_post' ) );
				add_filter( 'manage_edit-testimonials_columns', array( self::$_instance, 'columns_title' ) );
				add_action( 'manage_posts_custom_column', array( self::$_instance, 'columns_content' ), 10, 2 );
			}

			return self::$_instance;
		}

		/**
		 * Data validation and saving
		 *
		 * This functions is attached to the 'save_post' action hook.
		 *
		 * @param int $post_id
		 */
		public function save_post( $post_id ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! isset( $_POST['testimonials'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['testimonials'], 'testimonials' ) ) {
				return;
			}

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
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

			remove_action( 'save_post', array( $this, 'save_post' ) );

			wp_update_post( array(
				'ID'         => $post_id,
				'post_title' => 'Testimonial - ' . $post_id
			) );

			add_action( 'save_post', array( $this, 'save_post' ) );

			$ct = $_POST['testimonial'];

			$data['client_name'] = empty( $ct['client_name'] ) ? '' : sanitize_text_field( $ct['client_name'] );
			$data['source']      = empty( $ct['source'] ) ? '' : sanitize_text_field( $ct['source'] );
			$data['link']        = empty( $ct['link'] ) ? '' : esc_url( $ct['link'] );

			update_post_meta( $post_id, '_testimonial', $data );
		}

		/**
		 * Modifying the list view columns
		 *
		 * This functions is attached to the 'manage_edit-testimonials_columns' filter hook.
		 *
		 * @return array
		 */
		public function columns_title() {
			$columns = array(
				'cb'          => '<input type="checkbox">',
				'title'       => __( 'Title', 'client-testimonials' ),
				'testimonial' => __( 'Testimonial', 'client-testimonials' ),
				'client-name' => __( 'Client Name', 'client-testimonials' ),
				'source'      => __( 'Client Company', 'client-testimonials' ),
				'link'        => __( 'Client Website', 'client-testimonials' ),
				'avatar'      => __( 'Client Avatar', 'client-testimonials' ),
			);

			return $columns;
		}

		/**
		 * Customizing the list view columns
		 *
		 * This functions is attached to the 'manage_posts_custom_column' action hook.
		 *
		 * @param string $column
		 * @param int $post_id
		 */
		public function columns_content( $column, $post_id ) {
			$data = get_post_meta( $post_id, '_testimonial', true );
			switch ( $column ) {
				case 'testimonial':
					$text = get_the_excerpt( $post_id );
					echo esc_attr( wp_trim_words( $text, 10, '...' ) );
					break;
				case 'client-name':
					if ( ! empty( $data['client_name'] ) ) {
						echo esc_attr( $data['client_name'] );
					}
					break;
				case 'source':
					if ( ! empty( $data['source'] ) ) {
						echo esc_attr( $data['source'] );
					}
					break;
				case 'link':
					if ( ! empty( $data['link'] ) ) {
						echo esc_url( $data['link'] );
					}
					break;
				case 'avatar':
					if ( has_post_thumbnail() ) {
						echo get_the_post_thumbnail( get_the_ID(), array( 64, 64 ) );
					}
					break;
			}
		}

		/**
		 * Creating the custom post type
		 *
		 * This functions is attached to the 'init' action hook.
		 */
		public function post_type() {
			$labels = array(
				'name'                  => _x( 'Testimonials', 'Post Type General Name', 'client-testimonials' ),
				'singular_name'         => _x( 'Testimonial', 'Post Type Singular Name', 'client-testimonials' ),
				'menu_name'             => __( 'Testimonials', 'client-testimonials' ),
				'name_admin_bar'        => __( 'Testimonial', 'client-testimonials' ),
				'archives'              => __( 'Testimonial Archives', 'client-testimonials' ),
				'attributes'            => __( 'Testimonial Attributes', 'client-testimonials' ),
				'parent_item_colon'     => __( 'Parent testimonial:', 'client-testimonials' ),
				'all_items'             => __( 'All testimonials', 'client-testimonials' ),
				'add_new_item'          => __( 'Add New Testimonial', 'client-testimonials' ),
				'add_new'               => __( 'Add New', 'client-testimonials' ),
				'new_item'              => __( 'New Testimonial', 'client-testimonials' ),
				'edit_item'             => __( 'Edit Testimonial', 'client-testimonials' ),
				'update_item'           => __( 'Update Testimonial', 'client-testimonials' ),
				'view_item'             => __( 'View Testimonial', 'client-testimonials' ),
				'view_items'            => __( 'View Testimonials', 'client-testimonials' ),
				'search_items'          => __( 'Search Testimonial', 'client-testimonials' ),
				'not_found'             => __( 'Not found', 'client-testimonials' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'client-testimonials' ),
				'featured_image'        => __( 'Client Avatar', 'client-testimonials' ),
				'set_featured_image'    => __( 'Set client avatar image', 'client-testimonials' ),
				'remove_featured_image' => __( 'Remove client avatar image', 'client-testimonials' ),
				'use_featured_image'    => __( 'Use as client avatar image', 'client-testimonials' ),
				'insert_into_item'      => __( 'Insert into testimonial', 'client-testimonials' ),
				'uploaded_to_this_item' => __( 'Uploaded to this testimonial', 'client-testimonials' ),
				'items_list'            => __( 'Testimonials list', 'client-testimonials' ),
				'items_list_navigation' => __( 'Testimonials list navigation', 'client-testimonials' ),
				'filter_items_list'     => __( 'Filter testimonials list', 'client-testimonials' ),
			);
			$args   = array(
				'label'                => __( 'Testimonial', 'client-testimonials' ),
				'description'          => __( 'Post Type Description', 'client-testimonials' ),
				'labels'               => $labels,
				'supports'             => array( 'editor', 'thumbnail' ),
				'hierarchical'         => false,
				'public'               => true,
				'show_ui'              => true,
				'show_in_menu'         => true,
				'menu_position'        => 10,
				'menu_icon'            => 'dashicons-testimonial',
				'show_in_admin_bar'    => true,
				'show_in_nav_menus'    => false,
				'can_export'           => false,
				'has_archive'          => false,
				'exclude_from_search'  => true,
				'publicly_queryable'   => false,
				'rewrite'              => false,
				'capability_type'      => 'page',
				'show_in_rest'         => true,
				'register_meta_box_cb' => array( $this, 'add_meta_box' ),
			);
			register_post_type( 'testimonials', $args );

		}

		/**
		 * Adding the necessary metabox
		 */
		public function add_meta_box() {
			add_meta_box(
				'testimonials_form',
				__( 'Testimonial Details', 'client-testimonials' ),
				array( $this, 'meta_box_cb' ),
				'testimonials',
				'side',
				'high'
			);
		}

		/**
		 * Adding the necessary metabox
		 */
		public function meta_box_cb() {
			$post_id          = get_the_ID();
			$testimonial_data = get_post_meta( $post_id, '_testimonial', true );
			$client_name      = ( empty( $testimonial_data['client_name'] ) ) ? '' : $testimonial_data['client_name'];
			$source           = ( empty( $testimonial_data['source'] ) ) ? '' : $testimonial_data['source'];
			$link             = ( empty( $testimonial_data['link'] ) ) ? '' : $testimonial_data['link'];

			wp_nonce_field( 'testimonials', 'testimonials' );
			?>
            <style type="text/css">
                .form-table--row {
                    margin-bottom: 10px
                }
            </style>
            <div class="form-table">
                <div class="form-table--row">
                    <label for="client_name">
                        <strong><?php esc_html_e( 'Client Name', 'client-testimonials' ) ?></strong>
                    </label>
                    <input type="text" class="widefat" id="client_name" name="testimonial[client_name]"
                           value="<?php echo esc_attr( $client_name ); ?>">
                </div>
                <div class="form-table--row">
                    <label for="source">
                        <strong><?php esc_html_e( 'Client Company', 'client-testimonials' ) ?></strong>
                    </label>
                    <input type="text" class="widefat" id="source" name="testimonial[source]"
                           value="<?php echo esc_attr( $source ); ?>">
                </div>
                <div class="form-table--row">
                    <label for="link">
                        <strong><?php esc_html_e( 'Client Website', 'client-testimonials' ) ?></strong>
                    </label>
                    <input type="url" class="widefat" id="link" name="testimonial[link]"
                           value="<?php echo esc_attr( $link ); ?>">
                </div>
            </div>
			<?php
		}
	}
}
