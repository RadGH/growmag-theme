<?php

// https://growmag.com/?rad_20224518_24539
function rad_20224518_24539() {
	setcookie( 'lsp-closed', '', time()-3600, '/' );
	unset($_COOKIE['lsp-closed']);
	echo 'cookie cleared';
	?>
	<script type="text/javascript">
		document.cookie = "wordpress_test_cookie=WP%20Cookie%20check; tk_ai=woo%3AFdjo1yML%2FXUB4hzlNHQyT0OD; wp_lang=en_US; lsp_closed=true; __stripe_mid=65a19775-bc17-4c15-b395-f8f012ec235becd352; wp-settings-1521=libraryContent%3Dbrowse%26ampeditor%3Dtinymce; wp-settings-time-1521=1666127878; __stripe_sid=a8bba7ca-1ac4-4db5-8ce7-9c3434972f948336a7; ";
		localStorage.setItem('leavingsite-popup-closed', 0);
		document.write('localstorage and local cookies cleared');
	</script>
	<?php
	exit;
}
if ( isset($_GET['rad_20224518_24539']) ) add_action( 'init', 'rad_20224518_24539' );

function ld_leavingsite_has_closed_popup() {
	if ( isset($_COOKIE['lsp-closed']) ) {
		return true;
	}

	return false;
}

function ld_leavingsite_remember_closed_popup() {
	if ( isset($_REQUEST['lsp_closed']) ) {
		$settings = ld_leavingsite_get_settings();
		setcookie('lsp-closed', 1, strtotime('+' . absint($settings['remember_duration']) . ' days'), '/');
		$_COOKIE['lsp-closed'] = 1;
		exit;
	}
	if ( isset($_REQUEST['lsp_reset_cookie']) ) {
		setcookie( 'lsp-closed', '', time()-3600, '/' );
		unset($_COOKIE['lsp-closed']);
		exit;
	}
}
add_action( 'template_redirect', 'ld_leavingsite_remember_closed_popup' );

function ld_leavingsite_get_settings() {
	static $settings = null;

	if ( $settings === null ) {
		$settings = array(
			'title'               => get_field('lsp_title', 'options'),
			'content'             => do_shortcode( get_field('lsp_content', 'options') ),
			'close_text'          => get_field('lsp_close_text', 'options'),
			'remember'            => get_field('lsp_remember', 'options'),
			'remember_duration'   => get_field('lsp_remember_duration', 'options'),
			'ask_to_stay'         => get_field('lsp_ask_to_stay', 'options'),
			'ask_to_stay_message' => get_field('lsp_ask_to_stay_message', 'options'),
		);

		if ( empty($settings['remember_duration']) ) $settings['remember_duration'] = 0;
		if ( empty($settings['ask_to_stay_message']) ) $settings['ask_to_stay'] = false; // No message? Disable the popup
	}

	return $settings;
}

function ld_leavingsite_print_scripts() {
	if ( is_admin() ) return;
	if ( ld_leavingsite_has_closed_popup() ) return;

	$settings = ld_leavingsite_get_settings();

	?>
	<script type="text/javascript">
		var lsp_settings = <?php echo json_encode($settings); ?>;
	</script>
	<?php
}
add_action( 'wp_print_scripts', 'ld_leavingsite_print_scripts' );


function ld_leavingsite_modal_footer() {
	if ( ld_leavingsite_has_closed_popup() ) return;

	$settings = ld_leavingsite_get_settings();
	
	?>
	<div id="leavingsite-popup">
		<div class="underlay"></div>
		<div class="modal">
			<?php if ( $settings['title'] ) { ?>
			<div class="modal-title">
				<h3><?php echo $settings['title']; ?></h3>
			</div>
			<?php } ?>

			<?php if ( $settings['content'] ) { ?>
			<div class="modal-body">
				<?php echo do_shortcode($settings['content']); ?>
			</div>
			<?php } ?>

			<?php if ( $settings['close_text'] ) { ?>
			<div class="modal-footer">
				<p><a href="#close" class="modal-close"><?php echo $settings['close_text'] ? $settings['close_text'] : 'No thanks'; ?></a></p>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'ld_leavingsite_modal_footer', 3 );