<?php

class twitterWidget extends WP_Widget
{

	public function __construct() {
		parent::__construct( 'twitterWidget', 'Eugene Magazine Twitter Feed', array( 'description' => 'Displays Twitter posts for Eugene Magazine.' ) );
	}

	public function widget( $widget, $instance ) {
		extract( $widget );
		echo $widget['before_widget'];
		?>
		<a class="twitter-timeline" data-width="300" data-height="550" href="https://twitter.com/officialgrowmag">Tweets by officialgrowmag</a>
		<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
		<?php
		echo $widget['after_widget'];
	}
}

function twitterWidget_register_widget() {
	register_widget( 'twitterWidget' );
}

add_action( 'widgets_init', 'twitterWidget_register_widget' );