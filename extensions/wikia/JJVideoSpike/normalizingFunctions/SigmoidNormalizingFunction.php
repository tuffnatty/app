<?php
/**
 * User: artur
 * Date: 29.04.13
 * Time: 10:48
 */

class SigmoidNormalizingFunction implements INormalizingFunction {
	private $shift;
	private $scale;

	function __construct( $shift = 0.5, $scale = 2.0 ) {
		$this->shift = $shift;
		$this->scale = $scale;
	}

	function normalize( $arg ) {
		return ( 1 / ( 1 + exp(-$arg) ) - $this->shift ) * $this->scale;
	}

	public function setShift($shift) {
		$this->shift = $shift;
	}

	public function getShift() {
		return $this->shift;
	}

	public function setScale($scale) {
		$this->scale = $scale;
	}

	public function getScale() {
		return $this->scale;
	}
}
