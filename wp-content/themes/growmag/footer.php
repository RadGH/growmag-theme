<?php
// Close wrapper
gm_page_wrapper_end();
?>

<footer id="footer">
    <div class="inside narrow">
        <div class="footer-left">
            <?php
            echo '<h2 class="eugmaglogo"><a href="/">Eugene Magazine</a></h2>';

            gm_display_secondary_menu();
			
			
//             if ( $menu = ld_nav_menu( 'footer', 'pages' ) ) {
//                 echo '<nav class="nav-menu nav-footer nav-pages">';
//                 echo $menu;
//                 echo '<ul class="nav-login"><li><a href="https://growmag.com/wp-login.php?redirect_to=https://growmag.com/wp-admin/&reauth=1">Log in</a></li></ul>';
//                 echo '</nav>';
//             }
			
            
            ld_social_menu();
            ?>
        </div>
        <div class="footer-center">
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
            
            /*
            echo '<h2>Categories</h2>';
            echo '<nav class="nav-menu nav-footer nav-departments">';
            dynamic_sidebar( 'footer_center' );
            echo '</nav>';
			*/
            ?>
        </div>
        <div class="footer-right">
            <?php
                echo do_shortcode('[ad location="Footer"]');
            ?>

            <?php
            /*
            if ( $copyright = get_field('copyright_text', 'options') ) {
                echo '<div class="copyright">';
                echo do_shortcode( wpautop( $copyright ) );
                echo '</div>';
            }
            */

            dynamic_sidebar( 'footer_right' );
            ?>
        </div>
    </div>
</footer> <!-- /#footer -->

<?php wp_footer(); ?>
</body>
</html>