<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 24.04.13 16:48
 *
 */

class WikiSubjects {

	protected $wikiId = null;

	public function __construct( $wikiId = null ) {

		if ( empty( $wikiId ) ) {

			$app = F::app();
			$this->wikiId = $app->wg->cityId;
		} else {

			$this->wikiId = $wikiId;
		}
	}

	public function get() {

		//TODO: get this data from DB
		$allSubjects = array(
			array("Call of Duty", "game"),
			array("Call of Duty: Black Ops", "game"),
			array("Call of Duty: Black Ops: Declassified", "game"),
			array("Call of Duty: Black Ops (Nintendo DS)", "game"),
			array("Call of Duty: Black Ops II", "game"),
			array("Call of Duty: Black Ops II Official Soundtrack", "game"),
			array("Call of Duty: Black Ops Zombies", "game"),
			array("Call of Duty: Combined Forces", "game"),
			array("Call of Duty: Deluxe Edition", "game"),
			array("Call of Duty: Devil's Brigade", "game"),
			array("Call of Duty: Finest Hour", "game"),
			array("Call of Duty: Modern Warfare: Mobilized", "game"),
			array("Call of Duty: Modern Warfare: Reflex Edition", "game"),
			array("Call of Duty: Modern Warfare 2", "game"),
			array("Call of Duty: Modern Warfare 2: Force Recon", "game"),
			array("Call of Duty: Modern Warfare 3", "game"),
			array("Call of Duty: Modern Warfare 3: Defiance", "game"),
			array("Call of Duty: Roads to Victory", "game"),
			array("Call of Duty: Trilogy", "game"),
			array("Call of Duty: United Offensive", "game"),
			array("Call of Duty: War Chest", "game"),
			array("Call of Duty: World at War", "game"),
			array("Call of Duty: World at War: Final Fronts", "game"),
			array("Call of Duty: World at War (Mobile)", "game"),
			array("Call of Duty: World at War (Nintendo DS)", "game"),
			array("Call of Duty: Zombies", "game"),
			array("Call of Duty 2", "game"),
			array("Call of Duty 2: Big Red One", "game"),
			array("Call of Duty 2 (Mobile)", "game"),
			array("Call of Duty 2 (Windows Mobile)", "game"),
			array("Call of Duty 2 Special Edition Bonus DVD", "game"),
			array("Call of Duty 3", "game"),
			array("Call of Duty 4: Modern Warfare", "game"),
			array("Call of Duty 4: Modern Warfare (Mobile)", "game"),
			array("Call of Duty Online", "game"),
		);

		return $allSubjects;
	}


}