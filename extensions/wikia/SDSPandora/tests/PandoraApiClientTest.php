<?php
/**
 * @author Jacek 'mech' Wozniak
 */

class PandoraApiClientTest extends WikiaBaseTest {

	public function setUp() {
		$this->setupFile =  dirname(__FILE__) . '/../SDSPandora.setup.php';
		parent::setUp();
		$this->mockGlobalVariable('wgDBname', 'sdsdbmock');
		$this->mockApp();
	}

	/**
	 * Test testGetObjectUrl method
	 */
	public function testGetObjectUrl() {
		$apiClient = F::build( 'PandoraAPIClient', array('http://sds.fake.pl', '/api/v0.1/') );

		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/videos', $apiClient->getCollectionUrl( 'videos' ), 'Providing simple collection name' );
		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/sdsdbmock', $apiClient->getCollectionUrl( ), 'Check if collection defaults to current wiki db name' );
		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/ab98%2Bd-c_', $apiClient->getCollectionUrl( 'ab98+d-c_' ), 'Check all characters allowed in dbname' );
		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/callofduty', $apiClient->getCollectionUrl( 'CallOfDuty' ), 'Only characters in lowcerase can be used for collection name' );
	}

	/**
	 * Test getObjectUrl method
	 */
	public function testGetCollectionUrl() {
		$apiClient = F::build( 'PandoraAPIClient', array('http://sds.fake.pl', '/api/v0.1/') );

		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/videos/12345', $apiClient->getObjectUrl( '12345', 'videos' ), 'Provide simple collection and object name' );
		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/videos/12345', $apiClient->getObjectUrl( 12345, 'videos' ), 'Check if object name can be an integer' );
		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/sdsdbmock/4523', $apiClient->getObjectUrl( '4523' ), 'Check if collection defaults to current wiki db name' );
		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/ab98%2Bd-c_/4523', $apiClient->getObjectUrl( '4523', 'ab98+d-c_' ), 'Check all characters allowed in dbname' );
		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/videos/aaa%20bbb', $apiClient->getObjectUrl( 'aaa bbb', 'videos' ), 'Check if space in object name is properly escaped' );
		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/callofduty/lowercased', $apiClient->getObjectUrl( 'LowerCased', 'CallOfDuty' ), 'Only characters in lowcerase can be used for collection and object name' );
	}

	/**
	 * Test if all object name characters are properly encoded when generating object urls
	 * @param $character - character to be encoded
	 * @param $encodedValue - expected character encoding
	- expected character encoding
	 * @dataProvider charactersEncodingDataProvider
	 */
	public function testObjectNameEncoding($character, $endodedValue) {
		$apiClient = F::build( 'PandoraAPIClient', array('http://sds.fake.pl', '/api/v0.1/') );
		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/db' . $endodedValue . '/' . $endodedValue, $apiClient->getObjectUrl( $character, 'db' . $character ) );
	}

	/**
	 * Test if all object name characters are properly encoded when generating collection urls
	 * @param $character - character to be encoded
	 * @param $encodedValue - expected character encoding
	 * @dataProvider charactersEncodingDataProvider
	 */
	public function testCollectionNameEncoding($character, $endodedValue) {
		$apiClient = F::build( 'PandoraAPIClient', array('http://sds.fake.pl', '/api/v0.1/') );
		$this->assertEquals( 'http://sds.fake.pl/api/v0.1/' . $endodedValue, $apiClient->getCollectionUrl( $character ) );
	}

	/*
	 * Generates input parameters for methods testing url generation
	 */
	public function charactersEncodingDataProvider() {
		return array(
			array( '%', '%25' ), array( '|', '%7C' ), array( '<', '%3C' ), array( '#', '%23' ), array( '"', '%22' ),
			array( '$', '%24' ), array( '&', '%26' ), array( '?', '%3F' ), array( '@', '%40' ), array( '=', '%3D' ),
			array( ';', '%3B' ), array( ':', '%3A' ), array( '/', '%2F' ), array( ',', '%2C' ), array( '+', '%2B' )
		);
	}

	public function testCreateObject() {
		$mockedClient = $this->getMock('PandoraAPIClient', array('call'), array( 'http://sds.fake.pl', '/api/v0.1/' ) );

		$mockedClient->expects($this->once())
			->method('call')
			->with( $this->equalTo( 'http://sds.fake.pl/api/v0.1/sdsdbmock' ),
					$this->equalTo( true ),
					$this->equalTo( 'POST' ),
					$this->equalTo( '{"id":"http://fake.id"}' ) )
			->will( $this->returnValue( new PandoraResponse( Status::newGood(), 200, '{}' ) ) );

		$mockedClient->createObject( $mockedClient->getCollectionUrl(), '{"id":"http://fake.id"}' );
	}

	public function testGetSuggestions() {
		$mockedClient = $this->getMock('PandoraAPIClient', array('call'), array( 'http://sds.fake.pl', '/api/v0.1/' ) );

		$mockedClient->expects($this->once())
			->method('call')
			->with( $this->equalTo( 'http://sds.fake.pl/api/v0.1/suggestions/actor/whatever_user_types' ),
			$this->equalTo( false ),
			$this->equalTo( 'GET' ),
			$this->equalTo( null ) )
			->will( $this->returnValue( new PandoraResponse( Status::newGood(), 200, '[]' ) ) );

		$mockedClient->getSuggestions( 'actor', 'whatever_user_types' );
	}

	/*
	 * Test if getObjectAsJson returns JSON object in case of the correct response
	 */
	public function testGetObjectAsJson() {
		$mockedClient = $this->getMock('PandoraAPIClient', array('call'), array( 'http://sds.fake.pl', '/api/v0.1/' ) );
		$mockedClient->expects($this->once())
			->method('call')
			->with( $this->equalTo( 'http://sds.fake.pl/api/v0.1/sdsdbmock/testid01' ) )
			->will( $this->returnValue( new PandoraResponse( Status::newGood(), 200, '{"a":"b"}' ) ) );
	    $return = $mockedClient->getObjectAsJson( $mockedClient->getObjectUrl( 'testid01' ) );
		$this->assertInternalType( 'object', $return );
		$this->assertAttributeEquals( 'b', 'a', $return );
	}

	/*
	 * Test if getObjectAsJson returns null in case server returns "Not found"
	 */
	public function testGetObjectAsJsonNotFound() {
		$mockedClient = $this->getMock('PandoraAPIClient', array('call'), array( 'http://sds.fake.pl', '/api/v0.1/' ) );
		$mockedClient->expects($this->once())
			->method('call')
			->with( $this->equalTo( 'http://sds.fake.pl/api/v0.1/sdsdbmock/testid02' ) )
			->will( $this->returnValue( new PandoraResponse( Status::newFatal( '' ), 404, '{"a":"b"}' ) ) );
		$this->assertNull( $mockedClient->getObjectAsJson( $mockedClient->getObjectUrl( 'testid02' ) ) );
	}

	/**
	 * Test if getObjectAsJson throws an exception in case there is a server error
	 * @expectedException WikiaException
	 */
	public function testGetObjectAsJsonError() {
		$mockedClient = $this->getMock('PandoraAPIClient', array('call'), array( 'http://sds.fake.pl', '/api/v0.1/' ) );
		$mockedClient->expects($this->once())
			->method('call')
			->with( $this->equalTo( 'http://sds.fake.pl/api/v0.1/sdsdbmock/testid03' ) )
			->will( $this->returnValue( new PandoraResponse( Status::newFatal( '' ), 500, '{"a":"b"}' ) ) );
		$mockedClient->getObjectAsJson( $mockedClient->getObjectUrl( 'testid03' ) );
	}

	/**
	 * Test if the object url is translated to the correct entry point
	 */
	public function testGetObjectUrlFromId() {
		$apiClient = new PandoraAPIClient('http://sds.fake.pl', '/api/v0.1/');
		$this->assertEquals('http://sds.fake.pl/api/v0.1/video151/982734', $apiClient->getObjectUrlFromId( 'http://sds.wikia.com/video151/982734' ) );
	}

	/**
	 * Test if the request is made only once when getting the same object
	 */
	public function testGetObjectCache() {
		$mockedClient = $this->getMock('PandoraAPIClient', array('call'), array( 'http://sds.fake.pl', '/api/v0.1/' ) );
		$mockedClient->expects($this->once())
			->method('call')
			->with( $this->equalTo( 'http://sds.fake.pl/api/v0.1/sdsdbmock/testid04' ) )
			->will( $this->returnValue( new PandoraResponse( Status::newGood(), 200, '{"a":"b"}' ) ) );
		$this->assertEquals( $mockedClient->getObject( $mockedClient->getObjectUrl( 'testid04' ) ), $mockedClient->getObject( $mockedClient->getObjectUrl( 'testid04' ) ) );
	}

}
