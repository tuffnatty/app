<?php

	// ----------------------------- Main ------------------------------------

	ini_set( "include_path", dirname( __FILE__ )."/../" );
	ini_set('display_errors', 1);

	require_once( "commandLine.inc" );

	if ( isset($options['help']) ) {
		die( "Usage: php maintenance.php [--help] [--wikiName=xyz] [--articleName=xyz] [--category=xyz]
		--keyword
		--help                         you are reading it right now\n\n" );
	}

	if ( empty($wgCityId) ) {
		die( "Error: Invalid wiki id." );
	}

	echo "Base wiki: ".$wgCityId."\n";
