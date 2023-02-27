<?php
function ld_add_editor_styles() {
	add_editor_style( get_template_directory_uri() . '/includes/css/style.css' );
	add_editor_style( get_template_directory_uri() . '/includes/css/grow-custom.css' );
	add_editor_style( get_template_directory_uri() . '/includes/admin/editor-styles.css' );
}
add_action( 'admin_init', 'ld_add_editor_styles' );

function ld_custom_visual_editor_styles( $buttons ) {
	$first = array_shift( $buttons );
	array_unshift( $buttons, 'styleselect' );
	array_unshift( $buttons, $first );

	return $buttons;
}
add_filter( 'mce_buttons_2', 'ld_custom_visual_editor_styles' );


function ld_custom_visual_editor_style_formats( $init_array ) {
	// Define the style_formats array
	$style_formats = array(

		array(
			'title'   => 'Button',
			'selector'  => 'a',
			'classes' => 'button',
		),

		array(
			'title'   => 'Large Button',
			'selector'  => 'a.button',
			'classes' => 'button-large',
		),

		array(
			'title'   => 'Alternate Button',
			'selector'  => 'a.button',
			'classes' => 'alt',
		),
	);

	// Insert the array, JSON ENCODED, into 'style_formats'
	$init_array['style_formats'] = json_encode( $style_formats );

	return $init_array;

}
add_filter( 'tiny_mce_before_init', 'ld_custom_visual_editor_style_formats' );
