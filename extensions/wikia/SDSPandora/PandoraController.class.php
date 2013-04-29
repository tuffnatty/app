<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artur
 * Date: 28.03.13
 * Time: 15:22
 * To change this template use File | Settings | File Templates.
 */
class PandoraController extends WikiaSpecialPageController {

	function __construct() {
		parent::__construct('Pandora');
	}
	/**
	 * ajax suggestions entry point.
	 *
	 * usage: $.nirvana.sendRequest({method: 'getSuggestions', controller: 'PandoraController', data: {type: 'music_recording', query: 'a'}});
	 */
	function getSuggestions() {
		$params = $this->getRequest()->getParams();
		if ( isset($params['type']) && isset($params['query']) ) {
			$type = $params['type'];
			$query = $params['query'];
			$limit = 10;
			if( isset($params['limit']) && $params['limit'] < 100 && $params['limit'] > 0 ) {
				$limit = $params['limit'];
			}
			$client = new PandoraAPIClient();
			$resp = $client->getSuggestions($type, $query, array("limit" => $limit));
			if ( $resp->isOK() ) {
				$suggestions = array();
				foreach( $resp->asJson() as $i => $sug ) {
					$suggestions[] = new SuggestionViewModel($sug);
				}
				$this->response->setData( array( "data" => $suggestions, "success" => true ) );
			} else {
				$this->response->setData( array(
					"message" => "".$resp->getMessage(),
					"success" =>  false,
					"data" => array(),
				) );
			}
		} else {
			$this->response->setData( array(
				"message" => "type and query params Expected.",
				"success" =>  false,
				"data" => array(),
			) );
		}
	}
}
