<?php
/*
  
Functions:

	limelight_mail( $to_name, $to_email, $subject, $body, [$from_name], [$from_email], [$use_html = true] )
		Sends an email to the specified user. Uses HTML content type by default. Will use admin email and site title as $from email by default
		
	format_address( $address, $city = null, $state = null, $zip = null, [$use_linebreaks = true], [$expand_state = false] )
		Takes a multi-part address and converts it to a single string.
		$address can be multiple lines.
		Does NOT scan the address for city, state and zip.
		Does NOT manage linebreaks of the city, state, or zip code.
	
	format_phone( $number, $html = true, $force_valid = true )
		Converts any phone number to a 10-digit phone number.
		
			- If $html is true (default), the returned value will be an HTML-formatted phone number, including "tel:+" in the link.
			- Extensions are supported. An extension will be identified starting with one of the following: +, -, x, ex, ext, ext.
			- If a pattern match is not found, the original string will be returned without HTML formatting.
			- If $force_valid is enabled (default), invalid phone numbers will return an empty string.
			- If $force_valid is DISABLED, the original $input is returned.
			
	format_sms( $number, $body, $text = false, $force_valid = false )
		Like format_phone, but for generating an sms link which opens the device's texting app.
		Does not have an $html option.
	
	match_phone( $input )
		Splits a phone number into 4 parts. The array lengths are 3, 3, 4, *. This represents a phone number: 555.444.3333 x9999
		[0] = 555
		[1] = 444
		[2] = 333
		[4] = 9999 (optional)
	
	format_usd( $amount, $decimals = 2, $dollar_sign = true, $strip_zeroes = false )
		Returns the amount given in USD format, eg: $1,234.56
		
	state_to_full( $state )
		Converts a state code into a full name. Also available as a filter.
		
	state_to_code( $state )
		Converts a state name into abbreviated code. Also available as a filter.
		
	state_code_array()
		Returns an array of all US states in key->value format. Keys are uppercase abbreviations, values are proper case state names.
	
	month_convert( $input, $format )
		Converts a month provided into the given format.
		$input: Can be numeric month index (starting from 1), short or full month name
		$format: Date() keys are case sensitive, aliases (such as "short") are not.
			short, M: Sep
			med: Sept
			long, full, F: September
			number, numeric, n: 9
			2digit, leading, m: 09
		
	ap_format_month( $month_number ) 
		Returns a medium-length version of month, such as "Sept"
		1 would return "Jan". 4 would return "April", 9 would return "Sept"
	
	day_ordinal( $input )
		Returns the ordinal version of the number, eg "1st" for 1.

	number_format_short( $value, $precision = 1 )
		Returns a short notation for an integer, where 1003 = 1k, and 1525 = 1.5k.

	is_post_in_menu( $menu_key, $post_id = null )
		Returns true if the given post is in the specified menu.
	
	external_url( $url )
		Automatically converts external URLs to include http:// or https:// when it is not already provided.
		Intended for user input, where users might type "google.com".
		Available as a filter.

	is_external_url( $url, $check_fragments = false )
	Tests whether a URL is external or internal by looking if it includes HTTP:// (or HTTPS), and checking if it is pointed to this website.
	$check_fragments can be used to allow incomplete URLs to be tested, such as "google.com". If a url starts with a forward slash, it is always relative.
	
	basename_url( $url, $show_path )
		Retrieve the hostname and path of a URL for presentation. Strips out protocol, username, password and query
		Available as a filter with one argument (you cannot set $show_path to false when using a filter)
	
	format_filesize( $bytes, $precision = 2 )
		Returns a user-friendly file size such as 0.43mb
	
	get_user( $value, $return = 'object' )
		Provide a user ID or object and return the type of data you want. Possible options are "object" and "id". Other terms will return the value provided from object->get( $return ) (even if this returns false)
		Returns false if user does not exist
		
	video_get_service( $video_url )
		Gets the service, "youtube" or "vimeo", based on a look at the url.

	youtube_get_video_id( $video_url )
	vimeo_get_video_id( $video_url )
		Gets the video ID from the noted service

	video_get_embed_code( $video_url, $user_options = null )
		Gets the embed code for the provided Vimeo or Youtube video URL. Each service has separate options, but both share options that are similar (width, autoplay...)

	video_get_image( $video_url )
		Returns the attachment ID for the given video by service. An easier version of the two functions below.

	youtube_get_image_attachment( $url )
	vimeo_get_image_attachment( $url )
		Gets the thumbnail for the given video URL as an attachment array.

	vimeo_get_info_by_url( $url, $option_id = null )
		Determines the ID of a video and looks up the thumbnail(s) of the video. Results are stored as a wordpress option.
	
	image_get_attachment_id( $input )
		Takes an image URL or an <img> tag and returns the media ID of any image found. When a thumbnail is provided, we look for the original.

	smart_media_id( $image_url )
		Returns the ID of an image. Works for image urls, html image tags, or even an image id which seems silly!

	smart_media_array( $image_url, $size = 'full', $fallback_to_full )
		Performance Note: This function takes 38% longer than smart_media_size, and 53% longer than wp_get_attachment_image_src()
		A combination of smart_media_size and smart_media_alt, but also returns other information like the actual width/height of the image, local path, other sizes, etc.
			Return: Array(
				// Global for all sizes:
				'id'          => '',
				'ID'          => '',
				'sizes'       => array of arrays, each containing: file, path, url, width, height, mime-type,
				'alt'         => '',
				'real_alt'    => '',
				'title'       => '',
				'description' => '',
				'caption'     => '',

				// Relative to requested size:
				'size'   => '', // This will change to 'full' if the requested size isn't found.
				'url'    => '',
				'file'   => '',
				'path'   => '',
				'width'  => '',
				'height' => ''
			)

	smart_media_size( $image_url, $size = 'full' )
		Performance Note: This function takes 15% longer than wp_get_attachment_image_src()
		Takes an image URL or ID and attempts to get the provided size using wordpress' attachment system. If the URL cannot be optimized, the original is returned.
		This function does not retroactively resize images.
		$size: Array of width/height, or a string of an image size defined with add_image_size()
		
	smart_media_alt( $url )
		Looks for alt text relating to the given image URL or attachment ID.
		Priority: Alt tag, Caption, Description, (pretty) Filename
		
	smart_excerpt( $id_or_string = null, $words = 45, $more = "&hellip;", $keep_linebreaks = true, $do_shortcode = true )
		Converts the post ID or string of content to an excerpt using a custom word count, more tag, and the option to keep linebreaks (no more than 2 in sequence).
		Returns a string with no HTML tags. Linebreaks will be provided as "\n".
		
	limelight_pagination( [$show_if_empty = true] )
		Displays pagination for the current wp_query.
	
	limelight_list_terms( $post_ID, $taxonomy, $sep = ', ', $use_hyperlinks = true, $limit = null, $limit_text = "%s more" )
		Returns a string containing a separated list of terms for the given post.
	
	limelight_archive_thumbnail( [$post_id = false], [$meta_key = false] )
		Returns an image to use for the blog archive preview. Will retrieve featured image if available, falling back to a single meta key, and finally the first image in the page.
		Returns an <img> tag
	
	limelight_file_download( $file, [$filename = null], [$attachment = true] )
		Process a file download through PHP streaming, with the option to force download (attachment, default) or stream to browser (attachment FALSE)
		Will use original filename if not provided or null.

	ld_handle_upload_from_url( $image_url, $attach_to_post = 0, $add_to_media = true )
		Uploads a file from a URL. Optionally attaches it to a post, and/or displays it in the media screen.

	ld_handle_upload_from_path( $path, $add_to_media = true )
		Uploads a file from a URL. Optionally attaches it to a post, and/or displays it in the media screen.
			Success: Returns an array including file, url, type, attachment_id.
			Failure: Returns an array with the key "error" and a value including the error message.
*/

function limelight_mail( $to_name, $to_email, $subject, $body, $from_name = false, $from_email = false, $use_html = true ) {
	// Get the sender details from options.php
	if ( $from_name === false ) {
		$from_name = get_bloginfo( 'title' );
	}
	if ( $from_email === false ) {
		$from_email = get_option( 'admin_email' );
	}

	// Format emails like: john doe <jdoe@gmail.com> (if no name, do email twice)
	if ( $to_name ) {
		$to_full = sprintf( '%s <%s>', $to_name, $to_email );
	}else{
		$to_full = sprintf( '%s <%s>', $to_email, $to_email );
	}

	if ( $from_name ) {
		$from_full = sprintf( '%s <%s>', $from_name, $from_email );
	}else{
		$from_full = sprintf( '%s <%s>', $from_email, $from_email );
	}

	// Headers
	$headers = array();
	$headers[] = 'From: ' . $from_full;
	if ( $use_html ) {
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
	}

	// Send the email
	$result = wp_mail( $to_full, $subject, $body, $headers );

	return $result;
}

function format_address( $address, $city = null, $state = null, $zip = null, $use_linebreaks = true, $expand_state = false ) {
	if ( $use_linebreaks ) {
		$output = preg_replace( "/[\r\n]*/", "\n", $address ); // Remove double line breaks
		$output = preg_replace( "/ {2,}/", " ", $output ); // Collapse spaces
	}else{
		$output = preg_replace( "/\s+/", " ", $address ); // Replace all whitespace with single spaces
	}

	if ( $state ) {
		if ( $expand_state ) {
			$state = state_to_full( $state ) . ' ';
		}else{
			$state = state_to_code( $state ) . ' ';
		}
	}

	if ( $city && $state && $zip ) {
		$location = sprintf( '%s, %s %s', $city, $state, $zip );
	}else{
		$location = '';
		if ( $city ) {
			$location .= $city . ' ';
		}
		if ( $state ) {
			$location .= $state . ' ';
		}
		if ( $zip ) {
			$location .= $zip . ' ';
		}
	}

	if ( $location ) {
		if ( $address ) {
			if ( $use_linebreaks ) {
				$output .= "\n" . $location;
			}else{
				$output .= ", " . $location;
			}
		}else{
			$output = $location;
		}
	}

	return trim( preg_replace( '/  +/', ' ', $output ) );
}

function format_phone( $number, $html = true, $force_valid = false ) {
	// Pattern to collect 10 digits from a phone number, and optional extension
	// Extensions can be identified using: + - x ex ex. ext ext. extension extension.

	$matches = match_phone( $number );

	if ( $matches ) {
		// number: "1 (541) 123-4567 x999"
		// 1 => 541
		// 2 => 123
		// 3 => 4567
		// 6 => 999
		$sep = apply_filters( 'format_phone_separators', array(
			'-',
			'-',
		) );

		$result = array();

		// Output (HTML):
		// <span class="tel"><a href="tel+15411234567" class="tel-link">541-123-4567</a><span class="tel-ext"><span> x</span>999</span></span>
		// Output (Raw):
		// 541-123-4567 x999
		if ( $html ) {
			$result[] = '<span class="tel">';
		}
		if ( $html ) {
			$result[] = sprintf( '<a href="tel:+1%s%s%s" class="tel-link">', $matches[0], $matches[1], $matches[2] );
		}
		$result[] = sprintf( '%s%s%s%s%s', $matches[0], $sep[0], $matches[1], $sep[1], $matches[2] );
		if ( $html ) {
			$result[] = sprintf( '</a>' );
		}

		// Note: tel: links cannot *reliably* include an extension, so it comes after the link.
		if ( isset( $matches[4] ) ) {
			$ext = $matches[4];
		}else{
			$ext = '';
		}

		if ( $ext && $html ) {
			$result[] = sprintf( '<span class="tel-ext"><span> x</span>%s</span>', $ext );
		}else{
			if ( $ext ) {
				$result[] = sprintf( ' x%s', $ext );
			}
		}

		if ( $html ) {
			$result[] = '</span>';
		}

		return implode( $result );
	}

	// Pattern not found
	if ( $force_valid ) {
		return '';
	} // We REQUIRE a valid phone number. Do not show a phone number.
	else{
		return $number;
	} // The phone number isn't valid, but that's ok. Keep the original.
}

function format_sms( $number, $body, $text = false, $force_valid = false ) {
	$matches = match_phone( $number );

	$_phone = '';
	$_body = '';
	$_text = '';

	if ( $matches ) {
		// number: "1 (541) 123-4567 x999"
		$_phone = "+1" . $matches[0] . $matches[1] . $matches[2];
	}else{
		if ( $force_valid && $number ) {
			return false;
		}
	}

	if ( $body ) {
		$_body = urlencode( $body );
	}

	if ( $text ) {
		$_text = $text;
	}else{
		if ( $matches ) {
			// Default link text: Show phone number such as: "SMS 555-123-1234"
			$sep = apply_filters( 'format_phone_separators', array(
				'-',
				'-',
			) );
			$_text = sprintf( '%s%s%s%s%s', $matches[0], $sep[0], $matches[1], $sep[1], $matches[2] );
			if ( $matches[3] ) {
				$_text .= ' <span class="sms-ext"> x' . $matches[3] . '</span>';
			}
		}else{
			if ( $body ) {
				// If body available, link text to "Send via SMS"
				$_text = 'Send via SMS';
			}else{
				// We don't have a phone number, body, or text? abort.
				return false;
			}
		}
	}


	return sprintf( '<span class="sms"><a href="sms:%s?body=%s" class="sms-link" target="_blank">%s</a></span>', esc_attr( $_phone ), esc_attr( $_body ), $_text );
}

function match_phone( $input ) {
	$pattern = '/([0-9]{3,3})[^0-9]*([0-9]{3,3})[^0-9]*([0-9]{4,4})[^0-9]*([^0-9]*(-|e?xt?\.?)[^0-9]*([0-9]{1,}))?[^0-9]*$/i';

	$result = preg_match( $pattern, $input, $found );

	if ( $result && $found[1] && $found[2] && $found[3] ) {
		return array(
			// 1-555-123-4567 x999:
			$found[1],
			// 555
			$found[2],
			// 123
			$found[3],
			// 4567
			!empty( $found[6] ) ? $found[6] : '',
			// 999
		);
	}

	return false;
}

function format_usd( $amount, $decimals = 2, $dollar_sign = true, $strip_zeroes = false ) {

	$format = '%';

	if ( !$dollar_sign ) {
		$format .= "!";
	}

	if ( $decimals > 0 ) {
		$format .= "." . absint( $decimals );
	}else{
		$format .= ".0";
	}

	setlocale( LC_MONETARY, 'en_US' );
	$format .= "n"; // Use national local (for en_US): $1,000.00

	$output = money_format( $format, $amount );

	if ( $strip_zeroes && $decimals > 0 ) {
		$output = preg_replace( "/\.0+$/", "", $output );
	}

	// Ensure we are using dollar symbol, not the abbreviation "USD"
	if ( $dollar_sign ) {
		$output = str_replace( 'USD ', '$', $output );
	}

	return $output;
}

function state_to_full( $state ) {
	$statecodes = state_code_array();

	$state_term = preg_replace( '/[^A-Z]/', '', strtoupper( $state ) );

	if ( isset( $statecodes[$state_term] ) ) {
		return $statecodes[$state_term];
	} // Match found, return full name

	return $state; // No match found
}

add_filter( 'state_to_full', 'state_to_full' );

function state_to_code( $state ) {
	$statecodes = state_code_array();

	$state_term = preg_replace( '/[^A-Z]/', '', strtoupper( $state ) );

	foreach ( $statecodes as $key => $value ) {
		$value_term = preg_replace( '/[^A-Z]/', '', strtoupper( $value ) );
		if ( $state_term == $value_term ) {
			return $key; // Match found, return state abbreviation
		}
	}

	return $state; // No match found
}

add_filter( 'state_to_short', 'state_to_short' );

function state_code_array() {
	static $state_list = null;
	if ( $state_list === null ) {
		$state_list = array(
			'AL' => "Alabama",
			'AK' => "Alaska",
			'AZ' => "Arizona",
			'AR' => "Arkansas",
			'CA' => "California",
			'CO' => "Colorado",
			'CT' => "Connecticut",
			'DE' => "Delaware",
			'DC' => "D.C.",
			'FL' => "Florida",
			'GA' => "Georgia",
			'HI' => "Hawaii",
			'ID' => "Idaho",
			'IL' => "Illinois",
			'IN' => "Indiana",
			'IA' => "Iowa",
			'KS' => "Kansas",
			'KY' => "Kentucky",
			'LA' => "Louisiana",
			'ME' => "Maine",
			'MD' => "Maryland",
			'MA' => "Massachusetts",
			'MI' => "Michigan",
			'MN' => "Minnesota",
			'MS' => "Mississippi",
			'MO' => "Missouri",
			'MT' => "Montana",
			'NE' => "Nebraska",
			'NV' => "Nevada",
			'NH' => "New Hampshire",
			'NJ' => "New Jersey",
			'NM' => "New Mexico",
			'NY' => "New York",
			'NC' => "North Carolina",
			'ND' => "North Dakota",
			'OH' => "Ohio",
			'OK' => "Oklahoma",
			'OR' => "Oregon",
			'PA' => "Pennsylvania",
			'RI' => "Rhode Island",
			'SC' => "South Carolina",
			'SD' => "South Dakota",
			'TN' => "Tennessee",
			'TX' => "Texas",
			'UT' => "Utah",
			'VT' => "Vermont",
			'VA' => "Virginia",
			'WA' => "Washington",
			'WV' => "West Virginia",
			'WI' => "Wisconsin",
			'WY' => "Wyoming",
		);
	}

	return $state_list;
}

function month_convert( $input, $format ) {
	static $months = null;
	if ( $months === null ) {
		$months = array();
		for ( $i = 1; $i <= 12; $i++ ) {
			$t = mktime( 0, 0, 0, $i, 1 );
			$months[$i] = array(
				'number' => date( 'n', $t ),
				'2digit' => date( 'm', $t ),
				'short'  => date( 'M', $t ),
				'med'    => apply_filters( 'ap_format_month', date( 'n', $t ) ),
				'long'   => date( 'F', $t ),
			);
		}
	}

	$input_month = false;

	foreach ( $months as $m ) {
		if ( is_numeric( $input ) ) {
			if ( absint( $m['number'] ) == absint( $input ) ) {
				$input_month = $m;
			}
		}else{
			if ( strtolower( substr( $input, 0, 3 ) ) == strtolower( $m['short'] ) ) {
				$input_month = $m;
			}
		}
	}

	if ( !$input_month ) {
		return false;
	}

	switch ( $format ) {
		case 'short':
		case 'abbrev':
		case 'M':
			return $input_month['short'];
			break;
		case 'med':
		case 'ap':
		case 'ap-style':
			return $input_month['med'];
			break;
		case 'long':
		case 'full':
		case 'F':
			return $input_month['long'];
			break;
		case 'number':
		case 'numeric':
		case 'n':
			return $input_month['number'];
			break;
		case '2digit':
		case 'leading':
		case 'm':
			return $input_month['2digit'];
			break;
	}

	return false;
}


// Returns a medium-length version of month, such as "Sept"
// 1 would return "Jan". 4 would return "April", 9 would return "Sept"
function ap_format_month( $month_number ) {
	$mo = array(
		'',
		// Array starts at 0, months start at 1
		'Jan',
		'Feb',
		'March',
		'April',
		'May',
		'June',
		'July',
		'Aug',
		'Sept',
		'Oct',
		'Nov',
		'Dec',
	);

	if ( isset( $mo[$month_number] ) ) {
		return $mo[$month_number];
	}else{
		return $month_number;
	}
}

add_filter( 'ap_format_month', 'ap_format_month' );

function day_ordinal( $input ) {
	$ends = array(
		'th',
		'st',
		'nd',
		'rd',
		'th',
		'th',
		'th',
		'th',
		'th',
		'th',
	);
	if ( ( $input % 100 ) >= 11 && ( $input % 100 ) <= 13 ) {
		return 'th';
	}else{
		return $ends[$input % 10];
	}
}

// Converts a number into a short version, eg: 1000 -> 1k
// Based on: http://stackoverflow.com/a/4371114
function number_format_short( $n, $precision = 1 ) {
	if ($n < 900) {
		// 0 - 900
		$n_format = number_format($n, $precision);
		$suffix = '';
	} else if ($n < 900000) {
		// 0.9k-850k
		$n_format = number_format($n / 1000, $precision);
		$suffix = 'K';
	} else if ($n < 900000000) {
		// 0.9m-850m
		$n_format = number_format($n / 1000000, $precision);
		$suffix = 'M';
	} else if ($n < 900000000000) {
		// 0.9b-850b
		$n_format = number_format($n / 1000000000, $precision);
		$suffix = 'B';
	} else {
		// 0.9t+
		$n_format = number_format($n / 1000000000000, $precision);
		$suffix = 'T';
	}

	// Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
	// Intentionally does not affect partials, eg "1.50" -> "1.50"
	if ( $precision > 0 ) {
		$dotzero = '.' . str_repeat( '0', $precision );
		$n_format = str_replace( $dotzero, '', $n_format );
	}

	return $n_format . $suffix;
}


function is_post_in_menu( $menu_key, $post_id = null ) {
	if( $post_id === null ) $post_id = get_queried_object_id();

	$menu_object = wp_get_nav_menu_items( esc_attr( $menu_key ) );
	if( ! $menu_object ) return false;

	// Get an array of object ids from the menu
	$menu_items = wp_list_pluck( $menu_object, 'object_id' );

	return in_array( (int) $post_id, $menu_items );
}


function external_url( $url ) {
	if ( !stristr( $url, 'http://' ) && !stristr( $url, 'https://' ) ) {
		if ( substr( $url, 0, 1 ) != '/' ) {
			// If a URL starts with a slash, it is relative to the current website. Otherwise, add http or https
			if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ) {
				$url = 'https://' . $url;
			}else{
				$url = 'http://' . $url;
			}
		}
	}

	return $url;
}

add_filter( 'external_url', 'external_url' );

function is_external_url( $url, $check_fragments = false ) {
	$url = strtolower( $url );
	$url = str_replace( 'https:', 'http:', $url );

	// If the URL has http:// or https://, check if it is pointing to this website
	if ( stristr( $url, 'http://' ) ) {
		$blog_url = strtolower( home_url() );
		$blog_url = str_replace( 'https:', 'http:', $blog_url );
		$blog_url = untrailingslashit( $blog_url );

		// If this site url is not in the target url, it's external.
		if ( !strstr( $url, $blog_url ) ) {
			return true;
		}
	}else{
		// Check a partial URL (not an absolute url), such as "google.com" or "/about-us/"
		if ( !$check_fragments ) {
			return false;
		}

		// Two slashes indicate SSL-agnostic HTTP. AKA, it means HTTPS:// or HTTP://. We'll run it through as HTTP:// instead.
		if ( substr( $url, 0, 2 ) != '//' ) {
			return is_external_url( 'http:' . $url );
		}

		// If the URL starts with a single slash, it is relative
		if ( substr( $url, 0, 1 ) != '/' ) {
			return false;
		}

		// At this point, the url does NOT start with a slash, NOR http://. Run it through with http:// appended, so we can check if it is self-hosted.
		return 'http://' . $url;
	}

	return false;
}

function basename_url( $url, $show_path = true ) {
	$parse = parse_url( $url );
	if ( $parse === false ) {
		return $url;
	}

	$base_url = $parse['host'];

	if ( isset( $parse['path'] ) && $show_path ) {
		$base_url .= $parse['path'];
	}

	return untrailingslashit( $base_url );
}

add_filter( 'basename_url', 'basename_url' );


function format_filesize( $bytes, $precision = 2 ) {
	$units = array(
		'B',
		'KB',
		'MB',
		'GB',
		'TB',
	);

	$bytes = max( $bytes, 0 );
	$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
	$pow = min( $pow, count( $units ) - 1 );

	// Uncomment one of the following alternatives
	$bytes /= pow( 1024, $pow );

	// $bytes /= (1 << (10 * $pow));

	return round( $bytes, $precision ) . ' ' . $units[$pow];
}

function get_user( $user_id, $return = 'object' ) {
	if ( $user_id === null ) {
		// user_id not provided, use current user
		$user = wp_get_current_user();
		if ( $user ) {
			$user_id = $user->ID;
		}
	}else{
		if ( is_object( $user_id ) && property_exists( $user_id, 'ID' ) ) {
			// user_id is a wp_user object, retrieve ID separately
			$user = $user_id;
			$user_id = $user->ID;
		}else{
			// user_id is likely an integer, try to get account by ID
			$user = get_user_by( 'id', $user_id );
		}
	}

	if ( $user && $user->ID ) {
		switch ( strtolower( $return ) ) {

			case 'id':
			case 'user_id':
				return $user->ID;
				break;

			case 'object':
			case 'wp_user':
				return $user;
				break;

			default:
				return $user->get( $return );
				break;

		}
	}

	return false;
}

function video_get_service( $video_url ) {
	if ( stristr( $video_url, 'vimeo.com') ) {
		return 'vimeo';
	}

	if ( stristr( $video_url, 'youtube.com' ) || stristr( $video_url, 'youtu.be' ) ) {
		return 'youtube';
	}

	return false;
}

function youtube_get_video_id( $video_url ) {
	if ( preg_match( '/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/', $video_url, $matches ) ) {
		return $matches[1];
	}

	return false;
}

function vimeo_get_video_id( $video_url ) {
	if ( preg_match( '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $video_url, $matches ) ) {
		return $matches[5];
	}

	return false;
}

function video_get_embed_code( $video_url, $user_options = null ) {
	static $player_id_increment = 0;
	$player_id_increment++;

	$service = video_get_service( $video_url );

	$default_options = array(
		'width' => 1280,
		'height' => null, // Calculates to proper aspect ratio, which would be 720.
		'autoplay' => false,
		'player_id' => 'video-player-' . $player_id_increment,

		// Vimeo Specific
		'vimeo_autopause' => false,
		'vimeo_badge' => false,
		'vimeo_byline' => false,
		'vimeo_color' => '00adef',
		'vimeo_portrait' => false,
		'vimeo_title' => false,

		// Youtube Specific
		'youtube_autohide' => 2,
		'youtube_color' => 'red',
		'youtube_enablejsapi' => 1,
		'youtube_modestbranding' => 1,
	);
	$options = wp_parse_args( $user_options, $default_options );

	if ( $options['height'] === null ) {
		$options['height'] = round( $options['width'] * (720/1280) );
	}

	if ( $service == 'vimeo' ) {

		$video_id = vimeo_get_video_id( $video_url );
		if ( !$video_id ) return false;

		$iframe_args = array(
			'autoplay'  => $options['autoplay'] ? '1' : '0',

			'autopause' => $options['vimeo_autopause'] ? '1' : '0',
			'badge'     => $options['vimeo_badge'] ? '1' : '0',
			'byline'    => $options['vimeo_byline'] ? '1' : '0',
			'color'     => substr(str_replace('#', '', $options['vimeo_color']), 0, 6),
			'player_id' => $options['player_id'],
			'portrait'  => $options['vimeo_portrait'] ? '1' : '0',
			'title'     => $options['vimeo_title'] ? '1' : '0',
		);

		$player_url = add_query_arg( $iframe_args, '//player.vimeo.com/video/' . $video_id );

		return sprintf(
			'<iframe id="%s" src="%s" width="%s" height="%s" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
			esc_attr( $options['player_id'] ),
			esc_attr( $player_url ),
			esc_attr( $options['width'] ),
			esc_attr( $options['height'] )
		);
	}

	if ( $service == 'youtube' ) {
		$video_id = youtube_get_video_id( $video_url );
		if ( !$video_id ) return false;

		$iframe_args = array(
			'autoplay' => $options['autoplay'] ? '1' : '0',
			'origin' => site_url(),

			'color' => $options['youtube_color'] == 'white' ? 'white' : 'red',
			'autohide' => (int) $options['youtube_autohide'],
			'enablejsapi' => $options['youtube_enablejsapi'] ? '1' : '0',
			'playerapiid' => $options['player_id'],
			'modestbranding' => $options['youtube_modestbranding'] ? '1' : '0',
		);

		$player_url = add_query_arg( $iframe_args, '//www.youtube.com/embed/' . $video_id );

		return sprintf(
			'<iframe id="%s" src="%s" width="%s" height="%s" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
			esc_attr( $options['player_id'] ),
			esc_attr( $player_url ),
			esc_attr( $options['width'] ),
			esc_attr( $options['height'] )
		);
	}

	return false;
}

function video_get_image( $video_url ) {
	$service = video_get_service( $video_url );

	if ( $service == 'vimeo' ) {
		$data = vimeo_get_image_attachment( $video_url );
	}else if ( $service == 'youtube' ) {
		$data = youtube_get_image_attachment( $video_url );
	}else{
		return false;
	}

	return empty($data['attachment_id']) ? false : $data['attachment_id'];
}

function youtube_get_image_attachment( $url ) {
	$video_id = youtube_get_video_id( $url );
	if ( !$video_id ) return false;

	$video_image_option = 'youtube-image-' . $video_id;

	// Return the image that has already been uploaded for this video ID
	if ( $v = get_option( $video_image_option ) ) {
		if ( isset($v['attachment_id']) && get_post( (int) $v['attachment_id'] ) ) {
			return $v;
		}
	}

	$image_url = 'http://img.youtube.com/vi/'. $video_id .'/0.jpg';

	$attachment = ld_handle_upload_from_url( $image_url );

	if ( !empty($attachment['attachment_id']) ) {
		update_option( $video_image_option, $attachment, false );
		return $attachment;
	}

	return false;
}

function vimeo_get_image_attachment( $url ) {
	$video_id = vimeo_get_video_id( $url );
	if ( !$video_id ) return false;

	$video_image_option = 'vimeo-image-' . $video_id;

	// Return the image that has already been uploaded for this video ID
	if ( $v = get_option( $video_image_option ) ) {
		if ( isset($v['attachment_id']) && get_post( (int) $v['attachment_id'] ) ) {
			return $v;
		}
	}

	$video_data = vimeo_get_info_by_url( $url );

	if ( $video_data && !empty($video_data['thumbnail_url']) ) {
		$image_url = $video_data['thumbnail_url'];

		$attachment = ld_handle_upload_from_url( $image_url );

		if ( !empty($attachment['attachment_id']) ) {
			update_option( $video_image_option, $attachment, false );
			return $attachment;
		}
	}
}



function vimeo_get_info_by_url( $url, $option_id = null ) {
	/*
	Here is the result on a successful request, and also what will be cached. The ellipsis are added by me, you'll get full responses:
	{
	  type:              "video",
	  version:           "1.0",
	  provider_name:     "Vimeo",
	  provider_url:      "https://vimeo.com/",
	  title:             "PORTRAIT",
	  author_name:       "MILKYEYES - donato sansone",
	  author_url:        "http://vimeo.com/milkyeyes",
	  is_plus:           "0",
	  html:              "<iframe src="//player.vimeo.com/video/84241262" width="1280" ...></iframe>",
	  width:             1280,
	  height:            600,
	  duration:          171,
	  description:       "A slow and surreal video|slideshow of nightmareish, grotesque and a...",
	  thumbnail_url:     "http://b.vimeocdn.com/ts/461/023/461023899_1280.jpg",
	  thumbnail_width:   1280,
	  thumbnail_height:  600,
	  video_id:          84241262

	  // WordPress imports the thumbnail, giving you a full size embed code as well as the attachment ID so you can obtain your own.
	  // Note that this image is attached to the post obtained by get_the_ID()
	  media:             "<img src=\"http://dyscover.limelightmethod.com/wp-content/uploads/2013/10/461023899_128020.jpg\" alt=\"Vimeo: PORTRAIT\">",
	  media_id:          437,
	}
	*/

	// If no specific option ID is provided, create our own using the URL as input.
	if ( $option_id === null ) {
		$option_id = 'vimeo-' . sanitize_title( preg_replace( '/(https?:\/\/(www\.)?vimeo\.com\/)?/', '', $url ) );
	}
	if ( $option_id == 'vimeo-' ) {
		return false;
	}

	// Get the results from cache if we have it
	$from_cache = get_option( $option_id );
	if ( $from_cache ) {
		return $from_cache;
	}

	// We don't have cache, perform an oembed API request
	$oembed_url = sprintf( 'http://vimeo.com/api/oembed.json?url=%s', $url );

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $oembed_url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
	$data_raw = curl_exec( $ch );
	curl_close( $ch );
	if ( !$data_raw ) {
		return false;
	}

	$data = json_decode( $data_raw, true );
	if ( !$data ) {
		return false;
	}

	if ( $data['thumbnail_url'] ) {
		// Sideload image using media_sideload_image: http://codex.wordpress.org/Function_Reference/media_sideload_image
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$data['media'] = media_sideload_image( $data['thumbnail_url'], get_the_ID(), 'Vimeo: ' . $data['title'] );
		$data['media_id'] = image_get_attachment_id( $data['media'] );
	}

	update_option( $option_id, $data, false );

	return $data;
}

function image_get_attachment_id( $input ) {
	// $input can be either a full URL, or an embed code. In the latter case, the URL must begin with http and end with an extension
	preg_match( '/(https?:\/\/[^ \'\"]+\.(jpg|jpeg|png|gif|bmp))/', $input, $m );

	if ( $m && $m[1] ) {
		$url = $m[1];
	}else{
		$url = $input;
	}

	// Split the $url into two parts with the wp-content directory as the separator.
	$parse_url = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );
	if ( !$parse_url || !isset( $parse_url[1] ) ) {
		return false;
	}

	$parse_url[1] = preg_replace( '/-[0-9]{1,4}x[0-9]{1,4}\.(jpg|jpeg|png|gif|bmp)$/i', '.$1', $parse_url[1] );

	// Get the host of the current site and the host of the $url, ignoring www.
	$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

	// Return nothing if there aren't any $url parts or if the current host and $url host do not match.
	if ( !isset( $parse_url[1] ) || empty( $parse_url[1] ) || ( $this_host != $file_host ) ) {
		return false;
	}

	// Now we're going to quickly search the DB for any attachment GUID with a partial path match.
	// Example: /uploads/2013/05/test-image.jpg
	global $wpdb;

	$prefix = $wpdb->prefix;
	$sql = $wpdb->prepare( "SELECT ID FROM " . $prefix . "posts WHERE guid RLIKE %s;", $parse_url[1] );
	$attachment = $wpdb->get_col( $sql );

	// Returns null if no attachment is found.
	if ( !$attachment ) {
		return null;
	}

	return intval( $attachment[0] );
}

function smart_media_id( $image_url ) {
	if ( is_numeric( $image_url ) ) {
		$media_id = (int) $image_url;
	}else{
		$media_id = image_get_attachment_id( $image_url );
	}

	return $media_id > 0 ? $media_id : false;
}

function smart_media_array( $image_url, $size = 'full', $fallback_to_full = true ) {
	$result = array(
		// Global for all sizes:
		'id'          => '',
		'ID'          => '',
		'sizes'       => array(),
		'alt'         => '',
		'real_alt'    => '',
		'title'       => '',
		'description' => '',
		'caption'     => '',

		// Relative to requested size:
		'size'   => '', // This might change to 'full'!
		'url'    => '',
		'file'   => '',
		'path'   => '',
		'width'  => '',
		'height' => '',
	);

	$result['id'] = smart_media_id( $image_url );
	$result['ID'] = &$result['id']; //  alias of id

	$attachment = wp_get_attachment_metadata( $result['id'] );
	if ( !$attachment ) return false;

	$upload_dir = wp_upload_dir();

	$result['sizes'] = $attachment['sizes'];

	// "Full" doesn't get put in the sizes, but that's where we want it with this function.
	$result['sizes']['full'] = array(
		'file' => $attachment['file'],
		'width' => $attachment['width'],
		'height' => $attachment['height'],
		'mime-type' => 'image/' . pathinfo( $attachment['file'], PATHINFO_EXTENSION ),
	);

	foreach( $result['sizes'] as $key => $img ) {
		$result['sizes'][$key]['path'] = trailingslashit($upload_dir['basedir']) . $img['file'];
		$result['sizes'][$key]['url'] = trailingslashit($upload_dir['baseurl']) . $img['file'];
	}

	// Check for the requested size, if not set, fall back to full size
	if ( !isset($result['sizes'][$size]) ) {
		if ( !$fallback_to_full ) return false;

		$size = 'full';
	}

	// Requested image size info
	$result['size'] = $size;
	$result['width'] = $result['sizes'][$size]['width'];
	$result['height'] = $result['sizes'][$size]['height'];
	$result['file'] = $result['sizes'][$size]['file'];
	$result['path'] = trailingslashit($upload_dir['basedir']) . $result['file'];
	$result['url'] = trailingslashit($upload_dir['baseurl']) . $result['file'];

	// The optimal alt tag. If the real alt tag is missing, this will infer the title, description, caption or filename to get one.
	$result['alt'] = smart_media_alt( trailingslashit($upload_dir['baseurl']) . $attachment['file'] );

	$post = get_post( $result['id'] );

	if ( $post ) {
		// Return the actual alt tag, even if it is blank
		$result['real_alt'] = get_post_meta( $result['id'], '_wp_attachment_image_alt', true );

		// The rest of the image metadata
		$result['title'] = $post->post_title;
		$result['description'] = $post->post_content;
		$result['caption'] = $post->post_excerpt;
	}

	return $result;
}

function smart_media_size( $image_url, $size = 'full' ) {
	$media_id = smart_media_id( $image_url );

	if ( $media_id ) {
		$media = wp_get_attachment_image_src( $media_id, $size, false );

		if ( $media ) {
			return $media[0];
		}
	}

	return $image_url;
}

function smart_media_alt( $url ) {
	if ( !$url ) {
		return false;
	}

	// Let us use an attachment ID (or even a post ID, who cares?)
	if ( is_numeric( $url ) ) {
		$attachment_id = (int) $url;
		$src = wp_get_attachment_image_src( $attachment_id, 'full' );
		if ( $src ) {
			$url = $src[0];
		}else{
			return false;
		}
	}else{
		$attachment_id = smart_media_id( $url );
	}

	$alt = false;

	if ( has_filter( 'smart_media_alt' ) ) {
		$alt = apply_filters( 'smart_media_alt', $alt, $attachment_id, $url );
	}

	if ( $alt ) {
		return $alt;
	}

	// Look up the media alt tag
	if ( $attachment_id ) {
		$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
	}

	if ( $alt ) {
		return $alt;
	}

	// Look for the title
	if ( $attachment_id ) {
		$post = get_post( $attachment_id );
		if ( $post->post_title ) {
			$alt = _smart_alt_from_text( $post->post_title );
		}
	}

	if ( $alt ) {
		return $alt;
	}

	// Look for the media excerpt (caption)
	if ( $attachment_id ) {
		$post = get_post( $attachment_id );
		if ( $post->post_excerpt ) {
			$alt = _smart_alt_from_text( $post->post_excerpt );
		}
	}

	if ( $alt ) {
		return $alt;
	}

	// Look for the media content (description)
	if ( $attachment_id ) {
		if ( !isset( $post ) ) {
			$post = get_post( $attachment_id );
		}
		if ( $post->post_content ) {
			$alt = _smart_alt_from_text( $post->post_content );
		}
	}

	if ( $alt ) {
		return $alt;
	}

	// Create an alt tag using the filename. "http://example.org/my-amazing-image.png" will become "My Amazing Image"
	if ( !empty( $url ) && is_string( $url ) ) {
		$filename = pathinfo($url, PATHINFO_FILENAME);

		$alt = preg_replace( '/-[0-9]{1,5}x[0-9]{1,5}$/i', '', $alt ); // Remove image sizes (from thumbnails)

		$alt = _smart_alt_from_text( $filename );
	}

	return $alt;
}

function _smart_alt_from_text( $alt ) {
	$alt = preg_replace( '/[^a-zA-Z0-9]+/i', ' ', $alt ); // Replace non alphanumeric characters with whitespace
	$alt = preg_replace( '/[ \t\r\n]+/i', ' ', $alt ); // Collapse all whitespace into single spaces
	$alt = trim($alt); // trim leading and trailing whitespace
	$alt = ucfirst( $alt ); // Upper-case the first letter

	$alt = smart_excerpt( $alt, 12, '&hellip;', false, true );

	return $alt;
}

function smart_excerpt( $id_or_string = null, $word_count = 45, $more = '&hellip;', $keep_linebreaks = true, $do_shortcode = true ) {
	$text = false;
	$show_more = false;

	if ( $id_or_string === null ) {
		$id_or_string = get_post( get_the_ID() );
	}else{
		if ( is_numeric( $id_or_string ) ) {
			$post = get_post( $id_or_string );
			if ( $post ) {
				$text = $post->post_content;
			}
		}else{
			if ( is_string( $id_or_string ) ) {
				$text = $id_or_string;
			}
		}
	}

	if ( !$text ) {
		return false;
	}

	if ( has_filter( 'smart_excerpt' ) ) {
		$text = apply_filters( 'smart_excerpt', $text, $word_count, $more, $keep_linebreaks, $do_shortcode );
	}

	// Process or remove shortcodes
	if ( $do_shortcode ) {
		$text = do_shortcode( $text );
	}else{
		$text = strip_shortcodes( $text );
	}

	if ( $keep_linebreaks ) {
		// Replace ending block tags with new lines
		$text = preg_replace( '/<(\/(p|h[1-5]|div)|(br|hr)\/?)>/', "\n", $text );

		// Only keep up to two lines, do not keep whitespace around them.
		$text = preg_replace( '/[ \t]*[\r\n]{3,}[ \t]*/', "\n\n", $text );

		// Keep linebreaks, treat them as text until we're done
		$text = preg_replace( '/(\r\n|\r|\n)/', '@LINEBREAK@', $text );
	}else{
		// Do not keep any linebreaks
		$text = preg_replace( '/[ \t]*[\r\n]+[ \t]*/', ' ', $text );
	}

	// Stop at the <!--more--> tag
	if ( preg_match( '<!-- ?more.*-->', $text, $matches, PREG_OFFSET_CAPTURE ) ) {
		$text = substr( $text, 0, $matches[0][1] );

		$show_more = true;
	}

	// Strip HTML
	$text = wp_strip_all_tags( $text );

	// Add a "read more" suffix, such as "..." (default)
	if ( $text ) {
		$before = strlen( $text );
		$text = wp_trim_words( $text, $word_count, '' );

		if ( $before > strlen( $text ) && $more ) {
			$show_more = true;
		}
	}

	// Add the more tag
	if ( $show_more ) {
		$text .= $more;
	}

	if ( $keep_linebreaks ) {
		// Restore linebreaks
		$text = str_replace( '@LINEBREAK@', "\n", $text );
	}

	return $text;
}

function limelight_pagination( $show_if_empty = true ) {
	// Pagination variables...
	global $wp_query;

	$current_page = get_query_var( 'paged' );
	if ( !$current_page ) {
		$current_page = 1;
	}

	$pages_found = $wp_query->found_posts; // 40 actual posts
	$post_per_page = $wp_query->get( 'posts_per_page' ) ? $wp_query->get( 'posts_per_page' ) : get_option( 'posts_per_page' ); // 10 per page

	$page_count = ceil( $pages_found / $post_per_page ); // 3.75 pages

	// Don't show if empty.
	if ( !$show_if_empty && $pages_found == 0 ) {
		return false;
	}
	?>

	<div class="navigation">
		<?php
		if ( $current_page < $page_count ) {

			$next_url = add_query_arg( array( 'paged' => $current_page + 1 ) );

			echo '<p class="next">';
			echo '<a href="', $next_url, '" rel="next">Next Page &raquo;</a>';
			echo '</p>';
		}
		?>

		<p class="detail"><?php
			if ( $page_count == 0 ) {
				echo 'Nothing to display';
			}else{
				echo 'Page ', $current_page, ' of ', $page_count;
			}
			?></p>

		<?php
		if ( $current_page > 1 ) {

			$prev_url = add_query_arg( array( 'paged' => $current_page - 1 ) );

			echo '<p class="prev">';
			echo '<a href="', $prev_url, '" rel="prev">&laquo; Previous Page</a>';
			echo '</p>';
		}
		?>
	</div>

	<?php
}

// Returns a string of terms from a taxonomy
function limelight_list_terms( $post_ID, $taxonomy, $sep = ', ', $use_hyperlinks = true, $limit = null, $limit_text = "%s more" ) {
	$terms = get_the_terms( $post_ID, $taxonomy ); // term_id, name, slug, parent || term_taxonomy_id, taxonomy
	$term_list = array();

	$i = 0;
	if ( $terms ) {
		foreach ( $terms as $term ) {
			$i++;

			$term_str = '';

			if ( $use_hyperlinks ) {
				$term_str .= sprintf( '<a href="%s" title="%s" class="term-item">', esc_attr( get_term_link( $term ) ), esc_attr( $term->name ) );
			}

			$term_str .= esc_html( $term->name );

			if ( $use_hyperlinks ) {
				$term_str .= '</a>';
			}

			$term_list[] = $term_str;

			if ( $limit !== null && $i == $limit && count( $terms ) > $limit + 1 ) {
				if ( strstr( $limit_text, '%s' ) ) {
					$more = sprintf( $limit_text, count( $terms ) - 3 );
				}else{
					$more = $limit_text;
				}

				$term_list[] = sprintf( '<span class="more-terms">%s</span>', $more );
				break;
			}
		}
	}

	return implode( $sep, $term_list );
}


function limelight_archive_thumbnail( $post_id = false, $size = 'thumbnail' ) {
	if ( !$post_id ) $post_id = get_the_ID();
	if ( !$post_id ) return false;

	// Use featured image if provided
	$featured = get_the_post_thumbnail( $post_id, $size );
	if ( $featured ) return $featured[0];

	// Look for an image in post meta. Meta key can be customized via filter or directly.
	$meta_key = apply_filters( 'ld-archive-thumbnail-meta-key', 'featured-image', $post_id, $size );

	if ( $meta_key ) {
		$meta_thumbnail = get_post_meta( $post_id, $meta_key, true );

		if ( $meta_thumbnail ) {
			$img = false;

			if ( is_numeric($meta_thumbnail) ) {
				// Attachment ID
				$img = smart_media_size( (int) $meta_thumbnail, $size );
			} else if ( is_string($meta_thumbnail) ) {
				// Image URL
				$img = smart_media_size( (string) $meta_thumbnail, $size );
			}

			if ( $img ) {
				return $img;
			}
		}
	}

	// If we have already detected one through content before, use the cached version.
	$meta_thumbnail = get_post_meta( $post_id, 'lm_thumbnail', true );

	if ( $meta_thumbnail ) {
		$img = smart_media_size( (string) $meta_thumbnail, $size );

		if ( $img ) return $img;
	}

	// In case we have looked before but didn't find anything
	if ( $meta_thumbnail === 0 ) {
		return false;
	}

	// We're going to scrape the post content and look for an image. We'll store it in lm_thumbnail if we find it so we don't have to scrape in the future.
	global $post;

	if ( $post->ID == $post_id ) {
		$content = $post->post_content;

		// Looks for an image, roughly checking if it is on this website
		// Regex looks like: <img*src=*(__frequentflyeracademy.com__)*
		if ( !preg_match( '/<img.*?src=[\'\"](.*?' . $_SERVER['HTTP_HOST'] . '.*?)[\'\"].*?>/i', $content, $matches ) ) {
			return false;
		}

		$attachment_id = image_get_attachment_id( $matches[1] );

		if ( $attachment_id ) {

			// Attachment ID was found, return the thumbnail
			$attachment = wp_get_attachment_image_src( $attachment_id, $size );

			if ( $attachment ) {
				$attachment_src = sprintf( '<img src="%s" alt="%s - Featured Image" width="%s" height="%s" />', $attachment[0], // attachment URL
					esc_attr( get_the_title() ), $attachment[1], $attachment[2] // Width/height
				);

				update_post_meta( $post_id, 'lm_thumbnail', $attachment_src );

				return $attachment_src;
			}

		}

		// No attachment found, cannot use a thumbnail. Return the full size image.
		$default_src = sprintf( '<img src="%s" alt="%s - Featured Image" />', $matches[1], esc_attr( get_the_title() ) );
		update_post_meta( $post_id, 'lm_thumbnail', $default_src );

		return $default_src;
	}

	update_post_meta( $post_id, 'lm_thumbnail', 0 );
	return false;
}


function limelight_file_download( $file, $filename = null, $attachment = true ) {
	if ( preg_match( '/^https?:\/\//', $file ) ) {
		$file = untrailingslashit( ABSPATH ) . wp_make_link_relative( $file );
	}

	if ( !$file || !file_exists( $file ) ) {
		$msg = '<h3>File does not exist</h3><p>The requested file does not exist.</p>';
		if ( current_user_can( 'administrator' ) ) {
			$msg .= '<p><strong>Admin Notice:</strong> The requested file does not exist on the server:</p><pre>' . esc_html( $file ) . '</pre></p>';
		}
		wp_die( $msg );
		exit;
	}

	$pathinfo = pathinfo( $file );

	if ( $filename === null ) {
		// Use the original filename by default
		$filename = $pathinfo["basename"];
	}else{
		// Add extension to filename if it hasn't been added already
		if ( !strstr( $filename, '.' . $pathinfo["extension"] ) ) {
			$filename .= '.' . $pathinfo["extension"];
		}
	}

	while ( ob_get_level() ) {
		ob_end_clean();
	}

	header( 'Accept-Ranges: bytes' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-Type: application/octet-stream' );
	header( 'Content-Transfer-Encoding: binary' );

	// Attachment: Force download as a file directly.
	// Inline: Display in browser (if plugin available). Great for images and PDFs
	if ( $attachment ) {
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	}else{
		header( 'Content-Disposition: inline; filename="' . $filename . '"' );
	}

	header( 'Connection: Keep-Alive' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Pragma: public' );
	if ( $fz = filesize( $file ) ) {
		header( 'Content-Length: ' . $fz );
	}

	@readfile( $file );
	exit;
}



/**
 * Retrives an image from a URL and uploads it using ld_handle_upload_from_path. See that function for more details.
 *
 * Note: This function should also work for local file paths as well, but the implementation is slightly different than ld_handle_upload_from_path.
 *
 * @param $image_url
 * @param int $attach_to_post
 * @param bool|true $add_to_media
 * @return array|bool
 */
function ld_handle_upload_from_url( $image_url, $attach_to_post = 0, $add_to_media = true ) {
	$remote_image = fopen($image_url, 'r');

	if ( !$remote_image ) return false;

	$meta = stream_get_meta_data( $remote_image );

	$image_meta = false;
	$image_filetype = false;

	if ( $meta && !empty($meta['wrapper_data']) ) {
		foreach( $meta['wrapper_data'] as $v ) {
			if ( preg_match('/Content\-Type: ?((image)\/?(jpe?g|png|gif|bmp))/i', $v, $matches ) ) {
				$image_meta = $matches[1];
				$image_filetype = $matches[3];
			}
		}
	}

	// Resource did not provide an image.
	if ( !$image_meta ) return false;

	$v = basename($image_url);
	if ( $v && strlen($v) > 6 ) {
		// Create a filename from the URL's file, if it is long enough
		$path = $v;
	}else{
		// Short filenames should use the path from the URL (not domain)
		$url_parsed = parse_url( $image_url );
		$path = isset($url_parsed['path']) ? $url_parsed['path'] : $image_url;
	}

	$path = preg_replace('/(https?:|\/|www\.|\.[a-zA-Z]{2,4}$)/i', '', $path );
	$filename_no_ext = sanitize_title_with_dashes( $path, '', 'save' );

	$extension = $image_filetype;
	$filename = $filename_no_ext . "." . $extension;

	// Simulate uploading a file through $_FILES. We need a temporary file for this.
	$stream_content = stream_get_contents( $remote_image );

	$tmp = tmpfile();
	$tmp_path = stream_get_meta_data( $tmp )['uri'];
	fwrite( $tmp, $stream_content );
	fseek( $tmp, 0 ); // If we don't do this, WordPress thinks the file is empty

	$fake_FILE = array(
		'name'     => $filename,
		'type'     => 'image/' . $extension,
		'tmp_name' => $tmp_path,
		'error'    => UPLOAD_ERR_OK,
		'size'     => strlen( $stream_content ),
	);

	// Trick is_uploaded_file() by adding it to the superglobal
	$_FILES[basename( $tmp_path )] = $fake_FILE;

	// For wp_handle_upload to work:
	include_once ABSPATH . 'wp-admin/includes/media.php';
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/image.php';

	$result = wp_handle_upload( $fake_FILE, array(
		'test_form' => false,
		'action'    => 'local',
	) );

	fclose( $tmp ); // Close tmp file
	@unlink( $tmp_path ); // Delete the tmp file. Closing it should also delete it, so hide any warnings with @
	unset( $_FILES[basename( $tmp_path )] ); // Clean up our $_FILES mess.

	fclose( $remote_image ); // Close the opened image resource

	$result['attachment_id'] = 0;

	if ( empty( $result['error'] ) && $add_to_media ) {
		$args = array(
			'post_title'     => $filename_no_ext,
			'post_content'   => '',
			'post_status'    => 'publish',
			'post_mime_type' => $result['type'],
		);

		$result['attachment_id'] = wp_insert_attachment( $args, $result['file'], $attach_to_post );

		$attach_data = wp_generate_attachment_metadata( $result['attachment_id'], $result['file'] );
		wp_update_attachment_metadata( $result['attachment_id'], $attach_data );

		if ( is_wp_error( $result['attachment_id'] ) ) {
			$result['attachment_id'] = 0;
		}
	}

	return $result;
}

/**
 * Takes a path to a file, simulates an upload and passes it through wp_handle_upload. If $add_to_media
 * is set to true (default), the file will appear under Media in the dashboard. Otherwise, it's hidden,
 * but stored in the uploads folder.
 *
 * Return Values: Similar to wp_handle_upload, but with attachment_id:
 *  - Success: Returns an array including file, url, type, attachment_id.
 *  - Failure: Returns an array with the key "error" and a value including the error message.
 *
 * @param $path
 * @param int $attach_to_post
 * @param bool $add_to_media
 *
 * @return array
 */
function ld_handle_upload_from_path( $path, $attach_to_post = 0, $add_to_media = true ) {
	if ( !file_exists( $path ) ) {
		return array( 'error' => 'File does not exist.' );
	}

	$filename = basename( $path );
	$filename_no_ext = pathinfo( $path, PATHINFO_FILENAME );
	$extension = pathinfo( $path, PATHINFO_EXTENSION );

	// Simulate uploading a file through $_FILES. We need a temporary file for this.
	$tmp = tmpfile();
	$tmp_path = stream_get_meta_data( $tmp )['uri'];
	fwrite( $tmp, file_get_contents( $path ) );
	fseek( $tmp, 0 ); // If we don't do this, WordPress thinks the file is empty

	$fake_FILE = array(
		'name'     => $filename,
		'type'     => 'image/' . $extension,
		'tmp_name' => $tmp_path,
		'error'    => UPLOAD_ERR_OK,
		'size'     => filesize( $path ),
	);

	// Trick is_uploaded_file() by adding it to the superglobal
	$_FILES[basename( $tmp_path )] = $fake_FILE;

	// For wp_handle_upload to work:
	include_once ABSPATH . 'wp-admin/includes/media.php';
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/image.php';

	$result = wp_handle_upload( $fake_FILE, array(
		'test_form' => false,
		'action'    => 'local',
	) );

	fclose( $tmp ); // Close tmp file
	@unlink( $tmp_path ); // Delete the tmp file. Closing it should also delete it, so hide any warnings with @
	unset( $_FILES[basename( $tmp_path )] ); // Clean up our $_FILES mess.

	$result['attachment_id'] = 0;

	if ( empty( $result['error'] ) && $add_to_media ) {
		$args = array(
			'post_title'     => $filename_no_ext,
			'post_content'   => '',
			'post_status'    => 'publish',
			'post_mime_type' => $result['type'],
		);

		$result['attachment_id'] = wp_insert_attachment( $args, $result['file'], $attach_to_post );

		$attach_data = wp_generate_attachment_metadata( $result['attachment_id'], $result['file'] );
		wp_update_attachment_metadata( $result['attachment_id'], $attach_data );

		if ( is_wp_error( $result['attachment_id'] ) ) {
			$result['attachment_id'] = 0;
		}
	}

	return $result;
}

/**
 * Get the "Read More" text label to use on archive pages for a post.
 * This can be customized per post. Videos may want "Watch More", for example.
 *
 * @param int|null $post_id
 *
 * @return string
 */
function get_read_more_text( $post_id = null, $default = 'Read More' ) {
	if ( $post_id === null ) {
		$post_id = get_the_ID();
	}

	$read_more_text = get_post_meta( $post_id, 'read_more_button_text', true );
	
	if ( ! $read_more_text ) {
		$read_more_text = $default;
	}

	return $read_more_text;
}