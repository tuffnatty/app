<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 16:38
 */

class CompositeRelevancyEstimatorFactory {
	function get() {
		$estimator = new CompositeRelevancyEstimator();
		$estimator->addEstimator( "MatchAllEstimator (unique tokens)", new MatchAllRelevancyEstimator(
			new UniqueTokensTokenizerFilter(
				new StopWordsTokenizerFilter(
					new ToLowerTokenizerFilter(
						new Tokenizer()))))
		);
		$estimator->addEstimator( "MatchAllEstimator", new MatchAllRelevancyEstimator() );

		$matchAllCountAll = new MatchAllRelevancyEstimator();
		$matchAllCountAll->setMaxMatchesPerToken(1000);
		$estimator->addEstimator( "MatchAllEstimator (allow multiple matches)", $matchAllCountAll );
		return $estimator;
	}
}
