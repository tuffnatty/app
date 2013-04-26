<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 18:54
 */

class MatchFullTokensEstimator implements IRelevancyEstimator {
	private $splitTagsTokenizer;

	function __construct($splitTagTokenizer = null, $splitTagsTokenizer = null) {
		if( $splitTagsTokenizer == null ) {
			$splitTagsTokenizer = new UniqueTokensTokenizerFilter(
									new StopWordsTokenizerFilter(
										new ToLowerTokenizerFilter(
											new Tokenizer("/[,;.]/"))));
		}

		$this->splitTagsTokenizer = $splitTagsTokenizer;
	}


	public function estimate( ArticleInformation $article, VideoInformation $metatags ) {
		$content = strtolower( $article->getArticle()->getContent() );
		$count = 0;
		//var_dump( $metatags );
		foreach ( $metatags->getMetadata() as $tagType => $tagString ) {
			$tagTokens = $this->splitTagsTokenizer->tokenize( $tagString );
			foreach ( $tagTokens as $i => $tagToken ) {
				$offset = 0;
				if ( ($offset = strpos( $content, $tagToken, $offset )) != false ) {
					// echo "Found: " . $tagToken . "\n";
					$count += 1;
					$offset += 1;
				}
			}
		}
		return $count;
	}
}
