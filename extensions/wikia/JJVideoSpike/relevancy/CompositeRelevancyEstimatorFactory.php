<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 16:38
 */

class CompositeRelevancyEstimatorFactory {
	public function get() {
		$estimator = new CompositeRelevancyEstimator();
		$estimator->addEstimator( "MatchAllEstimator (unique tokens)", new MatchAllRelevancyEstimator(
			new UniqueTokensTokenizerFilter(
				new StopWordsTokenizerFilter(
					new ToLowerTokenizerFilter(
						new Tokenizer())))),
			$this->buildNormalizer( 0, 20, 0.8 )
		);
		$estimator->addEstimator( "MatchAllEstimator", new MatchAllRelevancyEstimator(), $this->buildNormalizer( 0, 20, 1 ) );

		$matchAllCountAll = new MatchAllRelevancyEstimator();
		$matchAllCountAll->setMaxMatchesPerToken(1000);
		$estimator->addEstimator( "MatchAllEstimator (allow multiple matches)", $matchAllCountAll, $this->buildNormalizer( 0, 1000, 0.2 ) );
		$estimator->addEstimator( "MatchFullTokensEstimator"                  , new MatchFullTokensEstimator(), $this->buildNormalizer( 0, 2, 0.4 ));
		$estimator->addEstimator( "FuzzyMatchFullTokensEstimator"             , new FuzzyMatchFullTokensEstimator(), $this->buildNormalizer( 0, 5, 1.5 ));
		$estimator->addEstimator( "TitleRelevancyEstimator"                   , new TitleRelevancyEstimator(), $this->buildNormalizer( 0, 1, 1 ));
		$estimator->addEstimator( "TitleRelevancyEstimator (all meta)"        , new TitleRelevancyEstimator( array( "keywords", "tags", "description", "category", "title" ) ), $this->buildNormalizer( 0, 1, 1 ));
		$estimator->addEstimator( "SubjectRelevancyEstimator"                 , new SubjectRelevancyEstimator(), $this->buildNormalizer( 0, 2, 1 ) );
		$estimator->addEstimator( "SubjectRelevancyEstimator2"                , new SubjectRelevancyEstimator2(), $this->buildNormalizer( 0, 5, 5 ) );

		return $estimator;
	}

	protected function buildNormalizer( $low, $high, $scale ) {
		$normalizingFunction = new CompositeNormalizingFunction();
		$normalizingFunction->add( new LinearNormalizingFunction( $low, $high ) );
		$normalizingFunction->add( new SigmoidNormalizingFunction( 0.5, $scale * 2 ) );
		return $normalizingFunction;
	}
}
