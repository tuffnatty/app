<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 29.04.13 13:15
 *
 */

require_once( dirname(__FILE__)."../../../../../maintenance/commandLine.inc" );


$db = wfGetDB( DB_SLAVE, array(), "video151" );


$elems = $db->query("SELECT img_minor_mime FROM image GROUP BY img_minor_mime");

while ( $r = $elems->fetchObject() ) {


	echo "\n ====== \n ";
	echo 'Provider: ' . $r->img_minor_mime . "\n";

}