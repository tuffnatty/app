<?php
require_once( '/home/nandy/Phpstormprojects/app/tests/php-webdriver-master/wikia/WikiaSeleniumBaseTest.class.php' );

class ForumSeleniumTests extends WikiaSeleniumBaseTest {

	public function testForumPoliciesModal() {
		$this->open( 'http://www.muppet.wikia.com/wiki/Special:Forum' );

		$forumPoliciesBtn = $this->session->element(
			PHPWebDriver_WebDriverBy::CSS_SELECTOR,
			'#Forum .policies-link'
		);

		// open the modal and wait for it to be present
		$forumPoliciesBtn->click();
		$this->waitForElementById( 'ForumPoliciesModal' );

		// close modal by clicking close button on the modal
		$this->session->element(
			PHPWebDriver_WebDriverBy::CSS_SELECTOR,
			'#ForumPoliciesModal .close'
		)->click();

		$this->verifyElementByIdIsNotPresent( 'ForumPoliciesModal' );

		$this->close();
	}

}
