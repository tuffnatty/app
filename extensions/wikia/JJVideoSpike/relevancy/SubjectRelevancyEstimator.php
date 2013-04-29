<?php
/**
 * User: artur
 * Date: 26.04.13
 * Time: 17:46
 */

class SubjectRelevancyEstimator implements IRelevancyEstimator {
	private $tokenizer;
	private $maxMatchesPerToken = 1;

	function __construct( ITokenizer $tokenizer = null ) {
		if ( $tokenizer == null ) {
			$tokenizer = new Tokenizer();
			$tokenizer = new ToLowerTokenizerFilter( $tokenizer );
			$tokenizer = new StopWordsTokenizerFilter( $tokenizer );
		}
		$this->tokenizer = $tokenizer;
	}

	public function estimate( ArticleInformation $article, VideoInformation $metatags ) {
		$result = 0;
		$subjects = $this->extractFirstElements( $article->getSubjects() );
		foreach ( $subjects as $i => $subject ) {
			$result += $this->estimateOne( $subject, $metatags );
		}
		return $result;
	}

	public function estimateOne( $subject, VideoInformation $metatags ) {
		$tokens = $this->tokenizer->tokenize( $subject );
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

	private function extractFirstElements( array $array ) {
		$resultArray = array();
		foreach( $array as $i => $el ) {
			$resultArray[] = $el[0];
		}
		return $resultArray;
	}
}
