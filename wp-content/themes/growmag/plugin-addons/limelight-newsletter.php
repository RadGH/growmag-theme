<?php

// Add custom message next to newsletter submit buttons
function ac_custom_submit_message() {
	?>
	<div class="newsletter-submit-field">
		<input class="submit button" type="submit" value="<?php echo apply_filters('newsletter_submit_text', "Subscribe"); ?>">
		<span class="newsletter-submit-tip">We won't share or sell your information.</span>
	</div>
	<?php

	return true;
}
add_filter( 'newsletter_hide_submit', 'ac_custom_submit_message' );