<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 19.02.13 15:52
 *
 */
class Pandora {

	public static $config = array(

		'endpoint_base' => 'http://dev-arturd:9494',
		'endpoint_api_v' => '/api/v0.1/',
		'id_base_url' => 'http://sds.wikia.com/',
		'current_collection_name' => '',
		'common_object_collection_name' => 'sds'
	);

	public static function getConfig( $key = null ) {
		if ( $key ) {
			return static::$config[ $key ];
		}
		return static::$config;
	}

	/**
	 * @param Title $title
	 * @return string Pandora Id, for example http://sds.wikia.com/video151/183, this is ID that identifies an object in pandora database
	 */
	public static function pandoraIdFromTitle( Title $title ) {

		// for example: 'http://sds.wikia.com/video151/'.$articleId
		$id = static::$config['id_base_url'] . static::$config['current_collection_name'] . '/' . $title->getArticleID();
		return $id;
	}

	/**
	 * @param $wikiArticleId the id of described object, for example articleId for video (File Page id)
	 * @return string Pandora Id, for example http://sds.wikia.com/video151/183, this is ID that identifies an object in pandora database
	 */
	public static function pandoraIdFromArticleId( $wikiArticleId ) {

		// for example: 'http://sds.wikia.com/video151/'.$articleId
		$id = static::$config['id_base_url'] . static::$config['current_collection_name'] . '/' . $wikiArticleId;
		return $id;
	}

	/**
	 * @return string $id generates new Id for common object (object that is not direct representation of wiki article, for example: movie object, actor object)
	 */
	public static function generateCommonObjectId() {

		// for example: "http://sds.wikia.com/sds/~" . base64_encode(microtime(true) . rand());
		$id = static::$config['id_base_url'] . static::$config['common_object_collection_name'] . '/~' . base64_encode(microtime(true) . rand());
		return $id;
	}
}
