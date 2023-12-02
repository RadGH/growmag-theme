<?php

$post_id = get_the_ID();
if ( is_search() ) $post_id = gm_get_front_page_id();

$cover = em_get_cover_header( $post_id );

$classes = array();
$classes[] = 'cover-header';
$classes[] = 'cover-standard';
$classes[] = !empty($cover['image']) ? 'has-cover-photo' : 'no-cover-photo';
$classes[] = ($cover['iconcolor'] == 'light') ? 'light-photo' : 'dark-photo';

$bg_tag = '';
if ( $cover['image'] ) {
	$id = is_array($cover['image']) ? $cover['image']['ID'] : (int) $cover['image'];
	$url = $id ? wp_get_attachment_image_url( $id, 'cover-full' ) : false;
	if ( $url ) $bg_tag = 'style="background-image: url('. esc_attr($url) .');"';
}

$logo_id = '';
if ( $cover['logo']['image'] ) {
	$logo_id = is_array($cover['logo']['image']) ? $cover['logo']['image']['ID'] : (int) $cover['logo']['image'];
}

// Category title for single posts and category archive pages
$category_link = false;
$category_title = false;

if ( is_singular() || is_category() ) {
	$terms = get_the_category();
	if ( $terms ) {
		$term = $terms[0];
		$classes[] = 'post';
		$classes['header-type'] = 'header-post';
		$classes[] = 'first-header-post';
		if ( get_post_type() == 'weekender' ) {
			$category_link = get_post_type_archive_link( 'weekender' );
			$category_title = 'The Weekender';
		}else{
			$category_link = get_term_link( $term );
			$category_title = $term->name;
		}
	}
}

if ( is_woocommerce() || ld_is_woocommerce_page() ) {
	$classes['header-type'] = 'header-woocommerce';
}

if ( get_post_type() == 'digital-issue' || is_post_type_archive('digital-issue') ) {
	$classes['header-type'] = 'header-digital-issue';
}

if ( !isset($classes['header-type']) ) {
	$classes['header-type'] = 'header-unknown';
}

// Category archive page latest post
$latest_post_box = false;
if ( is_category() ) {
	$latest_post_box = true;
}

?>

<header <?php gm_data_location(); ?> id="header" <?php echo $bg_tag; ?> class="<?php echo implode(' ', $classes); ?>">

		<div class="inside narrow">
			
			<?php get_template_part( 'template-parts/menu-button' ); ?>
			
			<?php if ( $logo_id ) { ?>
				<div class="cover-logo">
					<a href="/"><?php echo wp_get_attachment_image($logo_id, 'full'); ?></a>
				</div>
			<?php }else{ ?>
				<div class="eugmaglogo"><a href="/"><?php bloginfo( 'title' ); ?></a></div>
			<?php } ?>
			
			<?php
			if ( $latest_post_box ) {
				gm_display_primary_overlay( get_the_ID(), get_permalink() );
				
				/*
				?>
				<div class="overlay">
					<a href="<?php the_permalink(); ?>">
						<h1 class="title"><?php the_title(); ?></h1>
						<h4 class="subtitle"><?php the_field( 'subtitle' ); ?></h4>
						<div class="readmore button button-white">Read Now</div>
					</a>
				</div>
				<?php
				*/
			} ?>
			
			<?php
			if ( $category_link && $category_title ) {
				?>
				<div class="header-line">
					<h2 class="category-header">
						<a href="<?php echo esc_url( $category_link ); ?>"><?php echo $category_title; ?></a>
					</h2>
				</div>
				<?php
			}
			?>
			
		</div>

</header> <!-- /#header -->