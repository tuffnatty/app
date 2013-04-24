<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 16:40
 */

class MatchAllRelevancyEstimator implements IRelevancyEstimator {
	private $tokenizer;

	function __construct( ITokenizer $tokenizer = null ) {
		if ( $tokenizer == null ) $tokenizer = new StopWordsTokenizerFilter( new Tokenizer() );
		$this->tokenizer = $tokenizer;
	}

	public function estimate(Article $article, array $metatags) {
		$content = $article->getContent();
		$count = 0;
		var_dump( $metatags );
		foreach ( $metatags as $tagType => $tagString ) {
			$tagTokens = $this->tokenizer->tokenize( $tagString );
			foreach ( $tagTokens as $i => $tagToken ) {
				if ( strpos( strtolower( $content ), strtolower( $tagToken) ) ) {
					echo "Found: " . $tagToken . "<br/>";
					$count += 1;
				}
			}
		}
		return $count;
	}
}
