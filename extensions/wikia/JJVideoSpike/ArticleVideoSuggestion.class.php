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

	public function __construct( $articleId ) {

		$this->articleId = $articleId;

		$this->wikiSubject = new WikiSubjects();
		$this->articleSubject = new ArticleSubject( $articleId );
		$this->articleSubject->setAllSubjectList( $this->wikiSubject->get() );
	}

	public function makeQuery( $queryString, $start=0, $length=10 ) {


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

	public function getBySubject() {


		$wikiSubject = $this->wikiSubject->get();

		$articleSubject = $this->articleSubject->getPrioritizedList();

		$result = array();

		if ( count( $articleSubject ) > 0 ) {


			$result = $this->makeQuery( $articleSubject[0][0] );

		}

		return $result;
	}
}