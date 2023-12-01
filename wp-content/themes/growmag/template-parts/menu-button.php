<?php
// Menu, sharing, search, and subscribe buttons
// See: menu.php
?>
<div id="menu-buttons">
	<div class="menu-button menu-button-mobile">
		<button id="mobile-button">
			<span class="mobile-text"><span class="mobile-hidden">Menu</span><span class="mobile-visible">Close</span></span>
			<span class="bar bar-1"></span>
			<span class="bar bar-2"></span>
			<span class="bar bar-3"></span>
		</button>
	</div>
	
	<?php /*
	<div class="menu-button menu-button-sharing">
		<?php ld_social_menu(); ?>
		<div class="toggle">sharing button</div>
	</div>
 	*/ ?>
	
	<div class="menu-button menu-button-search">
		<?php get_search_form(); ?>
		<div class="toggle">search button</div>
	</div>
	
	<div class="menu-button menu-button-subscribe">
		<a href="/shop">
			<div class="subscribe-popup">
				<div class="subscribe-popup-inner">
					<?php
					if ( $imageID = get_field( "subscribe_popup_image", "options" ) ) {
						?>
						<div class="subscribe-popup-left">
							<?php
							echo wp_get_attachment_image( $imageID, 'thumbnail-uncropped' );
							?>
						</div>
						<?php
					}
					?>
					<div class="subscribe-popup-right">
						<?php
						if ( $content = get_field( "subscribe_popup_content", "options" ) ) {
							echo $content;
						}
						?>
					</div>
					<div class="subscribe-popup-button">
						<?php
						echo get_field( "subscribe_popup_button", "options" ) ?: 'SUBSCRIBE NOW';
						?>
					</div>
				</div>
			</div>
			<div class="menu-button-text subscribe-text">SUBSCRIBE</div>
		</a>
	</div>
	
	<div class="menu-button menu-button-newsletter">
		<a href="/newsletter">
			<div class="menu-button-text">NEWSLETTER /<br>DIGITAL COPIES</div>
		</a>
	</div>
</div>
