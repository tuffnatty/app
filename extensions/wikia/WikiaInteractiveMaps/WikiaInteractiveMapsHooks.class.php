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

	/**
	 * @desc Adds the JS asset to the bottom scripts
	 *
	 * @param $skin
	 * @param String $text
	 *
	 * @return bool
	 */
	public static function onSkinAfterBottomScripts( $skin, &$text ) {
		global $wgEnableWikiaInteractiveMaps, $wgExtensionsPath;

		if( !empty( $wgEnableWikiaInteractiveMaps ) ) {
			$text .= sprintf(
				'<script src="%s/%s"></script>',
				$wgExtensionsPath,
				'wikia/WikiaInteractiveMaps/js/WikiaInteractiveMaps.js'
			);
		}

		return true;
	}

}
