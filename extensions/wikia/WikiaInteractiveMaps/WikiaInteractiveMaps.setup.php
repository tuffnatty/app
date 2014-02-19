<?php
$dir = dirname(__FILE__) . '/';

$wgExtensionCredits[ 'specialpage' ][] = [
	'name' => 'Wikia Interactive Maps',
	'authors' => [
		'Andrzej "nAndy" Łukaszewski',
		'Evgeniy "aquilax" Vasilev',
	],
	'description' => 'Create your own maps with point of interest or add your own point of interest into a real world map',
	'version' => 0.1
];

// constroller classes
$wgAutoloadClasses[ 'WikiaInteractiveMapsController' ] = $dir . 'WikiaInteractiveMapsController.class.php';

// model classes
$wgAutoloadClasses[ 'WikiaMapPointArticle' ] = $dir . '/models/WikiaMapPointArticle.class.php';
$wgAutoloadClasses[ 'WikiaMapPoint' ] = $dir . '/models/WikiaMapPoint.class.php';
$wgAutoloadClasses[ 'WikiaMapArticle' ] = $dir . '/models/WikiaMapArticle.class.php';
$wgAutoloadClasses[ 'WikiaMap' ] = $dir . '/models/WikiaMap.class.php';
$wgAutoloadClasses[ 'WikiaMaps' ] = $dir . '/models/WikiaMaps.class.php';

// hooks classes
$wgAutoloadClasses[ 'WikiaInteractiveMapsHooks' ] = $dir . 'WikiaInteractiveMapsHooks.class.php';

// hooks
$wgHooks[ 'ArticleFromTitle' ][] = 'WikiaInteractiveMapsHooks::onArticleFromTitle';
$wgHooks[ 'ArticleSaveComplete' ][] = 'WikiaInteractiveMapsHooks::onArticleSaveComplete';

// special pages
$wgSpecialPages[ 'InteractiveMaps' ] = 'WikiaInteractiveMapsController';
$wgSpecialPageGroups[ 'InteractiveMaps' ] = 'wikia';

// i18n mapping
$wgExtensionMessagesFiles[ 'WikiaInteractiveMaps' ] = $dir . 'WikiaInteractiveMaps.i18n.php';

// namespaces
define( "NS_WIKIA_MAP", 600 );
define( "NS_WIKIA_MAP_POINT", 602 );
$wgExtensionNamespacesFiles[ 'WikiaInteractiveMaps' ] = $dir . 'WikiaInteractiveMaps.namespaces.php';
wfLoadExtensionNamespaces( 'WikiaInteractiveMaps', array( NS_WIKIA_MAP, NS_WIKIA_MAP_POINT ) );
