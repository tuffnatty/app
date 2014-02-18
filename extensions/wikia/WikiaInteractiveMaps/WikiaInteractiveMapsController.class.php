<?php
class WikiaInteractiveMapsController extends WikiaSpecialPageController {

	public function __construct( $name = null, $restriction = 'editinterface', $listed = true, $function = false, $file = 'default', $includable = false ) {
		parent::__construct( 'InteractiveMaps', $restriction, $listed, $function, $file, $includable );
	}

	public function index() {
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
	 * @requestParam String title unique title of the POI
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
		$content = $data['desc'] . " ". json_encode( $json );

		$this->status = $page->doEdit( $content, '', 0, false, $wgUser );
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

		$title = Title::newFromText( $data['title'], NS_WIKIA_MAP_POINT );
		if( $title->exists() ) {
			throw new \Wikia\Sass\Exception( 'This point already exist' );
		}
	}

}
