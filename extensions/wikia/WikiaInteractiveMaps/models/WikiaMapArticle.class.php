<?php
class WikiaMapArticle extends Article {

	/**
	 * @desc Render hubs page
	 */
	public function view() {
		global $wgOut, $wgRequest;
		wfProfileIn(__METHOD__);

		$wgOut->clearHTML();
		$wgOut->addHTML( F::app()->sendRequest(
			'WikiaInteractiveMapsController',
			'map',
			[
				'map_id' => $this->getTitle()->getArticleID(),
				'x' => $wgRequest->getVal('x'),
				'y' => $wgRequest->getVal('y'),
				'z' => $wgRequest->getVal('z')
			]
		) );

		wfProfileOut(__METHOD__);
	}

}
