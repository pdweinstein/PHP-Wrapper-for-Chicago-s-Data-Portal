<?php

	// A collection of simple examples of using class.windy.php

	// Load our class file and create our object.
	include_once( 'class.windy.php' );
	$chicago = new windy( '', '', '', false );

	echo var_dump( $chicago->getViews() );

?>