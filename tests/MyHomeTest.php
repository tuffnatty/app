<?php

class MyHomeTest extends PHPUnit_Framework_TestCase {
	function setUp() {
		global $IP;
		require_once("$IP/extensions/wikia/MyHome/MyHome.class.php");
	}

	function doEdit($edit) {
		// create "fake" EditPage
		$editor = (object) array(
			'textbox1' => $edit['text'],
		);

		// try to get section name
		MyHome::getSectionName($editor, '', !empty($edit['section']) ? $edit['section'] : false, $errno);

		// create "fake" RecentChange object
		$row = (object) array(
			'rev_timestamp' => time(),
			'rev_user' => 1,
			'rev_user_text' => 'test',
			'page_namespace' => NS_MAIN,
			'page_title' => 'Test',
			'rev_comment' => isset($edit['comment']) ? $edit['comment'] : '',
			'rev_minor_edit' => true,
			'page_is_new' => !empty($edit['is_new']),
			'page_id' => 1,
			'rev_id' => 1,
			'rc_id' => 1,
			'rc_patrolled' => 1,
			'rc_old_len' => 1,
			'rc_new_len' => 1,
			'rc_deleted' => 1,
			'rc_timestamp' => time(),
		);
		$rc = RecentChange::newFromCurRow($row);

		// call MyHome to add its data to rc object and Wikia vars
		MyHome::storeInRecentChanges(&$rc);

		$data = Wikia::getVar('rc_data');

		//var_dump($rc); var_dump($data);

		return $data;
	}

	function testNewPageCreation() {
		// set content of new article
		global $wgParser;
		$wgParser->clearState();
		$wgParser->mOutput->setText('<p>new content</p>');

		$edit = array(
			'text' => '123',
			'is_new' => true,
		);

		$out = array(
			'intro' => 'new content',
		);

		$this->assertEquals(
			$out,
			$this->doEdit($edit) );
	}

	function testSectionEditWithComment() {
		$edit = array(
			'text' => "== foo ==\n",
			'section' => 1,
			'comment' => 'comment',
		);

		$out = array(
			'sectionName' => ' foo ',
			'summary' => 'comment',
		);

		$this->assertEquals(
			$out,
			$this->doEdit($edit) );
	}

	function testSectionEditWithDefaultComment() {
		$edit = array(
			'text' => "=== foo bar ===\n",
			'section' => 1,
			'comment' => '/* foo */',
		);

		$out = array(
			'sectionName' => ' foo bar ',
		);

		$this->assertEquals(
			$out,
			$this->doEdit($edit) );
	}

	function testEditFromViewMode() {
		Wikia::setVar('EditFromViewMode', true);

		$edit = array(
			'text' => '123',
		);

		$out = array(
			'viewMode' => 1,
		);

		$this->assertEquals(
			$out,
			$this->doEdit($edit) );
	}

	function testPackData() {
		$in = array('foo' => 'bar');
		$out = MyHome::customDataPrefix . '{"foo":"bar"}';

		$this->assertEquals(
			$out,
			MyHome::packData($in) );
	}

	function testUnpackData() {
		$in = MyHome::customDataPrefix . '{"foo":"bar"}';
		$out = array('foo' => 'bar');

		$this->assertEquals(
			$out,
			MyHome::unpackData($in) );
	}
}
