<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 24.04.13 16:48
 *
 */

class WikiSubjects {
	private static $cache = array();
	protected $wikiId = null;

	public function __construct( $wikiId = null ) {

		if ( empty( $wikiId ) ) {

			$app = F::app();
			$this->wikiId = $app->wg->cityId;
		} else {

			$this->wikiId = $wikiId;
		}
	}

	public function get() {
		if( isset( self::$cache[$this->wikiId] ) ) return self::$cache[$this->wikiId];
		$serviceFactory = new WikiPageCategoryServiceFactory();
		$service = $serviceFactory->get();
		$result = array();
		foreach( array("game", "book", "movie", "character") as $i => $cat ) {
			$entities = $service->getArticleTitlesByCategory( $cat );
			foreach( $entities as $j => $entity ) {
				$result[] = array( $entity, $cat );
			}
		}
		self::$cache[$this->wikiId] = $result;
		return $result;
	}


}
