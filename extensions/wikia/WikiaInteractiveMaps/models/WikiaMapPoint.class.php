<?php
class WikiaMapPoint extends WikiaModel {
	private $title;

	public function __construct( Title $title ) {
		$this->title = $title;
	}

	/**
	 * @desc Based on namespace tells if it's a a map point
	 *
	 * @return bool
	 */
	public function isMapPoint() {
		return ( $this->title->getNamespace() === NS_WIKIA_MAP_POINT );
	}

}
