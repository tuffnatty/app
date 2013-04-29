<?php
/**
 * Structured Data
 *
 * extension for manipulating with SDS objects
 *
 * @author Adrian 'ADi' Wieczorek <adi@wikia-inc.com>
 * @author Jacek Jursza <jacek@wikia-inc.com>
 * @author Jacek 'mech' Woźniak <mech@wikia-inc.com>
 * @author Rafał Leszczyński <rafal@wikia-inc.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @package MediaWiki
 *
 */

$wgExtensionCredits['specialpage'][] = array(
    'name' => 'Video Metadata',
    'author' => array( 'Adrian \'ADi\' Wieczorek', 'Jacek Jursza', 'Jacek \'mech\' Woźniak', 'Rafał Leszczyński', 'Adam Robak' ),
    'url' => 'http://callofduty.wikia.com/wiki/Special:VMD',
    'descriptionmsg' => 'sdsvideometadata-desc'
);

$app = F::app();
$dir = dirname(__FILE__) . '/';

/**
 * classes
 */
$app->registerClass('SDSFormMapping', $dir . 'mappings/SDSFormMapping.php');
$app->registerClass('VideoClipGamingVideo', $dir . 'mappings/VideoClipGamingVideo.class.php');
$app->registerClass('VideoClipMovieTrailersVideo', $dir . 'mappings/VideoClipMovieTrailersVideo.class.php');
$app->registerClass('VideoClipTVVideo', $dir . 'mappings/VideoClipTVVideo.class.php');
$app->registerClass('VideoClipCookingVideo', $dir . 'mappings/VideoClipCookingVideo.class.php');
$app->registerClass('VideoClipCraftVideo', $dir . 'mappings/VideoClipCraftVideo.class.php');
$app->registerClass('VideoClipHowToVideo', $dir . 'mappings/VideoClipHowToVideo.class.php');
$app->registerClass('VideoClipMusicVideo', $dir . 'mappings/VideoClipMusicVideo.class.php');
$app->registerClass('VideoClipTravelVideo', $dir . 'mappings/VideoClipTravelVideo.class.php');
$app->registerClass('SDSVideoMetadataHooks', $dir . 'SDSVideoMetadataHooks.class.php');
/**
 * hooks
 */
$app->registerHook( 'ImagePageShowTOC', 'SDSVideoMetadataHooks', 'onImagePageShowTOC' );
/**
 * controllers
 */
$app->registerClass('SDSVideoMetadataController', $dir . 'SDSVideoMetadataController.class.php');

/**
 * special pages
 */
$app->registerSpecialPage('VMD', 'SDSVideoMetadataController');


/**
 * access rights
 */

/**
 * DI setup
 */

/**
 * message files
 */
$app->registerExtensionMessageFile('SDSVideoMetadata', $dir . 'SDSVideoMetadata.i18n.php' );

F::build( 'JSMessages' )->registerPackage( 'VMD-messages', array(
	'sdsvideometadata-vc-remove-item',
	'sdsvideometadata-vc-create-new-item',
	'sdsvideometadata-no-suggestions-found'
));
