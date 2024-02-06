(function() {

	// ----------------------
	// Variables and Settings
	// ----------------------

	let o = this;

	// Get settings, provided by enqueue.php
	const s = window.rs_downloads_settings || {};

	// Get settings for Gravity Forms integration
	//     @type  bool          $enabled
	if ( ! s.form_settings ) s.form_settings = {};

	const form_settings = {
		enabled: s.form_settings.enabled || false
	};

	// Get query args to be automatically removed from the URL
	const remove_query_args = s.remove_query_args || [];

	// Elements to be used by the form and popup
	let $popup = false;
	let $form = false;
	let $submit = false;

	// The cached entry to be loaded in and stored in the browser session
	o.cached_entry = false;

	// -----------------
	// Initialization functions
	// -----------------

	// Called as soon as this file is loaded:
	o.on_init = function() {

		// Loads data from the browser URL (query args)
		o.load_active_query_args();

		// Removes query args from the browser URL, without reloading the page.
		o.remove_active_query_args();

	};

	// Called after the document is ready:
	o.on_ready = function() {

		// Load elements from the page into variables
		o.load_elements();

		// Change all download links to include the cached key and entry ID
		o.change_download_links();

		// Set up the form and popup
		o.setup_popup_form();

	};

	// -----------------
	// Utility Functions
	// -----------------

	// Checks if a user can download an item
	o.can_download = function() {
		let entry = o.get_cached_entry();
		return o.validate_entry_time( entry );
	};

	// Check if the entry has expired (if expiration feature has been enabled)
	o.validate_entry_time = function( entry ) {
		// Entry must be passed as a parameter, and must have key and entry_id properties
		if ( ! entry ) return false;
		if ( ! entry.key || ! entry.entry_id ) return false;

		// If expiration is disabled in the settings, the entry is always valid
		if ( ! form_settings.entry_expiration_enabled ) return true;

		// Get expiration time in minutes, then convert to seconds
		let expiration_minutes = parseInt( form_settings.entry_expiration );
		let expiration_seconds = expiration_minutes * 60;

		// Get the entry time and current time to compare
		let stored_time = parseInt( entry.timestamp );
		let current_time = Date.now();

		let diff_in_seconds = (current_time - stored_time) / 1000;

		if ( diff_in_seconds > expiration_seconds ) {

			// The entry has expired, so remove it
			console.log( 'Previous download entry expired ' + (diff_in_seconds - expiration_seconds) + ' seconds ago.', entry );
			o.remove_cached_entry();
			return false;

		}

		return true;
	};

	// Removes the cached entry from the browser session
	o.remove_cached_entry = function() {
		localStorage.removeItem( 'rsd_entry' );
		o.cached_entry = false;
	};

	// Get the cached entry from the browser session
	o.get_cached_entry = function() {
		if ( o.cached_entry ) return o.cached_entry;

		let entry = localStorage.getItem( 'rsd_entry' );
		if ( ! entry ) return false;

		o.cached_entry = JSON.parse( entry );

		return o.cached_entry;
	};

	// Store a cached entry and entry ID in the browser session
	o.store_cached_entry = function( key, entry_id ) {
		o.cached_entry = {
			key: key,
			entry_id: entry_id,
			timestamp: Date.now()
		};

		localStorage.setItem( 'rsd_entry', JSON.stringify(o.cached_entry));
	};

	// Add a query arg to a URL
	o.add_query_arg = function( property, value, url = null ) {
		if ( url === null ) url = window.location.href;

		let separator = url.indexOf('?') !== -1 ? '&' : '?';
		return url + separator + property + '=' + encodeURIComponent(value);
	};

	// Get a query arg from a URL
	o.get_query_arg = function( property, url = null ) {
		if ( url === null ) url = window.location.href;

		let regex = new RegExp('[?&]' + property + '(=([^&#]*)|&|#|$)');
		let matches = regex.exec(url);
		if ( matches ) {
			return matches[2] ? decodeURIComponent(matches[2].replace(/\+/g, ' ')) : '';
		}
		return false;
	};

	// Remove a query arg from a URL
	// Example if removing "name":
	//    "example.com/?name=radley&month=11" -> "example.com/?month=11"
	o.remove_query_arg = function( property, url ) {
		let regex = new RegExp('[?&]' + property + '(=([^&#]*)|&|#|$)');
		let matches = regex.exec(url);

		if ( matches ) {
			url = url.replace(matches[0], '');

			// If it started with a ?, replace the first & with a ?
			if ( url.indexOf('?') === -1 ) url = url.replace('&', '?');
		}

		return url;
	};

	// ----------------------------
	// Functions hooked in o.on_ready()
	// ----------------------------

	// Loads data from the browser URL (query args)
	o.load_active_query_args = function() {
		let key = o.get_query_arg( 'rsd_key' );
		let entry_id = o.get_query_arg( 'rsd_entry' );

		// Check if the download key has expired on the server
		if ( key === 'expired' ) {
			console.log( '[RS Downloads] Download key has expired. Removing cached entry.' );
			o.remove_cached_entry();
			return;
		}

		// Check if the key and entry are valid, and store them if so.
		if ( key && entry_id ) {
			o.store_cached_entry( key, entry_id );
		}
	};

	// Removes query args from the browser URL, without reloading the page.
	o.remove_active_query_args = function() {
		let url = window.location.href;

		// Remove query args from the url
		for (let i = 0; i < remove_query_args.length; i++) {
			let arg = remove_query_args[i];
			url = o.remove_query_arg( arg, url );
		}

		// If the URL was changed, replace it
		if ( window.location.href !== url ) {
			// console.log( 'Query arg removal is temporarily disabled' );
			window.history.replaceState({}, document.title, url);
		}
	}

	// Load elements from the page into variables
	o.load_elements = function() {
		$popup = form_settings.enabled ? jQuery('#rs-downloads-popup') : false;
		if ( ! $popup || $popup.length < 1 ) $popup = false;

		$form = $popup ? $popup.find('form').first() : false;
		if ( ! $form || $form.length < 1 ) $form = false;

		$submit = $form ? $form.find('input[type="submit"]').first() : false;
		if ( ! $submit || $submit.length < 1 ) $submit = false;
	};

	// Change all download links to include the cached key and entry ID
	o.change_download_links = function() {
		if ( ! form_settings.enabled ) return;
		if ( ! o.can_download() ) return;

		let entry = o.get_cached_entry();

		let $links = jQuery('.rs-download-link');

		$links.each(function() {
			let $link = jQuery(this);

			let href = $link.attr('href');
			if ( ! href && href.substr(0,1) === '#' ) return;

			// Add the key and entry ID to the URL
			href = o.add_query_arg( 'rsd_key', entry.key, href );
			href = o.add_query_arg( 'rsd_entry', entry.entry_id, href );

			$link.attr('href', href);
		});
	};

	// Set up the form
	o.setup_popup_form = function() {
		if ( ! form_settings.enabled ) return;
		if ( ! $popup ) return;

		const update_form_fields = function( download_id ) {
			$form.find('input[name="rs_downloads_download_id"]').val( download_id );
		};

		const show_popup = function() {
			// Un-hide the form
			$popup.css('display', '');

			// Show the form using a class after a short delay, to use with CSS animations
			setTimeout(function() {
				$popup.addClass('rsd-form-visible')
			}, 50);
		};

		const hide_popup = function() {
			$popup.removeClass('rsd-form-visible');
		};

		// Download Links: When clicking on any download link or button, update and show the form
		jQuery(document.body).on('click', '.rs-download-link', function(e) {
			if ( ! o.can_download() ) {

				// Update the form to indicate which download was requested
				let download_id = jQuery(this).attr('data-download-id');
				update_form_fields( download_id );

				// Show the popup
				show_popup();

				// Prevent default click behavior
				return false;

			}
		});

		// Click the close button to hide the popup
		$popup.on('click', '.rsd-popup--close-link', function(e) {
			hide_popup();
			return false;
		});

		// Clicking outside the popup should also hide it
		$popup.on('click',  function(e) {
			// Ensure that we clicked the containing form and not on an element inside of it
			if ( e.target === this ) {
				hide_popup();
				return false;
			}
		});

		// If the form has validation errors, show the popup immediately
		if ( $form && $form.closest('.gform_validation_error').length > 0 ) {
			show_popup();
		}

	};

	// Now the script has loaded, call o.on_init()
	o.on_init();

	// Once the document is ready, call o.on_ready()
	jQuery(document).ready(function($) {
		o.on_ready();
	});

})();