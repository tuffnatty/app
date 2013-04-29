<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artur
 * Date: 28.03.13
 * Time: 14:56
 * To change this template use File | Settings | File Templates.
 */
class SuggestionViewModel {
	public $objectName;
	public $objectId;

	function __construct($suggestion) {
		$this->objectName = $suggestion->name;
		$this->objectId = $suggestion->s;
	}
}
