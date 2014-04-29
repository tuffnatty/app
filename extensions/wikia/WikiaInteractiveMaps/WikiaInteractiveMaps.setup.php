<?php
$dir = dirname(__FILE__) . '/';

$wgExtensionCredits[ 'specialpage' ][] = [
	'name' => 'Wikia Interactive Maps',
	'author' => [
		'Andrzej "nAndy" Łukaszewski',
		'Bart(łomey) K.',
		'Evgeniy "aquilax" Vasilev',
		'Jakub "Student" Olek',
		'Rafał Leszczyński',
	],
	'description' => 'Create your own maps with point of interest or add your own point of interest into a real world map',
	'version' => 0.1
];

// controller classes
$wgAutoloadClasses[ 'WikiaInteractiveMapsController' ] = $dir . 'WikiaInteractiveMapsController.class.php';
$wgAutoloadClasses[ 'WikiaInteractiveMapsParserTagController' ] = $dir . 'WikiaInteractiveMapsParserTagController.class.php';

// model classes
$wgAutoloadClasses[ 'WikiaMaps' ] = $dir . '/models/WikiaMaps.class.php';
$wgAutoloadClasses[ 'WikiaMapArticle' ] = $dir . '/models/WikiaMapArticle.class.php';

// hooks classes
$wgAutoloadClasses[ 'WikiaInteractiveMapsHooks' ] = $dir . 'WikiaInteractiveMapsHooks.class.php';

// special pages
$wgSpecialPages[ 'InteractiveMaps' ] = 'WikiaInteractiveMapsController';
$wgSpecialPageGroups[ 'InteractiveMaps' ] = 'wikia';

// hooks
$wgHooks['ParserFirstCallInit'][] = 'WikiaInteractiveMapsParserTagController::parserTagInit';
$wgHooks[ 'ArticleFromTitle' ][] = 'WikiaInteractiveMapsHooks::onArticleFromTitle';

// i18n mapping
$wgExtensionMessagesFiles[ 'WikiaInteractiveMaps' ] = $dir . 'WikiaInteractiveMaps.i18n.php';

// namespaces
define( "NS_WIKIA_MAP", 600 );
$wgExtensionNamespacesFiles[ 'WikiaInteractiveMaps' ] = $dir . 'WikiaInteractiveMaps.namespaces.php';
wfLoadExtensionNamespaces( 'WikiaInteractiveMaps', array( NS_WIKIA_MAP ) );