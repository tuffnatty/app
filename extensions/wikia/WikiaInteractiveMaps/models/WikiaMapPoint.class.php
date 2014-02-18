<?php
class WikiaMapPoint extends WikiaModel {
	const MAP_POINTS_TBL = 'wikia_map_point';
	const MAP_POINTS_JSON_KEY = 'coordinates';

	/**
	 * @var Integer $pageId
	 */
	private $pageId;

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

	public function getFullURL() {
		return $this->title->getFullURL();
	}

	public function load( $master = false ) {
		if( wfReadOnly() ) {
			throw new Exception( 'DB in read-only mode' );
		}

		$db = $this->getDB( ( $master ? DB_MASTER : DB_SLAVE ) );
		$res = $db->selectRow(
			self::MAP_POINTS_TBL,
			[
				'x',
				'y'
			],
			[
				'page_id' => $this->pageId
			],
			__METHOD__
		);

		if( $res ) {
			$this->setX( $res->x );
			$this->setY( $res->y );
			$this->existInDb = true;
		} else {
			$this->setX( 0 );
			$this->setY( 0 );
			$this->existInDb = false;
		}
	}

	public function save() {
		if( wfReadOnly() ) {
			throw new Exception( 'DB in read-only mode' );
		}

		$db = $this->getDB( DB_MASTER );

		$data = [
			'x' => $this->getX(),
			'y' => $this->getY(),
			'flag' => 0,
		];

		if( $this->existsInDb() ) {
			$db->update( self::MAP_POINTS_TBL, $data, [ 'page_id' => $this->pageId ], __METHOD__ );
		} else {
			$data[ 'page_id' ] = $this->pageId;
			$db->insert( self::MAP_POINTS_TBL, $data, __METHOD__ );
		}

		$db->commit();
	}

	public function getX() {
		return $this->x;
	}

	public function getY() {
		return $this->y;
	}

	public function setX( $x ) {
		$this->x = $x;
	}

	public function setY( $y ) {
		$this->y = $y;
	}

	public function getCoordinates() {
		$coordinates = new stdClass();
		$coordinates->x = $this->getX();
		$coordinates->y = $this->getY();

		return $coordinates;
	}

	public function getCoordinatesFromText() {
		$rev = $this->getRevision();
		$text = $rev->getText();
		$pattern = '/{"' . self::MAP_POINTS_JSON_KEY . '":(.*)}/';
		$matches = [];
		preg_match( $pattern, $text, $matches );
		$results = ( !empty($matches[0]) ? $matches[0] : [] );
		if( $results ) {
			$json = json_decode( $results );
			$this->setX( ( isset( $json->coordinates->x ) ? $json->coordinates->x : $this->getX() ) );
			$this->setY( ( isset( $json->coordinates->y ) ? $json->coordinates->y : $this->getY() ) );
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
		return $rev->getText();
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
