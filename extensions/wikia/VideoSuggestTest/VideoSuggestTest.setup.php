<?php
/**
 * VideoSuggestTest
 * @author Liz Lee, Saipetch Kongkatong, Garth Webb
 */

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'VideoSuggesteTest',
	'author' => array( 'Liz Lee', 'Saipetch Kongkatong', 'Garth Webb' )
);

$dir = dirname(__FILE__) . '/';
$app = F::app();

//classes
$app->registerClass( 'VideoSuggestTestHooksHelper', $dir.'VideoSuggestTestHooksHelper.class.php' );

$app->registerHook( 'PageHeaderVideoSuggest', 'VideoSuggestTestHooksHelper', 'onPageHeaderVideoSuggest' );

$wgGroupPermissions['*']['VideoSuggestTest'] = false;
$wgGroupPermissions['staff']['VideoSuggestTest'] = true;
$wgGroupPermissions['sysop']['VideoSuggestTest'] = true;
$wgGroupPermissions['helper']['VideoSuggestTest'] = true;
$wgGroupPermissions['vstf']['VideoSuggestTest'] = true;