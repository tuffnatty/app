<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 16:38
 */

class CompositeRelevancyEstimatorFactory {
	function get() {
		$estimator = new CompositeRelevancyEstimator();
		$estimator->addEstimator( "MatchAllEstimator", new MatchAllRelevancyEstimator() );
		return $estimator;
	}
}
