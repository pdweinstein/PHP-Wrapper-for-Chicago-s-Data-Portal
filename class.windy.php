<?php

/*
 *   @package		windy-php
 *   @author		Paul Weinstein, <pdw@weinstein.org>
 *   @version		0.15
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
		Initial idea: if the format is developer centric, let's take the extra step and give the developer
		something to run with. For example, if JSON, let's go ahead and decode it a return a usable object or array
		For XML this would mean the developer would need SimpleXML installed. For RDF it would mean having to add http://pear.php.net/package/RDF
		and to make this even more fun, our CSV parser function, str_getcsv is PHP 5.3 or greater 
		
		So this could get real messy, real fast. Instead let's try this: Everything we call for is via JSON an we'll decode into an object. If the
		developer wants an array instead, they can specify that. Otherwise, if they want XML or XLS or whatever, they're on their own.
	*/
	
	// API key can be provided for additional functionality, but not required.
	public function __construct( $format = 'json', $type = 'object', $apiKey = '', $debug = false ) {
	
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
	 *	@param 	string	$page number to retrieve additional pages of results. Optional. default: ''
	 *	@return
	 *
	 */
	public function getViews( $category = '', $name = '', $desc = '', $tags = '', $full = '', $count = 'false', $limit = '', $page = '' ) {
	
		$args = 'category=' .urlencode( $category ). '&name=' .urlencode( $name ). '&description=' .urlencode( $desc ). '&tags=' .urlencode( $tags ). '&full=' .urlencode( $full ). '&count=' .urlencode( $count ). '&limit=' .urlencode( $limit ). '&page=' .urlencode( $page );
		$response = $this->httpRequest( $this->apiURL. 'views.' .$this->format, $args );
	 
		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}
		
	}
	
	public function getViewsByID( $viewID ) {

		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '.' .$this->format );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}				
	
	}
	
	public function getColumnViewsByViewID( $viewID ) {
	
		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '/columns.' .$this->format );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}					
	
	}
	
	public function getColumnViewsByColumnID( $viewID, $columnID ) {
	
		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '/columns/' .$columnID. '.' .$this->format );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}	
	
	}
	
	public function getSubColumns( $viewID, $columnID ) {

		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '/columns/' .$columnID. '/sub_columns.' .$this->format );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}
			
	}

	// Should take a filename and path arguments and save file in specificed location?
	public function getFileByViewID( $fileID, $viewID ) {
	
		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '/files/' .$fileID );		
		return $response;
			
	}

	/*
	string	view ID is the ID of the view that contains the row, Required
	boolean	row_ids_only the service will only return row IDs id true. Optional. Default is false.
	integer	max_rows is the limit the number of rows returned. Optional. Default '' (return all rows).
	integer	include_ids_after, include this number of rows, after which only row IDs are returned. Optional. Default ''
	string	search, run a full text search on the view and only return rows/ids that match. Optional. Default ''
	boolean	meta, if set to 'true', will write the view object. Only valid if rendering JSON. Optional. Default is true.
	boolean	as_hashes, if set to 'true', write fields in hash format. Otherwise, write fields in array. 
				Only valid if rendering JSON. Default to false (array).
	boolean	most_recent, if set to 'true', return only the most recent rows added to the dataset. Only valid if rendering RSS. Default to true.
	string	access_type, valid values are PRINT, EMAIL, API, RSS, WIDGET, DOWNLOAD, WEBSITE Optional. Default ''
	*/	
	public function getRows( $viewID, $row_ids_only = 'false', $max_rows, $include_ids_after, $search, $meta = 'true', $as_hashes = 'false', $most_recent = 'true', $access_type ) {
	
			$args = 'row_ids_only=' .urlencode( $row_ids_only ). '&max_rows=' .urlencode( $max_rows ). '&include_ids_after=' .urlencode( $include_ids_after ). '&search=' .urlencode( $search ). '&meta=' .urlencode( $meta ). '&as_hashes=' .urlencode( $as_hashes ). '&most_recent=' .urlencode( $most_recent ). '&access_type=' .urlencode( $access_type );
		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. 'rows.' .$this->format, $args );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}
	
	}
	
	public function getRowByRowID( $viewID, $rowID ) {
	
		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '/rows/' .$rowID. '.' .$this->format );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}		
	
	
	}
	
	// Note: You must have read permissions on the view to access this resource.
	public function getAllRowTags( $viewID, $rowID ) {

		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '/rows/' .$rowID. '/tags.' .$this->format );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}	
	
	}
	
	public function getAllViewTags( $viewID ) {
	
		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '/tags.' .$this->format );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}	
	
	}
	
	public function getAllViewUserTags( $viewID ) {
	
		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '/user_tags.' .$this->format );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

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