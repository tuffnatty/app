<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 16:33
 */

class CompositeRelevancyEstimator implements IRelevancyEstimator {
	private $estimators = array();
	private $normalizingFunctions = array();

	public function addEstimator( $name , IRelevancyEstimator $estimator, INormalizingFunction $normalizingFunction ) {
		$this->estimators[ $name ] = $estimator;
		$this->normalizingFunctions[ $name ] = $normalizingFunction;
	}

	public function compositeEstimate( ArticleInformation $article, VideoInformation $metatags ) {
		$result = array();
		foreach ( $this->estimators as $name => $estimator ) {
			$result[ $name ] = $estimator->estimate( $article, $metatags );
			$result[ $name . "(normalized)" ] = $this->normalizingFunctions[$name]->normalize(
				$estimator->estimate( $article, $metatags ) );
		}
		$result[ "aggregate" ] = $this->estimate( $article, $metatags );
		return $result;
	}

	public function estimate(ArticleInformation $article, VideoInformation $metatags) {
		$estimate = 0;
		foreach ( $this->estimators as $name => $estimator ) {
			$estimate += $this->normalizingFunctions[$name]->normalize(
				$estimator->estimate( $article, $metatags ) );
		}
		return $estimate;
	}
}
