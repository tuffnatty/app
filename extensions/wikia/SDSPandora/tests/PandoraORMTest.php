<?php

class PandoraORMTest extends WikiaBaseTest {

	public function setUp() {
		$this->setupFile =  dirname(__FILE__) . '/../SDSPandora.setup.php';
		parent::setUp();
	}

	/**
	 * @dataProvider typeProvider
	 * @param $type ORM type creating
	 */
	public function testBuildFromType( $type, $expectedType ) {
		$orm = PandoraORM::buildFromType( $type );
		$this->assertEquals( $expectedType, get_class( $orm ) );
	}

	/**
	 * @dataProvider setProvider
	 * @param $key
	 * @param $value
	 */
	public function testSet( $key, $value, $setReturn, $expectedValue ) {
		$orm = $this->getOrm();

//		$orm = PandoraORM::buildFromType( 'MapperTest' );
		$this->assertEquals( $setReturn, $orm->set( $key, $value ) );

		$pandoraObj = $orm->getRoot();
		$json = PandoraJsonLD::toJsonLD( $pandoraObj );
		$this->assertEquals( json_encode( json_decode( $expectedValue ) ) , json_encode( json_decode( $json ) ) );
	}

	/**
	 * @dataProvider setProviderExisting
	 * @param $key
	 * @param $value
	 */
	public function testSetWithExisting( $key, $value, $setReturn, $expectedValue ) {
		$orm = $this->getOrm();
		//fill with mockup data
		$orm->set( 'name', 'mock' );
		$orm->set( 'collection', array( 'one', 'two', 'three' ) );
		$orm->set( 'child', array( 'id' => 'http://sds.fake.wikia.com/fake_sub' ) );

//		$orm = PandoraORM::buildFromType( 'MapperTest' );
		$this->assertEquals( $setReturn, $orm->set( $key, $value ) );

		$pandoraObj = $orm->getRoot();
		$json = PandoraJsonLD::toJsonLD( $pandoraObj );
		$this->assertEquals( json_encode( json_decode( $expectedValue ) ) , json_encode( json_decode( $json ) ) );
	}

	/**
	 * @dataProvider setWrongDataProvider
	 * @expectedException WikiaException
	 */
	public function testSetError( $key, $value ) {
		$orm = $this->getOrm();

		$orm = PandoraORM::buildFromType( 'MapperTest' );
		$orm->set( $key, $value );
	}

	/**
	 * @dataProvider getProvider
	 * @param $key
	 */
	public function testGet ( $key, $expected ) {
		$orm = $this->getOrm();

		//set simple data
		$orm->set( 'name', 'name data' );
		$orm->set( 'id', 'http://sds.fake.wikia.com/fake_parent_id' );
		$orm->set( 'child', array( 'id' => 'http://sds.fake.wikia.com/fake_sub' ) );
		$orm->set( 'child', array( 'id' => 'http://sds.fake.wikia.com/fake_sub_2' ) );
		$orm->set( 'collection', array( 'one', 'two', 'three' ) );

		$this->assertEquals( $expected, $orm->get( $key ) );
	}

	public function getOrm() {
		$orm = new PandoraORM( 'http://sds.fake.wikia.com/fake_parent_id' );
		$orm::$config = array (
			'id' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' ),
			'name' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'name' ),
			'child' => array( 'type' => PandoraSDSObject::TYPE_COLLECTION, 'subject' => 'sub', 'childType' => 'Test' ),
			'collection' => array( 'type' => PandoraSDSObject::TYPE_COLLECTION, 'subject' => 'col' )
		);
		return $orm;
	}

	public function typeProvider() {
		return array(
			array( 'VideoObject', 'VideoObject' ),
			array( 'NoneExistingObject', 'PandoraORM' )
		);
	}

	public function setWrongDataProvider() {
		return array(
			array( 'child', 'child name' )
		);
	}

	public function setProvider() {
		$this->setUp();
		$child = new PandoraORM( 'http://sds.fake.wikia.com/fake_id' );
		return array(
			array( 'notExistingKey', '', false, '{}' ),
			array( 'name', 'single name', true, '{"name":"single name"}' ),
			array( 'name', array( 'single name in array' ), true, '{"name":"single name in array"}' ),
			array( 'collection', 'single name for col', true, '{"col":["single name for col"]}' ),
			array( 'collection', array( 'name for col in array' ), true, '{"col":["name for col in array"]}' ),
			array( 'collection', array( 'name for col in array', 'second name' ), true, '{"col":["name for col in array","second name"]}' ),
			array( 'child', array( 'name' => 'child name in array', 'id' => 'http://sds.fake.wikia.com/id' ), true, '{"sub":[{"id":"http://sds.fake.wikia.com/id"}]}' ),
			array( 'child', $child, true, '{"sub":[{"id":"http://sds.fake.wikia.com/fake_id"}]}')
//			array( 'child', array( 'name' => 'child name in array'), true, '{"sub":[{"id":"http://sds.fake.wikia.com/fake_id"}]}')
		);
	}

	public function setProviderExisting() {
		$this->setUp();
		return array(
			array( 'name', 'changed', true, '{"name":"changed","col":["one","two","three"],"sub":[{"id":"http:\/\/sds.fake.wikia.com\/fake_sub"}]}' ),
			array( 'name', array( 'changed1', 'changed2' ), true, '{"name":"changed1","col":["one","two","three"],"sub":[{"id":"http:\/\/sds.fake.wikia.com\/fake_sub"}]}' ),
			array( 'collection', 'four', true, '{"name":"mock","col":["one","two","three","four"],"sub":[{"id":"http:\/\/sds.fake.wikia.com\/fake_sub"}]}' ),
			array( 'collection', array( 'four', 'five' ), true, '{"name":"mock","col":["one","two","three","four","five"],"sub":[{"id":"http:\/\/sds.fake.wikia.com\/fake_sub"}]}' )
		);
	}

	public function getProvider() {
		$this->setUp();
		$orm = new PandoraORM( 'http://sds.fake.wikia.com/fake_sub' );
		$orm->exist = true;
		$orm2 = new PandoraORM( 'http://sds.fake.wikia.com/fake_sub_2' );
		$orm2->exist = true;
		return array(
			array( 'name', 'name data' ),
			array( 'id', 'http://sds.fake.wikia.com/fake_parent_id' ),
			array( 'child', array( $orm, $orm2 ) ),
			array( 'collection', array( 'one', 'two', 'three' ) )
		);
	}

}
