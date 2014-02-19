<?php
class WikiaMap extends WikiaModel {

	/**
	 * @var Title MW title
	 */
	private $title;

	/**
	 * @var Integer pageId
	 */
	private $pageId;

	public function __construct( Title $title ) {
		$this->title = $title;
		$this->pageId = $title->getArticleID();
	}

	/**
	 * @desc Based on namespace tells if it's a map
	 *
	 * @return bool
	 */
	public function isMap() {
		return ( !is_null( $this->title ) && $this->title->getNamespace() === NS_WIKIA_MAP );
	}

}
