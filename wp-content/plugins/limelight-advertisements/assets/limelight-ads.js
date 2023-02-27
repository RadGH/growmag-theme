jQuery(function () {

	if ( jQuery('.ld-ad-insert, .ld-ad-hardcode').first().length ) {
		// do this if have at least one ad to insert or that was hardcoded
                
		init_ld_ads();
	}

});

function init_ld_ads() {

	// insert random ads: loop through each empty ad space and insert an ad
	jQuery('.ld-ad-insert').each(function () {
		var id = jQuery(this).attr("id").substring(10); // unique generated ID for the location, minus the "ld-insert-" prefix
		if ( typeof ld_ads_markup[id] === "undefined" ) return;

		var markup = ld_ads_markup[id]; // corresponding markup ( = array of post IDs and html for each possible ad)
		var max = markup.length;
		if ( max > 0 ) {
			var randAd = Math.floor(Math.random() * max); // pick random ad from the array
                       
			jQuery(this)
				.html(markup[randAd][1])            // insert markup for the chosen ad
				.data("ld-id", markup[randAd][0]);   // save the chosen ad's post ID as a data attribute
		}
	});


	// check which ads are hidden ( = get added to watch list on resize), and which are visible ( = get views right now)

	var postIDs = [];       // array of visible ad post IDs (data attrs) passed to ajax to increment their view counts
	var hiddenAdIDs = [];   // array of hidden ad location IDs (unique id attrs) to be watched, and have their view counts incremented if they become visible

	// loop through each ad space and count a view if visible
	jQuery('.ld-ad-insert, .ld-ad-hardcode').each(function () {
		if ( jQuery(this).children().first().css("display") === "block" ) {
			postIDs.push(jQuery(this).data("ld-id")); // store the (possibly not unique) post ID for the ad
		} else {
			hiddenAdIDs.push(jQuery(this).attr("id").substring(10));  // store the unique generated ID for the location, minus the "ld-insert-" prefix
		}
	});

	// increment view count for each visible ad (can increment twice if the same ad appears twice)
	record_views(postIDs);

	// if some ads aren't immediately visible, watch on screen resize to see if they become visible
	if ( hiddenAdIDs.length > 0 ) {
		var resizetimer;

		// start checking for newly-visible ads on window resize
		jQuery(window).on("resize", check_for_new_ad_views_on_resize);

		function check_for_new_ad_views_on_resize() {
			clearTimeout(resizetimer);
			resizetimer = setTimeout(function () {
				var postIDs = [];

				var lengthHidden = hiddenAdIDs.length;
				for ( var i = 0; i < lengthHidden; i++ ) { // check if each previously-hidden ad is now visible
					var $elt = jQuery("#ld-insert-" + hiddenAdIDs[i]);
					if ( $elt.children().first().css("display") === "block" ) { // if visible, we'll count a view for it
						postIDs.push($elt.data("ld-id")); // add to array of post IDs to send over ajax
						hiddenAdIDs.splice(i, 1); // remove from list; it's been recorded as a view
					}
				}

				// if at least one previously-hidden ad is now visible, send to ajax
				if ( postIDs.length > 0 ) {
					record_views(postIDs);
				}

				// if all possible ads have been viewed, stop checking on window resize
				if ( hiddenAdIDs.length === 0 ) {
					jQuery(window).off("resize", check_for_new_ad_views_on_resize);
				}

			}, 200);
		}
	}

	// count a click
	jQuery('.ldad-external-link').click(function ( e ) {
		e.preventDefault();

		var adID = jQuery(this).closest(".ld-ad").parent().data("ld-id");
		var href = jQuery(this).attr("href");

		var new_tab = window.open( href, "_blank" );
		new_tab.focus();

		// update the view counts
		jQuery.ajax({
			url: ad_ajax_url.ajax_url,
			data: {
				ldad: 'track_click',
				post_ids: adID
			},
			type: "POST",
			complete: function () {
				/*
				var new_tab = window.open( href, "_blank" );
				new_tab.focus();
				*/
			}
		});
	})
}


// count views for an array of post IDs
function record_views( postIDs ) {

	// update the view counts
	jQuery.ajax({
		url: ad_ajax_url.ajax_url,
		data: {
			ldad: 'track_view',
			post_ids: postIDs
		},
		type: "POST",
		complete: function ( data ) {
			if ( data.responseText !== 'success' ) {
				console.log('Ad Plugin ' + data.responseText);
			}
		}
	});

}