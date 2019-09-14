<?php
$args = array(
	'posts_per_page' => intval( $posts_per_page ),
	'orderby'        => $orderby,
	'post_type'      => 'testimonials',
	'post_status'    => 'publish',
	'no_found_rows'  => true,
);

$query = new WP_Query( $args );
?>
<div
        id="client-testimonials"
        class="owl-carousel client-testimonials"
        data-mobile="<?php echo intval( $mobile ); ?>"
        data-tablet="<?php echo intval( $tablet ); ?>"
        data-desktop="<?php echo intval( $desktop ); ?>"
        data-widescreen="<?php echo intval( $widescreen ); ?>"
        data-fullhd="<?php echo intval( $fullhd ); ?>"
        data-loop="<?php echo esc_attr( $loop ); ?>"
        data-nav="<?php echo esc_attr( $nav ); ?>"
        data-autoplay="<?php echo esc_attr( $autoplay ); ?>"
>
	<?php
	if ( $query->have_posts() ):
		while ( $query->have_posts() ) : $query->the_post();

			$testimonial   = get_post_meta( get_the_ID(), '_testimonial', true );
			$client_name   = ( empty( $testimonial['client_name'] ) ) ? '' : $testimonial['client_name'];
			$client_source = ( empty( $testimonial['source'] ) ) ? '' : $testimonial['source'];
			$client_link   = ( empty( $testimonial['link'] ) ) ? '' : $testimonial['link'];

			?>
            <div class="single-feedback">
				<?php if ( has_post_thumbnail() ): ?>
                    <div class="client-pic">
						<?php the_post_thumbnail( array( 64, 64 ) ); ?>
                    </div>
				<?php endif; ?>
                <div class="box">
                    <p class="message">
						<?php echo get_the_content(); ?>
                    </p>
                </div>
                <div class="client-info">
                    <div class="client-name colored-text strong">
						<?php echo $client_name; ?>
                    </div>
                    <div class="company">
                        <a href="<?php echo $client_link; ?>" target="_blank">
							<?php echo $client_source; ?>
                        </a>
                    </div>
                </div>
            </div>
		<?php
		endwhile;
		wp_reset_query();
	endif;
	?>
</div>