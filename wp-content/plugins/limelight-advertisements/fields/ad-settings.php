<?php
if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array (
		'key' => 'group_563bc732283a8',
		'title' => 'Ad Settings',
		'fields' => array (
			array (
				'key' => 'field_563bc77f31dd6',
				'label' => 'Type of Ad',
				'name' => 'ad-type',
				'type' => 'radio',
				'instructions' => 'If multiple image ads share the same location, one will be displayed at random. Embedded ad codes cannot share a location and cannot track impressions.',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array (
					'image' => 'Image (upload)',
					'external_image' => 'Image (external)',
					'embed' => 'Embed code',
				),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => '',
				'layout' => 'vertical',
				'allow_null' => 0,
			),
			array (
				'key' => 'field_563bc75431dd3',
				'label' => 'Image Upload',
				'name' => 'ad-image',
				'type' => 'image',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_563bc77f31dd6',
							'operator' => '==',
							'value' => 'image',
						),
					),
				),
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'id',
				'preview_size' => 'full',
				'library' => 'all',
				'min_width' => '',
				'min_height' => '',
				'min_size' => '',
				'max_width' => '',
				'max_height' => '',
				'max_size' => '',
				'mime_types' => '',
			),
			array (
				'key' => 'field_5723ab06302c2',
				'label' => 'External Image URL',
				'name' => 'ad-external-image',
				'type' => 'url',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_563bc77f31dd6',
							'operator' => '==',
							'value' => 'external_image',
						),
					),
				),
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
			),
			array (
				'key' => 'field_563bc76831dd5',
				'label' => 'Ad Link',
				'name' => 'ad-url',
				'type' => 'url',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_563bc77f31dd6',
							'operator' => '==',
							'value' => 'image',
						),
					),
					array (
						array (
							'field' => 'field_563bc77f31dd6',
							'operator' => '==',
							'value' => 'external_image',
						),
					),
				),
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
			),
			array (
				'key' => 'field_563bc7a631dd7',
				'label' => 'Ad Code',
				'name' => 'ad-embed-code',
				'type' => 'textarea',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_563bc77f31dd6',
							'operator' => '==',
							'value' => 'embed',
						),
					),
				),
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
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'ld_ad',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 1,
		'description' => '',
	));

endif;