jQuery(function() {

	const s = window.digital_issues_mailchimp || {};

	const ajax_url = s.ajax_url || '';
	const nonce = s.nonce || '';

	const $list = jQuery('.digital-issues-list');

	let popup = null;

	const init = function() {

		// If the user is a Mailchimp subscriber, do not protect the page
		if ( is_mailchimp_subscriber() ) {
			console.log('Digital issues unlocked because the user is subscribed to the newsletter.');
			return;
		}else{
			console.log('Digital issues are protected because the user is not subscribed to the newsletter.');
		}

		// Replace all digital issue links with a data-href so you can't open them directly
		$list.find('.issue-cover a').each(function() {
			const $a = jQuery(this);
			$a.attr('data-href', $a.attr('href'));
			$a.attr('href', '#');
		});

		// Clicking on a digital issue should ask you to verify your subscription
		$list.on('click', '.issue-cover a', function(e) {
			e.preventDefault();
			const $a = jQuery(this);
			const href = $a.attr('data-href');
			const post_id = $a.attr('data-id'); // from the data-id attribute provided by the theme
			check_mailchimp_subscription( $a, href, post_id );
		});

	};

	// Check if a user is a mailchimp subscriber by looking at a cookie from a previous attempt
	const is_mailchimp_subscriber = function() {
		return get_cookie('mailchimp_subscriber') === '1';
	};

	// Get a cookie
	const get_cookie = function(name) {
		const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
		if (match) return match[2];
	};

	// Set a cookie
	const set_cookie = function(name, value, days) {
		const expires = new Date();
		expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
		document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
	};

	// Use a popup to confirm mailchimp subscription
	const check_mailchimp_subscription = function( $a, href, post_id ) {
		// $a: The link element that was clicked on
		// href: The href of the link element
		// post_id: The post ID of the digital issue that was clicked on

		// Set up the popup once
		if ( popup === null ) {
			popup = new subscribe_popup();
		}

		// Hook after email is checked and is a subscriber
		const after_email_verified = function( message ) {
			// Remember the user is a subscriber
			set_cookie('mailchimp_subscriber', '1', 365);

			// Replace the popup content with the success message
			if ( ! message || typeof message !== 'string' ) {
				message = 'Thank you for subscribing!';
			}

			let issue_title = $a.closest('.issue-cover').find('h3').text();

			if ( issue_title ) {
				issue_title = 'Go to Issue: ' + issue_title;
			}else{
				issue_title = 'Go to Issue';
			}

			// Remove paragraphs from content, but keep headings
			popup.elements.wrapper.find('.popup-inner--content').find('p').remove();

			// Add a link to the issue below the content
			popup.elements.wrapper.find('.popup-inner--content')
				.after('<div class="popup-inner--success-message"><p>' + message + '</p><p><a href="'+ href +'" target="_blank" class="button digital-issue-popup-link">' + issue_title + '</a></p></div>');

			// Make clicking the button in the popup also close the popup
			popup.elements.wrapper.find('.digital-issue-popup-link').on('click', function() {
				popup.toggle(false);
			});

			// Remove form elements from the popup
			popup.elements.wrapper.find('.popup-inner--error-message').remove();
			popup.elements.wrapper.find('.popup-inner--form').remove();
			popup.elements.wrapper.find('.popup-inner--disclaimer').remove();

			// Disable click events on digital issues
			$list.off('click', '.issue-cover a');
			$list.find('.issue-cover a').off('click');

			// Change links back
			$list.find('.issue-cover a').each(function() {
				const $a = jQuery(this);
				let href = $a.attr('href');
				let data_href = $a.attr('data-href');

				if ( data_href.length > 1 && href === '#' ) {
					href = data_href;
					$a.attr('href', href);
				}
			});
		};

		// Hook after email is checked, but is not a subscriber
		const after_email_failed = function( message ) {

			// Add the message to the popup
			popup.set_error_message( message );

		};

		// When the popup form is submitted, use Ajax to verify the subscription
		popup.get_form_element().off('submit');

		popup.get_form_element().on('submit', function(e) {
			verify_subscription( popup.get_email(), after_email_verified, after_email_failed );
			e.preventDefault();
		});

		// Show the popup, then wait for the user to complete the form
		popup.toggle( true );
	};

	// Verify the user's subscription
	const verify_subscription = function( email, verified_cb, failed_cb ) {
		if ( ! email ) {
			failed_cb( 'Email address is invalid.' );
			return;
		}

		jQuery.ajax({
			url: ajax_url,
			type: 'POST',
			data: {
				action: 'verify_mailchimp_subscription',
				nonce: nonce,
				email: email
			},
			success: function( response ) {
				if ( typeof response.success === 'undefined' ) {
					failed_cb('Sorry, we could not verify your subscription. Please try again.');
				}else{
					if ( response.success ) {
						verified_cb( response.data );
					}else{
						failed_cb( response.data );
					}
				}
			},
			error: function() {
				failed_cb('Sorry, we could not verify your subscription. Please try again.');
			}
		});
	};

	// Popup class
	const subscribe_popup = function() {
		let p = this;

		p.elements = {
			wrapper: null,
			close: null,
			form: null,
			email: null,
			error_message: null
		};

		p.toggle = function( make_visible ) {
			p.elements.wrapper.toggleClass('visible', make_visible);
			p.elements.wrapper.css('display', '');
			p.elements.wrapper.prop('hidden', ! make_visible);

			if ( make_visible ) {
				p.elements.wrapper.addClass('reveal');
				setTimeout(function() {
					p.elements.wrapper.removeClass('reveal');
				}, 500);

			}else{
				// Reset the email field
				p.elements.email.val('');

				// Reset the error message
				p.set_error_message(false);
			}
		}

		p.get_form_element = function() {
			return p.elements.form;
		};

		p.get_form_email_element = function() {
			return p.elements.email;
		};

		p.get_email = function() {
			return p.elements.email.val();
		};

		p.set_error_message = function( message ) {
			if ( message ) {
				p.elements.error_message.html( message );
				p.elements.error_message.css('display', '');
			}else{
				p.elements.error_message.html('');
				p.elements.error_message.css('display', 'none');
			}
		};

		p.init = function() {

			// Get the popup elements, hidden by default
			p.elements.wrapper = jQuery('.rs-di-popup-wrapper');
			p.elements.close = p.elements.wrapper.find('.popup--close');
			p.elements.form = p.elements.wrapper.find('#rs-di-email-form');
			p.elements.email = p.elements.wrapper.find('#rs-di-email-input');
			p.elements.error_message = p.elements.wrapper.find('.popup-inner--error-message');

			// Click the close button to close the popup
			p.elements.close.on('click', function() {
				p.toggle(false);
				return false;
			});

			// Click the wrapper to close the popup
			p.elements.wrapper.on('click', function(e) {
				if ( e.target === p.elements.wrapper[0] ) {
					p.toggle(false);
					return false;
				}
			});

		};

		// Initialize the popup
		p.init();

		return p;
	};

	init();
});