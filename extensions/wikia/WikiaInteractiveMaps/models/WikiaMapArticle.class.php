<?php

class WikiaMapArticle extends Article {

	public function view() {
		global $wgOut;

		wfProfileIn(__METHOD__);

		$wgOut->clearHTML();
		$wgOut->addHTML(
			F::app()->sendRequest(
				'WikiaInteractiveMapsController',
				'map',
				[
					'map_id' =>  $this->getTitle()->getArticleID()
				]
			)
		);

		wfProfileOut(__METHOD__);
	}
} 