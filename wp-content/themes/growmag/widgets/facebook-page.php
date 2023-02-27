<?php

class facebookWidget extends WP_Widget
{

	public function __construct() {
		parent::__construct( 'facebookWidget', 'Grow Magazine Facebook Page', array( 'description' => 'Displays Facebook widget for Eugene Magazine.' ) );
	}

	public function widget( $widget, $instance ) {
		extract( $widget );
		echo $widget['before_widget'];
		?>
		<div id="fb-root"></div>
		<script>(function ( d, s, id ) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if ( d.getElementById(id) ) return;
				js = d.createElement(s);
				js.id = id;
				js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		</script>
		<div class="fb-page" data-href="https://www.facebook.com/growmag/" data-width="300" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true">
			<blockquote cite="https://www.facebook.com/growmag/" class="fb-xfbml-parse-ignore">
				<a href="https://www.facebook.com/growmag/">Grow Magazine</a></blockquote>
		</div>
		<?php
		echo $widget['after_widget'];
	}
}

function facebookWidget_register_widget() {
	register_widget( 'facebookWidget' );
}

add_action( 'widgets_init', 'facebookWidget_register_widget' );