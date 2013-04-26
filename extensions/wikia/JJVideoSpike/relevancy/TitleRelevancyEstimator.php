<?php
/**
 * User: artur
 * Date: 26.04.13
 * Time: 14:42
 */

class TitleRelevancyEstimator implements IRelevancyEstimator {
	private $tokenizer;
	private $tokenFields;

	function __construct( $tokenFields = array( "title" ), ITokenizer $tokenizer = null ) {
		if ( $tokenizer == null ) {
			$tokenizer = new Tokenizer();
			$tokenizer = new ToLowerTokenizerFilter( $tokenizer );
			$tokenizer = new StopWordsTokenizerFilter( $tokenizer );
		}
		$this->tokenizer = $tokenizer;
		$this->tokenFields = $tokenFields;
	}

	public function estimate( ArticleInformation $article, VideoInformation $metatags ) {
		$title = $article->getArticle()->getTitle();
		$tokens = $this->tokenizer->tokenize( $title );
		$tokensLengthSum = $this->sumArrayStringLength( $tokens );
		$result = 0;
		foreach ( $tokens as $i => $token ) {
			foreach ( $metatags->getMetadata() as $j => $tagstring ) {
				$tagstring = strtolower( $tagstring );
				if( strpos( $tagstring, $token ) != false ) {
					$result += strlen( $token ) / $tokensLengthSum;
					break;
				}
			}
		}
		return $result;
	}

	protected function sumArrayStringLength( array $array ) {
		$len = 0;
		foreach ( $array as $i => $str ) {
			$len += strlen( $str );
		}
		return $len;
	}
}
