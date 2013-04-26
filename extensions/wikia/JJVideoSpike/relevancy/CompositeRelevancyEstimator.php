<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 16:33
 */

class CompositeRelevancyEstimator {
	private $estimators = array();

	public function addEstimator( $name , IRelevancyEstimator $estimator ) {
		$this->estimators[ $name ] = $estimator;
	}

	public function compositeEstimate( ArticleInformation $article, VideoInformation $metatags ) {
		$result = array();
		foreach ( $this->estimators as $name => $estimator ) {
			$result[ $name ] = $estimator->estimate( $article, $metatags );
		}
		return $result;
	}
}
