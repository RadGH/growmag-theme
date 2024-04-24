<?php

function di_enqueue_mailchimp_assets() {
	
	// Only apply mailchimp protection to digital issue archive
	if ( ! is_post_type_archive( 'digital-issue' ) ) return;
	
	// Get settings from Mailchimp tab
	$mailchimp_settings = get_field('mailchimp', 'digital_issues');
	
	// Only apply if required in the settings page
	if ( ! $mailchimp_settings['required'] ) return;
	
	$url = DI_URL;
	$path = DI_PATH;
	$v = filemtime("$path/assets/mailchimp.js");
	
	// Enqueue Mailchimp script
	wp_enqueue_script( 'digital-issues-mailchimp', "{$url}/assets/mailchimp.js?em={$v}", array( 'jquery' ), null );
	
	// Add data to the script
	$settings = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'mailchimp' ),
	);
	
	wp_localize_script( 'digital-issues-mailchimp', 'digital_issues_mailchimp', $settings );
	
	add_action( 'wp_print_footer_scripts', 'di_print_mailchimp_popup', 20 );
}
add_action( 'wp_enqueue_scripts', 'di_enqueue_mailchimp_assets', 20 );


// Print Mailchimp popup, hooked during di_enqueue_mailchimp_assets
function di_print_mailchimp_popup() {
	// Get settings from Mailchimp tab
	$mailchimp_settings = get_field('mailchimp', 'digital_issues');
	
	// Add Gravity Form popup to the page, hidden by default
	?>
	<div class="rs-di-popup-wrapper" style="display:none;">
		<div class="popup-content">
			<div class="popup-header">
				<a href="#" class="popup--close" title="Close popup">
					&times;
				</a>
			</div>
			<div class="popup-body">
				<div class="popup-inner--content">
					<?php echo $mailchimp_settings['subscription_message']; ?>
				</div>
				<div class="popup-inner--error-message" style="display: none;"></div>
				<div class="popup-inner--form">
					<form action="" method="" id="rs-di-email-form">
						<input type="hidden" name="action" value="verify_mailchimp_subscription">
						<input type="hidden" name="nonce" value="">
						<p><label for="rs-di-email-input">Email Address:</label>
							<input type="email" name="email" id="rs-di-email-input"></p>
						<p class="submit"><input type="submit" value="Subscribe" class="button button-primary"></p>
					</form>
				</div>
				<div class="popup-inner--disclaimer">
					<?php echo $mailchimp_settings['subscription_disclaimer']; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}


// Add Mailchimp AJAX handler
function di_mailchimp_ajax_handler() {
	
	// Check nonce
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'mailchimp' ) ) {
		// If you see this message, it might be that a javascript error is preventing ajax from sending the nonce
		wp_send_json_error( 'Invalid nonce' );
	}
	
	// Check email
	if ( ! isset( $_POST['email'] ) || ! is_email( $_POST['email'] ) ) {
		wp_send_json_error( 'Invalid email' );
	}
	
	// Get settings from Mailchimp tab
	$mailchimp_settings = get_field('mailchimp', 'digital_issues');
	
	$api_key = $mailchimp_settings['api_key'] ?? false;
	$primary_list_id = $mailchimp_settings['list_id'] ?? false;
	$secondary_list_ids = $mailchimp_settings['secondary_list_ids'] ?? false;
	$server_code = $mailchimp_settings['server_code'] ?? false;
	$signup_form_link = $mailchimp_settings['signup_form_link'] ?? false;
	
	// Check if Mailchimp credentials are valid
	if ( ! $api_key || ! $primary_list_id || ! $server_code ) {
		wp_send_json_error( 'Mailchimp API has not been configured under Settings' );
	}
	
	// Combine lists to search in one array
	$list_ids = array( $primary_list_id );
	
	if ( $secondary_list_ids ) {
		$secondary_list_ids = explode(' ', $secondary_list_ids);
		$list_ids = array_merge( $list_ids, $secondary_list_ids );
	}
	
	// Get email
	$email = sanitize_email( $_POST['email'] );
	
	// Check each list
	$api = new DI_Mailchimp_API( $api_key, $primary_list_id, $server_code );
	$found_in_list = false;
	$pending_list = false;
	$unsubscribed_list = false;
	
	foreach( $list_ids as $list_id ) {
		$subscribed = $api->is_email_subscribed( $email, $list_id );
		
		if ( $subscribed === true ) {
			$found_in_list = true;
			break;
		}else if ( is_string( $subscribed ) ) {
			
			// Check why the user is not subscribed to the primary list:
			// Are they pending, or did they unsubscribe?
			if ( $primary_list_id == $list_id ) {
				if ( $subscribed === 'pending' ) {
					$pending_list = $list_id;
				}else if ( $subscribed === 'unsubscribe' ) {
					$unsubscribed_list = $list_id;
				}
			}
		}
	}
	
	if ( $found_in_list ) {
		
		// Already subscribed. Send a success message.
		// Success
		wp_send_json_success('You are subscribed to our newsletter');
		
	}else if ( $pending_list ) {
		
		// Subscription pending
		// When a signup is pending, show a message that they need to activate the subscription.
		wp_send_json_error( 'Your newsletter subscription is pending. Please activate your subscription by clicking the verification link sent in an email.' );
	
	}else if ( $unsubscribed_list ) {
		
		// Unsubscribed
		// When a user unsubscribes, the only way to resubscribe is through a form directly on Mailchimp.
		if ( $signup_form_link ) {
			wp_send_json_error( 'You previously unsubscribed from our newsletter. If you would like to resubscribe, please use <a href="' . esc_url( $signup_form_link ) . '">this signup form</a> instead.' );
		}else{
			wp_send_json_error( 'You previously unsubscribed from our newsletter. Please contact us if you would like to resubscribe.' );
		}
		
	}else{
		
		// Not subscribed
		// Attempt to subscribe the user to the primary list
		$success = $api->subscribe_email( $email, $primary_list_id );

		if ( $success ) {
			// Success
			wp_send_json_success('You have been successfully subscribed to our newsletter');
		}else{
			$error = $api->get_last_error();
			wp_send_json_error( 'You could not be subscribed to our newsletter. Error details: ' . $error['response_message'] );
		}
	}
}
add_action( 'wp_ajax_verify_mailchimp_subscription', 'di_mailchimp_ajax_handler' );
add_action( 'wp_ajax_nopriv_verify_mailchimp_subscription', 'di_mailchimp_ajax_handler' );
