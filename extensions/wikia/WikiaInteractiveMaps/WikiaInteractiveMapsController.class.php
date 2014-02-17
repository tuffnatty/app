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
		$pointId = $this->request->getInt( 'point_id' );
		$title = Title::newFromID( $pointId );

		if( !is_null( $title ) ) {
			$point = new WikiaMapPoint( $title );

			$this->setVal( 'pointId', $pointId );
			$this->setVal( 'title', $point->getText() );
			$this->setVal( 'link', $point->getFullURL() );
			$this->setVal( 'photo', $point->getPhoto() );
			$this->setVal( 'coordinates', $point->getCoordinates() );
			$this->setVal( 'createdby', $point->getAuthor() );
			$this->setVal( 'created', $point->getCreateDate() );
			$this->setVal( 'description', $point->getDescription() );
			$this->setVal( 'notCreated', false );
		} else {
			$this->setVal( 'notCreated', true );
		}

		$this->response->setTemplateEngine( WikiaResponse::TEMPLATE_ENGINE_MUSTACHE );
	}

}
