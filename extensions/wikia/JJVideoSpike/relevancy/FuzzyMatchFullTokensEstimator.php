<?php
/**
 * User: artur
 * Date: 26.04.13
 * Time: 10:55
 */

class FuzzyMatchFullTokensEstimator implements IRelevancyEstimator {
	private $splitTagsTokenizer;
	private $splitTagTokenizer;

	function __construct($splitTagTokenizer = null, $splitTagsTokenizer = null) {
		if( $splitTagsTokenizer == null ) {
			$splitTagsTokenizer = new UniqueTokensTokenizerFilter(
				new StopWordsTokenizerFilter(
					new ToLowerTokenizerFilter(
						new Tokenizer("/[,;.]/"))));
		}
		if( $splitTagTokenizer == null ) {
			$splitTagTokenizer = new UniqueTokensTokenizerFilter(
				new StopWordsTokenizerFilter(
					new ToLowerTokenizerFilter(
						new Tokenizer())));
		}
		$this->splitTagTokenizer = $splitTagTokenizer;
		$this->splitTagsTokenizer = $splitTagsTokenizer;
	}


	public function estimate( ArticleInformation $article, VideoInformation $metatags ) {
		$content = strtolower( $article->getArticle()->getContent() );
		$count = 0;
		//var_dump( $metatags );
		foreach ( $metatags->getMetadata() as $tagType => $tagString ) {
			$tagTokens = $this->splitTagsTokenizer->tokenize( $tagString );
			foreach ( $tagTokens as $i => $tagToken ) {
				if ( $this->fuzyFindTag( $content, $tagToken ) ) {
					$count += 1;
				}
			}
		}
		return $count;
	}

	private function fuzyFindTag( $content, $tagToken ) {
		$tokens = $this->splitTagTokenizer->tokenize($tagToken);
		if ( sizeof( $tokens ) == 0 ) return false;
		$token = $tokens[0];
		$offset = 0;
		while( ( $offset = strpos( $content, $token, $offset ) ) != false ) {
			$res = $this->findNextPart( $content, $tokens, $offset );
			if ( $res ) {
				// echo $tagToken . "\n";
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
}
