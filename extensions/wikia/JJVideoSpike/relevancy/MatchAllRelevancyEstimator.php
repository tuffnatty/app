<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 16:40
 */

class MatchAllRelevancyEstimator implements IRelevancyEstimator {
	private $tokenizer;
	private $maxMatchesPerToken = 1;

	function __construct( ITokenizer $tokenizer = null ) {
		if ( $tokenizer == null ) $tokenizer = new StopWordsTokenizerFilter( new Tokenizer() );
		$this->tokenizer = $tokenizer;
	}

	public function estimate(Article $article, array $metatags) {
		$content = $article->getContent();
		$count = 0;
		//var_dump( $metatags );
		foreach ( $metatags as $tagType => $tagString ) {
			$tagTokens = $this->tokenizer->tokenize( $tagString );
			foreach ( $tagTokens as $i => $tagToken ) {
				$offset = 0;
				$countForToken = 0;
				while ( ($offset = strpos( strtolower( $content ), strtolower( $tagToken), $offset )) != false ) {
					// echo "Found: " . $tagToken . "<br/>";
					$count += 1;
					$offset += 1;
					$countForToken += 1;
					if ( $countForToken >= $this->maxMatchesPerToken ) {
						break;
					}
				}
			}
		}
		return $count;
	}

	public function setMaxMatchesPerToken($maxMatchesPerToken) {
		$this->maxMatchesPerToken = $maxMatchesPerToken;
	}

	public function getMaxMatchesPerToken() {
		return $this->maxMatchesPerToken;
	}

	public function setTokenizer($tokenizer) {
		$this->tokenizer = $tokenizer;
	}

	public function getTokenizer() {
		return $this->tokenizer;
	}
}
