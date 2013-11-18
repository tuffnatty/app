<?php

class WikiaMobileEditorController extends WikiaController{

	const TEMPLATE_ENGINE = WikiaResponse::TEMPLATE_ENGINE_MUSTACHE;

	/**
	 * @brief Returns true
	 * @details Adds assets needed on Edit page on WikiaMobile skin
	 *
	 * @return true
	 */
	public static function onWikiaMobileAssetsPackages( &$head, &$body, &$scss ){

        global $wgOut;

        $action = F::app()->wg->Request->getVal( 'action' );

        //$wgOut->mBodytext = '';
        //var_dump($wgOut->getHTML());
        //exit();
        //$wgOut->clearHTML();

		if ( $action == 'edit' || $action == 'submit') {
			$body[] = 'wikiamobile_editor_js';
			$scss[] = 'wikiamobile_editor_scss';
		}

		return true;
	}

    public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
        if(F::app()->wg->Request->getVal( 'action' ) == 'edit'){
            $out->clearHTML();
            $out->addHTML(F::app()->renderView( __CLASS__, 'index' ));
            return false;
        }
        return true;
    }

    //Alternating edit view on mobile (no header, no footer)
    public static function onAlternateEdit( $editpage ){

        RenderContentOnlyHelper::setRenderContentVar( true );
        RenderContentOnlyHelper::setRenderContentLevel( RenderContentOnlyHelper::LEAVE_NAV_ONLY );

        $app = F::app();
        $wg = F::app()->wg;

        if ( $app->checkSkin( 'wikiamobile' ) ) {

            $wgOut = F::app()->getGlobal('wgOut');
            //$wgOut->setArticleBodyOnly(true);
            $wgOut->clearHTML();
        }
        else{
            $wg->Out->addHTML('<h1>NoHello</h1>');
        }
        return true;
    }

	/**
	 * @brief Returns true
	 * @details This function doesn't actually do anything - handler for MediaWiki hook
	 *
	 * @param OutputPage &$out MediaWiki OutputPage passed by reference
	 * @param string &$text The article contents passed by reference
	 *
	 * @return true
	 */
	public static function onEditPageInitial( EditPage $editPage ) {
        $app = F::app();

		if ( $app->checkSkin( 'wikiamobile' ) ) {



            //$editPage->editFormTextBottom .= F::app()->renderView( __CLASS__, 'editPage' );
            $section = $app->getGlobal('wgRequest')->getVal('section');
            $editPage->editFormTextTop .= '<h1>Editing ' .
                $editPage->mTitle->mTextform;
            if($section || $section==0){
                $editPage->editFormTextTop .= ' (Section)';
            }

            $editPage->editFormTextTop .= '</h1>';
            $editPage->editFormTextBeforeContent = '';
            $editPage->editFormTextAfterWarn = '';
            $editPage->editFormTextAfterTools = '';
            $editPage->editFormTextBottom = '';
            $editPage->editFormTextAfterContent = '';
            $editPage->previewTextAfterContent = '';
            $editPage->mPreloadText = '';
            $wgOut = F::app()->getGlobal('wgOut');
            //$wgOut->clearHTML();
            //var_dump($wgOut->getOutput()->getHTML());

            //$editPage->editFormTextBottom .= F::app()->renderView( __CLASS__, 'index' );
            //exit();

		}

		return false;
	}

	public function editPage(){
		$this->response->setTemplateEngine( self::TEMPLATE_ENGINE );
	}

    public function index(){
        $this->response->setTemplateEngine( self::TEMPLATE_ENGINE );
    }

	public function tagList(){
		$this->response->setTemplateEngine( self::TEMPLATE_ENGINE );
	}
}