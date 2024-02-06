<?php
/**
 * @global RS_Downloads_Item  $download         The download item being displayed
 * @global WP_Post           $post             The post object
 * @global bool              $display_images   Whether to show images
 * @global bool              $display_content  Whether to show content
 * @global bool              $display_button   Whether to show the button
 */

$display_title = is_singular( 'rs_download') ? false : true;

$title   = $display_title   ? $download->get_title()          : false;
$image   = $display_images  ? $download->get_featured_image() : false;
$content = $display_content ? $download->get_content()        : false;
$button  = $display_button  ? $download->get_button()         : false;

$type = $download->get_type();
$url = $download->get_url();

$classes = array();
$classes[] = 'rs-download-item';
$classes[] = 'id-' . $download->get_id();
if ( $display_images ) $classes[] = $image ? 'has-image' : 'no-image';

$link_start = '<a href="'. esc_attr($url) .'" class="rs-download-link" data-download-id="'. $download->get_id() .'" target="_blank">';
$link_end = '</a>';
?>

<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
	
	<div class="item--image">
		<?php if ( $image ) echo $link_start, $image, $link_end; ?>
	</div>
	
	<div class="item--details">
		
		<?php if ( $title ) { ?>
			<h3 class="item--title"><?php echo $link_start, $title, $link_end; ?></h3>
		<?php } ?>
		
		<?php if ( $content ) { ?>
			<div class="item--content">
				<?php echo $content; ?>
			</div>
		<?php } ?>
		
		<?php if ( $button ) { ?>
			<div class="item--button">
				<?php echo $button; ?>
			</div>
		<?php } ?>
		
	</div>
	
</div>
