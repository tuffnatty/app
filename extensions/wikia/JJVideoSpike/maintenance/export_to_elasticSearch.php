<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 29.04.13 13:15
 *
 */

require_once( dirname(__FILE__)."../../../../../maintenance/commandLine.inc" );


$db = wfGetDB( DB_SLAVE, array(), "video151" );


/*
 Provider: anyclip
 Provider: dailymotion
 Provider: ign
 Provider: movieclips
 Provider: realgravity
 Provider: screenplay
 */

$elems = $db->query("SELECT * FROM image WHERE img_minor_mime = 'realgravity' LIMIT 50");

while ( $r = $elems->fetchObject() ) {


	echo "\n ====== \n ";
	echo 'Title: ' . $r->img_name . "\n";
	echo 'Provider: ' . $r->img_minor_mime . "\n";
	echo 'Meta : ' ;
	print_r( unserialize( $r->img_metadata ) );
}