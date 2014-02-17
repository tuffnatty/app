<?php
class WikiaMapPoint extends WikiaModel {
	const MAP_POINTS_TBL = 'wikia_map_point';

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
		} else {
			$this->x = 0;
			$this->y = 0;
		}
	}

	public function save( $data ) {
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

	public function getAuthor() {
		return '';
	}

	public function getCreateDate() {
		return '';
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
