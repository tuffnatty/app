<?php
/**
 * User: artur
 * Date: 26.04.13
 * Time: 17:34
 */

class VideoInformation {
	private $metadata;

	function __construct( array $metadata ) {
		$this->metadata = $metadata;
	}

	public function setMetadata($metadata) {
		$this->metadata = $metadata;
	}

	public function getMetadata() {
		return $this->metadata;
	}
}
