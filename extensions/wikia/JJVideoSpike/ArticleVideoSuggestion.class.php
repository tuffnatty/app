<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 25.04.13 15:47
 *
 */

class ArticleVideoSuggestion {

	protected $articleId;
	protected $articleSubject;
	protected $wikiSubject;
	protected $article;

	protected $lastQuery;

	public function __construct( $articleId ) {

		$this->articleId = $articleId;
		$this->article = new Article( Title::newFromID( $articleId ) );
		$this->wikiSubject = new WikiSubjects();
		$this->articleSubject = new ArticleSubject( $articleId );
		$this->articleSubject->setAllSubjectList( $this->wikiSubject->get() );
	}

	public function setLastQuery( $lastQuery ) {
		$this->lastQuery = $lastQuery;
	}

	public function getLastQuery() {
		return $this->lastQuery;
	}


	public function makeQuery( $queryString, $start=0, $length=10 ) {


		$this->setLastQuery( $queryString );

		$wikiaSearchConfig = new Wikia\Search\Config();
		$wikiaSearchConfig  ->setStart( $start )
			->setLength( $length*2 )   // fetching more results to make sure we will get desired number of results in the end
			->setCityID( Wikia\Search\QueryService\Select\Video::VIDEO_WIKI_ID )
			->setIsVideo( true )
			->setNamespaces( array( NS_FILE ) )
			->setQuery( $queryString );


		$container = new Wikia\Search\QueryService\DependencyContainer( array( 'config' => $wikiaSearchConfig ) );
		$search = (new Wikia\Search\QueryService\Factory)->get( $container );
		$response = $search->search();

		$videoController = new VideoEmbedToolController();

		$response = $videoController->processSearchResponse( $response, $start, $length );


		return $response;
	}


	public function getSubject() {
		$articleSubject = $this->articleSubject->getPrioritizedList();
		return $articleSubject;
	}

	public function getBySubject( $subjectNo = 0 ) {
		$wikiSubject = $this->wikiSubject->get();
		$articleSubject = $this->articleSubject->getPrioritizedList();
		$result = array();

		if ( count( $articleSubject ) > $subjectNo ) {
			$result = $this->makeQuery( $articleSubject[$subjectNo][0] );
		}

		return $result;
	}

	public function getDefaultSuggestions() {

		$app = F::app();

		$articleId = $this->articleId;
		$article           = ( $articleId > 0 ) ? F::build( 'Article', array( $articleId ), 'newFromId' ) : null;
		$articleTitle      = ( $article !== null ) ? $article->getTitle() : '';
		$wikiTitleSansWiki = preg_replace( '/\bwiki\b/i', '', $app->wg->Sitename );

		$query =  $articleTitle . ' ' . $wikiTitleSansWiki;

		return $this->makeQuery( $query );
	}

	public function getFromElasticSearch( $forceQuery = false ) {

		$subject = $this->getSubject();

		if ( isset( $subject[0][0] ) ) {

			$query = $subject[0][0];

		} else {
			$app = F::app();

			$articleId = $this->articleId;
			$article           = ( $articleId > 0 ) ? F::build( 'Article', array( $articleId ), 'newFromId' ) : null;
			$articleTitle      = ( $article !== null ) ? $article->getTitle() : '';
			$wikiTitleSansWiki = preg_replace( '/\bwiki\b/i', '', $app->wg->Sitename );

			$query =  $articleTitle . ' ' . $wikiTitleSansWiki;

		}

		if ( $forceQuery ) {
			$query = $forceQuery;
		}

		$this->setLastQuery( $query );

		$elastic = new ElasticSearchQuery('testing','test');
		$result = $elastic->search( $query );

		$data = array();
		$data['items'] = array();

		if ( $result && $result->hits->total > 0 ) {

			foreach ( $result->hits->hits as $hit ) {
				$data['items'][] = array(
					'title' => $hit->_source->title,
					'id' => $hit->_source->video_id
				);
			}
		}

		return $data;

	}

	public function getMergedElastic() {
		$articleSubject = new ArticleSubject( $this->articleId );
		$prioritizedSubjects = $articleSubject->getPrioritizedList(5);
		$articleTitle = Title::newFromID( $this->articleId )->getBaseText();
		$relevancyService = new RelevancyEstimatorService();

		$resultSets[] = $this->getFromElasticSearch($articleTitle);
		foreach ( $prioritizedSubjects as $i => $subject ) {
			$resultSets[] = $this->getFromElasticSearch( $subject );
		}
		$result = $relevancyService->mergeResults(
			$articleTitle
			, $resultSets );
		return $result;
	}
}
