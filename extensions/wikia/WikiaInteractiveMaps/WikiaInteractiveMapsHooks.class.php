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

		if ( !is_null( $title ) ) {
			if ( ( new WikiaMapPoint( $title ) )->isMapPoint() ) {
				$article = new WikiaMapPointArticle( $title );
				wfProfileOut(__METHOD__);
				return true;
			}

			$WikiaMap = WikiaMapFactory::build( $title );
			if( !( is_null( $WikiaMap ) && $WikiaMap->isMap() ) ) {
				$article = new WikiaMapArticle( $title );
				wfProfileOut(__METHOD__);
				return true;
			}
		}
		wfProfileOut(__METHOD__);
		return true;
	}

	/**
	 * @param Article $article
	 * @param $user
	 * @param $text
	 * @param $summary
	 * @param $flag
	 * @param $fake1
	 * @param $fake2
	 * @param $flags
	 * @param $revision
	 * @param $status
	 * @param $baseRevId
	 * @return bool
	 */
	public static function onArticleSaveComplete( &$article, &$user, $text, $summary, $flag, $fake1, $fake2, &$flags, $revision, &$status, $baseRevId ) {
		$title = $article->getTitle();

		if( !is_null( $title ) && ( $point = new WikiaMapPoint( $title ) ) && $point->isMapPoint() ) {
			$point->getCoordinatesFromText();
			$point->getMapIdFromText();
			$point->save();
		}

		return true;
	}

}
