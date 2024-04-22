jQuery(function() {
	var $popup, $ouibounce;

	var get_timestamp = function() {
		// js timestamps are in microseconds. convert to regular seconds
		return Date.now() / 1000;
	};

	var was_popup_displayed = function() {
		var ts_last_displayed = localStorage.getItem('leavingsite-popup-closed') || 0;
		var ts_30_days_ago = get_timestamp() - (60 * 60 * 24 * 7); // seconds * minutes * hours * days = 30 days in seconds

		// last displayed: 1666128875.026
		// 30 days ago:    1663536875.026

		// Check if popup was displayed in the last 30 days
		return ( ts_last_displayed > ts_30_days_ago );
	};

	var remember_popup_displayed = function() {
		localStorage.setItem('leavingsite-popup-closed', get_timestamp());
	};

	// Do not show the popup more than once (unless localstorage is cleared)
	if ( was_popup_displayed() ) return;

	$popup = jQuery('#leavingsite-popup');

	/*
	if ( lsp_settings.title ) {
		$popup.find('.modal-title h3').html( lsp_settings.title );
	}else{
		$popup.find('.modal-title').css('display', 'none');
	}

	if ( lsp_settings.content ) {
		$popup.find('.modal-body').html( lsp_settings.content );
	}else{
		$popup.find('.modal-body').css('display', 'none');
	}

	if ( lsp_settings.close_text ) {
		$popup.find('.modal-footer a').html( lsp_settings.close_text );
	}
	*/

	var toggle_visibility = function( visible ) {
		$popup
			.toggleClass('lsp-visible', visible)
			.toggleClass('lsp-hidden', ! visible)
			.attr('hidden', ! visible)
			.css('display', visible ? '' : 'none');
	};

	var show_popup = function() {
		console.log('LSP Visible');

		toggle_visibility( true );
	};

	var close_ouibounce = function() {
		console.log('LSP Hidden');

		// Hide popup
		toggle_visibility( false );

		// Remember popup was displayed to not show again for some time
		remember_popup_displayed();

		// Remember that it was closed, serverside
		jQuery.ajax({
			url: '',
			data: {
				'lsp_closed': 1
			}
		});
	};

	var waitingtimeout = setInterval(function() {
		if ( typeof window.ouibounce !== "function" ) return;
		clearInterval( waitingtimeout );

		// if you want to use the 'fire' or 'disable' fn,
		// you need to save OuiBounce to an object

		$ouibounce = window.ouibounce(document.getElementById('leavingsite-popup'), {
			aggressive: true,
			timer: 0,
			cookieExpire: parseInt(lsp_settings.remember_duration),
			cookieName: 'lsp_closed',
			sitewide: true,
			callback: function() {

				// Just a double check to make sure the popup does not get displayed if it was closed before
				if ( was_popup_displayed() ) {
					close_ouibounce();
					return;
				}

				// Remember popup was displayed to not show again for some time
				remember_popup_displayed();

				// Close when clicking on the background, or a link with the class "modal-close"
				$popup.on('click', '.modal-footer a, .modal-close', function() {
					close_ouibounce();
					return false;
				});

				// Close when form submitted
				$popup.on('submit', 'form', function() {
					close_ouibounce();
				});

				// Show the popup
				show_popup();

				// Don't show alert on tab close anymore
				jQuery(window).unbind('beforeunload', leave_native_alert);
			}
		});

		var leave_native_alert = function() {
			$ouibounce.fire();
			$ouibounce.disable();
			return lsp_settings.ask_to_stay_message;
		};

		if ( lsp_settings.ask_to_stay ) {
			jQuery(window).bind('beforeunload', leave_native_alert);
		}

	}, 1000);
});