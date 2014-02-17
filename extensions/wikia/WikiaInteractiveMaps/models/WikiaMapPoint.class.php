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

	private $existInDb = false;

	public function __construct( Title $title ) {
		$this->title = $title;
		$this->pageId = $title->getArticleID();
		$this->load();
	}

	public function getText() {
		return $this->title->getText();
	}

	public function getFullURL() {
		return $this->title->getFullURL();
	}

	public function getPhoto() {
		return '';
	}

	public function load( $master = false ) {
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
			$this->x = $res->x;
			$this->y = $res->y;
			$this->existInDb = true;
		} else {
			$this->x = 0;
			$this->y = 0;
			$this->existInDb = false;
		}
	}

	public function save() {
		$db = $this->getDB( DB_MASTER );

		$data = [
			'x' => $this->getX(),
			'y' => $this->getY(),
			'flag' => 0,
		];

		if( $this->existInDb ) {
			$db->update( self::MAP_POINTS_TBL, $data, [ 'page_id' => $this->pageId ], __METHOD__ );
		} else {
			$data[ 'page_id' ] = $this->pageId;
			$db->insert( self::MAP_POINTS_TBL, $data, __METHOD__ );
		}
	}

	public function getX() {
		return $this->x;
	}

	public function getY() {
		return $this->y;
	}

	public function getCoordinates() {
		$coordinates = new stdClass();
		$coordinates->x = $this->getX();
		$coordinates->y = $this->getY();

		return $coordinates;
	}

	public function getCoordinatesFromText() {
		$rev = Revision::newFromId( $this->title->getLatestRevID() );
		$text = $rev->getText();
		$pattern = '/{"' . self::MAP_POINTS_JSON_KEY . '":(.*)}/';
		$matches = [];
		preg_match( $pattern, $text, $matches );
		$results = ( !empty($matches[0]) ? $matches[0] : [] );
		if( $results ) {
			$json = json_decode( $results );
			$this->x = ( isset( $json->coordinates->x ) ) ? $json->coordinates->x : $this->x;
			$this->y = ( isset( $json->coordinates->y ) ) ? $json->coordinates->y : $this->y;
		}
	}

	public function getAuthor() {
		return '';
	}

	public function getCreateDate() {
		return '';
	}

	public function getDescription() {
		$rev = Revision::newFromId( $this->title->getLatestRevID() );
		return $rev->getText();
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
