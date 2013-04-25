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

		$result = array(
			'caption' => $this->wf->Msg( 'vet-suggestions' ),
			'totalItemCount' => 0,
			'nextStartFrom' => $response['nextStartFrom'],
			'currentSetItemCount' => count($response['items']),
			'items' => $response['items']
		);

		return $result;
	}

	public function getBySubject() {


		$wikiSubject = $this->wikiSubject->get();

		$articleSubject = $this->articleSubject->getPrioritizedList();



		//var_dump( $wikiSubject );
		var_dump( $articleSubject );

	}
}