<?php

	// A collection of simple examples of using class.windy.php

	// Load our class file and create our object.
	include_once( 'class.windy.php' );
	
	// Create an object to get city data
	$chicago = new windy( 'city' );

	// Create an object to get county data
	$cook = new windy( 'county' );

	echo var_dump( $chicago->getViews() );
	//echo var_dump( $cook->getViews() );
	
	//echo var_dump( $chicago->getDocs() );
	//echo var_dump( $chicago->getDocsByID( 'views' ))

?>