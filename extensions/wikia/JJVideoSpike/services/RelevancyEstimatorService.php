<?php
/**
 * User: artur
 * Date: 30.04.13
 * Time: 13:08
 */

class RelevancyEstimatorService {
	private $relevancyEstimator;
	private $videoInformationProvider;

	public function __construct() {
		$relevancyEstimatorFactory = new CompositeRelevancyEstimatorFactory();
		$this->relevancyEstimator = $relevancyEstimatorFactory->get();
		$this->videoInformationProvider = new VideoInformationProvider();
	}

	public function getRelevancy( $videoTitle, $articleTitle ) {
		$article = $this->getArticleInfo( $articleTitle );
		$video = $this->getVideoInfo( $videoTitle );

		return getRelevancyInternal($video, $article);
	}

	public function mergeResults( $articleTitle, $resultSets, $expectedResults = 5 ) {
		$articleInfo = $this->getArticle( $articleTitle );
		$items = array();
		foreach ( $resultSets as $i => $resultSet ) {
			foreach( $resultSet["items"] as $j => $result ) {
				$videoTitle = $result["title"];
				$videoInfo = $this->getVideoInfo( $videoTitle );
				$score = $this->getRelevancyInternal( $videoInfo, $articleInfo );
				$res = clone $result;
				$res["_score"] = $score;
				$res["_pos"] = $j;
				$items[] = $res;
			}
		}
		usort( $items, function( $a, $b ) { return $a["_pos"] < $b["_pos"]; } );
		$threshold = $this->binarySearch( 0, 100, $items, $expectedResults );
		return $this->filterResults( $items, $threshold );
	}

	public function binarySearch ( $a, $b, $items, $expectedCount ) {
		if ( $b - $a <= 0.01 ) {
			return $b;
		}
		$pivot = ($a + $b) / 2;
		$count = count( $this->filterResults( $items, $pivot ) );
		if ( $count < $expectedCount ) {
			return $this->binarySearch( $a, $pivot, $items, $expectedCount );
		} else if ( $count > $expectedCount ) {
			return $this->binarySearch( $pivot, $b, $items, $expectedCount );
		} else {
			return $pivot;
		}
	}

	public function  filterResults( $items, $score ) {
		$resultItems = array();
		foreach ( $items as $i => $v ) {
			if ( $v["_score"] >= $score ) {
				$resultItems[] = $v;
			}
		}
		return $resultItems;
	}

	protected  function getArticleInfo( $articleTitle ) {
		$title = $articleTitle;
		if( $title ) {
			$titleObject = Title::newFromText( $title );
		} else {
			throw new Exception("No such article title.");
		}
		$article = null;
		if ( !empty( $titleObject ) && $titleObject->exists() ) {
			$article = new ArticleInformation( new Article( $titleObject ) );
			return $article;
		} else {
			throw new Exception("No such article.");
		}
	}

	protected  function getVideoInfo( $videoTitle ) {
		$videoMetadata = $this->videoInformationProvider->get( $videoTitle );
		if ( $videoMetadata == null ) {
			throw new Exception("No such video title.");
		}
	}

	protected function getRelevancyInternal( $videoMetadata, $article ) {
		$estimate = $this->relevancyEstimator->estimate(
			$article,
			$videoMetadata );
		return $estimate;
	}
}
