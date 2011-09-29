<?php

	// A collection of simple examples of using class.windy.php

	// Load our class file and create our object.
	include_once( 'class.windy.php' );
	
	// Create an object to get city data
	$chicago = new windy( 'city' );

	// Let's find any views that describe themselves as about Chicago's neighborhood boundaries and are tagged with KML data
	$views = $chicago->getViews( '', '', 'neighborhood boundaries', 'kml', '', 'false', '', '' );
	
	echo "Here are the views with a description including 'neighborhood boundaries' and are tagged as KML:\n";
	foreach ( $views as $view ) {
	
		echo "View ID: ".$view->id. " is named " .$view->name. " and is described as a " .$view->description. "\n\n";
	
		// With a little foreknowledge View ID buma-fjbv looks interesting, let's get that file
		if ( $view->id == 'buma-fjbv' ) {
					
			$file = $chicago->getFileByViewID( $view->blobId, $view->id );

			// Since KML is an XML notation for expressing geographic annotation, let's use SimpleXML to parse this data
			// Note: SimpleXML requires the libxml PHP extension
			$xml = simplexml_load_string( $file );

			// Ok, now let's find out what the boundaries are for Albany Park
			foreach ( $xml->Document->Folder->Placemark as $hood ) {
			
				if ( preg_match( '/Albany Park/', $hood->description )) {
				
					echo "Here are Albany Park's boundaries: " .$hood->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates. "\n";
				
				}
			
			}
		
		}
	
	}

	//echo var_dump( $chicago->getDocs() );
	//echo var_dump( $chicago->getDocsByID( 'views' ))

	// Create an object to get county data
	//$cook = new windy( 'county' );
	
	//echo var_dump( $cook->getViews() );

	// Create an object to get state data
	//$illinois = new windy( 'state' );
	
	//echo var_dump( $illinois->getViews() );

?>