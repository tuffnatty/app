<?php

/**
 * Class VideoContainer
 *
 * Video specific MediaContainer
 */
class VideoContainer extends MediaContainer {

	/** @var bool */
	protected $isPremium = false;

	/** @var VideoInfo */
	protected $videoInfo;

	/**
	 * @return VideoInfo
	 */
	public function getVideoInfo() {
		if ( $this->videoInfo == null ) {
			$this->videoInfo = VideoInfo::newFromTitle( $this->getDBKey() );
		}
		return $this->videoInfo;
	}

	/**
	 * @return mixed
	 */
	public function getDuration() {
		// Used to get this from file metadata
		return $this->getVideoInfo()->getDuration();
	}

	/**
	 * @return int
	 */
	public function getViewsTotal() {
		$videoInfo = $this->getVideoInfo();
		return $videoInfo->getViewsTotal();
	}

	/**
	 * @return mixed
	 */
	public function getProviderName() {
		// Used to get this from file metadata (WikiaLocalFileShared::getProviderName)
		return $this->getVideoInfo()->getProvider();
	}

	/**
	 * @return mixed
	 */
	public function getEmbedUrl() {
		return $this->getFile()->getHandler()->getEmbedUrl();
	}
}