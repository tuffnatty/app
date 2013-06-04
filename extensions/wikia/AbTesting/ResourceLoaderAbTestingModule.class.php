<?php
/**
 * Implements combining dynamic config with static assets
 *
 * @author Władysław Bodzek
 */
class ResourceLoaderAbTestingModule extends ResourceLoaderModule {

	public function getScript(ResourceLoaderContext $context) {
		return AbTestingConfig::getInstance()->getScript();
	}

	public function supportsURLLoading() {
		return false;
	}

	public function getModifiedTime( ResourceLoaderContext $context ) {
		$modifiedTime = AbTestingConfig::getInstance()->getModifiedTime();
		if ( empty($modifiedTime) ) {
			$modifiedTime = 1;
		}
		return $modifiedTime;
	}

}
