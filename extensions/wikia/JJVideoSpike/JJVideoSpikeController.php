<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 23.04.13 15:36
 *
 */

class JJVideoSpikeController extends WikiaSpecialPageController {

	const FREEBASE_URL = 'https://www.googleapis.com/freebase/v1/search';
	const CACHE_DURATION = 86400; //1 day

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

	public function moar() {

		print_r( '<pre>' );
		$q = $this->getVal( 'q' );
		$d = $this->getVal( 'd' );
		$score = $this->getVal( 'score', 0 );

		$types = array(
			'actor',
			'/fictional_universe/fictional_character',
			'/film/film',
			'/film/film_series',
			'/tv/tv_program',
			'/tv/tv_series_season',
			'/cvg/game_series',
			'/cvg/computer_videogame',
			'/book/literary_series',
			'/book/book_edition',
			'/book/book',
		);

		$typesMapping = array(
			'actor' => 'actor',
			'/m/02hrh1q' => 'actor', //actor id in freebase
			'/fictional_universe/fictional_character' => 'character',
			'/film/film' => 'movie',
			'/film/film_series' => 'movie',
			'/tv/tv_program' => 'series',
			'/tv/tv_series_season' => 'season',
//			'/cvg/game_series' => ,
			'/cvg/computer_videogame' => 'game',
//			'/book/literary_series',
			'/book/book_edition' => 'book',
			'/book/book' => 'book'
		);

		$json = $this->queryFreebase( $q, $d );
		print_r( $json );
		$resultTypes = array();
//		$maxScore = $json->result[0]->score;
		foreach ( $json->result as $res ) {
//			print_r( $res );
			if ( $res->score > $score && ( isset( $res->notable ) && isset( $typesMapping[ strtolower( $res->notable->id ) ] ) ) ) {
				if ( isset( $resultTypes[ $typesMapping[ strtolower( $res->notable->id ) ] ] ) && $res->score < $resultTypes[ $typesMapping[ strtolower( $res->notable->id ) ] ] ) {
					continue;
				}
				$resultTypes[ $typesMapping[ strtolower( $res->notable->id ) ] ] = $res->score;
			}
		}
		$sortedResult = array_unique( $resultTypes );
		arsort( $sortedResult );
		$result = array();
		foreach( $sortedResult as $key => $score ) {
			$result[] = array( $key => $score );
		}
		print_r( $result );
		die();

	}

	public function queryFreebase( $query, $domain = null, $limit = 5 ) {

		$q = array(
			'indent' => 'true',
			'limit' => $limit,
			'query' => $query
		);
		if ( $domain !== null ) {
			$q[ 'filter' ] = "(all (all domain:\"{$domain}\"))";
		}

		$url = static::FREEBASE_URL . '?' . http_build_query( $q );

		$key = $this->generateMemKey( __METHOD__, md5( $url ) );
		$content = $this->getFromCache( $key );

		if ( empty( $content ) ) {
			print_r( 'Connecting: ' . $url . "\n" );
			$fb = MWHttpRequest::factory( $url );
			$fb->execute();
			$content = $fb->getContent();
			$this->saveToCache( $key, $content );
		}

		return json_decode( $content );
	}


	protected function generateMemKey( $method, $url ) {
		return $this->app->wf->memcKey( $method, $url );
	}

	protected function saveToCache( $key, $value ) {
		$this->app->wg->memc->set( $key, $value, static::CACHE_DURATION );
	}

	protected function getFromCache( $key ) {
		return $this->app->wg->memc->get( $key );
	}

	public function getMoarDataForThoseVideosHere() {
		$videos = array(
				'Around the World in 80 Days (2,004) - Clip The wager',
				'Arena_(2,011)_-_Open-ended_Trailer_for_Arena',
				'Annie_Hall_(1,977)_-_Open-ended_Trailer_(e10,940)',
				'Anaconda_(1,997)_-_Trailer',
				'America\'s Heart and Soul (2,004) - CT 3 Post',
				'All Purpose Cat Girl Nuku Nuku (1,992) - Home Video Trailer',
				'Alex And Emma (2,003) - Trailer',
				'Affliction_(1,997)_-_Open-ended_Trailer_(e10,448)',
				'A Walk Into The Sea: Danny Williams And The Warhol Factory (2,007) - Open-ended Trailer ',
				'A Muppets Christmas Letters to Santa (2,008) - Featurette Miss Piggy',
				'A_Clockwork_Orange_(1,971)_-_Theatrical_Trailer_(e11,729)',
				'3D Dot Game Heroes (VG) (2,010) - Vignette 3 trailer'
		);

		$video = reset( $videos );

		//change underscores for spaces, removes comas and dots
		$tmp = str_replace(
			array( '_', ',', '.' ),
			array( ' ', '', '' ),
			$video
		);
		print_r( '<pre>' );
		//get the data before ( character
		if ( ($parenthisStart = strpos( $tmp, '(' ) ) !== false ) {
			$title = substr( $video, 0, $parenthisStart );
			$freebaseResult = $this->queryFreebase( $title );
			foreach( $freebaseResult->result as $res ) {
				print_r( $res->name." ".$this->matchStrings( $res->name, $tmp )."\n" );

			}
		}



		die;
	}

	protected function matchStrings( $first, $second ) {
		//normalize first
		$firstNorm = str_replace(
			array( '_', ',', '.' ),
			array( ' ', '', '' ),
			$first
		);
		$secondNorm = str_replace(
			array( '_', ',', '.' ),
			array( ' ', '', '' ),
			$second
		);

		$firstArray = explode( ' ', $firstNorm );
		$secondArray = explode( ' ', $secondNorm );

		$count = 0;
		foreach( $firstArray as $f ) {
			foreach( $secondArray as $s ) {
				if ( $f === $s ) {
					$count++;
				}
			}
		}
		return $count;
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

		$result = $suggestions->getBySubject();

		$subjects = $suggestions->getSubject();

		$this->setVal( 'subject', $subjects[0][0] );

		$this->inflateWithVideoData( $result );


		$this->setVal( 'results' , $result );


	}

	private function inflateWithVideoData( &$result ) {

		$config = array(
			'contextWidth' => 460,
			'maxHeight' => 250
		);

		foreach ( $result['items'] as $i => $r ) {


			$title = Title::newFromText( $r['title'], NS_FILE );
			$file = wfFindFile( $title );

			$htmlParams = array(
				'custom-title-link' => $title,
				'linkAttribs' => array( 'class' => 'video-thumbnail' )
			);

			if ( !empty( $file ) ) {
				$thumb = $file->transform( array('width'=>460, 'height'=>250), 0 );
				$result['items'][$i]['thumb'] = $thumb->toHtml( $htmlParams );
			}
		}

	}

}
