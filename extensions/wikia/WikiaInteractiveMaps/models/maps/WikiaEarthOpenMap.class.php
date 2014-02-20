<?php
/**
 * Created by PhpStorm.
 * User: aquilax
 * Date: 2/20/14
 * Time: 11:15 AM
 */

class WikiaEarthOpenMap extends WikiaBaseMap {

	/**
	 * @var bool
	 * If true, inverses Y axis numbering for tiles (turn this on for TMS services).
	 */
	private $tms = false;

	/**
	 * @var bool
	 */
	private $noWrap = false;

	/**
	 * @var int
	 */
	private $minZoom = 0;

	/**
	 * @var int
	 */
	private $maxZoom = 16;

	protected function getAttribution() {
		return 'Attribution for Open Street Maps';
	}
	protected function getTms() {
		return $this->tms;
	}
	protected function noWrap() {
		return $this->noWrap;
	}
	protected function getPathTemplate() {
		return 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	}

	protected function getImage() {
		return false;
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

} 