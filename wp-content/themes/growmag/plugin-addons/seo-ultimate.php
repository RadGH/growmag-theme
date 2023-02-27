<?php
/*

This script will offer suggestions to optimize SEO Ultimate, by disabling optional modules that can slow the website down.

*/

/*
Functions:

	ld_seo_list_bad_modules()
		An array of "bad" modules - or rather, modules that have little to do with SEO or have a huge performance tradeoff.

	ld_seo_suggest_action()
		Suggests to disable unnecessary modules with SEO Ultimate, if it is installed.

	ld_seo_disable_modules()
		Triggered by a link from ld_seo_suggest_action(), this disables the bad modules

*/

remove_action( 'wp_head', 'ld_seo_meta', 3 );

function ld_seo_list_bad_modules() {
	static $bad_modules = array();
	if ( count( $bad_modules ) < 1 ) {
		$bad_modules = explode( ",", "canonical,author-links,user-code,autolinks,files,internal-link-aliases,linkbox,more-links,rich-snippets,widgets,webmaster-verify,sds-blog" );
	}

	return $bad_modules;
}

function ld_seo_suggest_action() {
	global $seo_ultimate;
	if ( !isset( $seo_ultimate->modules ) || count( $seo_ultimate->modules ) < 1 ) {
		return;
	}
	if ( !current_user_can( 'administrator' ) ) {
		return;
	}
	if ( get_option( 'ldseo_sugggestions_closed' ) == 1 ) {
		return;
	}

	// Clicked button to hide this message?
	if ( isset( $_REQUEST['hide_seoultimate_suggest'] ) ) {
		update_option( 'ldseo_sugggestions_closed', 1 );

		return;
	}

	// Clicked button to disable modules, then redirected here?
	if ( isset( $_REQUEST['ldseo_disabled_count'] ) ) {
		?>
		<div class="updated">
			<p><strong>SEO Ultimate:</strong> <?php echo absint( $_REQUEST['ldseo_disabled_count'] ); ?> optional module(s) have been deactivated.</p>
		</div>
		<?php
		return;
	}

	$seo_settings = get_option( 'seo_ultimate' );
	if ( !isset( $seo_settings['modules'] ) ) {
		return;
	}

	$modules = $seo_settings['modules'];

	$checkfor = ld_seo_list_bad_modules();
	$enabled = array();
	$not_enabled = array();
	$module_popup = "The following modules are not recommended:\n";

	foreach ( $checkfor as $key ) {
		if ( isset( $seo_ultimate->modules[$key] ) && $modules[$key] != -10 ) {
			$title = $seo_ultimate->modules[$key]->get_module_title();
			$enabled[$key] = $title;
			$module_popup .= "\n &bull; " . $title;
		}else{
			$not_enabled[$key] = $key;
		}
	}

	if ( count( $enabled ) < 1 ) {
		return;
	}

	// Display the notification:
	$disable_notification_url = add_query_arg( array( 'hide_seoultimate_suggest' => 1 ), admin_url( '/admin.php?page=seo' ) );
	$disable_modules_url = add_query_arg( array( 'ldseo_disable_modules' => 1 ), admin_url( '/admin.php?page=seo' ) );
	?>
	<div class="error">
		<p>
			<strong>Theme Suggestion:</strong> The SEO Ultimate plugin has <?php echo count( $seo_ultimate->modules ); ?> active modules.
			<a href="#" onclick="alert(<?php echo esc_attr( json_encode( $module_popup ) ); ?>); return false;"><?php echo count( $enabled ); ?> of these modules are optional</a> and should be disabled to improve performance.
		</p>

		<p>
			<a href="<?php echo esc_attr( $disable_modules_url ); ?>" class="button button-primary" style="text-decoration: none;">Disable Optional Modules</a>
			<a href="<?php echo esc_attr( $disable_notification_url ); ?>" class="button button-secondary" style="text-decoration: none;">Hide this message</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'ld_seo_suggest_action' );

function ld_seo_disable_modules() {
	global $seo_ultimate;
	if ( !isset( $seo_ultimate->modules ) || count( $seo_ultimate->modules ) < 1 ) {
		return;
	}
	if ( !current_user_can( 'administrator' ) ) {
		return;
	}
	if ( !isset( $_REQUEST['ldseo_disable_modules'] ) ) {
		return;
	}

	$seo_settings = get_option( 'seo_ultimate' );
	if ( !isset( $seo_settings['modules'] ) ) {
		return;
	}


	$checkfor = ld_seo_list_bad_modules();
	$modules = $seo_settings['modules'];
	$count_disabled = 0;

	foreach ( $checkfor as $key ) {
		if ( isset( $seo_ultimate->modules[$key] ) && $modules[$key] != -10 ) {
			$modules[$key] = -10;
			$count_disabled += 1;
		}
	}

	$seo_settings['modules'] = $modules;

	if ( $count_disabled < 1 ) {
		wp_die( '<h2>Deactivation Failed</h2><p>Sorry, no modules could be deactivated. Please go back and check if the modules have been deactivated already.</p>' );
		exit;
	}else{
		update_option( 'seo_ultimate', $seo_settings );
		wp_redirect( add_query_arg( array( 'ldseo_disabled_count' => $count_disabled ), admin_url( 'admin.php?page=seo' ) ) );
		exit;
	}
}
add_action( 'admin_init', 'ld_seo_disable_modules' );