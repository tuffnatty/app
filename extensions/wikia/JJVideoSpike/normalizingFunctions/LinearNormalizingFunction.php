<?php
/**
 * User: artur
 * Date: 29.04.13
 * Time: 10:52
 */

class LinearNormalizingFunction implements INormalizingFunction {
	private $min;
	private $max;

	function __construct( $min, $max ) {
		$this->max = $max;
		$this->min = $min;
		if( $min == $max ) throw new InvalidArgumentException("Bad LinearNormalizingFunction initialization $min == $max.");
	}

	function normalize($arg) {
		return ( $arg - ($this->min) ) / ( $this->max - $this->min );
	}

	public function setMax($max) {
		$this->max = $max;
	}

	public function getMax() {
		return $this->max;
	}

	public function setMin($min) {
		$this->min = $min;
	}

	public function getMin() {
		return $this->min;
	}
}
