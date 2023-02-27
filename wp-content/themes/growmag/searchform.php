<form role="search" method="GET" class="searchform searchform-widget clearfix" action="<?php echo esc_attr( site_url() ); ?>">
	<div class="searchform-inputwrapper sliding">
		<input type="text" name="s" class="input text" value="<?php echo empty( $_REQUEST['s'] ) ? '' : esc_attr( stripslashes( $_REQUEST['s'] ) ); ?>" placeholder="" />
		<?php //	<input type="submit" value="Search" class="input button submit search-button" /> ?>
	</div>
</form>