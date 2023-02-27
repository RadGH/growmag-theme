<?php
/*
Number of columns and width of each column is detected automatically, so set as many as you'd like.

Only supports a single set of columns per page.

Usage:
[ll_column]
Column 1
[/ll_column]
[ll_column]
Column 2
[/ll_column]
[ll_column]
Column 3
[/ll_column]

Optional Usage:
Width: Set the minimum width of each column, forces the columns to stack ontop of eachother once a single column reaches this width.
Classes: A space seperated list of classes you'd like added to each column.

You only have to set the options on the first column, they will automatically be applied to the rest of the columns.

[ll_column width='200' classes='firstclass secondclass']
Column 1
[/ll_column]
[ll_column]
Column 2
[/ll_column]


*/


class ll_site_columns {

	public static $count = 0;

	public static function renderColumn( $atts , $content = null) {
	    $a = shortcode_atts( array(
	        'classes' => '',
	        'width' => 0
	    ), $atts );

		$html = "";

		if($a['width'] == 0) {
			$a['width'] = 200;
		} 

		if(self::$count === 0) {
			$html .= "<script>
				jQuery(document).on('ready', function($) {
					var $ = jQuery;	
					var columnWidth = Math.floor(1/$('.ll_site_column').length*100)-6;

					$(window).on('resize', function(event) {
						var minColumnWidth = " . $a['width'] . ";3
						var screenWidth = $('.ll_site_column').parent().width()/$('.ll_site_column').length;

						if(screenWidth < minColumnWidth) {
							$('.ll_site_column').css('width','100%');
						} else {
							$('.ll_site_column').css('width',columnWidth + '%');							
						}

					});

					$(window).trigger('resize');
				});

			</script>";

			$html .= "<style>
				.ll_site_column {
					display:inline-block;
					vertical-align:top;
					margin-left:3%;
					margin-right:3%;
				}
			</style>";
		}

		self::$count++;

	    $html .= "<div class='ll_site_column " . $a['classes'] . "'>" . do_shortcode($content) . "</div>";
	    return $html;
	}

}



add_shortcode( 'll_column', 'll_site_columns::renderColumn' );