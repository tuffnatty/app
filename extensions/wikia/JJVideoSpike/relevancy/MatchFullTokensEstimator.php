<?php
/**
 * User: artur
 * Date: 24.04.13
 * Time: 18:54
 */

class MatchFullTokensEstimator implements IRelevancyEstimator {
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


	public function estimate(Article $article, array $metatags) {
		// TODO: Implement estimate() method.
	}
}
