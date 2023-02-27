<?php
if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array (
		'key' => 'group_5625563c3ca0b',
		'title' => 'Tracking Code',
		'fields' => array (
			array (
				'key' => 'field_5625594880400',
				'label' => 'Page-Specific Tracking Codes',
				'name' => 'page-tracking-codes',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'min' => '',
				'max' => 1,
				'layout' => 'block',
				'button_label' => 'Enable Tracking',
				'sub_fields' => array (
					array (
						'key' => 'field_562556a5c8b3f',
						'label' => 'Head',
						'name' => 'head',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => 4,
						'new_lines' => '',
						'readonly' => 0,
						'disabled' => 0,
					),
					array (
						'key' => 'field_5625578541131',
						'label' => 'Body',
						'name' => 'body',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => 4,
						'new_lines' => '',
						'readonly' => 0,
						'disabled' => 0,
					),
				),
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'page',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'side',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 1,
		'description' => '',
	));

endif;