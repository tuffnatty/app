<?php
/**
 * User: artur
 * Date: 29.04.13
 * Time: 10:22
 */

class SubjectRelevancyEstimator2 implements IRelevancyEstimator {
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
		$content = join( " ", $metatags->getMetadata() );
		if ( $this->fuzyFindTag( $content, $subject ) ) {
			return 1;
		} else {
			return 0;
		}
	}

	private function fuzyFindTag( $content, $tagToken ) {
		$tokens = $this->tokenizer->tokenize($tagToken);
		if ( sizeof( $tokens ) == 0 ) return false;
		$token = $tokens[0];
		$offset = 0;
		while( ( $offset = strpos( $content, $token, $offset ) ) != false ) {
			$res = $this->findNextPart( $content, $tokens, $offset );
			if ( $res ) {
				return true;
			}
			$offset += 1;
		}
		return false;
	}

	private function findNextPart( $content, array $tags, $offset ) {
		if ( sizeof( $tags ) == 0 ) return true;
		$tag = $tags[0];
		$newTags = array_slice( $tags, 1 );
		$pos = strpos( $content, $tag, $offset );
		if( $pos == false ) return false;
		if ( $pos - $offset > 3 ) return false;
		$pos += strlen( $tag );
		return $this->findNextPart( $content, $newTags, $pos );
	}

	private function extractFirstElements( array $array ) {
		$resultArray = array();
		foreach( $array as $i => $el ) {
			$resultArray[] = $el[0];
		}
		return $resultArray;
	}
}
