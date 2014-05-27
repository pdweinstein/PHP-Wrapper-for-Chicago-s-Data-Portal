<?php

/*
 *   @package		windy-php
 *   @author		Paul Weinstein, <pdw@weinstein.org>
 *   @version		0.8
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

	var $chicagoAPIURL = 'data.cityofchicago.org/api/';
	var $cookAPIURL = 'datacatalog.cookcountyil.gov/api/';
	var $illinoisAPIURL = 'data.illinois.gov/api/';
	var $combinedAPIURL = 'www.metrochicagodata.org/api/';
	var $apiKey = '';
	var $format = '';
	var $timeout = '300';
	var $debug = false;

	/* 	Format types, JSON, XML, RDF, XLS and XLSX (Execl), CSV, TXT, PDF All these different formats, what to do?
		
		Initial idea: if the format is developer centric, let's take the extra step and give the developer
		something to run with. For example, if JSON, let's go ahead and decode it a return a usable object or array
		For XML this would mean the developer would need libXML installed. For RDF it would mean having to add http://pear.php.net/package/RDF
		and to make this even more fun, our CSV parser function, str_getcsv is PHP 5.3 or greater 
		
		This could get real messy, real fast. Instead let's try this: Everything we call for is via JSON an we'll decode into an object. If the
		developer wants an array instead, they can specify that. Otherwise, if they want XML or XLS or whatever, they're on their own.
		
		
		The initial plan for this class file didn't include supporting multiple data portals, but both the county and state have adapted Socrata's platform, so adding these sources is "trivial". Trival that is if the implementation is at the object level, as currently done. But should choosing a data source be an object-level choice OR should it be at the method/function level where an additional parameter sets which data source to use for that specific moment?
		
		Moreover, as of March 2012, the city, county and state have come together to create MetroChicagoData.org, a Socrata driven portal that brings data from the City of Chicago, Cook County and the State of Illinois into one single interface. Thus future versions of this class may adopt the single all encompassing portal. For now support for all four is being maintained. 		
		
	*/
	
	/**
	 *	__construct function, let's get this object created.
	 * 
	 *	@access	public
	 *	@param	string	$source is flag to set which Socrata data source to use, City of Chicago's, Cook County's or State of Illinois'. Takes
	 *						'city' for City of Chicago, 'county' for Cook County, 'state' for State of Illinois and 'federated' for Metro Chicago Data
	 *	@param	string	$format is what format to provide the data in. Supported format types include:
	 *						JSON, XML, RDF, XLS and XLSX (Execl), CSV, TXT, PDF
	 *						If JSON is chosen, the default option, then the next argument, $type allows for accessing the data from an object
	 *						or an array. For all other formats, it's  developer's choice as to how to handle the raw data format. 
	 *						Optional. default: 'json'
	 *	@param	string	$type allows for what data type to provided the featched data in, if the inital format is JSON. 
	 *						Data can be provided in object or an array. Optional. default: 'object'
	 *	@param	string	$apiKey can be provided for additional functionality, but is not required. Optional. default: ''
	 *	@param	bool		$debug turn on debugging. Optional. (default: false)
	 *
	 */
	public function __construct( $source, $format = 'json', $type = 'object', $apiKey = '', $debug = false ) {
	
		if ( $source == 'state' ) {
		
			$this->apiURL = $this->illinoisAPIURL;
		
		} else if ( $source == 'county' ) {
		
			$this->apiURL = $this->cookAPIURL;
		
		} else if ( $source == 'city' ) {
		
			$this->apiURL = $this->chicagoAPIURL;
		
		} else if ( $source == 'federated' ) {
		
			$this->apiURL = $this->combinedAPIURL;
		
		}
	
		$this->format = $format;
		$this->type = $type;
		$this->apiKey = $apiKey;
		$this->debug = $debug;	
	
	}

/*
	public function getAuthentication( $username, $password ) {
	
		$response = $this->httpRequest( $this->apiURL. 'api/docs.' .$this->format );
	
	
	}
*/	
	public function getDocs() {

		$response = $this->httpRequest( $this->apiURL. 'api/docs.' .$this->format );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}	
	
	}
	
	/**
	 * getDocsByID function retrieve documentation on a specific service.
	 * 
	 *	@access	public
	 *	@param	string	$docID is the service URL root such as views or user for which documentation to retrieve. Required.
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
	public function getDocsByID( $docID ) {

		$response = $this->httpRequest( $this->apiURL. 'api/docs/' .$docID. '.' .$this->format );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}	
	
	}
	
	/**
	 *	getUsers function provides a method to query for all users. 
	 *		The resulting output will include summary information about the users within the page.
	 * 
	 *	@access	public
	 *	@param	string	$name, a name to search for. Optional. Default: ''
	 *	@param	string	$tags a set of tags to seach for. Optional. Default: ''
	 *	@param	integer	$limit is the total number of results to return. Can't exceed 200. Optional. Default: ''
	 *	@param 	integer	$page is the page number to offset the results by. Offset is (page-1) * limit. Optional. Default: ''
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
	public function getUsers( $name = '', $tags = '', $limit = '', $page = '' ) {

		$args = 'name=' .urldecode( $name ). '&tags=' .urldecode( $tags ). '&limit=' .urldecode( $limit ). '&page=' .urldecode( $page ) ;	
		$response = $this->httpRequest( $this->apiURL. 'users.' .$this->format, $args );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}
		
	}
	
	/**
	 *	getUserByID function will retrieve a specific user by username or user ID. Can also fetch user profile information or image
	 * 
	 *	@access	public
	 *	@param	string	$userID is the username or user ID of the desired user. Required.
	 *	@param	string	$profile can profile a user's contacts, groups, picklists, views or profile image. Optional Default: ''
	 *						Valid parameters are "contacts", "groups", "picklists", "views" or "image"
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
	public function getUserByID( $userID, $profile = '' ) {
	
		if ( $profile == 'image' ) {
		
			$response = $this->httpRequest( $this->apiURL. 'users/' .$userID. '/profile_images' );
		
		} else if (( $profile != 'image' ) AND ( $profile != '' )) {
	
			$response = $this->httpRequest( $this->apiURL. 'users/' .$userID. '/' .$profile. '.' .$this->format );	
		
		} else {

			$response = $this->httpRequest( $this->apiURL. 'users/' .$userID. '.' .$this->format );

		}
		
		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}	
	
	
	}
	
	/**
	 *	getUserViews function will retrieve a list of the views that a user has created.
	 *		If unauthenticated, only their public views will be returned. 
	 *		Authenticated users will also see views that have been shared to them, and will be able to see private views from their own account.
	 * 
	 *	@access	public
	 *	@param	string	$userID is the username or user ID whose views to retrieve. Required.
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
	public function getUserViews( $userID ) {
	
		$response = $this->httpRequest( $this->apiURL. 'users/' .$userID. '/views.' .$this->format );
		
		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}	
	
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
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
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
	
	/**
	 *	getViewsByID function provides a method to retrieve metadata about a view using its ID:
	 * 
	 *	@access	public
	 *	@param	string	$viewID of what view wish to retrieve metadata for
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.	
	 *
	 */
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
	
	/**
	 *	getColumnViewsByViewID function provides a method to get metadata about all of the columns on a given view
	 * 
	 *	@access 	public
	 *	@param 	string	$viewID for what view to retreive column metadata for.
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
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
	
	/**
	 *	getColumnViewsByColumnID function provides metadata about a particular columns on a given view
	 * 
	 *	@access	public
	 *	@param	string	$viewID the view id to retreive
	 *	@param	integer	$columnID the view's cooresponding column id 
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
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
	
	/**
	 *	getSubColumns function provides all of the sub-columns for a given nested table.
	 * 
	 *	@access	public
	 *	@param	string	$viewID the view id to retreive
	 *	@param	integer	$columnID the view's cooresponding column id 
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
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
	/**
	 * 	getFileByViewID function retrieves a file that has been attached to a view.
	 * 
	 *	@access	public
	 *	@param	string	$fileID is the id of the file to fetch
	 *	@param	mixed	$viewID is the view the file is attached to
	 *	@return	mixed	Returns the requested file
	 */
	public function getFileByViewID( $fileID, $viewID ) {
	
		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '/files/' .$fileID );		
		return $response;
			
	}
	
	/**
	 *	getRows function retrieve multiple rows from a view as nested arrays instead of in the expanded form.
	 * 
	 *	@param	string	view ID is the ID of the view that contains the row, Required
	 *	@param	boolean	row_ids_only the service will only return row IDs id true. Optional. Default is false.
	 *	@param	integer	max_rows is the limit the number of rows returned. Optional. Default '' (return all rows).
	 *	@param	integer	include_ids_after, include this number of rows, after which only row IDs are returned. Optional. Default ''
	 *	@param	string	search, run a full text search on the view and only return rows/ids that match. Optional. Default ''
	 *	@param	boolean	meta, if set to 'true', will write the view object. Only valid if rendering JSON. Optional. Default is true.
	 *	@param	boolean	as_hashes, if set to 'true', write fields in hash format. Otherwise, write fields in array. 
							Only valid if rendering JSON. Default to false (array).
	 *	@param	boolean	most_recent, if set to 'true', return only the most recent rows added to the dataset. Only valid if rendering RSS. 
	 						Default to true.
	 *	@param	string	access_type, valid values are PRINT, EMAIL, API, RSS, WIDGET, DOWNLOAD, WEBSITE Optional. Default ''

	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
	public function getRows( $viewID, $row_ids_only = 'false', $max_rows, $include_ids_after, $search, $meta = 'true', $as_hashes = 'false', $most_recent = 'true', $access_type ) {
	
			$args = 'row_ids_only=' .urlencode( $row_ids_only ). '&max_rows=' .urlencode( $max_rows ). '&include_ids_after=' .urlencode( $include_ids_after ). '&search=' .urlencode( $search ). '&meta=' .urlencode( $meta ). '&as_hashes=' .urlencode( $as_hashes ). '&most_recent=' .urlencode( $most_recent ). '&access_type=' .urlencode( $access_type );
		$response = $this->httpRequest( $this->apiURL. 'views/' .$viewID. '/rows.' .$this->format, $args );

		if (( $this->format == 'json' ) AND ( $this->type == 'object' )) {
		
			return json_decode( $response );
		
		} else if (( $this->format == 'json' ) AND ( $this->type == 'array' )) { 

			return json_decode( $response, true );

		} else {
		
			return $response;
		
		}
	
	}
	
	/**
	 * 	getRowByRowID function retrieve the expanded representation of a particular row by ID. 
	 * 
	 *	@access 	public
	 *	@param	string	$viewID is the identifier of the view that contains the row
	 *	@param	string	$rowID is the ID of the row to retrieve
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
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
	/**
	 *	getAllRowTags function will etrieve all of the tags for a particular row.
	 * 
	 *	@access	public
	 *	@param	string	$viewID would be the ID of the view that contains the row
	 *	@param	string	$rowID is of course the ID of the row for which to return tags
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
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
	
	/**
	 *	getAllViewTags function provides a method for retrieving tags for a view
	 * 
	 *	@access	public
	 *	@param	string	$viewID the ID for the view
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
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
	
	/**
	 *	getAllViewUserTags function allows for the retrieval of user tags for a view
	 * 
	 *	@access	public
	 *	@param	string	$viewID the ID for the view
	 *	@return	mixed	An orbject, array or raw data, depending on format and type choosen what object created.
	 *
	 */
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
	private function httpRequest( $reqURL, $args = '', $type = 'GET', $secure = false ) {

		if ( $secure ) {
		
			$reqURL = 'https://' .$reqURL;
		
		} else {
		
			$reqURL = 'http://' .$reqURL;
		
		}
			
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
