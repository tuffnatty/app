<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 18:06
 */

class UniqueTokensTokenizerFilter implements ITokenizer {
	private $tokenizer;

	function __construct( ITokenizer $tokenizer ) {
		$this->tokenizer = $tokenizer;
	}

	public function tokenize( $string ) {
		$tokens = $this->tokenizer->tokenize( $string );
		$tokenSet = array();
		$resultTokens = array();
		foreach ( $tokens as $i => $token ) {
			if ( !isset( $tokenSet[$token] ) ) {
				$resultTokens[] = $token;
				$tokenSet[$token] = 1;
			}
		}
		return $resultTokens;
	}
}
