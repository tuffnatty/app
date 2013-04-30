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

//elastic search
$app->registerClass('ElasticSearchClient', $dir.'elasticsearch/ElasticSearchClient.class.php');
$app->registerClass('ElasticSearchQuery', $dir.'elasticsearch/ElasticSearchQuery.class.php');


// relevancy
$app->registerClass('CompositeRelevancyEstimatorFactory', $dir.'relevancy/CompositeRelevancyEstimatorFactory.php');
$app->registerClass('CompositeRelevancyEstimator',        $dir.'relevancy/CompositeRelevancyEstimator.php');
$app->registerClass('IRelevancyEstimator',                $dir.'relevancy/IRelevancyEstimator.php');
$app->registerClass('MatchAllRelevancyEstimator',         $dir.'relevancy/MatchAllRelevancyEstimator.php');
$app->registerClass('MatchFullTokensEstimator',           $dir.'relevancy/MatchFullTokensEstimator.php');
$app->registerClass('FuzzyMatchFullTokensEstimator',      $dir.'relevancy/FuzzyMatchFullTokensEstimator.php');
$app->registerClass('TitleRelevancyEstimator',            $dir.'relevancy/TitleRelevancyEstimator.php');
$app->registerClass('SubjectRelevancyEstimator',          $dir.'relevancy/SubjectRelevancyEstimator.php');
$app->registerClass('SubjectRelevancyEstimator2',         $dir.'relevancy/SubjectRelevancyEstimator2.php');

// normalization
$app->registerClass('CompositeNormalizingFunction', $dir.'normalizingFunctions/CompositeNormalizingFunction.php');
$app->registerClass('INormalizingFunction',         $dir.'normalizingFunctions/INormalizingFunction.php');
$app->registerClass('LinearNormalizingFunction',    $dir.'normalizingFunctions/LinearNormalizingFunction.php');
$app->registerClass('SigmoidNormalizingFunction',   $dir.'normalizingFunctions/SigmoidNormalizingFunction.php');

// services
$app->registerClass('WikiPageCategoryService',         $dir.'services/WikiPageCategoryService.php');
$app->registerClass('WikiPageCategoryServiceFactory',  $dir.'services/WikiPageCategoryServiceFactory.php');

$app->registerClass('ArticleInformation', $dir.'ArticleInformation.php');
$app->registerClass('VideoInformation', $dir.'VideoInformation.php');


$app->registerSpecialPage('JJVideoSpike', 'JJVideoSpikeController');
$app->registerClass('ArticleVideoSuggestion', $dir.'ArticleVideoSuggestion.class.php');

$app->registerClass('FreebaseClient', $dir . 'FreebaseClient.class.php');


