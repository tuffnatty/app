<?php

class SpecialVideoGenresController extends WikiaSpecialPageController
{

	public function __construct() {
		parent::__construct( 'VideoGenres', 'VideoGenres', false );
	}


	public function index() {
		$config = new Wikia\Search\Config;
		$genre = $this->getVal( 'genre', null );
		if ( $genre !== null ) {
			$config->setFilterQuery( 'genre', sprintf( 'video_genre_txt:%s', $genre ) );
		}
		$config->setQuery( 'video_genres_txt:*' );
		$config->setVideoGenreSearch( true );
		$search = (new Wikia\Search\QueryService\Factory)->getFromConfig( $config );
		$results = $search->search();
		if ( $genre ) {
			$this->setVal( 'results', $results );
			$this->setVal( 'genre', $genre );
		}
		$this->setVal( 'facets', $search->getFacets() );
	}

}