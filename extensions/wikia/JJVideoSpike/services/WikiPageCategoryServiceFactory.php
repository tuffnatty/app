<?php
/**
 * User: artur
 * Date: 29.04.13
 * Time: 13:24
 */

class WikiPageCategoryServiceFactory {
	private $cache = array();

	public function get ( $wiki_id = null ) {
		if ( $wiki_id == null ) {
			global $wgCityId;
			$wiki_id = $wgCityId;
		}
		if ( !isset( $this->cache[$wiki_id] ) ) {
			$this->cache[$wiki_id] = new WikiPageCategoryService( $wiki_id );
		}
		return $this->cache[$wiki_id];
	}
}
