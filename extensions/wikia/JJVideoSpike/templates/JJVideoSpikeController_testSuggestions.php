<?php

echo '<h1>query for:'. $subject .'</h1>';

foreach ( $results['items'] as $item ) {
	echo '<h2>' . $item['title'] . '</h2>';
	echo $item['thumb'];
	echo '<hr>';
}