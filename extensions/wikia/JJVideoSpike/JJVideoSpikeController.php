<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 23.04.13 15:36
 *
 */

class JJVideoSpikeController extends WikiaSpecialPageController {
	private $videoMetadataProvider;
	private $relevancyEstimator;
	public function __construct() {

		// parent SpecialPage constructor call MUST be done
		parent::__construct( 'JJVideoSpike', '', false );
		$this->videoMetadataProvider = new JJVideoMetadataProvider();
		$estimatorFactory = new CompositeRelevancyEstimatorFactory();
		$this->relevancyEstimator = $estimatorFactory->get();
	}


	public function index() {

		$videoTitle = $this->getVal( "video" );
		if ( $videoTitle == null ) {
			$videoTitle = "Scarface_-_This_is_paradise";
		}
		$videMetadata = $this->videoMetadataProvider->get( $videoTitle );
		$title = $this->getVal( "articleTitle" );
		if( $title ) {
			$titleObject = Title::newFromText( $title );
		} else {
			$id = $this->getVal( "articleId" );
			if( !$id ) {
				$id = 15;
			} else {
				$id = intval( $id );
			}
			$titleObject = Title::newFromID( $id );
		}
		$article = false;
		if ( !empty( $titleObject ) && $titleObject->exists() ) {
			$article = new Article( $titleObject );
		}
		var_dump($article);
		$estimate = $this->relevancyEstimator->compositeEstimate( $article, $videMetadata );
		//var_dump($estimate);
		$this->setVal("estimates:", $estimate);
		$this->getResponse()->setFormat("json");
		die("AAAA");

	}

	private function getArticleId( $param = 'art' ) {

		$title = $this->request->getVal( $param, '' );
		$art = false;

		if ( !empty( $title ) ) {

			$titleObj = Title::newFromText( $title );
			if ( !empty( $titleObj ) && $titleObj->exists() ) {

				$art = $titleObj->getArticleID();
			}
		}

		return $art;
	}

	public function test() {

		$articleId = $this->getArticleId();
		if ( !$articleId ) {
			die("ARTICLE NOT FOUND");
		}

		$art = new ArticleSubject( $articleId );


		$subjectsObject = new WikiSubjects();
		$art->setAllSubjectList( $subjectsObject->get() );

		$subjects = $art->getSubjects();
		var_dump( $subjects );

		die("<hr>!");
	}

	public function rel() {
		$videoTitle = $this->getVal( "video" );
		if ( $videoTitle == null ) {
			$videoTitle = "IGN Live Presents WWE '13";
		}
		$videMetadata = $this->videoMetadataProvider->get( $videoTitle );
		$title = $this->getVal( "articleTitle" );
		if( $title ) {
			$titleObject = Title::newFromText( $title );
		} else {
			$id = $this->getVal( "articleId" );
			if( !$id ) {
				$id = 383882;
			} else {
				$id = intval( $id );
			}
			$titleObject = Title::newFromID( $id );
		}
		$article = false;
		if ( !empty( $titleObject ) && $titleObject->exists() ) {
			$article = new Article( $titleObject );
		}
		$estimate = $this->relevancyEstimator->compositeEstimate( $article, $videMetadata );
		//var_dump($estimate);
		$this->setVal("estimates:", $estimate);
		$this->getResponse()->setFormat("json");
		//die();
	}

	public function testSuggestions() {

		$articleId = $this->getArticleId();
		if ( !$articleId ) {
			die("ARTICLE NOT FOUND");
		}

		$suggestions = new ArticleVideoSuggestion( $articleId );
		$suggestions->getBySubject();
		die("<hr>");
	}

}
