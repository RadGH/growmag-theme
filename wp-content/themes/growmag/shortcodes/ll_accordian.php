<?php
/* 

Usage:

[ll_accordian title='Accordian Title' classes='classes added to accordian']

Accordian content, can include shortcodes

[/ll_accordian]

Options:
title: The title of the accordian, is shown even when the accordian is closed.
classes: A space seperated list of classes you'd like added to the accordian div.


*/

class ll_site_accordian {

	public static $count = 0;

	public static function doAccordian( $atts , $content = null) {
	    $a = shortcode_atts( array(
	    	'title' => '',
	        'classes' => ''
	    ), $atts );

		$html = "";

		if(self::$count == 0) {
			$html .= "<script>
				jQuery(function($) {					
					if($('.ll_site_accordian').length) {
						$('.ll_accordian_title').on('click touchstart', function(event) {
							event.preventDefault();
							$(this).parent().toggleClass('ll_accordian_closed').toggleClass('ll_accordian_open');
						});
					}
				});

			</script>";
		}

		self::$count++;

	    $html .= "<div class='ll_site_accordian ll_accordian_closed " . $a['classes'] . "'><a href='#null' class='ll_accordian_title'>" . $a['title'] .  "</a><div class='ll_accordian_content'>" . do_shortcode($content) . "</div></div>";
	    return $html;
	}

}



add_shortcode( 'll_accordian', 'll_site_accordian::doAccordian' );