<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 23.04.13 15:33
 *
 */
$dir = dirname(__FILE__) . '/';
$app = F::app();
/**
 * special pages
 */
$app->registerClass('JJVideoSpikeController', $dir . 'JJVideoSpikeController.php');
$app->registerClass('ArticleSubject', $dir.'ArticleSubject.class.php');
$app->registerClass('WikiSubjects', $dir.'WikiSubjects.class.php');

$app->registerSpecialPage('JJVideoSpike', 'JJVideoSpikeController');