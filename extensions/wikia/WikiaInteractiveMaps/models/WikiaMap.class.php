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

	public function getName() {
		return $this->title->getText();
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
						'point_type' => $pointObj->getType(),
						'article' => $pointObj->getFullURL(),
					];
				}
			}
		} else {
			$points = [];
		}

		return $points;
	}

	public function getMapsParameters() {
		$parameters = new stdClass();

		$parameters->name = $this->getName();
		$parameters->min_zoom = 0;
		$parameters->max_zoom = 6;
		$parameters->width = 600;
		$parameters->height = 480;
		$parameters->type = 2;
		$parameters->status = 1;
		$parameters->url = $this->title->getFullURL();

		return $parameters;
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
