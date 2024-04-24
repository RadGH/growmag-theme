<?php

class DI_Mailchimp_API {
	
	private $api_key;
	private $list_id;
	private $server_code;
	private $signup_source = 'Website - Digital Issues';
	
	public function __construct( $api_key, $list_id, $server_code ) {
		$this->api_key = $api_key;
		$this->list_id = $list_id;
		$this->server_code = $server_code;
	}
	
	/**
	 * Get the visitor's IP address
	 *
	 * @return string
	 */
	public function get_visitor_ip_address() {
		$ip_address = $_SERVER['REMOTE_ADDR'];
		if ( !empty($_SERVER["HTTP_CF_CONNECTING_IP"]) ) $ip_address =  $_SERVER["HTTP_CF_CONNECTING_IP"];
		if ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		return (string) $ip_address;
	}
	
	/**
	 * Get the subscriber hash, used by the API
	 *
	 * @param string $email
	 * @return string
	 */
	public function get_subscriber_hash( $email ) {
		return md5( strtolower( $email ) );
	}
	
	/**
	 * Check if a subscriber already exists
	 *
	 * @param string $email
	 * @param string|null $list_id
	 *
	 * @return bool|string  True if status = subscribed. Status such as "unsubscribed" otherwise. False if no user found.
	 */
	public function is_email_subscribed( $email, $list_id = null) {
		if ( $list_id === null ) $list_id = $this->list_id;
		
		$subscriber_hash = $this->get_subscriber_hash( $email );
		
		// curl -X GET
		//  https://${dc}.api.mailchimp.com/3.0/lists/{list_id}/members/{subscriber_hash}
		//  ?fields=<SOME_ARRAY_VALUE>&exclude_fields=<SOME_ARRAY_VALUE>
		//  --user "anystring:${apikey}"'
		
		// Prepare URL
		$url = "https://{$this->server_code}.api.mailchimp.com/3.0/lists/{$list_id}/members/{$subscriber_hash}";
		
		// Prepare the fields
		$url_data = array(
			'fields' => array( 'id', 'email_address', 'status' ),
			'exclude_fields' => array(),
		);
		
		$body_data = array();
		
		// Get the response
		$response = $this->get_response( 'GET', $url, $url_data, $body_data );
		
		if ( $response['response_code'] === 200 ) {
			$body = $response['body'];
			if ( $body['status'] ) {
				if ( $body['status'] === 'subscribed' ) {
					return true;
				}else{
					return $body['status'];
				}
			}
		}
		
		return false;
		
	}
	
	/**
	 * Attempt to subscribe a user's email
	 *
	 * @param string $email
	 * @param string|null $list_id The list ID to subscribe to
	 * @param bool $allow_existing If true and a user is already subscribed, this
	 *                             function will return true as if it was a new subscriber.
	 *
	 * @return bool
	 */
	public function subscribe_email( $email, $list_id = null, bool $allow_existing = true ) {
		if ( $list_id === null ) $list_id = $this->list_id;
		
		// curl -X GET
		//  https://${dc}.api.mailchimp.com/3.0/lists/{list_id}/members/{subscriber_hash}
		//  ?fields=<SOME_ARRAY_VALUE>&exclude_fields=<SOME_ARRAY_VALUE>
		//  --user "anystring:${apikey}"'
		
		// Prepare URL
		$url = "https://{$this->server_code}.api.mailchimp.com/3.0/lists/{$list_id}/members";
		
		// Prepare the fields
		$url_data = array(
			// 'skip_merge_validation' => 'true',
		);
		
		$body_data = array(
			'email_address' => $email,
			'source' => $this->signup_source,
			'ip_signup' => $this->get_visitor_ip_address(),
			'ip_opt' => $this->get_visitor_ip_address(),
			'status' => 'pending',
		);
		
		// Get the response
		$response = $this->get_response( 'POST', $url, $url_data, $body_data );
		
		if ( $response['response_code'] === 200 ) {
			$body = $response['body'];
			if ( $body['status'] ) {
				if ( $body['status'] === 'subscribed' ) {
					return true;
				}else{
					return $body['status'];
				}
			}
		}
		
		// Already subscribed. Only consider that an error if allow_existing is false.
		if ( $response['response_code'] === 400 && $response['body']['title'] === 'Member Exists' ) {
			return $allow_existing;
		}
		
		return false;
	}
	
	/**
	 * Perform a generic API response by supplying the method, URL, and data
	 *
	 * @param string $method    The HTTP method to use
	 * @param string $url       The URL to send the request to
	 * @param array $url_data   Data to add to the URL during the request. "Query Parameters" in the docs.
	 * @param array $body_data  Data to add to the body of the request. "Body Parameters" in the docs.
	 *
	 * @return array
	 */
	public function get_response( $method, $url, $url_data, $body_data ) {
		
		// Apply URL data to the URL
		if ( $url_data ) {
			$url = add_query_arg($url_data, $url);
		}
		
		// Prepare request
		$request = array(
			'method' => $method,
			'headers' => array( 'Authorization' => 'api ' . $this->api_key ),
		);
		
		if ( $body_data ) $request['body'] = json_encode($body_data);
		
		// Send request
		$method = strtolower($method);
		
		switch( $method ) {
			
//			case 'post':
//				$response = wp_remote_post( $url, $request );
//				break;
//
//			case 'get':
//				$response = wp_remote_get( $url, $request );
//				break;
				
			default:
				$response = wp_remote_request( $url, $request );
				break;
				
		}
		
		// Return the response data
		$data = array(
			'response_code' => wp_remote_retrieve_response_code( $response ), // int(200)
			'response_message' => wp_remote_retrieve_response_message( $response ), // "OK"
			'body' => wp_remote_retrieve_body( $response ) // JSON string
		);
		
		if ( $data['body'] ) {
			$decode = json_decode( $data['body'], true );
			if ( $decode !== null ) $data['body'] = $decode;
		}
		
		// Check if error, and set the error message
		// (Errors are anything not in the 200 range)
		if ( $data['response_code'] < 200 || $data['response_code'] >= 300 ) {
			$this->set_last_response(false);
			$this->set_last_error($data);
		}else{
			$this->set_last_response($data);
			$this->set_last_error(false);
		}
		
		return $data;
	}
	
	private $last_response = null;
	private function set_last_response($data) {
		$this->last_response = $data;
	}
	public function get_last_response() {
		return $this->last_response;
	}
	
	
	private $last_error = null;
	private function set_last_error($data) {
		$this->last_error = $data;
	}
	public function get_last_error() {
		return $this->last_error;
	}
}