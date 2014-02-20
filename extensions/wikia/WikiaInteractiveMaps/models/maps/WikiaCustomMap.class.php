<?php
/**
 * Created by PhpStorm.
 * User: aquilax
 * Date: 2/20/14
 * Time: 11:15 AM
 */

class WikiaCustomMap extends WikiaBaseMap {

	/**
	 * @var bool
	 * If true, inverses Y axis numbering for tiles (turn this on for TMS services).
	 */
	private $tms = true;

	/**
	 * @var bool
	 */
	private $noWrap = true;

	/**
	 * @var int
	 */
	private $minZoom = 0;

	/**
	 * @var int
	 */
	private $maxZoom = 6;

	protected function getAttribution() {
		return 'Attribution for wikia map';
	}
	protected function getTms() {
		return $this->tms;
	}
	protected function noWrap() {
		return $this->noWrap;
	}
	protected function getPathTemplate() {
		return '/i/<?php echo $id; ?>/{z}/{x}/{y}.png';
	}

	protected function getMapSetup() {
		return [
			'minZoom' => $this->minZoom,
			'maxZoom' => $this->maxZoom,
			'attribution' => $this->getAttribution(),
			'tms' => $this->getTms(),
			'noWrap' => $this->noWrap()
		];
	}

	public function getImageServing() {
		if( is_null( $this->imageServing ) ) {
			$this->imageServing = new ImageServing( [ $this->pageId ] );
		}

		return $this->imageServing;
	}

	protected function getImage() {
		$is = $this->getImageServing();
		$images = $is->getImages( 1 ); //get one image from the article;

		if( !empty( $images ) ) {
			$images = array_shift( $images );
			$img = array_shift( $images );
			$file = wfFindFile( $img['name'] );

			return $file->getFullUrl();
		} else {
			return '';
		}
	}
}