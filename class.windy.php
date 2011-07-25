<?php

/*
 *   @package		windy-php
 *   @author		Paul Weinstein, <pdw@weinstein.org>
 *   @version		0.1
 *	@copyright	Copyright (c) 2011 Paul Weinstein, <pdw@weinstein.org>
 *	@license		MIT License, <https://github.com/pdweinstein/PHP-Wrapper-for-CTA-APIs/blob/master/LICENSE>
 *
 *	Copyright (c) 2011 Paul Weinstein, <pdw@weinstein.org>
 *
 *	Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files 
 *	(the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, 
 *	publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do
 *	so, subject to the following conditions:
 *
 *	The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 *	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 *	MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE 
 *	FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION 
 *	WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. 
 *
 */

// Class is in session
class windy {

	var $apiURL = 'data.cityofchicago.org/';
	var $apiKey = '';
	var $format = '';
	var $timeout = '300';
	var $debug = false;

	/* 	Format types, JSON, XML, RDF, XLS and XLSX (Execl), CSV, TXT, PDF
		All these different formats, what to do?
		If the format is developer centric, let's take the extra step and give the developer
		something to run with. For example, if JSON, let's go ahead and decode it. 
		Note: This means for XML we'll need SimpleXML installed. For RDF it will mean having to add http://pear.php.net/package/RDF
		Note: And to make this even more fun, our CSV parser function, str_getcsv is PHP 5.3 or greater 
			
		On the other hand, if the format is user centric, let's just dump it to the developer
		and let them figure out what to do with it.
	*/
	
	// API key can be provided for additional functionality, but not required.
	public function __construct( $format, $apiKey = '', $debug = false ) {
	
		$this->format = $format;
		$this->apiKey = $apiKey;
		$this->debug = $debug;	
	
	}
	
	/**
	 *	getViews function, Get views that match a given set of criteria.
	 * 
	 *	@access public
	 *	@param	string	$category filters for views matching this category. Optional. Default: ''
	 *	@param	string	$name filters for views containing this text in their name Optional. default: ''
	 *	@param	string	$desc filters for views with this text in their description. Optional. default: ''
	 *	@param 	string	$tags filters for views matching these tags. Optional. default: ''
	 *	@param 	string	$full filters for views with this text in the metadata or content. Optional. default: ''
	 *	@param 	boolean	$count executes the query with the given parameters and only returns the total number of rows
	 *						ignoring the limit. Optional. default: 'false'
	 *	@param 	string	$limit the number of results to return, up to 200 at a time. Optional.default: ''
	 *	@param 	string	$page number to retrieve additional pages of results. Optional.default: ''
	 *	@return
	 *
	 */
	public function getViews( $category = '', $name = '', $desc = '', $tags = '', $full = '', $count = 'false', $limit = '', $page = '' ) {
	
		$args = 'category=' .urlencode( $category ). '&name=' .urlencode( $name ). '&description=' .urlencode( $desc ). '&tags=' .urlencode( $tags ). '&full=' .urlencode( $full ). '&count=' .urlencode( $count ). '&limit=' .urlencode( $limit ). '&page=' .urlencode( $page );
		$response = $this->httpRequest( $this->apiURL. 'views.' .$this->format, $args );
	 
		if ( $this->format == 'json' ) {
		
			return json_decode( $response );
		
		} else if ( $this->format == 'xml' ) {
		
			return simplexml_load_string( $response ); 
	
		} else if ( $this->format == 'csv' ) {
		
			$return = array();
			
			// First let us split up each line of data into an array
			$lines = str_getcsv( $response, '\n' ); 
			$noLines = sizeof( $lines );
			
			for( $counter = 0; $counter <= $noLines; $counter++ ) {
			
				$return[$counter] = str_getcsv( $lines[$counter], ',' );
			
			}
		
		} else {
		
			return $response;
		
		}
		
	}
	
	 /**
	 *	httpRequest: A method for handling HTTP requests
	 * 
	 *	@access	private
	 *	@param	string	$requestURL, URL for HTTP request Required
	 *	@param	array	$args, arguments for HTTP request. Optional
      *	@param	bool		$type, Method for HTTP request. Default: GET
      *						Options: GET, POST, PUT and DELETE
	 *	@return	string	Results of HTTP request 	
	 *	@author	Paul Weinstein
	 *
	 */
	private function httpRequest( $reqURL, $args = '', $type = 'GET' ) {

		
		$reqURL = 'http://' .$reqURL;
			
		// Configure cURL for our request
		$curl_handle = curl_init();
		
		// Set our HTTP method
		if ( $type == 'POST' ) {
		                  
			curl_setopt( $curl_handle, CURLOPT_POST, 1 );
			curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, $args );

		} else if ( $type == 'PUT' ) {
		
			curl_setopt( $curl_handle, CURLOPT_CUSTOMREQUEST, "PUT" );
			curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, $args );

		} else if ( $type == 'DELETE' ) {
		
			curl_setopt( $curl_handle, CURLOPT_CUSTOMREQUEST, "DELETE" );

		} else  if ( $type == 'GET' ) {
		
			// We should fall in here by 'default'
			curl_setopt( $curl_handle, CURLOPT_HTTPGET, 1 );
			
			if ( $args != '' ) {
			
				$reqURL .= "?".$args;

			}
		}
		
		// Provide our URL
		curl_setopt ( $curl_handle, CURLOPT_URL, $reqURL );	
		
		// Set add'l cURL headers
		curl_setopt( $curl_handle, CURLOPT_HEADER, 0 );
		curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl_handle, CURLOPT_TIMEOUT, $this->timeout ); 
		curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT, 1 );

		// Debug?
		if ( $this->debug ) {
		
			echo 'Web Service Request: Request URL ' .$reqURL. ' via ' .$type. ' with ' .$args. '\n';
		
		}
		
		// And execute
		$response = curl_exec( $curl_handle );
		$code = curl_getinfo( $curl_handle, CURLINFO_HTTP_CODE );
		
		// Close up shop
		curl_close( $curl_handle );
					
		if (( $code != '200' ) OR ( $this->debug )) {
		
			echo 'Web Service Request: Returned Code: ' .$code. ' and Returned Results ' .$response. '\n';
		
		}

		return $response;

	}

}
// Class Dismissed

?>