<?php
// Flyout menu
// Revealed by clicking the menu button.
// See: mobile-button.php
?>
<div id="mobile-menu-wrap">
<div id="mobile-menu-container">
	<div class="inside narrow">
		<div class="mobile-outer">
			<div class="mobile-inner">
				<?php
				gm_display_primary_menu();
				
				/*
				if ( $menu = ld_nav_menu( 'footer', 'departments' ) ) {
					echo '<h2>Categories</h2>';
					echo '<nav class="nav-menu nav-footer nav-departments">';
					echo $menu;
					echo '</nav>';
				}
				*/
				
				gm_display_secondary_menu();
				
				/*
				if ( $menu = ld_nav_menu( 'mobile', 'pages' ) ) {
					echo '<nav class="nav-menu nav-mobile nav-pages">';
					echo $menu;
					echo '<ul class="nav-login"><li><a href="/wp-admin/">Log in</a></li></ul>';
					echo '</nav>';
				}
				*/
				
				ld_social_menu();
				?>
			</div>
		</div>
	</div>
</div>
</div>