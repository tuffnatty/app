<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 18:08
 */

class ToLowerTokenizerFilter implements ITokenizer {
	private $tokenizer;

	function __construct( ITokenizer $tokenizer ) {
		$this->tokenizer = $tokenizer;
	}

	public function tokenize( $string ) {
		$tokens = $this->tokenizer->tokenize( $string );
		$resultTokens = array();
		foreach ( $tokens as $i => $token ) {
			$resultTokens[] = strtolower( $token );
		}
		return $resultTokens;
	}
}
