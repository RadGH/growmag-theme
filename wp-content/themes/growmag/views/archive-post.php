<?php
$classes = array( 'post' );
get_field( 'photo_darkness', get_the_ID() ) == 'dark' ? $classes[] = 'dark-photo' : $classes[] = 'light-photo';
$img = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'cover-small' );
?>

<article <?php post_class( $classes ); ?> style="background-image: url(<?php echo $img[0]; ?>);">
	<a href="<?php the_permalink(); ?>">
		<div class="overlay">
			<h3><?php
				the_title();
				if ( get_the_title() && get_field( "subtitle" ) ) {
					echo ':<br />';
				}
				echo '<span class="overlay-subtitle">' . get_field( 'subtitle' ) . '</span>';
				?></h3>
		</div>
	</a>
</article>