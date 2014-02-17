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

		$model = new WikiaMapPoint( $title );
		if( $model->isMapPoint( $title ) ) {
			$article = new WikiaMapPointArticle( $title );
		}

		wfProfileOut(__METHOD__);
		return true;
	}

}
