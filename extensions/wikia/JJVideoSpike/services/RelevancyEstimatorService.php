<?php
/**
 * User: artur
 * Date: 30.04.13
 * Time: 13:08
 */

class RelevancyEstimatorService {
	private $relevancyEstimator;
	private $videoInformationProvider;

	function __construct() {
		$relevancyEstimatorFactory = new CompositeRelevancyEstimatorFactory();
		$this->relevancyEstimator = $relevancyEstimatorFactory->get();
		$this->videoInformationProvider = new VideoInformationProvider();
	}

	function getRelevancy( $videoTitle, $articleTitle ) {
		$videoMetadata = $this->videoInformationProvider->get( $videoTitle );
		if ( $videoMetadata == null ) {
			throw new Exception("No such video title.");
		}
		$title = $articleTitle;
		if( $title ) {
			$titleObject = Title::newFromText( $title );
		} else {
			throw new Exception("No such article title.");
		}
		$article = false;
		if ( !empty( $titleObject ) && $titleObject->exists() ) {
			$article = new Article( $titleObject );
		}
		$estimate = $this->relevancyEstimator->estimate(
			new ArticleInformation( $article ),
			$videoMetadata );
		return $estimate;
	}
}
