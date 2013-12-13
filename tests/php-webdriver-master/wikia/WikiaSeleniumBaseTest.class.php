<?php
require_once( '/home/nandy/Phpstormprojects/app/tests/php-webdriver-master/PHPWebDriver/__init__.php' );

class WikiaSeleniumBaseTest extends PHPUnit_Framework_TestCase {
	const DEFAULT_WD_HOST = 'http://localhost:4444/wd/hub';
	const DEFAULT_BROWSER = 'firefox';

	protected $webDriver;
	protected $webDriverHost;
	public $session;

	public function __construct( $config = array() ) {
		$this->webDriverHost = (
			isset( $config['host'] ) && is_string( $config['host'] ) && !empty( $config['host'] ) ?
				$config['host'] :
				self::DEFAULT_WD_HOST
		);

		$browser = (
			isset( $config['browser'] ) && is_string( $config['browser'] ) && !empty( $config['browser'] ) ?
			$config['browser'] :
			self::DEFAULT_BROWSER
		);

		$this->webDriver = new PHPWebDriver_WebDriver( $this->webDriverHost );

		$this->session = $this->webDriver->session( $browser );
	}

	public function open( $url ) {
		$this->session->open( $url );
	}

	public function close() {
		$this->session->close();
	}

	public function waitForElementById( $id ) {
		$w = new PHPWebDriver_WebDriverWait( $this->session );
		$session = $this->session;

		$w->until( function( $session ) use ( $id, $session ) {
			return count( $session->elements( PHPWebDriver_WebDriverBy::ID, $id ) );
		} );
	}

}
