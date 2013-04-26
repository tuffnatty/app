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
		if ( $tokenizer == null ) {
			$tokenizer = new Tokenizer();
			$tokenizer = new ToLowerTokenizerFilter( $tokenizer );
			$tokenizer = new StopWordsTokenizerFilter( $tokenizer );
			$tokenizer = new StopWordsTokenizerFilter( $tokenizer, array("man", "releasedate", "dvd", "the", "interview") );
		}
		$this->tokenizer = $tokenizer;
	}

	public function estimate( ArticleInformation $article, VideoInformation $metatags ) {
		$content = $article->getArticle()->getContent();
		$count = 0;
		//var_dump( $metatags );
		foreach ( $metatags->getMetadata() as $tagType => $tagString ) {
			$tagTokens = $this->tokenizer->tokenize( $tagString );
			foreach ( $tagTokens as $i => $tagToken ) {
				$offset = 0;
				$countForToken = 0;
				while ( ($offset = strpos( strtolower( $content ), strtolower( $tagToken), $offset )) != false ) {
					// echo "Found: " . $tagToken . "\n";
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
