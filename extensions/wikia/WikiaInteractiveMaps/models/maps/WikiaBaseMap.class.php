<?php
abstract class WikiaBaseMap extends WikiaModel {

	/**
	 * @var Title MW title
	 */
	protected $title;

	/**
	 * @var Integer pageId
	 */
	protected $pageId;

	/**
	 * @var ImageServing $imageServing
	 */
	protected $imageServing;

	/**
	 * @var Integer type
	 */
	protected $type;

	/**
	 * @var Revision $revision
	 */
	protected $revision;

	abstract protected function getAttribution();
	abstract protected function getTms();
	abstract protected function noWrap( );
	abstract protected function getPathTemplate();
	abstract protected function getImage();
	abstract protected function getMapSetup();

	public function __construct( Title $title, $mapType ) {
		$this->title = $title;
		$this->setType( $mapType );
		$this->pageId = $title->getArticleID();
	}

	public function getName() {
		return $this->title->getText();
	}

	public function getType() {
		return $this->type;
	}

	public function setType( $type ) {
		$this->type = $type;
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
						'y' => $pointObj->getY(),
						'desc' => $pointObj->getDescription(),
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

		$parameters->width = 600;
		$parameters->height = 480;
		$parameters->type = $this->getType();
		$parameters->status = 1;
		$parameters->url = $this->title->getFullURL();
		$parameters->type = $this->getType();
		$parameters->pathTemplate = $this->getPathTemplate();
		$parameters->image = $this->getImage();
		$parameters->mapSetup = $this->getMapSetup();
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
