<?php
/*

Functions
  
  generate_sharing_links( $sites, $args_or_id )
    Generates an array of sharing links based on the given arguments.
    
    $args_or_id:
      If not provided, will use current page.
      If provided as a is_numeric value, will be used as ID
      Default usage is an array with the following options:
          ID             The title/url/image will inherit from the given post ID. The image will utilize the default featured image, if available.
          title          Inherits current page title if not provided (and ID is not provided)
          url            Inherits current page url if not provided (and ID is not provided)
          image          (not used for all services)
          type           The post/content type (default: '')
          use_html       True by default, which returns an array of HTML links. False returns an array of plain URLs.
          before_text    HTML added inside the <a> element, before the text (default: <span class="share-text">)
          after_text     HTML added inside the <a> element, after the text (default: </span>)
          twitter_user   Name of the twitter user name to use as a retweet
          element  The element to wrap each result in, which contains the class "share-item" as well as sharing site class such as "share-facebook" (Default: span)
    
    $sites:
      True or "all" will include all sites
      
      Available sites:
      'facebook' (NOTE: Facebook looks for open graph tags on the page, and may not use your specific settings. An ID or link must be provided for facebook!)
      'twitter' 
      'googleplus' (Alias: google, google+)
      'pinterest'

*/

function generate_sharing_links( $sites = true, $args_or_id = null ) {
  // Defaults for $sites
  if ( $sites === true || $sites === 'all' ) $sites = array( 'facebook', 'googleplus', 'twitter', 'pinterest' );
  if ( !$sites ) return false;
  
  // $sites must be an array of lower case strings
  if ( !is_array($sites) ) $sites = (array) $sites;
  foreach($sites as $k=>$v) $sites[$k] = strtolower($v);
  if ( !$sites ) return false;
  
  // Defaults for args
  if ( $args_or_id === null ) {
    $args_or_id = array( 'ID' => get_the_ID() );
  }else if ( !is_array($args_or_id) && is_numeric($args_or_id) ) {
    $args_or_id = array( 'ID' => absint($args_or_id) );
  }
  
  // Fill our arguments with default values
  $default = array(
    'ID'          => null,
    'title'       => null,
    'url'         => null,
    'image'       => null,
    'type'       => null,
    
    'use_html'    => true,
    
    'before_text' => '<span class="share-text">',
    'after_text'  => '</span>',
    'element' => 'span',
    
    'twitter_user'  => false,
  );
  $args = wp_parse_args( $args_or_id, $default );
  $args = apply_filters('sharing_link_args', $args, $sites, $args_or_id);
  
  // Build the item that will be shared
  $share_item = array(
    'title' => '',
    'url' => '',
    'image' => '',
    'type' => '',
  );
  
  // Use content from a wordpress post if an ID is specified
  if ( $args['ID'] !== null ) {
    $id = absint($args['ID']);
    $share_item['title'] = get_the_title( $id );
    $share_item['url'] = get_permalink( $id );
    $share_item['image'] = get_the_post_thumbnail( $id, 'full' );
    $share_item['type'] = get_post_type( $id );
  }
  
  // Use specified title/url/image if specified.
  if ( $args['title'] !== null ) $share_item['title'] = $args['title'];
  if ( $args['url'] !== null ) $share_item['url'] = $args['url'];
  if ( $args['image'] !== null ) $share_item['image'] = smart_media_size( $args['image'], 'full' );
  if ( $args['type'] !== null ) $share_item['type'] = $args['type'];
  
  // Allow filtering our sharing object, especially useful if you use a custom featured image in your theme.
  if ( has_filter('sharing_links_format') ) $share_item = apply_filters( 'sharing_links_format', $share_item, $args['ID'], $args );
  
  // If not provided elsewhere, use the current page url and title
  if ( !$args['url'] ) $share_item['url'] = get_permalink();
  if ( !$args['title'] ) $share_item['title'] = get_the_title();
  
  // Build a list of links. Each item should have the "text", "url" and "attr" properties. The text is what is displayed in the link.
  $share_links = array();
  
  // -------- Begin social site links
  
  if ( in_array( 'facebook', $sites ) ) {
    // Note: Facebook will check for open graph tags at $args['url'] and use that instead of the provided options
    $url_args = array();
    if ( $share_item['url'] ) $url_args['u'] = $share_item['url'];
    if ( $share_item['title'] ) $url_args['t'] = $share_item['title'];
    
    $url = add_query_arg( $url_args, 'https://www.facebook.com/sharer/sharer.php' );
  
    $onclick = 'javascript:window.open(this.href, \'_blank\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600\');return false;';
  
    $share_links['facebook'] = apply_filters('sharing_link', array(
      'text' => $share_item['type'] ? 'Share ' . strtolower($share_item['type']) . ' on Facebook' : 'Share on Facebook',
      'url' => $url,
      'onclick' => $onclick,
	  'attr' => '',
    ), 'facebook');
  }
  
  if ( in_array( 'twitter', $sites ) ) {
    $url_args = array();
    if ( $share_item['url'] ) $url_args['url'] = $share_item['url'];
    if ( $share_item['title'] ) $url_args['text'] = $share_item['title'];
    if ( $args['twitter_user'] ) $url_args['via'] = $args['twitter_user'];
      
    $url = add_query_arg( $url_args, 'https://twitter.com/share' );
  
    $onclick = 'javascript:window.open(this.href, \'_blank\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600\');return false;';
  
    $share_links['twitter'] = apply_filters('sharing_link', array(
      'text' => $share_item['type'] ? 'Tweet this ' . strtolower($share_item['type']) : 'Share on Twitter',
      'url' => $url,
      'onclick' => $onclick,
	  'attr' => '',
    ), 'twitter');
  }
  
  if ( in_array( 'googleplus', $sites ) || in_array( 'google', $sites ) || in_array( 'google+', $sites ) ) {
    $url_args = array();
    if ( $share_item['url'] ) $url_args['url'] = $share_item['url'];
      
    $url = add_query_arg( $url_args, 'https://plus.google.com/share' );
  
    $onclick = 'javascript:window.open(this.href, \'_blank\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=350,width=480\');return false;';
  
    $share_links['googleplus'] = apply_filters('sharing_link', array(
      'text' => $share_item['type'] ? 'Share ' . strtolower($share_item['type']) . ' on Google+' : 'Share on Google+',
      'url' => $url,
      'onclick' => $onclick,
	  'attr' => '',
    ), 'googleplus');
  }
  
  if ( in_array( 'pinterest', $sites ) ) {
    if ( !$share_item['image'] ) $share_item['image'] = apply_filters('share_default_image', false, 'pinterest');
    
    if ( $share_item['image'] ) {
      // We can only share on pinterest if we have an image.
      $url_args = array();
      if ( $share_item['url'] ) $url_args['url'] = $share_item['url'];
      if ( $share_item['title'] ) $url_args['description'] = $share_item['title'];
      if ( $share_item['image'] ) $url_args['media'] = $share_item['image'];
      
      $url = add_query_arg( $url_args, 'http://pinterest.com/pin/create/button/' );
      $attr = 'onclick="javascript:window.open(this.href, \'_blank\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600\');return false;"';
    
      $share_links['pinterest'] = apply_filters('sharing_link', array(
        'text' => $share_item['type'] ? 'Pin this ' . strtolower($share_item['type']) : 'Share on Pinterest',
        'url' => $url,
        'attr' => $attr,
        'onclick' => '',
      ), 'pinterest');
    }
  }
  
  // -------- End of social site links
  
  $share_links = apply_filters( 'sharing_links_ready', $share_links, $args );
  if ( !$share_links ) return false;
  
  // Begin building the results
  if ( $args['use_html'] ) {
    
    // Format our links as HTML items, and return in a numeric array
    $result = array();
    
    foreach($share_links as $k => $share) {
      $result[] = sprintf(
        '<%s class="share-item share-%s"><a href="%s" %s %s target="_blank" title="%s">%s%s%s</a></%s>',
        esc_attr( $args['element'] ),
        esc_attr( $k ),
        esc_attr( $share['url'] ),
        $share['onclick'] ? 'onclick="'. esc_attr($share['onclick']) .'"' : '',
        $share['attr'] ? 'attr="'. esc_attr($share['attr']) .'"' : '',
        esc_attr( $share_item['title'] ),
        $args['before_text'],
        esc_html( $share['text'] ),
        $args['after_text'],
        esc_attr( $args['element'] )
      );
    }
    
    return $result;
    
  }else{
    
    // Return our sharing link structure without formatting it.
    return $share_links;
    
  }
}

// Replace "Events" with "Event" for our sharing function
function sharing_links_rename_events( $share_item, $id, $args ) {
  if ( $share_item['type'] == 'events' ) $share_item['type'] = 'event';
  return $share_item;
}
add_filter( 'sharing_links_format', 'sharing_links_rename_events', 10, 3 );




