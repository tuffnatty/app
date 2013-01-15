<?php
/**
 * @desc Simple singleton which can be used on a page to provide unique tabindex values 
 */
class WikiaTabIndexer {
	/**
	 * @var int
	 */
	static $tabindex = 0;
	
	/**
	 * @var null | WikiaTabIndexer
	 */
	static $instance = null;

	/**
	 * @return WikiaTabIndexer
	 */
	public static function getInstance() {
		if( is_null(self::$instance) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * @desc Returns current but "used" value which was set in tabindex attribute
	 * @return int
	 */
	public function getCurrent() {
		return self::$tabindex;
	}

	/**
	 * @desc Returns next "free" value which can be set in tabindex attribute
	 * @return int
	 */
	public function getNext() {
		return ++self::$tabindex;
	}
}