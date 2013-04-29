<?php
/**
 * Created by adam
 * Date: 27.03.13
 */

class PandoraFormsTest extends WikiaBaseTest {

	public function setUp() {
		$this->setupFile =  dirname(__FILE__) . '/../SDSPandora.setup.php';
		parent::setUp();
	}

	public function preparePandoraForms( $config = null, $data = null ) {
		$forms = new PandoraForms();
		//set config
		if ( $config !== null ) {
			$forms->setConfig( $config );
		}
		//set data
		if ( $data !== null ) {
			$forms->load( $data );
		}
		return $forms;
	}

	public function prepareOrm( $data ) {
		$this->setUp();
		//should return PandoraORM typed object
		PandoraORM::$config = array(
			'id' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' ),
			'name' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' ),
			'description' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:description' ),
			'type' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type' ),
			'about' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType'=>'schema:Movie'),
			'about_person' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType' => 'schema:Person' ),
			'about_literal' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about' )
		);
		$orm = PandoraORM::buildFromType( 'TestType' );
		foreach ( $data as $key => $value ) {
			$orm->set( $key, $value );
		}
		return $orm;
	}

	/**
	 * @dataProvider pandoraFormProvider
	 * @param $config
	 * @param $data
	 */
	public function testRender( $config, $data ){
		$formsHelper = $this->preparePandoraForms( $config, $data );

		foreach ( $config as $key => $params ) {
			$renderedField = $formsHelper->renderField( $key );
			print_r( $renderedField );
		}
	}

	/**
	 * @dataProvider ormDataProvider
	 * @param $orm
	 * @param $config
	 */
	public function testLoadFromORM( $orm, $config ) {
		$formsHelper = $this->preparePandoraForms( $config );
		$formsHelper->loadFromORM( $orm );

		print_r( $formsHelper->getData() );
	}

	/**
	 * @dataProvider requestDataProvider
	 * @param $data
	 * @param $config
	 */
	public function testGetOrmFromRequest( $data, $config ) {
		$formsHelper = $this->preparePandoraForms( $config );
		$orm = $formsHelper->getOrmFromRequest( $data, 'http://sds.wikia.com/sds/testObject' );
		print_r( $orm );
	}



//	public function testRenderAll() {
//
//	}

//	public function testParseRequest() {
//
//	}

	public function pandoraFormProvider() {
		return array(
			array(
				array(
					'videoObject_description' => array(
						'controller' => 'PandoraForms',
						'template' => 'default',
						'name' => 'videoObject_description',
						'textarea' => true,
						'label' => 'sdsvideometadata-vc-description'
					)
				),
				array(
					'videoObject_description' => 'mocked description'
				)
			),
			array(
				array(
					'videoObject_inLanguage' => array(
						'controller' => 'PandoraForms',
						'template' => 'default',
						'name' => 'videoObject_inLanguage',
						'label' => 'sdsvideometadata-vc-language'
					)
				),
				array(
					'videoObject_inLanguage' => 'en'
				)
			),
			array(
				array(
					'recipe_name' => array(
						'controller' => 'PandoraForms',
						'template' => 'reference_list',
						'type' => 'VideoClipCookingVideo',
						'name' => 'recipe_name',
						'label' => 'sdsvideometadata-vc-language'
					)
				),
				array(
					'recipe_name' => 'kluski'
				)
			),
			array(
				array(
					'videoObject_contentFormat' => array(
						'controller' => 'PandoraForms',
						'template' => 'select',
						'type' => 'VideoClipMusicVideo',
						'name' => 'videoObject_contentFormat',
						'label' => 'sdsvideometadata-vc-pal',
						'options' => array(
							array(
								'value' => '',
								'text' => 'sdsvideometadata-vc-boolean-not-set'
							),
							array(
								'value' => 'PAL',
								'text' => 'sdsvideometadata-vc-boolean-true'
							)
						)
					)
				),
				array(
					'videoObject_contentFormat' => 'PAL'
				)
			)
		);
	}

	public function ormDataProvider() {
		return array(
			array(
				//orm
				$this->prepareOrm( array(
					'name' => 'testName',
					'type' => 'schema:VideoObject',
					'description' => 'simple description',
					'about' => array(
						'id' => 'http://sds.wikia.com/sds/about',
						'name' => 'subitem movie',
						'description' => 'subitem movie description',
						'type' => 'schema:Movie'
					),
					'about_person' => array(
						'id' => 'http://sds.wikia.com/sds/about_person',
						'name' => 'subitem person',
						'description' => 'subitem person description',
						'type' => 'schema:Person'
					),
					'about_literal' => array(
						'literal1',
						'literal2'
					)
				) ),
				//config
				array(
					'videoObject_description' => array(
						'controller' => 'PandoraForms',
						'template' => 'default',
						'textarea' => true,
						'label' => 'sdsvideometadata-vc-description',
						'ormKey' => 'description'
					),
					'about_movie' => array(
						'controller' => 'PandoraForms',
						'template' => 'default',
						'textarea' => true,
						'label' => 'sdsvideometadata-vc-description',
						'ormKey' => 'about_literal',
//						'childKey' => 'description'
					)
				)
			)
		);
	}

	public function requestDataProvider() {
		return array(
			array(
				//data
				array(
					'videoObject_description' => 'some description',
					'about_movie' => array(
						array( 'name' => 'first element', 'id' => 'http://sds.wikia.com/sds/existingOne'),
						array( 'name' => 'second element', 'id' => '')
					)
				),
				//config
				array(
					'videoObject_description' => array(
						'controller' => 'PandoraForms',
						'template' => 'default',
						'textarea' => true,
						'label' => 'sdsvideometadata-vc-description',
						'ormKey' => 'description'
					),
					'about_movie' => array(
						'controller' => 'PandoraForms',
						'template' => 'default',
						'textarea' => true,
						'label' => 'sdsvideometadata-vc-description',
						'ormKey' => 'about',
//						'childKey' => 'description'
					)
				)
			)
		);
	}
}