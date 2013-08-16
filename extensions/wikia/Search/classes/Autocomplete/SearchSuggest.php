<?php
/**
 * Class definition for Wikia\Search\Autocomplete\SearchSuggest
 */
namespace Wikia\Search\Autocomplete;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use Wikia\Search\MediaWikiService;
use DataMartService;
/**
 * Responsible for handling autocomplete use cases
 * @author relwell
 */
class SearchSuggest
{
	/**
	 * This helps us reduce complexity by applying a heurstic limit
	 * This is the top N articles by pageview descending
	 * @var int
	 */
	const MAX_ARTICLES_ANALYZED = 5000;
	
	/**
	 * This is the max number of suggestions we want to give the user
	 * @var int
	 */
	const MAX_AUTOCOMPLETE_SUGGESTIONS = 5;
	
	/**
	 * Used to tokenize results, so we can get semi-complete results.
	 * @var NlpTools\Tokenizers\WhitespaceTokenizer
	 */
	protected $tokenizer;
	
	/**
	 * Gives us access to MediaWiki behaviors
	 * @var Wikia\Search\MediaWikiService
	 */
	protected $mediaWikiService;
	
	/**
	 * Constructor method. Sets up dependencies.
	 */
	public function __construct() {
		$this->tokenizer = new WhitespaceTokenizer;
		$this->mediaWikiService = new MediaWikiService;
	}
	
	/**
	 * Returns an autocomplete trie in array form
	 * @return array
	 */
	public function getTrie() {
		return $this->createTrieFromTitles( $this->getPageIds() );
	}
	
	/**
	 * Returns an array of page IDs
	 * @return array
	 */
	protected function getPageIds() {
		$mediaWikiService = $this->getMediaWikiService();
		return array_keys (
				DataMartService::getTopArticlesByPageview( 
						$mediaWikiService->getWikiId(), 
						null,  // article filter -- unnecessary
						$mediaWikiService->getDefaultNamespacesFromSearchEngine(), 
						false, // namespace filter -- unneeded since we're specifying in the param above
						self::MAX_ARTICLES_ANALYZED // limit to topp N pages for complexity reasons
				)
		);
	}
	
	/**
	 * Builds a trie based on the words of each title and matches for other words too
	 * @param array $pageIds
	 * @return array
	 */
	protected function createTrieFromTitles( array $pageIds ) {
		$trie = [];
		$mediaWikiService = $this->getMediaWikiService();
		foreach ( $pageIds as $pageId ) {
			$titleStrings = array_merge( 
					[ $mediaWikiService->getTitleStringFromPageId( $pageId ) ],
					$mediaWikiService->getRedirectTitlesForPageId( $pageId )
			);
			foreach ( $titleStrings as $title ) {
				// the tokenization part allows us to have partial matches from words within the title
				$tokenized = $this->getTokenizer()->tokenize( $title );
				$candidates = [ $title ];
				for ( $i = 1; $i < count( $tokenized ); $i++ ) {
					$candidates[] = implode( ' ', array_slice( $tokenized, $i ) );
				}
			}
			foreach ( $candidates as $candidate ) {
				$this->applyInstanceToTrie( $title, $candidate, $trie );
			}
		}
		return $trie;
	}
	
	/**
	 * Subroutine for adding a specific title to a trie, iteratively
	 * @param string $url the url the instance refers to
	 * @param string $instance the string value we're treating as an autocomplete value, can be in the middle of a word
	 * @param array $trie
	 */
	protected function applyInstanceToTrie( $url, $instance, array &$trie ) {
		$concat = '';
		foreach ( $instance as $character ) {
			$concat .= $character;
			/**
			 * Instead of adding the $url value, we could do something like this:
			 * preg_replace( '/\b({$concat})/', '<strong>$1</strong>', $url )
			 */
			if (! empty( $trie[$concat] ) ) {
				if ( count( $trie[$concat] < self::MAX_AUTOCOMPLETE_SUGGESTIONS ) ) {
					$trie[$concat][] = $url;
				}
			} else {
				$trie[$concat] = [$url]; 
			}
		}
	}
	
	/**
	 * Accessor method
	 * @return \NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer
	 */
	protected function getTokenizer() {
		return $this->tokenizer;
	}
	
	/**
	 * Accessor method
	 * @return \Wikia\Search\MediaWikiService
	 */
	protected function getMediaWikiService() {
		return $this->mediaWikiService;
	}
	
}