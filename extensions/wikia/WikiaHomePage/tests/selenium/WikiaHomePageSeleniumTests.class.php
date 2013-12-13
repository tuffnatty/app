<?php
require_once( '/home/nandy/Phpstormprojects/app/tests/php-webdriver-master/wikia/WikiaSeleniumBaseTest.class.php' );

class WikiaHomePageSeleniumTests extends WikiaSeleniumBaseTest {

	public function testRemixButton() {
		$this->open( 'http://www.wikia.com' );
		$remixButton = $this->session->element( PHPWebDriver_WebDriverBy::CSS_SELECTOR, '.remix .remix-button' );
		$firstSlotVisitLink = $this->session->element( PHPWebDriver_WebDriverBy::CSS_SELECTOR, '.wikia-slot .goVisit' );
		$linkBeforeRemix = $firstSlotVisitLink->attribute( 'href' );

		$remixButton->click();

		$firstSlotVisitLink = $this->session->element( PHPWebDriver_WebDriverBy::CSS_SELECTOR, '.wikia-slot .goVisit' );
		$linkAfterRemix = $firstSlotVisitLink->attribute( 'href' );

		$this->assertNotEquals( $linkBeforeRemix, $linkAfterRemix );

		$this->close();
	}

}
