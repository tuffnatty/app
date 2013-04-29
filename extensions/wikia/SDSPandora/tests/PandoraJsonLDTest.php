<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adam
 * Date: 01.02.13
 * Time: 13:26
 * To change this template use File | Settings | File Templates.
 */

class PandoraJsonLDTest extends WikiaBaseTest {

	public function setUp() {
		$this->setupFile =  dirname(__FILE__) . '/../SDSPandora.setup.php';
		parent::setUp();
	}

	/**
	 * @dataProvider jsonStrDataProvider
	 * @param $jsonInput: encoded string for testing, also expected result
	 */
	public function  testPandoraSDSObjectFromJsonLD( $jsonInput, $jsonExpected ) {

		//escapes special chars in json
		$serializedInput = ( $jsonExpected !== null ) ?  json_encode( json_decode( $jsonExpected ) ) : json_encode( json_decode( $jsonInput ) );
		//create pandora class object
		$pandoraObject = PandoraJsonLD::pandoraSDSObjectFromJsonLD( $jsonInput );
		//deserialize into json
		$jsonOutput = PandoraJsonLD::toJsonLD( $pandoraObject );

		$this->assertEquals( $serializedInput, $jsonOutput );
	}

	/**
	 * @dataProvider jsonStrWrongDataProvider
	 * @param $jsonInput: encoded string for testing, should be malformed
	 * @expectedException WikiaException
	 */
	public function testPandoraInvalidJsonException( $jsonInput ) {
		$pandoraObject = PandoraJsonLD::pandoraSDSObjectFromJsonLD( $jsonInput );
	}

	public function jsonStrWrongDataProvider() {
		return array (
			array( '{ [] }' ),
			array( '{ "id" : \'cos ", "ops" : "injection", "id" : "tam\' }' ),
			array( '{ id : "0" }' ),
			array( 'a' )
		);
	}

	public function jsonStrDataProvider() {
		return array (
			array( '{}', null ),
			array( '{ "id" : {} }', '{}'),
			array( '{ "a" : [ "a", "b" ] }', null ),
			array( '{ "id" : "0" }', null ),
			array( '{ "id" : "0", "property" : { "id" : 1 } }', null ),
			array( '{ "property" : [], "property1" : [ "1", "2" ], "property3" : [ { "id" : "2", "schema:name" : "subject2" }, { "id" : "3"} ] }',
				'{ "property1" : [ "1", "2" ], "property3" : [ { "id" : "2", "schema:name" : "subject2" }, { "id" : "3"} ] }' ),
			array( '{ "id" : "1", "test" : [ { "id_in" : "1" }, { "id_in" : "2" } ] }', null ),
			array( '{ "id" : "1", "schema:about" : { "id" : "1", "schema:name" : "cos" }}', null )
		);
	}

}