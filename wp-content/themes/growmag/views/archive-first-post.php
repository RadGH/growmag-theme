<?php

/*
$classes = array( 'post', 'header-post', 'first-header-post' );

$cat      = get_category( get_query_var( 'cat' ) );
$cat_id   = $cat->cat_ID;
$deptLink = get_post_type() == 'post' ? get_category_link( $cat_id ) : get_post_type_archive_link( 'weekender' );
$deptName = get_post_type() == 'post' ? $cat->name : 'The Weekender';

$img_id            = false;
$img               = false;
$using_dept_header = false;

// if not on first page of archive, try to get the department's header image
if ( get_query_var( 'paged' ) > 1 && $img = get_field( 'dept_header_img', 'category_' . $cat_id ) ) {
	$using_dept_header = true;
	$classes[]         = ( get_field( 'photo_darkness', 'category_' . $cat_id ) == 'dark' ) ? 'dark-photo' : 'light-photo';
	
	$img_id = $img['ID'];
	$img    = $img['sizes']['medium'];
} else {
	$using_dept_header = false;
	$classes[]         = ( get_field( 'photo_darkness' ) == 'dark' ) ? 'dark-photo' : 'light-photo';
	
	$img_id = get_post_thumbnail_id();
	$img    = wp_get_attachment_image_src( $img_id, 'medium' );
	$img    = $img[0];
}

$mobile_image = false;
if ( $img_id && $m = ld_get_attachment_mobile( $img_id ) ) {
	$mobile_image = sprintf( 'style="background-image: url(%s);"', esc_attr( $m[0] ) );
	$classes[]    = 'with-mobile-alt';
} else {
	$classes[] = 'no-mobile-alt';
}
?>
	
	<header <?php gm_data_location(); ?> style="background-image: url(<?php echo $img; ?>);" class="<?php echo implode( ' ', $classes ); ?>">
		
		<?php if ( $mobile_image ) {
			echo '<div class="cover-image-mobile-alt" ' . $mobile_image . '>';
		} ?>
		
		<div class="inside narrow">
			<?php get_template_part( 'template-parts/menu-button' ); ?>
			<div class="header-line">
				<h1 class="category-header"><a href="<?php echo $deptLink; ?>"><?php echo $deptName; ?></a></h1>
			</div>
			<div class="eugmaglogo"><a href="/"><?php bloginfo( 'title' ); ?></a></div>
			
			<?php if ( ! $using_dept_header ) : ?>
				<div class="overlay">
					<a href="<?php the_permalink(); ?>">
						<h3 class="title"><?php the_title(); ?></h3>
						<h4 class="subtitle"><?php the_field( 'subtitle' ); ?></h4>
						<div class="readmore button button-white">Read Now</div>
					</a>
				</div>
			<?php endif; ?>
		</div>
		
		<?php if ( $mobile_image ) {
			echo '</div>';
		} ?>
	
	</header>

<?php
*/

// get_template_part( 'template-parts/cover' );