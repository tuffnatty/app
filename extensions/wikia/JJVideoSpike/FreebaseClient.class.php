<?php
/**
 * Created by adam
 * Date: 29.04.13
 */

class FreebaseClient {

	const FREEBASE_URL = 'https://www.googleapis.com/freebase/v1/search';
	const CACHE_DURATION = 86400; //1 day
//	const FREEBASE_API_KEY = 'AIzaSyCcxUdb9z4-7Y2oIX6Tq7lSQ7QMbU0XPfQ';

	protected static $apiKeys = array(
//		'AIzaSyCcxUdb9z4-7Y2oIX6Tq7lSQ7QMbU0XPfQ', //adamr@wikia-inc.com
		'AIzaSyCw6pe0LgDWX-3GRE7DyxF27OfeUGsW7Y4', //wikiawebtools001
		'AIzaSyDhn8zBdeZb4LAjTHfvyzAR3ew5hFKn6_A',	//wikiawebtools002
		'AIzaSyAPGE_YvtJTiKpDG59sVqiOcL7IsU-vz0M',	//wikiawebtools003
		'AIzaSyCILgqeNo3j_cTMgj2CYVY11iTXnsiT9QY',	//wikiawebtools004
		'AIzaSyDE1yvsyEJY8XsJp-Cq646x4rFjPhqcZdw',	//wikiawebtools005
	);

	protected $personTypes = array(
//		'actor' => 'actor',
		'/fictional_universe/fictional_character' => 'character',
//		'/m/02hrh1q' => 'actor', //actor id in freebase
	);

	protected $creativeWorkTypes = array(
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

	protected $app;

	public function __construct() {
		$this->app = F::app();
	}

	public function getAllTypes() {
		return array_merge( $this->personTypes, $this->creativeWorkTypes );
	}

	public function getPersonTypes() {
		return $this->personTypes;
	}

	public function getCreativeWorkTypes() {
		return $this->creativeWorkTypes;
	}

	public function getTypeMapping( $type ) {
		$mappings = $this->getAllTypes();
		if( isset( $mappings[ $type ] ) ) {
			return $mappings[ $type ];
		}
		return null;
	}

	public function queryWithDomain( $query, $domain, $limit = 5 ) {
		return $this->call( $query, null, $domain, $limit );
	}

	public function query( $query, $limit = 5 ) {
		return $this->call( $query, null, null, $limit );
	}

	public function queryWithTypeFilter ( $query, $types, $limit = 5 ) {
		if ( !is_array( $types ) ) {
			//types can be also a string containg a method name, which should be used for getting types list
			if ( method_exists( $this, $types ) ) {
				$types = array_keys( $this->{$types}() );
			} else {
				return null;
			}
		}
		return $this->call( $query, $types, null, $limit );
	}

	public function queryWithTypeDomainFilter( $query, $types, $domain, $limit = 5 ) {
		if ( !is_array( $types ) ) {
			//types can be also a string containg a method name, which should be used for getting types list
			if ( method_exists( $this, $types ) ) {
				$types = array_keys( $this->{$types}() );
			} else {
				return null;
			}
		}
		return $this->call( $query, $types, $domain, $limit );
	}

	public function call( $query, $type = null, $domain = null, $limit = 5 ) {
		$q = array(
			'indent' => 'true',
			'limit' => $limit,
			'query' => trim( $query ),
		);
		$filterDomain = ( $domain !== null ) ? "(all domain:\"{$domain}\"))" : null;
		$filterTypes = ( $type !== null ) ? '(any type:'.implode( ' type:', $type ).')' : null;

		//build filter
		if ( $filterDomain !== null || $filterTypes !== null ) {
			$q[ 'filter' ] = "(all {$filterDomain} {$filterTypes})";
		}
		$cacheUrl = static::FREEBASE_URL . '?' . http_build_query( $q );
		//get key for call
		$q[ 'key' ] = static::getApiKey();

		$url = static::FREEBASE_URL . '?' . http_build_query( $q );
		print_r( 'Proccessing: ' . $url . "\n" );

		$key = $this->generateMemKey( __METHOD__, md5( $cacheUrl ) );
		var_dump( $key );
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

	public static function getApiKey() {
		$key = next( static::$apiKeys );
		if ( $key !== false ) {
			return $key;
		}
		return reset( static::$apiKeys );
	}

}