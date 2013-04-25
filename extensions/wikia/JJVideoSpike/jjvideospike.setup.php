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
$app->registerClass('JJVideoMetadataProvider', $dir.'JJVideoMetadataProvider.class.php');


$app->registerClass('ITokenizer', $dir.'util/ITokenizer.php');
$app->registerClass('Tokenizer', $dir.'util/Tokenizer.php');
$app->registerClass('StopWordsTokenizerFilter', $dir.'util/StopWordsTokenizerFilter.php');
$app->registerClass('ToLowerTokenizerFilter', $dir.'util/ToLowerTokenizerFilter.php');
$app->registerClass('UniqueTokensTokenizerFilter', $dir.'util/UniqueTokensTokenizerFilter.php');

$app->registerClass('CompositeRelevancyEstimatorFactory', $dir.'relevancy/CompositeRelevancyEstimatorFactory.php');
$app->registerClass('CompositeRelevancyEstimator', $dir.'relevancy/CompositeRelevancyEstimator.php');
$app->registerClass('IRelevancyEstimator', $dir.'relevancy/IRelevancyEstimator.php');
$app->registerClass('MatchAllRelevancyEstimator', $dir.'relevancy/MatchAllRelevancyEstimator.php');


$app->registerSpecialPage('JJVideoSpike', 'JJVideoSpikeController');
$app->registerClass('ArticleVideoSuggestion', $dir.'ArticleVideoSuggestion.class.php');


