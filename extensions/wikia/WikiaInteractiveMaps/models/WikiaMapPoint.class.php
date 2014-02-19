<?php
class WikiaMapPoint extends WikiaModel {
	const MAP_POINT_TBL = 'wikia_map_point';
	
	const MAP_POINT_JSON_PATTERN = '/{(.*)}/';

	const MAP_POINT_TYPE_CHECKPOINT = 1;
	const MAP_POINT_TYPE_BATTLE = 2;

	/**
	 * @var Integer $pageId
	 */
	private $pageId;

	/**
	 * @var Integer $mapId
	 */
	private $mapId;

	/**
	 * @var Title MW title
	 */
	private $title;

	/**
	 * @var Integer $x
	 */
	private $x;

	/**
	 * @var Integer $y
	 */
	private $y;

	/**
	 * @var Revision $revision
	 */
	private $revision;

	/**
	 * @var Integer $type
	 */
	private $type;

	/**
	 * @var ImageServing $imageServing
	 */
	private $imageServing;

	private $existInDb = false;

	public function __construct( Title $title ) {
		$this->title = $title;
		$this->pageId = $title->getArticleID();
		$this->load();

		if( !$this->existsInDb() ) {
			$this->load( true );
		}
	}

	public function existsInDb() {
		return $this->existInDb;
	}

	public function getText() {
		return $this->title->getText();
	}

	public function setType( $type ) {
		$this->type = $type;
	}

	public function getType() {
		return $this->type;
	}

	public function getFullURL() {
		return $this->title->getFullURL();
	}

	public function load( $master = false ) {
		if( wfReadOnly() ) {
			throw new Exception( 'DB in read-only mode' );
		}

		$db = $this->getDB( ( $master ? DB_MASTER : DB_SLAVE ) );
		$row = $db->selectRow(
			self::MAP_POINT_TBL,
			[
				'map_id',
				'x',
				'y'
			],
			[
				'page_id' => $this->pageId
			],
			__METHOD__
		);

		if( $row ) {
			$this->setMapId( $row->map_id );
			$this->setX( $row->x );
			$this->setY( $row->y );
			$this->existInDb = true;
		} else {
			$this->setX( 0 );
			$this->setY( 0 );
			$this->existInDb = false;
		}

		$this->setType( self::MAP_POINT_TYPE_CHECKPOINT );
	}

	public function save() {
		if( wfReadOnly() ) {
			throw new Exception( 'DB in read-only mode' );
		}

		$db = $this->getDB( DB_MASTER );

		$data = [
			'map_id' => $this->getMapId(),
			'x' => $this->getX(),
			'y' => $this->getY(),
			'flag' => 0,
		];

		if( $this->existsInDb() ) {
			$db->update( self::MAP_POINT_TBL, $data, [ 'page_id' => $this->pageId ], __METHOD__ );
		} else {
			$data[ 'page_id' ] = $this->pageId;
			$db->insert( self::MAP_POINT_TBL, $data, __METHOD__ );
		}

		$db->commit();
	}

	public function getX() {
		return $this->x;
	}

	public function getY() {
		return $this->y;
	}

	public function getMapId() {
		return $this->mapId;
	}

	public function setX( $x ) {
		$this->x = $x;
	}

	public function setY( $y ) {
		$this->y = $y;
	}

	public function setMapId( $mapId ) {
		$this->mapId = $mapId;
	}

	public function getCoordinates() {
		$coordinates = new stdClass();
		$coordinates->x = $this->getX();
		$coordinates->y = $this->getY();

		return $coordinates;
	}

	public function getMap() {
		$mapData = new stdClass();
		$mapData->id = $this->getMapId();

		$mapTitle = Title::newFromID( $mapData->id );
		if( !is_null( $mapTitle ) ) {
			$mapData->url = $mapTitle->getFullURL();
			$mapData->title = $mapTitle->getText();
		} else {
			$mapData->url = '';
			$mapData->title = '';
		}

		return $mapData;
	}

	public function getCoordinatesFromText() {
		$json = $this->getJsonFromText();
		if( !empty( $json ) ) {
			$this->setX( ( isset( $json->coordinates->x ) ? $json->coordinates->x : $this->getX() ) );
			$this->setY( ( isset( $json->coordinates->y ) ? $json->coordinates->y : $this->getY() ) );
		}
	}

	private function getJsonFromText() {
		$rev = $this->getRevision();
		$text = $rev->getText();

		$json = '';
		$matches = [];

		preg_match( self::MAP_POINT_JSON_PATTERN, $text, $matches );
		$results = ( !empty($matches[0]) ? $matches[0] : [] );

		if( $results ) {
			$json = json_decode( $results );
		}

		return $json;
	}

	public function getMapIdFromText() {
		$json = $this->getJsonFromText();
		if( !empty( $json ) ) {
			$this->setMapId( $json->mapId );
		}
	}

	public function getAuthor() {
		$rev = $this->getRevision();
		$userId = $rev->getUser();
		$user = User::newFromId( $userId );

		return $user->getName();
	}

	public function getUpdateDate() {
		$rev = $this->getRevision();
		return $rev->getTimestamp();
	}

	public function getCreator() {
		$rev = $this->title->getFirstRevision();
		$userId = $rev->getUser();
		$user = User::newFromId( $userId );

		return $user->getName();
	}

	public function getCreateDate() {
		$rev = $this->title->getFirstRevision();
		return $rev->getTimestamp();
	}

	public function getDescription() {
		$rev = $this->getRevision();
		return trim( preg_replace( self::MAP_POINT_JSON_PATTERN, '', $rev->getText() ) );
	}

	public function getRevision() {
		if( is_null( $this->revision ) ) {
			$this->revision = Revision::newFromId( $this->title->getLatestRevID() );
		}

		return $this->revision;
	}

	public function getImageServing() {
		if( is_null( $this->imageServing ) ) {
			$this->imageServing = new ImageServing( [ $this->pageId ] );
		}

		return $this->imageServing;
	}

	public function getPhoto() {
		$is = $this->getImageServing();
		$images = $is->getImages( 1 ); //get one image from the article;

		if( !empty( $images ) ) {
			$images = array_shift( $images );
			$photo = array_shift( $images );

			return $photo['url'];
		} else {
			return '';
		}
	}

	/**
	 * @desc Based on namespace tells if it's a a map point
	 *
	 * @return bool
	 */
	public function isMapPoint() {
		return ( !is_null( $this->title ) && $this->title->getNamespace() === NS_WIKIA_MAP_POINT );
	}

}
