jQuery(function () {
	// Adds/removes body the classes "scrolled-from-top" and "scrolled-past-header", as you scroll down the page.
	init_scroll_tracking();

	// Enable our mobile nav menu
	init_mobile_button('#mobile-button', '#mobile-menu-container', '.mobile-inner', 'mobile_nav_open', '#mobile-menu-wrap');

	// Enable toggleable share/search items in header
	init_header_buttons();

	// Enable isotope filtering and sorting of Woocommerce products
	init_woocommerce_isotope();
});

function init_woocommerce_isotope() {
	var $filterList = jQuery("#woocommerce-category-filter");
	var $orderby = jQuery(".orderby");

	if ( $filterList.length && $orderby.length ) {

		var $products = jQuery('.products');

		var totalCount = $products.children("li").length;
		$products.on("arrangeComplete",function() {
			var count = $products.children("li:visible").length;
			var text  = '';
			if (count == totalCount ){
				text = "all ";
			}
			if (count==1) {
				if (count == totalCount) {
					text = text + "results";
				} else {
					text = text + count + " result";
				}
			} else {
				text = text + count + " results";
			}
			jQuery(".woocommerce-result-count").text("Showing " + text);
		});

		$products.isotope({
			// options
			itemSelector: '.product',
			layoutMode: 'fitRows',
			getSortData: {
				price: function ( itemElem ) { // function
					var price = jQuery(itemElem).find('.price').text().replace("$", '');
					return parseFloat(price);
				}
			}
		});

		$filterList.on("click", "a", function () {
			$filterList.find("a").removeClass("active");
			jQuery(this).addClass("active");
			$products.isotope({
				filter: '.' + jQuery(this).data("filterclass")
			});
			return false;
		});

		$orderby.find("option[value='popularity'],option[value='date']").remove();
		$orderby.change(function () {
			if ( jQuery(this).val() == "price" ) {
				$products.isotope({
					sortBy: "price",
					sortAscending: true
				});
			} else if ( jQuery(this).val() == "price-desc" ) {
				$products.isotope({
					sortBy: "price",
					sortAscending: false
				});
			} else {
				$products.isotope({
					sortBy: 'original-order',
					sortAscending: true
				});
			}
			return false;
		});
	}
}

function init_header_buttons() {
	jQuery("#menu-buttons .toggle").click(function () {
		if ( jQuery(this).prev().find(".sliding").css("left") == "0px" ) {
			jQuery(this).prev().find(".sliding").animate({ "left": "100%" });
		} else {
			jQuery(this).prev().find(".sliding").animate({ "left": "0px" });
		}
	});
}

function init_scroll_tracking() {
	var $body = jQuery('body');
	var $header = jQuery("#header");

	var header_height = $header.outerHeight(true);

	var state_body = false;
	var state_header = false;

	var updateScrollVariables = function ( e ) {
		header_height = $header.outerHeight(true);
		updateScrollClasses(e);
	};

	var updateScrollClasses = function ( e ) {
		var scrollTop = false;

		// scrollingElement saves is some effort, but isn't well supported
		if ( typeof e.target.scrollingElement == 'undefined' ) {
			scrollTop = jQuery(e.target).scrollTop;
		} else {
			scrollTop = e.target.scrollingElement.scrollTop;
		}

		// When scrolling any amount from the top of the page
		if ( scrollTop > 0 ) {
			if ( !state_body ) {
				state_body = true;
				$body.addClass('scrolled-from-top');
			}
		} else {
			if ( state_body ) {
				state_body = false;
				$body.removeClass('scrolled-from-top');
			}
		}

		// When scrolling past the header
		if ( scrollTop > header_height ) {
			if ( !state_header ) {
				state_header = true;
				$body.addClass('scrolled-past-header');
			}
		} else {
			if ( state_header ) {
				state_header = false;
				$body.removeClass('scrolled-past-header');
			}
		}
	};


	var scrollTimeout = false;

	jQuery(window).scroll(function ( e ) {
		// Modify classes as needed. This only affects the dom if it needs to.
		updateScrollClasses(e);

		// Rate-limited scroll events, to keep us from checking the dom too frequently
		if ( scrollTimeout !== false ) clearTimeout(scrollTimeout);

		scrollTimeout = setTimeout(function () {
			updateScrollVariables(e);
		}, 150);
	});
}

function init_mobile_button( button_selector, navigation_selector, inner_nav_selector, body_class, menu_selector ) {
	if ( typeof button_selector == 'undefined' || typeof navigation_selector == 'undefined' || !button_selector || !navigation_selector ) return;

	var $button = jQuery(button_selector);
	var $nav = jQuery(navigation_selector);
	var $menu = jQuery(menu_selector);

	if ( $button.length < 1 || $nav.length < 1 ) {
		return;
	}

	var $body = jQuery('body');
	var $header = jQuery('#header');
	var $inner = $nav.find(inner_nav_selector);

	var calculate_menu_offset = function () {

		// 2022-11-04: Adjust menu top positioning, give room for ads
		if ( $header.length > 0 ) {
			var site_top = $header.position().top;
			$menu.css( 'top', '' + (Math.round(site_top * 100) / 100) + 'px' );
		}

		// 2022-11-04: IDK what this did but it no longer seems to work
		$nav.children('ul.nav-ul').each(function () {
			if ( jQuery('#menu-container').is(':visible') && jQuery(this).outerHeight() ) {
				jQuery(this).css('margin-top', -1 * parseInt(jQuery(this).outerHeight()));
			}
		});
	};

	// ----------------------------

	calculate_menu_offset();

	// Open / Close the nav on clicking the button, using a body class
	$button.click(function () {
		// Close any open submenus
		$nav.find('li.sub-menu-open').removeClass('sub-menu-open');

		// Recalculate menu offset, which is required for the menu to be hidden
		calculate_menu_offset();

		// Toggle the mobile nav menu
		$body.toggleClass(body_class);
		return false;
	});

	// For all submenus, add the menu item as an immediate child of the menu.
	// Unless the first item of the menu has the same title or URL
	// This makes it obvious that dropdown menus are pages themselves.
	$nav.find('li.menu-item').each(function () {
		var $submenu = jQuery(this).children('ul.sub-menu');
		if ( $submenu.length < 1 ) return;

		var href = jQuery(this).children('a:first').attr('href');
		var text = jQuery(this).children('a:first').text();

		// If the menu already has a link to itself as a child item, do not re-create it.
		if ( $submenu.find('a:first').text() == text ) return;
		if ( $submenu.find('a:first').attr('href') == href ) return;

		var $new = jQuery('<li></li>').addClass('menu-item');
		$new.append(jQuery('<a></a>').attr('href', href).text(text));

		$submenu.prepend($new);
	});

	// Clicking a menu item should open up the submenu navigation, if it has one. If it is open, close it.
	$nav.on('click', 'a', function ( e ) {
		var $link = jQuery(this);
		var $item = $link.parent('li.menu-item');
		var $submenu = $item.children('ul.sub-menu:first');

		if ( $submenu.length > 0 ) {
			// Collapse sibling menus if they are open, as well as their children.
			$item.siblings('li.menu-item.sub-menu-open').each(function () {
				jQuery(this).removeClass('sub-menu-open');
				jQuery(this).find('li.menu-item.sub-menu-open').removeClass('sub-menu-open');
			});

			// Collapse or expand the clicked menu as needed.
			$item.toggleClass('sub-menu-open');

			e.preventDefault();
			return false;
		}
	});

	// Clicking outside of the menu (while the menu is active) should close the menu.
	$nav.click(function ( e ) {
		if ( $body.hasClass(body_class) ) {
			if ( $inner.length > 0 && (e.target == $inner[0] || $inner.find(e.target).length > 0) ) {
				// If the user clicked in the a menu, do not close the menu
				return true;
			} else {
				// User clicked out of the menu. Hide the menu and abort the action.
				$body.removeClass(body_class);
				$nav.find('.sub-menu-open').removeClass('sub-menu-open');
				e.stopPropagation();
				e.preventDefault();
				return false;
			}
		}
	});
}