<?php
/**
 * Created by adam
 * Date: 29.04.13
 */

class FreebaseClient {

	const FREEBASE_URL = 'https://www.googleapis.com/freebase/v1/search';
	const CACHE_DURATION = 86400; //1 day
	const FREEBASE_API_KEY = 'AIzaSyCcxUdb9z4-7Y2oIX6Tq7lSQ7QMbU0XPfQ';

	protected $app;

	public function __construct() {
		$this->app = F::app();
	}

	public function queryWithDomain( $query, $domain, $limit = 5 ) {
		return $this->call( $query, null, $domain, $limit );
	}

	public function query( $query, $limit = 5 ) {
		return $this->call( $query, null, null, $limit );
	}

	public function queryWithTypeFilter ( $query, $types, $limit = 5 ) {
		if ( !is_array( $types ) ) {
			$types = array( $types );
		}
		return $this->call( $query, $types, null, $limit );
	}

	public function queryWithTypeDomainFilter( $query, $types, $domain, $limit = 5 ) {
		if ( !is_array( $types ) ) {
			$types = array( $types );
		}
		return $this->call( $query, $types, $domain, $limit );
	}

	public function call( $query, $type = null, $domain = null, $limit = 5 ) {
		$q = array(
			'indent' => 'true',
			'limit' => $limit,
			'query' => trim( $query ),
			'key' => static::FREEBASE_API_KEY
		);

		$filterDomain = ( $domain !== null ) ? "(all domain:\"{$domain}\"))" : null;
		$filterTypes = ( $type !== null ) ? '(any type:'.implode( ' type:', $type ).')' : null;

		//build filter
		if ( $filterDomain !== null || $filterTypes !== null ) {
			$q[ 'filter' ] = "(all {$filterDomain} {$filterTypes})";
		}

		$url = static::FREEBASE_URL . '?' . http_build_query( $q );
		print_r( 'Proccessing: ' . $url . "\n" );

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

}