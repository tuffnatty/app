<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 16:43
 */

class Tokenizer implements ITokenizer {
	private $splitExpression;
	private $minTokenSize;

	function __construct( $splitExpression = "/[\t-=,.';\[\]~@!%&]+/", $minTokenSize = 2) {
		$this->minTokenSize = $minTokenSize;
		$this->splitExpression = $splitExpression;
	}

	public function tokenize( $string ) {
		$tokens = preg_split( $this->splitExpression, $string, -1, PREG_SPLIT_NO_EMPTY );
		$resultTokens = array();
		foreach ( $tokens as $i => $token ) {
			var_dump($token);
			if ( strlen( $token ) >= $this->minTokenSize ) $resultTokens[] = $token;
		}
		return $resultTokens;
 	}

	public function setSplitExpression($splitExpression) {
		$this->splitExpression = $splitExpression;
	}

	public function getSplitExpression() {
		return $this->splitExpression;
	}

	public function setMinTokenSize($minTokenSize) {
		$this->minTokenSize = $minTokenSize;
	}

	public function getMinTokenSize() {
		return $this->minTokenSize;
	}
}
