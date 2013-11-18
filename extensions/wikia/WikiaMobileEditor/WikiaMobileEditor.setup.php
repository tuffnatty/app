<?php
/**
 * WikiaMobile Editor enhancements
 *
 * @author Bartek <Bart(at)wikia-inc.com>
 *
 */

$dir = dirname(__FILE__) . '/';

/**
 * classes
 */
$wgAutoloadClasses['WikiaMobileEditorController'] =  $dir . 'WikiaMobileEditorController.class.php';

/**
 * hooks
 */
$wgHooks['WikiaMobileAssetsPackages'][] = 'WikiaMobileEditorController::onWikiaMobileAssetsPackages';
$wgHooks['EditPage::showEditForm:initial'][] = 'WikiaMobileEditorController::onEditPageInitial';
$wgHooks['AlternateEdit'][] = 'WikiaMobileEditorController::onAlternateEdit';
$wgHooks['BeforePageDisplay'][] = 'WikiaMobileEditorController::onBeforePageDisplay';

/**
 * message files
 */
$wgExtensionMessagesFiles['WikiaMobileEditor'] = $dir . 'WikiaMobileEditor.i18n.php';
