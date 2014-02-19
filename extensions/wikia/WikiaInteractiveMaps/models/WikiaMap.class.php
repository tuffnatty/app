<?php
class WikiaMap extends WikiaModel {
	const MAP_TYPE_EARTH_OPEN_MAPS = 1;
	const MAP_TYPE_CUSTOM = 2;
	const MAP_TYPE_EARTH_GOOGLE_MAPS = 3;

	/**
	 * @var Title MW title
	 */
	private $title;

	/**
	 * @var Integer pageId
	 */
	private $pageId;

	/**
	 * @var ImageServing $imageServing
	 */
	private $imageServing;

	/**
	 * @var Integer type
	 */
	private $type;

	/**
	 * @var Revision $revision
	 */
	private $revision;

	public function __construct( Title $title ) {
		$this->title = $title;
		$this->pageId = $title->getArticleID();
	}

	public function getName() {
		return $this->title->getText();
	}

	public function getType() {
		if( is_null( $this->type ) ) {
			$this->loadType();
		}

		return $this->type;
	}

	public function setType( $type ) {
		$this->type = $type;
	}

	public function loadType() {
		$rev = $this->getRevision( Title::GAID_FOR_UPDATE );
		$text = $rev->getText();

		if( stripos( $text, '__MAP_TYPE_CUSTOM__' ) !== false ) {
			$this->setType( self::MAP_TYPE_CUSTOM );
		} else {
			$this->setType( self::MAP_TYPE_EARTH_OPEN_MAPS );
		}
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
		$parameters->type = $this->getType();
		$parameters->status = 1;
		$parameters->url = $this->title->getFullURL();
		$parameters->image = $this->getImage();

		return $parameters;
	}

	public function getImageServing() {
		if( is_null( $this->imageServing ) ) {
			$this->imageServing = new ImageServing( [ $this->pageId ] );
		}

		return $this->imageServing;
	}

	public function getImage() {
		$is = $this->getImageServing();
		$images = $is->getImages( 1 ); //get one image from the article;

		if( !empty( $images ) ) {
			$images = array_shift( $images );
			$img = array_shift( $images );
			$file = wfFindFile( $img['name'] );

			return $file->getFullUrl();
		} else {
			return '';
		}
	}

	public function getRevision() {
		if( is_null( $this->revision ) ) {
			$this->revision = Revision::newFromId( $this->title->getLatestRevID() );
		}

		return $this->revision;
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
