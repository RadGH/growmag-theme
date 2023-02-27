<?php

/**
 * TRUE if on a digital issue page, or the digital issues archive page
 *
 * @return bool
 */
function is_digital_issues_page() {
	if ( is_admin() ) return false;
	if ( is_singular('digital-issue') ) return true;
	if ( is_post_type_archive('digital-issue') ) return true;
	return false;
}

/**
 * TRUE when editing a digital issue or viewing the digital issues listing page
 *
 * @return bool
 */
function is_digital_issues_admin_page() {
	if ( !is_admin() ) return false;
	if ( function_exists('acf_is_screen') ) {
		if ( acf_is_screen('edit-digital-issue') ) return true;
		if ( acf_is_screen('digital-issue') ) return true;
		if ( acf_is_screen('digital-issue_page_acf-options-settings') ) return true;
	}
	return false;
}