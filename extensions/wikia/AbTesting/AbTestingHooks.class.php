<?php

/**
 * AbTestingHooks contains all hook handlers used in AbTesting
 *
 * @author Władysław Bodzek <wladek@wikia-inc.com>
 */
class AbTestingHooks extends WikiaObject {

	public function onOasisSkinAssetGroupsBlocking( &$jsAssetGroups ) {
		array_unshift( $jsAssetGroups, 'abtesting' );
		return true;
	}

	public function onWikiaSkinTopScripts( &$vars, &$scripts, $skin ) {
		if ( $this->app->checkSkin( 'oasis', $skin ) ) {
			$scripts .= ResourceLoader::makeCustomLink($this->wg->out, array( 'wikia.ext.abtesting' ), 'scripts') . "\n";
		}
		return true;
	}

}