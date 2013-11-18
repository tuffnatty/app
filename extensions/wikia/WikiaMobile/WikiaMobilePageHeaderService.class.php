<?php
/**
 * WikiaMobile page header
 * 
 * @author Jakub Olek <bukaj.kelo(at)gmail.com>
 * @authore Federico "Lox" Lucignano <federico(at)wikia-inc.com>
 */
class  WikiaMobilePageHeaderService extends WikiaService {
	static $skipRendering = false;

	static function setSkipRendering( $value = false ){
		self::$skipRendering = $value;
	}

	public function index() {

		if(self::$skipRendering) return false;

        $fullUrl = $this->wg->Request->getFullRequestURL();

        $pageTitle = $this->wg->Out->getPageTitle();
        $action = F::app()->wg->Request->getVal( 'action' );

        if($action != 'edit'){
            $pageTitle .= '<a href=\'' . $fullUrl . '?action=edit&section=0\'>Edit</a>';
        }

		$this->response->setVal( 'pageTitle', $pageTitle );
		$this->response->setVal( 'sharingButton', $this->app->renderView( 'WikiaMobileSharingService', 'button' ) );
		return true;
	}
}
