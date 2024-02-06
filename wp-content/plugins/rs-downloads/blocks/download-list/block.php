<?php
/**
 * @see https://www.advancedcustomfields.com/resources/acf-blocks-key-concepts/
 * @global array $block      The block settings and attributes.
 * @global string $content   The block inner HTML (empty).
 * @global bool $is_preview  True during backend preview render.
 * @global array $context    The context provided to the block by the post or its parent block.
 */

$post_id = $context['postId'] ?? false;
$post_type = $context['postType'] ?? false;

// Classes
$classes = array();
$classes[] = 'rs-downloads-list-block';

// Custom classes
if ( $block['className'] ) {
	$classes[] = $block['className'];
}

// Alignment
$align = $block['align'] ?? 'full';
if ( $align ) $classes[] = 'align' . $align;

// Background color
if ( !empty($block['backgroundColor']) ) {
	$classes[] = 'has-background';
	$classes[] = 'has-'. $block['backgroundColor'] .'-background-color';
}

// Gradient background
if ( !empty($block['gradient']) ) {
	$classes[] = 'has-background';
	$classes[] = 'has-'. $block['gradient'] .'-gradient-background';
}

// Text color
if ( !empty($block['textColor']) ) {
	$classes[] = 'has-text-color';
	$classes[] = 'has-'. $block['textColor'] .'-color';
}

// Get ACF fields
$layout          = get_field('layout');
$display_images  = get_field('display_images');
$display_content = get_field('display_content');
$display_button  = get_field('display_button');

// Validate options
if ( $layout !== 'grid' ) $layout = 'list';
if ( $display_images === null )  $display_images = true;
if ( $display_content === null ) $display_content = true;
if ( $display_button === null )  $display_button = true;

$classes[] = 'layout-' . $layout; // this class is also applied to the shortcode

?>
<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" <?php if ( !empty($block['anchor']) ) echo 'id="'. esc_attr($block['anchor']) .'"'; ?>>
	
	<?php
	// Display the list using the shortcode [rs_download_list]
	echo shortcode_rs_download_list(array(
		'layout'          => $layout,
		'display_images'  => $display_images,
		'display_content' => $display_content,
		'display_button'  => $display_button,
	));
	?>
	
</div>