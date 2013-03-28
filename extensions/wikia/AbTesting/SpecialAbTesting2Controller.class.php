<?php
/**
 * @author Piotr Bablok
 */

class SpecialAbTesting2Controller extends WikiaSpecialPageController {

	public function __construct() {
		parent::__construct('AbTesting2', 'abtestpanel', false);
	}

	public function index() {
		if ( !$this->wg->User->isAllowed( 'abtestpanel' ) ) {
			$this->skipRendering();
			throw new PermissionsError( 'abtestpanel' );
		}

		$this->getResponse()->addModuleStyles('wikia.ext.abtesting.edit2.styles');
		$this->getResponse()->addModules('wikia.ext.abtesting.edit');

	}
}