jQuery(function() {
	if ( pagenow == 'page' ) init_custom_template();
});

function init_custom_template() {
	var $template = jQuery('#page_template');
	if ( $template.length < 1 ) return;

	var $editor = jQuery('#postdivrich');
	if ( $editor.length < 1 ) $editor = jQuery('#post-body-content').find('.postarea');
	if ( $editor.length < 1 ) return;

	$template.on('change', function(e) {
		if ( jQuery(this).val() == 'templates/custom-layout.php' ) {
			$editor.css('display', 'none');
		}else{
			$editor.css('display', 'block');
		}
	}).trigger('change');
}