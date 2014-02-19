<?php
class WikiaMap extends WikiaModel {

	/**
	 * @var Title MW title
	 */
	private $title;

	/**
	 * @var Integer pageId
	 */
	private $pageId;

	public function __construct( Title $title ) {
		$this->title = $title;
		$this->pageId = $title->getArticleID();
	}

	public function getAllPoints( $master = false ) {
		if( wfReadOnly() ) {
			throw new Exception( 'DB in read-only mode' );
		}

		$db = $this->getDB( ( $master ? DB_MASTER : DB_SLAVE ) );
		$results = $db->select(
			WikiaMapPoint::MAP_POINT_TBL,
			[
				'page_id',
				'x',
				'y'
			],
			[
				'map_id' => $this->pageId
			],
			__METHOD__
		);

		if( $db->numRows( $results ) > 0 ) {
			while( ( $point = $db->fetchObject( $results ) ) ) {
				$pointTitle = Title::newFromID( $point->page_id );
				if( !is_null( $pointTitle ) ) {
					$pointObj = new WikiaMapPoint( $pointTitle );
					$points[] = [
						'title' => $pointObj->getText(),
						'x' => $pointObj->getX(),
						'y' => $pointObj->getX(),
					];
				}
			}
		} else {
			$points = [];
		}

		return $points;
	}

	/**
	 * @desc Based on namespace tells if it's a map
	 *
	 * @return bool
	 */
	public function isMap() {
		return ( !is_null( $this->title ) && $this->title->getNamespace() === NS_WIKIA_MAP );
	}

}
