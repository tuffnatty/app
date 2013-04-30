<?php
/**
 * User: artur
 * Date: 29.04.13
 * Time: 13:30
 */

$optionsWithArgs = array( 'w', 'f' );
require_once( __DIR__ . '/../../commandLine.inc' );

class CategoriesFromFile {
	private $regex = "/\\\"(.*)\\\", \\\"(.*)\\\"/";
	private $wiki_id = null;
	private $file = null;

	function __construct() {
		$this->load();
	}

	function load() {
		global $options;
		$this->wiki_id = F::app()->wg->CityId;
		if( !$this->wiki_id ) {
			throw new Exception("No city id.");
		}
		if( isset( $options["f"] )) {
			$this->file = $options["f"];
		} else {
			throw new Exception("Provide filenam (-f).");
		}
	}

	public function execute() {

		$fileContent = file_get_contents( $this->file );
		$matches = array();
		preg_match_all( $this->regex, $fileContent, $matches);
		$serviceFactory = new WikiPageCategoryServiceFactory();
		$service = $serviceFactory->get( $this->wiki_id );
		foreach( $matches[0] as $i => $match) {
			$article = $matches[1][$i];
			$category = $matches[2][$i];
			if( $category == "??" ) {
				continue;
			}
			$title = Title::newFromText( $article );
			if( $title->getArticleID() != 0 ) {
				$service->setCategory( $title->getArticleID(), $category );
				echo "OK  : $article - $category\n";
			} else {
				echo "FAIL: $article - $category\n";
			}
		}
	}
}
$script = new CategoriesFromFile();
$script->execute();
