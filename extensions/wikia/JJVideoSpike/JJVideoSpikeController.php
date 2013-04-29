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

	private $fbClient;

	public function __construct() {

		// parent SpecialPage constructor call MUST be done
		parent::__construct( 'JJVideoSpike', '', false );
		$this->videoMetadataProvider = new JJVideoMetadataProvider();
		$estimatorFactory = new CompositeRelevancyEstimatorFactory();
		$this->relevancyEstimator = $estimatorFactory->get();
		$this->fbClient = new FreebaseClient();
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


	public function elastic() {

		$elastic = new ElasticSearchQuery('testing', 'test');
		$data = $elastic->getData('1');

		$dataToIndex = json_encode( array(
			'name' => 'test'
		));

		$resp = $elastic->indexData('3', $dataToIndex);

		var_dump( $resp );

		die("<hr>");
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

		$json = $this->fbClient->queryWithDomain( $q, $d );
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

	public function getTypes( $json, $score ) {
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

		$resultTypes = array();
//		$maxScore = $json->result[0]->score;
		foreach ( $json->result as $res ) {
//			print_r( $res );
			if ( $res->score > $score && ( isset( $res->notable ) && isset( $typesMapping[ strtolower( $res->notable->id ) ] ) ) ) {
				if ( isset( $resultTypes[ $typesMapping[ strtolower( $res->notable->id ) ] ] ) && $res->score < $resultTypes[ $typesMapping[ strtolower( $res->notable->id ) ] ][ 's' ] ) {
					continue;
				}
				$resultTypes[ $typesMapping[ strtolower( $res->notable->id ) ] ] = array( 's' => $res->score, 'n' => $res->name );
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

	public function filterTypes( $toFilter ) {
		$typesMapping = array(
			'actor' => 'actor',
			'/m/02hrh1q' => 'actor', //actor id in freebase
			'/fictional_universe/fictional_character' => 'character',
			'/film/film' => 'movie',
			'/film/film_series' => 'movie',
			'/tv/tv_program' => 'series',
			'/tv/tv_series_season' => 'season',
			'/tv/tv_series_episode' => 'episode',
//			'/cvg/game_series' => ,
			'/cvg/computer_videogame' => 'game',
//			'/book/literary_series',
			'/book/book_edition' => 'book',
			'/book/book' => 'book'
		);

		$result = array();
		foreach( $toFilter as $key => $res ) {
			if ( isset( $res->notable ) ) {
				if ( isset( $typesMapping[ $res->notable->id ] ) ) {
					$result[ $key ] = $res;
				}
			}
		}
		return $result;
	}

	public function whyICantHandleAllThisCokes() {
		$cokeProvider = new JJVideoMetadataProvider();

//		$cokeProvider->getExpanded( 'A Muppets Christmas Letters to Santa (2,008) - Featurette Miss Piggy' );
//		$cokeProvider->getExpanded( 'Ace_Attorney_5_-_Japanese_TGS_2012_Trailer' );
//		$cokeProvider->getExpanded( 'Age_of_Empires_Online_Video' );
//		$cokeProvider->getExpanded( 'Assassin\'s_Creed_3_The_Tyranny_of_King_Washington_The_Redemption_Walkthrough_(Part_1)' );
//		$cokeProvider->getExpanded( 'Aliens_Colonial_Marines_PC_Commentary' );
		$cokeProvider->getExpanded( 'Astro_Boy_The_Video_Game_Nintendo_Wii_Trailer_-_GC_2009_VO_Talent_Kristen_Bell_and_Freddie_Highmore' );
		die;
	}

	public function getMoarDataForThoseVideosHere() {
		$typesMapping = array(
			'actor' => 'actor',
			'/m/02hrh1q' => 'actor', //actor id in freebase
			'/fictional_universe/fictional_character' => 'character',
			'/film/film' => 'movie',
			'/film/film_series' => 'movie',
			'/tv/tv_program' => 'series',
			'/tv/tv_series_season' => 'season',
			'/tv/tv_series_episode' => 'episode',
//			'/cvg/game_series' => ,
			'/cvg/computer_videogame' => 'game',
//			'/book/literary_series',
			'/book/book_edition' => 'book',
			'/book/book' => 'book'
		);

		$videos = array(
//			'A Muppets Christmas Letters to Santa (2,008) - Featurette Miss Piggy',
//			'3D Dot Game Heroes (VG) (2,010) - Vignette 3 trailer',
//			'A_Clockwork_Orange_(1,971)_-_Theatrical_Trailer_(e11,729)',
//			'Around the World in 80 Days (2,004) - Clip The wager',
//			'Arena_(2,011)_-_Open-ended_Trailer_for_Arena',
//			'Annie_Hall_(1,977)_-_Open-ended_Trailer_(e10,940)',
//			'Anaconda_(1,997)_-_Trailer',
//			'America\'s Heart and Soul (2,004) - CT 3 Post',
//			'All Purpose Cat Girl Nuku Nuku (1,992) - Home Video Trailer',
//			'Alex And Emma (2,003) - Trailer',
//			'Affliction_(1,997)_-_Open-ended_Trailer_(e10,448)',
//			'A Walk Into The Sea: Danny Williams And The Warhol Factory (2,007) - Open-ended Trailer ',
//			'Ace Combat Joint Assault (VG) (2010) - Gameplay trailer',
//			'Ace Ventura Jr. (2008) - Home Video Trailer',
//			'Adam (2009) - Interview Hugh Dancy "On how Adam is revealed throughout the film"',
			'A_Night_in_Heaven_(1,983)_-_Open-ended_Trailer_(e25,362)',
			'A Nightmare On Elm Street (1,984) - HD',
			'A Witch\'s Tale (VG) (2,009) - Main trailer for A Witch\'s Tale',
			'Abba_You_Can_Dance_(VG)_(2,011)_-_Launch_trailer',
			'ABC TV On DVD 2,011 (2,010) - ABC TV on DVD Trailer 1',
			'Abduction_(2,011)_-_Clip:_Diner_Shoot_Out',
			'Abel\'s Field (2,012) - Home Video Trailer 2 for Abel\'s Field',
			'Adventure Time The Complete Second Season (2,012) - Clip Princess Rescue Party',
			'The muppet show (1980) - kermit the frog trailer'
		);

		$score = 100;
		$preDomainScore = 30;
		$domainScore = 100;

		foreach( $videos as $video ) {

			//change underscores for spaces, removes comas and dots
			$tmp = str_replace(
				array( '_' ),
				array( ' ' ),
				$video
			);
			print_r( '<pre>' );
			$domain = null;
			//get the data before ( character
			if ( ($parenthisStart = strpos( $tmp, '(' ) ) !== false ) {
				$title = substr( $tmp, 0, $parenthisStart );
				$typesFbresult = $this->fbClient->queryWithTypeFilter( $title, array_keys( $typesMapping ) );
				foreach( $typesFbresult->result as $res ) {
					if ( isset( $res->notable ) ) {
//						print_r( $res->name." ".$res->notable->id."\n" );
						if ( $res->score > $score ) {
							$keywords[ $title ][] = array( 'n' => $res->name, 't' => $typesMapping[ $res->notable->id ] , 's' => $res->score );
							$domain = $res->name;
						} else {
							//check for type and exact match if yes take as keyword, else drop
							if ( isset( $typesMapping[ $res->notable->id ] ) ) {
								if ( trim( $res->name ) === trim( $title ) ) {
									$keywords[ $title ][] = array( 'n' => $res->name, 't' => $typesMapping[ $res->notable->id ] , 's' => $res->score );
								}
							}
						}
					}
				}
			}

//			$domain = ( $freebaseResult->result && $freebaseResult->result[0]->notable ) ? $freebaseResult->result[0]->name : null;

			$words = explode( '-', $tmp );

			//remove date
			foreach ( $words as $key => $word ) {
				//drop the first sentence
				if ( $key == 0 ) continue;
				$wordSplitted = explode( ' ', trim( $word ) );

				$ok = false;
				//cut from back
				$count = count( $wordSplitted );

				//get result for every word with domain of object title
				if ( $domain !== null ) {
					foreach( $wordSplitted as $word ) {
						$fb = $this->fbClient->queryWithDomain( $word, $domain, 5 );
						print_r( $fb );
					}
				}
			}
		}
		print_r( $keywords );
		die;
	}

	protected function checkIfInTitle( $text, $title ) {
		$textCount = count( $text );
		$words = explode( ' ', $text );
		$tWords = explode( ' ', $title );
		$res = 0;
		foreach( $words as $w ) {
			if ( in_array( $w, $tWords ) ) {
				$res++;
			}
		}
		if ( $res == $textCount ) return true;
		return false;
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
		$estimate = $this->relevancyEstimator->compositeEstimate(
			new ArticleInformation( $article ),
			new VideoInformation( $videMetadata ) );
		//var_dump($estimate);
		$this->setVal("estimates", $estimate);
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
