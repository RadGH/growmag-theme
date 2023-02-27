<?php

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
'key' => 'group_56cce6cdec5f8',
'title' => 'Debugging',
'fields' => array (
array (
'key' => 'field_56cce8faf2315',
'label' => 'Disable Ads',
'name' => 'ld_ads_disable',
'type' => 'true_false',
'instructions' => '',
'required' => 0,
'conditional_logic' => 0,
'wrapper' => array (
'width' => '',
'class' => '',
'id' => '',
),
'message' => 'Disable advertisements. Admins will get a reminder that they are disabled.',
'default_value' => 0,
),
),
'location' => array (
array (
array (
'param' => 'options_page',
'operator' => '==',
'value' => 'ld-ad-settings',
),
),
),
'menu_order' => 50,
'position' => 'normal',
'style' => 'default',
'label_placement' => 'left',
'instruction_placement' => 'label',
'hide_on_screen' => '',
'active' => 1,
'description' => '',
));

endif;