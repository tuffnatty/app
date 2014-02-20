<?php
class WikiaInteractiveMapsController extends WikiaSpecialPageController {

	public function __construct( $name = null, $restriction = 'editinterface', $listed = true, $function = false, $file = 'default', $includable = false ) {
		parent::__construct( 'InteractiveMaps', $restriction, $listed, $function, $file, $includable );
	}

	public function index() {
		$mapsModel = new WikiaMaps();
		$maps = $mapsModel->getAllMaps();
		$this->setVal( 'maps', $maps );

		$this->response->setTemplateEngine( WikiaResponse::TEMPLATE_ENGINE_MUSTACHE );
	}

	/**
	 * @desc Displays POI page
	 *
	 * @requestParam Integer $point_id
	 */
	public function point() {
		/** @var Language $wgLang */
		/** @var User $wgUser */
		global $wgLang, $wgUser;

		$pointId = $this->request->getInt( 'point_id' );
		$title = Title::newFromID( $pointId );

		if( !is_null( $title ) ) {
			$point = new WikiaMapPoint( $title );

			$this->setVal( 'pointId', $pointId );
			$this->setVal( 'title', $point->getText() );
			$this->setVal( 'link', $point->getFullURL() );
			$this->setVal( 'photo', $point->getPhoto() );
			$this->setVal( 'map', $point->getMap() );
			$this->setVal( 'coordinates', $point->getCoordinates() );
			$this->setVal( 'createdBy', $point->getCreator() );
			$this->setVal( 'created', $wgLang->userDate( $point->getCreateDate(), $wgUser ) );
			$this->setVal( 'updated', $wgLang->userDate( $point->getUpdateDate(), $wgUser ) );
			$this->setVal( 'updatedBy', $point->getAuthor() );
			$this->setVal( 'description', $point->getDescription() );
			$this->setVal( 'notCreated', false );
		} else {
			$this->setVal( 'notCreated', true );
		}

		$this->response->setTemplateEngine( WikiaResponse::TEMPLATE_ENGINE_MUSTACHE );
	}

	/**
	 * @desc Displays map page
	 *
	 * @requestParam Integer $map_id
	 */
	public function map() {
		global $wgUser;

		$pointId = $this->request->getInt( 'map_id' );
		$title = Title::newFromID( $pointId );

		if( !is_null( $title ) ) {
			$this->setVal( 'notCreated', false );

			JSMessages::registerPackage( 'WikiaInteractiveMaps', array(
				'wikia-interactive-maps-add-new-point',
				'wikia-interactive-maps-center-map-here',
				'wikia-interactive-maps-zoom-in',
				'wikia-interactive-maps-zoom-out',
				'wikia-interactive-maps-article',
				'wikia-interactive-maps-description',
				'wikia-interactive-maps-poi-type',
				'wikia-interactive-maps-add-point'
			));
			JSMessages::enqueuePackage( 'WikiaInteractiveMaps', JSMessages::INLINE );

			$wikiaMap = WikiaMapFactory::build( $title );
			$mapParameters = $wikiaMap->getMapsParameters();
			$this->setVal( 'title', $mapParameters->name );
			$this->wg->Out->addJsConfigVars([
				interactiveMapSetup => [
					'canEdit' => $wgUser->isLoggedIn(),
					'mapId' => $title->mArticleID,
					'width' => $mapParameters->width,
					'height' => $mapParameters->height,
					'mapType' => $mapParameters->type,
					'pathTemplate' => $mapParameters->pathTemplate,
					'mapSetup' => $mapParameters->mapSetup
				]
			]);

			// Leaflet
			$this->response->addAsset( 'extensions/wikia/WikiaInteractiveMaps/js/leaflet/leaflet-src.js' );
			$this->response->addAsset( 'extensions/wikia/WikiaInteractiveMaps/js/leaflet/leaflet.css' );
			// Leaflet Fullscreen
			$this->response->addAsset( 'extensions/wikia/WikiaInteractiveMaps/js/leaflet/leaflet.fullscreen/Control.FullScreen.css' );
			$this->response->addAsset( 'extensions/wikia/WikiaInteractiveMaps/js/leaflet/leaflet.fullscreen/Control.FullScreen.js' );
			// Leaflet Context menu
			$this->response->addAsset( 'extensions/wikia/WikiaInteractiveMaps/js/leaflet/Leaflet.contextmenu/dist/leaflet.contextmenu.js' );
			$this->response->addAsset( 'extensions/wikia/WikiaInteractiveMaps/js/leaflet/Leaflet.contextmenu/dist/leaflet.contextmenu.css' );
			// Custom assets
			$this->response->addAsset( 'extensions/wikia/WikiaInteractiveMaps/css/InteractiveMaps.css');
			$this->response->addAsset( 'extensions/wikia/WikiaInteractiveMaps/js/WikiaInteractiveMaps.js');

		} else {
			$this->setVal( 'notCreated', true );
		}

		$this->response->setTemplateEngine( WikiaResponse::TEMPLATE_ENGINE_MUSTACHE );
	}

	/**
	 * @requestParam String title unique title of the POI
	 * @requestParam Integer map_id
	 * @requestParam Integer x
	 * @requestParam Integer y
	 * @requestParam Integer flag
	 * @requestParam String desc optional
	 *
	 * @throws Exception
	 */
	public function createPoint() {
		global $wgUser;

		$data = [
			'title' => $this->request->getVal( 'title' ),
			'map_id' => $this->request->getVal( 'mapId' ),
			'x' => $this->request->getInt( 'x' ),
			'y' => $this->request->getInt( 'y' ),
			'desc' => $this->request->getVal( 'desc' ),
			'flag' => $this->request->getInt( 'flag', 0 ),
		];

		$this->validateCreation( $data );
		$pointTitle = Title::newFromText( $data['title'], NS_WIKIA_MAP_POINT );
		$page = new WikiPage( $pointTitle );

		$json = new stdClass();
		$json->coordinates->x = $data['x'];
		$json->coordinates->y = $data['y'];
		$json->mapId = $data['map_id'];

		$content = $data['desc'] . " ". json_encode( $json );

		$status = $page->doEdit( $content, '', 0, false, $wgUser );
		$result = new stdClass();
		if( $status->isOK() ) {
			$result->status = 'ok';
			$json->title = $data['title'];
			$json->desc = $data['desc'];
			$result->point = $json;
		} else {
			$result->status = 'fail';
			$result->errors = $status->getErrorsArray();
		}

		$this->result = $result;
	}

	/**
	 * @param Array $data
	 * @throws Exception
	 */
	private function validateCreation( Array $data ) {
		global $wgUser;

		if( !$this->request->wasPosted() ) {
			throw new Exception( 'This request should be send via POST' );
		}

		if( !$wgUser->isLoggedIn() ) {
			throw new Exception( 'You are not authorized to execute this action' );
		}

		if( empty( $data[ 'title' ] ) ) {
			throw new Exception( 'Invalid title' );
		}

		if( empty( $data[ 'desc' ] ) ) {
			throw new Exception( 'Invalid description' );
		}

		$mapTitle = Title::newFromID( $data['map_id'], Title::GAID_FOR_UPDATE );
		if( is_null($mapTitle) || !$mapTitle->exists() ) {
			throw new Exception( 'Invalid map id' );
		}

		$pointTitle = Title::newFromText( $data['title'], NS_WIKIA_MAP_POINT );
		if( $pointTitle->exists() ) {
			throw new Exception( 'This point already exist' );
		}
	}

	/**
	 * @desc Gets all points for a given map
	 *
	 * @requestParam Integer $mapId
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getPoints() {
		$mapId = $this->request->getInt( 'mapId', 0 );

		$mapTitle = Title::newFromID( $mapId, Title::GAID_FOR_UPDATE );
		if( $mapId === 0 || is_null($mapTitle) || !$mapTitle->exists() ) {
			throw new Exception( 'Invalid map id' );
		}

		try {
			$mapModel = WikiaMapFactory::build( $mapTitle );
			$out = [
				'status' => 'ok',
				'points' => $mapModel->getAllPoints(),
			];
		} catch( Exception $e ) {
			$out = [
				'status' => 'fail',
				'error' => $e->getMessage(),
			];
		}

		$this->result = $out;
	}

}
