<?php

class WikiaInteractiveMapsHooks {

	/**
	 * @param Title $title
	 * @param Page $article
	 *
	 * @return true because it's a hook
	 */
	static public function onArticleFromTitle( &$title, &$article ) {
		wfProfileIn(__METHOD__);

		if ( !is_null( $title ) &&  $title->getNamespace() === NS_WIKIA_MAP) {
			$article = new WikiaMapArticle( $title );
		}
		wfProfileOut(__METHOD__);
		return true;
	}

} 