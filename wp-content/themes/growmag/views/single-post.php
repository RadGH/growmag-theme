<?php
$term = get_the_category()[0];
?>

<div class="inside narrow">
	<article class="main-column">
		<div class="post-header">
			<?php the_title( '<h2>', '</h2>' ); ?>
			<h3 class="subtitle"><?php echo esc_html( get_field( 'subtitle' ) ); ?></h3>
		</div>
		<div class="post-content">
			<div class="social-sharing">
				Share this page: <?php echo implode( generate_sharing_links() ); ?>
			</div>
			
			<?php
			$author = get_field( "post_author" ) ? get_field( "post_author" ) : get_the_author();
			
			if ( $author ) {
				echo '<p class="meta">By ', esc_html($author), '</p>';
				/*?> | Published <?php echo esc_html(get_the_date('F Y')); */
			}
			?>
			
			<?php the_content(); ?>
		</div>
		<?php
		$posts = get_posts( array(
			'numberposts'   => 3,
			'no_found_rows' => true,
			'post_type'     => get_post_type(), // could be "post" or "weekender"
			'category'      => $term->term_id,
			'orderby'       => 'rand',
			'post__not_in'  => array( get_the_ID() ),
		) );
		if ( $posts ):
			?>
			<h2>Related Stories</h2>
			<div class="post-related">
				<?php
				foreach ( $posts as $post ) :
					setup_postdata( $post );
					
					$cover = em_get_cover_header( get_the_ID() );
					$image_id = is_array($cover['image']) ? $cover['image']['ID'] : (int) $cover['image'];
					if ( !$image_id ) $image_id = get_post_thumbnail_id( get_the_ID() );
				
					$img = wp_get_attachment_image_src( $image_id, 'medium' );
					
					echo '<div class="post" style="background-image: url(' . esc_attr( $img[0] ) . ')">';
					echo '<a href="' . get_the_permalink() . '"><div class="overlay">';
					echo '<h3>' . get_the_title();
					if ( get_the_title() && get_field( 'subtitle', get_the_ID() ) ) {
						echo ':<br />';
					}
					echo '<span class="overlay-subtitle">' . get_field( 'subtitle', get_the_ID() ) . '</span></h3>';
					echo '</div></a>';
					echo '</div>';
				endforeach;
				?>
			</div>
		<?php
		endif;
		wp_reset_postdata();
		?>
	
	</article>
	<div class="sidebar">
		<?php
		echo do_shortcode( '[ad location="Article Sidebar (first)"]' );
		echo do_shortcode( '[ad location="Article Sidebar (second)"]' );
		echo do_shortcode( '[ad location="Article Sidebar (third)"]' );
		get_sidebar();
		?>
	</div>
</div>