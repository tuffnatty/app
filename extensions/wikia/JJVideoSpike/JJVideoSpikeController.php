<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 23.04.13 15:36
 *
 */

class JJVideoSpikeController extends WikiaSpecialPageController {

	public function __construct() {

		// parent SpecialPage constructor call MUST be done
		parent::__construct( 'JJVideoSpike', '', false );
	}


	public function index() {


		die("AAAA");

	}

	public function test() {

		$art = new ArticleSubject(383882);
		$art->getSubjects();


		die("<hr>");
	}

}