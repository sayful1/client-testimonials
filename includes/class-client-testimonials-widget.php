<?php

/**
 * Testimonials Widget
 */
class Client_Testimonials_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname'   => 'testimonial_widget',
			'description' => 'Display client testimonials'
		);
		parent::__construct( 'widget_client_testimonials', 'Client Testimonials', $widget_ops );
	}

	/**
	 * Echoes the widget content.
	 *
	 * Sub-classes should over-ride this function to generate their widget code.
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		$instance = wp_parse_args( $instance, array(
			'title'          => '',
			'mobile'         => 1,
			'tablet'         => 2,
			'desktop'        => 3,
			'widescreen'     => 4,
			'fullhd'         => 5,
			'autoplay'       => 'true',
			'nav'            => 'false',
			'posts_per_page' => 20,
		) );

		$title   = esc_attr( $instance['title'] );
		$orderby = esc_attr( $instance['orderby'] );

		$loop     = esc_attr( $instance['loop'] );
		$autoplay = esc_attr( $instance['autoplay'] );
		$nav      = esc_attr( $instance['nav'] );

		$total      = absint( $instance['posts_per_page'] );
		$mobile     = absint( $instance['mobile'] );
		$tablet     = absint( $instance['tablet'] );
		$desktop    = absint( $instance['desktop'] );
		$widescreen = absint( $instance['widescreen'] );
		$fullhd     = absint( $instance['fullhd'] );

		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		echo do_shortcode( '[client-testimonials fullhd="' . $fullhd . '" widescreen="' . $widescreen . '" desktop="' . $desktop . '" tablet="' . $tablet . '" mobile="' . $mobile . '" posts_per_page="' . $total . '" orderby="' . $orderby . '" loop="' . $loop . '" autoplay="' . $autoplay . '" nav="' . $nav . '"]' );

		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance                   = $old_instance;
		$instance['title']          = esc_attr( $new_instance['title'] );
		$instance['orderby']        = esc_attr( $new_instance['orderby'] );
		$instance['loop']           = esc_attr( $new_instance['loop'] );
		$instance['autoplay']       = esc_attr( $new_instance['autoplay'] );
		$instance['nav']            = esc_attr( $new_instance['nav'] );
		$instance['mobile']         = absint( $new_instance['mobile'] );
		$instance['tablet']         = absint( $new_instance['tablet'] );
		$instance['desktop']        = absint( $new_instance['desktop'] );
		$instance['fullhd']         = absint( $new_instance['fullhd'] );
		$instance['widescreen']     = absint( $new_instance['widescreen'] );
		$instance['posts_per_page'] = intval( $new_instance['posts_per_page'] );

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( $instance, array(
			'title'          => 'Client Testimonials',
			'mobile'         => 1,
			'tablet'         => 2,
			'desktop'        => 3,
			'widescreen'     => 4,
			'fullhd'         => 5,
			'loop'           => 'true',
			'autoplay'       => 'true',
			'nav'            => 'false',
			'posts_per_page' => 20,
			'orderby'        => 'none'
		) );

		extract( $instance );
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                Title:
            </label>
            <input
                    type="text"
                    class="widefat"
                    id="<?php echo $this->get_field_id( 'title' ); ?>"
                    name="<?php echo $this->get_field_name( 'title' ); ?>"
                    value="<?php echo esc_attr( $title ); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'mobile' ); ?>">
                No of items on Mobile:
            </label>
            <input
                    type="number" class="widefat" min="1"
                    id="<?php echo $this->get_field_id( 'mobile' ); ?>"
                    name="<?php echo $this->get_field_name( 'mobile' ); ?>"
                    value="<?php echo absint( $mobile ); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'tablet' ); ?>">
                No of items on Tablet:
            </label>
            <input
                    type="number" class="widefat" min="1"
                    id="<?php echo $this->get_field_id( 'tablet' ); ?>"
                    name="<?php echo $this->get_field_name( 'tablet' ); ?>"
                    value="<?php echo absint( $tablet ); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'desktop' ); ?>">
                No of items on small desktop:
            </label>
            <input
                    type="number" class="widefat" min="1"
                    id="<?php echo $this->get_field_id( 'desktop' ); ?>"
                    name="<?php echo $this->get_field_name( 'desktop' ); ?>"
                    value="<?php echo absint( $desktop ); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'widescreen' ); ?>">
                No of items on desktop:
            </label>
            <input
                    type="number" class="widefat" min="1"
                    id="<?php echo $this->get_field_id( 'widescreen' ); ?>"
                    name="<?php echo $this->get_field_name( 'widescreen' ); ?>"
                    value="<?php echo absint( $widescreen ); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'fullhd' ); ?>">
                No of items on large desktop:
            </label>
            <input
                    type="number" class="widefat" min="1"
                    id="<?php echo $this->get_field_id( 'fullhd' ); ?>"
                    name="<?php echo $this->get_field_name( 'fullhd' ); ?>"
                    value="<?php echo absint( $fullhd ); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>">
                Number of Testimonials:
            </label>
            <input
                    type="text"
                    class="widefat"
                    id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"
                    name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>"
                    value="<?php echo esc_attr( $posts_per_page ); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'loop' ); ?>">
                Loop slider
            </label>
            <select
                    class="widefat"
                    id="<?php echo $this->get_field_id( 'loop' ); ?>"
                    name="<?php echo $this->get_field_name( 'loop' ); ?>"
            >
                <option value="true" <?php selected( $loop, 'true' ); ?>>On</option>
                <option value="false" <?php selected( $loop, 'false' ); ?>>Off</option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'autoplay' ); ?>">
                Autoplay slider
            </label>
            <select
                    class="widefat"
                    id="<?php echo $this->get_field_id( 'autoplay' ); ?>"
                    name="<?php echo $this->get_field_name( 'autoplay' ); ?>"
            >
                <option value="true" <?php selected( $autoplay, 'true' ); ?>>On</option>
                <option value="false" <?php selected( $autoplay, 'false' ); ?>>Off</option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'nav' ); ?>">
                slider nav
            </label>
            <select
                    class="widefat"
                    id="<?php echo $this->get_field_id( 'nav' ); ?>"
                    name="<?php echo $this->get_field_name( 'nav' ); ?>"
            >
                <option value="true" <?php selected( $nav, 'true' ); ?>>On</option>
                <option value="false" <?php selected( $nav, 'false' ); ?>>Off</option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'orderby' ); ?>">
                Order By
            </label>
            <select
                    class="widefat"
                    id="<?php echo $this->get_field_id( 'orderby' ); ?>"
                    name="<?php echo $this->get_field_name( 'orderby' ); ?>"
            >
                <option value="none" <?php selected( $orderby, 'none' ); ?>>None</option>
                <option value="ID" <?php selected( $orderby, 'ID' ); ?>>ID</option>
                <option value="date" <?php selected( $orderby, 'date' ); ?>>Date</option>
                <option value="modified" <?php selected( $orderby, 'modified' ); ?>>Modified</option>
                <option value="rand" <?php selected( $orderby, 'rand' ); ?>>Random</option>
            </select>
        </p>
		<?php
	}

	/**
	 * Register current class as widget
	 */
	public static function register() {
		register_widget( __CLASS__ );
	}
}
