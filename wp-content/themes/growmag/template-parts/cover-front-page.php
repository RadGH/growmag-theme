<?php

$front_page_id = gm_get_front_page_id();

$cover = array(
	'logo' => array(
		'image' => get_field( 'logo', 'options', false ),
		'image_black' => get_field( 'logo_black', 'options', false ),
		'align' => get_field( 'logo-position', $front_page_id ),
	),

	'image' => get_field( 'cover-image', $front_page_id ),
	'mobile_image' => false,
	'align' => strtolower( get_field( 'cover-position', $front_page_id ) ),

	'title' => array(
		'text' => get_field( 'cover-title', $front_page_id ),
		'color' => get_field( 'cover-title-color', $front_page_id ),
		'align' => strtolower( get_field( 'cover-title-align', $front_page_id ) ),
	),

	'subtitle' => array(
		'text' => get_field( 'cover-subtitle', $front_page_id ),
		'color' => get_field( 'cover-subtitle-color', $front_page_id ),
		'align' => strtolower( get_field( 'cover-subtitle-align', $front_page_id ) ),
	),

	'button' => array(
		'data' => get_field( 'cover-button', $front_page_id ),
		'background' => get_field( 'cover-button-bg-color', $front_page_id ),
		'color' => get_field( 'cover-button-text-color', $front_page_id ),
		'align' => strtolower( get_field( 'cover-button-align', $front_page_id ) ),
	),

	'iconcolor' => get_field( 'cover-icon-color', $front_page_id )
);

// Use black logo
if ( $cover['iconcolor'] === 'light' ) {
	$cover['logo']['image'] = $cover['logo']['image_black'];
}

// Convert cover image into an inline CSS background property
if ( $cover['image'] ) {
	$i = wp_get_attachment_image_src($cover['image'], 'cover-full');
	if ( $i ) {
		if ( $m = ld_get_attachment_mobile( $cover['image'] ) ) {
			$cover['mobile_image'] = sprintf( 'style="background-image: url(%s);"', esc_attr($m[0]) );
		}

		$cover['image'] = sprintf( 'style="background-image: url(%s);"', esc_attr($i[0]) );
	}
}

if ( !$cover['logo']['align'] ) $cover['logo']['align'] = 'center';

// Convert cover logo into html img element
// if ( $cover['logo']['image'] ) {
	// $i = wp_get_attachment_image_src($cover['logo']['image'], 'full');
	// if ( $i ) {
	// 	$cover['logo']['image'] = sprintf( '<img src="%s" alt="%s" width="%s" height="%s" />', esc_attr($i[0]), esc_attr(smart_media_alt($i[0])), (int) $i[1], (int) $i[2] );
	// }
// }

// Split button URL / Text out of their initial array
if ( !empty($cover['button']['data'][0]['url']) ) {
	$cover['button']['url']  = $cover['button']['data'][0]['url'];
	$cover['button']['label'] = $cover['button']['data'][0]['label'] ?: 'Learn More';
    unset($cover['button']['data']);
}else{
	$cover['button'] = false;
}

?>
<header <?php gm_data_location(); ?> id="header" amicrazy="1" class="cover-header cover-front <?php echo ($cover['iconcolor']=='light') ? 'light-photo' : 'dark-photo'; ?>">
	<div class="cover-front-image <?php echo $cover['mobile_image'] ? "with-mobile-alt" : "no-mobile-alt"; ?>" <?php if ( $cover['image'] ) echo $cover['image']; ?>>
		
		<?php // if ( $cover['mobile_image'] ) echo '<div class="cover-front-image-mobile-alt" '. $cover['mobile_image'].'>'; ?>

		<div class="inside narrow clearfix">

            <?php get_template_part( 'template-parts/menu-button' ); ?>

			<div class="cover-inside first-header-post">
				
				<?php if ( $cover['logo']['image'] ) { ?>
					<div class="cover-logo cover-logo-front-page logo-align-<?php echo strtolower($cover['logo']['align']); ?>">
						<?php echo wp_get_attachment_image($cover['logo']['image'], 'full'); ?>
					</div>
				<?php } ?>

				<?php if ( $cover['title']['text'] || $cover['subtitle']['text'] || $cover['button'] ) { ?>
					<div class="cover-panel align-<?php echo $cover['align'] ? esc_attr($cover['align']) : 'right'; ?>">

						<?php if ( $cover['title']['text'] ) { ?>
							<div class="cover-title align-<?php echo $cover['title']['align'] ? esc_attr($cover['title']['align']) : 'left'; ?>">
								<h1 <?php if ( $cover['title']['color'] ) echo 'style="color: '.esc_attr($cover['title']['color']).'"'; ?>><?php echo $cover['title']['text']; ?></h1>
							</div>
						<?php } ?>

						<?php if ( $cover['subtitle']['text'] ) { ?>
							<div class="cover-subtitle align-<?php echo $cover['subtitle']['align'] ? esc_attr($cover['subtitle']['align']) : 'right'; ?>">
								<h3 <?php if ( $cover['subtitle']['color'] ) echo 'style="color: '.esc_attr($cover['subtitle']['color']).'"'; ?>><?php echo $cover['subtitle']['text']; ?></h3>
							</div>
						<?php } ?>

						<?php if ( $cover['button'] ) { ?>
							<div class="cover-button align-<?php echo $cover['button']['align'] ? esc_attr($cover['button']['align']) : 'right'; ?>">
								<a href="<?php echo esc_attr($cover['button']['url']); ?>" class="button" style="background-color: <?php echo esc_attr($cover['button']['background']); ?>; color: <?php echo esc_attr($cover['button']['color']); ?>;"><?php echo $cover['button']['label']; ?></a>
							</div>
						<?php } ?>

					</div>
				<?php } ?>
				
			</div>
		</div>

		 <?php // if ( $cover['mobile_image']) echo '</div>'; ?>

	</div>
</header> <!-- /#header -->
