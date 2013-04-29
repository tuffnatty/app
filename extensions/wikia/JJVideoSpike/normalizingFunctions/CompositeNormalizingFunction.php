<?php
/**
 * User: artur
 * Date: 29.04.13
 * Time: 10:57
 */

class CompositeNormalizingFunction implements INormalizingFunction {
	private $functions = array();

	function normalize($arg) {
		foreach( $this->functions as $i => $func ) {
			$arg = $func->normalize( $arg );
		}
		return $arg;
	}

	function add( INomralizingFunction $normalizingFunction ) {
		$this->functions[] = $normalizingFunction;
	}
}
