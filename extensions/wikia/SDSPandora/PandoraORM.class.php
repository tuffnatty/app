<?php
/**
 * Created by adam
 * Date: 14.03.13
 */

class PandoraORM {

	/**
	 * @var config static field, storing data for actual ORM type, used in saving and loading data
	 * to and from database
	 */
	public static $config = array(
		//thing mapping
		'id' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' ),
		'type' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value' => 'schema:Thing' ),
		'additional_type' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'schema:additionalType' ),
		'description' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'schema:description' ),
		'image' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'schema:image' ),
		'name' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'schema:name' ),
		'url' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'schema:url' )
	);
	public $name;
	public $type;
	public $exist = null;

	protected $objects = array();
	protected $root;
	protected $id;

	/**
	 * Generates new PandoraORM object, uses schema:Thing config
	 * @param $id new or existing id
	 * @param null $type default type used when saving to db
	 */
	public function __construct( $id, $type = null ) {
		if ( $type !== null ) {
			$this->type = $type;
		}
		$this->id = $id;
		$root = new PandoraSDSObject();
		$root->setType( PandoraSDSObject::TYPE_OBJECT );
		$this->root = $root;
	}

	/**
	 * Builds object of PandoraORM or child class using type as a class name
	 * @param $type can be class name or prefix:type, where type will be tratded as class name
	 * @param null $id if not supplied will be generated using Pandora method
	 * @return PandoraORM type class as default, or object of type class
	 */
	public static function buildFromType( $type, $id = null ) {
		if ( $id === null ) {
			//generate new id
			$id = Pandora::generateCommonObjectId();
		}
		if ( strpos( $type, ':' ) !== false ) {
			$typeParts = explode( ':', strtolower( $type ) );
			$prefixClassName = ucfirst( $typeParts[ 0 ] ) . ucfirst( $typeParts[ 1 ] );
			$nonPrefixClassName = ucfirst( $typeParts[ 1 ] );
			if ( class_exists( $prefixClassName ) ) {
				return new $prefixClassName( $id );
			} elseif ( class_exists( $nonPrefixClassName ) ) {
				return new $nonPrefixClassName( $id );
			}
			return new PandoraORM( $id, $type );
		} else {
			if ( class_exists( $type ) ) {
				return new $type( $id );
			}
			//use default class with Thing mapping
			return new PandoraORM( $id );
		}
	}

	/**
	 * Method loads object from database using id and API call, try to generate ORM using the type of object
	 * @param $id
	 * @return PandoraORM as default, if object found in db, the class is build upon information in object
	 */
	public static function buildFromId ( $id ) {
		$pandoraApi = new PandoraAPIClient();
		$serverUri = $pandoraApi->getObjectUrlFromId( $id );
		$result = $pandoraApi->getObject( $serverUri );
		if ( $result->isOK() ) {
			$obj = PandoraJsonLD::pandoraSDSObjectFromJsonLD( $result->response, $id );
			$orm = static::buildFromType( $obj->getValue( 'type' ) , $id, false );
			$orm->setRoot( $obj );
			$orm->exist = true;
			return $orm;
		} else {
			$orm = new PandoraORM( $id );
			$orm->exist = false;
			return $orm;
		}
	}

	/**
	 * Generates ORM based on value from field based on config[key][subject]
	 * @param $id
	 * @param $key config key value
	 * @param $default if given field doesnt exist use this type
	 * @return PandoraORM or other type based on found value
	 */
	public static function buildFromField ( $id, $key, $default = null ) {
		$pandoraApi = new PandoraAPIClient();
		$serverUri = $pandoraApi->getObjectUrlFromId( $id );
		$result = $pandoraApi->getObject( $serverUri );
		if ( $result->isOK() ) {
			$obj = PandoraJsonLD::pandoraSDSObjectFromJsonLD( $result->response, $id );
			$type = pathinfo( $obj->getValue( $key ), PATHINFO_BASENAME );
			if ( $type !== null && !empty( $type ) ) {
				$orm = static::buildFromType( $type, $id );
			} elseif ( $default !== null ) {
				//use default
				$orm = static::buildFromType( $default, $id );
			}
			$orm->setRoot( $obj );
			$orm->exist = true;
			return $orm;
		} else {
			$orm = new PandoraORM( $id );
			$orm->exist = false;
			return $orm;
		}
	}

	public static function buildFromConfig( $config, $id = null ) {
		return new PandoraORM( $id, $config );
	}

	/**
	 * Root is a main PandoraStructure object in which all data is stored, by using this function this
	 * data can be changed entairly
	 * @param PandoraSDSObject $root
	 */
	public function setRoot( PandoraSDSObject $root ) {
		$this->root = $root;
	}
	public function getRoot() {
		return $this->root;
	}

	public function getId() {
		return $this->id;
	}

	/**
	 * @return array|config contains config for actual class and all parents
	 */
	public static function getConfig() {
		//loaded class config as default
		$result = static::$config;
		$parents = class_parents( get_called_class() );
		if ( $parents ) {
			while ( $parent = array_pop( $parents ) ) { /** @var $parent PandoraORM */
				$result = array_merge( $parent::$config, $result );
			}
		}
		return $result;
	}

	/**
	 * Saves data to database using Root as a data source and config for mapping,
	 * also saves the subobjects but only for not existing previously ( this is done to
	 * maintain correct references to sub objects )
	 * @param null $collection
	 * @return bool|PandoraResponse
	 */
	public function save( $collection = null ) {
		if ( !$this->checkRequiredFields() ) {
			return false;
		}
		$pandoraApi = new PandoraAPIClient();
		$json = PandoraJsonLD::toJsonLD( $this->root );
		//save children first if not existing, else do not change
		foreach ( $this->objects as $obj ) {
			if ( $obj->exist !== true ) {
				$obj->save( Pandora::getConfig( 'common_object_collection_name' ) );
			}
		}
		if ( $this->exist === null ) {
			$this->load();
		}
		if ( $this->exist ) {
			$res =  $pandoraApi->saveObject( $pandoraApi->getObjectUrlFromId( $this->id ), $json );
		} else {
			$urlForCollection = $pandoraApi->getCollectionUrl( $collection );
			$res = $pandoraApi->createObject( $urlForCollection, $json );
		}
		return $res;
	}

	public function load() {
		$pandoraApi = new PandoraAPIClient();
		$serverUri = $pandoraApi->getObjectUrlFromId( $this->id );
		$obj = $pandoraApi->getObject( $serverUri );
		if ( $obj->isOK() ) {
			$this->exist = true;
			$this->root = PandoraJsonLD::pandoraSDSObjectFromJsonLD( $obj->response, $this->id );
			return true;
		}
		$this->exist = false;
		return false;
	}

	/**
	 * Creates a PandoraSDSObject structure containg single object with id property in it,
	 * used to create reference object in parent object
	 * @return PandoraSDSObject
	 */
	public function getReference() {
		$node = new PandoraSDSObject();
		$node->setType( PandoraSDSObject::TYPE_LITERAL );
		$node->setSubject( 'id' );
		$node->setValue( $this->id );

		$obj = new PandoraSDSObject();
		$obj->setType( PandoraSDSObject::TYPE_OBJECT );
		$obj->setValue( $node );

		return $obj;
	}

	/**
	 * Set value for given config[key], data is inserted into root object
	 * @param $key
	 * @param $value
	 * @return bool
	 */
	public function set( $key, $value = null ) {
		if ( isset( $this->getConfig()[ $key ] ) ) {
			if ( isset( $this->getConfig()[ $key ][ 'value' ] ) && $value === null ) {
				//set default value if provided and value is null
				$value = $this->getConfig()[ $key ][ 'value' ];
			}
			$existing = $this->root->getItem( $this->getConfig()[ $key ][ 'subject' ] );
			if ( !isset( $this->getConfig()[ $key ][ 'childType' ] ) ) {
				if ( $this->getConfig()[ $key ][ 'type' ] === PandoraSDSObject::TYPE_COLLECTION ) {
					$node = $this->buildCollectionNode( $this->getConfig()[ $key ][ 'subject' ], $value );
				} else {
					$node = $this->buildLiteralNode( $this->getConfig()[ $key ][ 'subject' ], $value );
				}
				if ( $existing instanceof PandoraSDSObject ) {
					$existing->setValue( $node->getValue() );
				} else {
					$this->root->setValue( $node );
				}
				return true;
			} else {
				$orm = $this->buildObjectORM( $this->getConfig()[ $key ][ 'childType' ], $value );
				//add if exists
				if ( $existing instanceof PandoraSDSObject ) {
					//change this so it adds only literal with id
					if ( $existing->getType() === PandoraSDSObject::TYPE_OBJECT ) {
						$node = new PandoraSDSObject( PandoraSDSObject::TYPE_OBJECT, null, $existing->getItem( 'id' ) );
						//set type will clear already added values
						$existing->setType( PandoraSDSObject::TYPE_COLLECTION );
						$existing->setValue( $node );
					}
					$existing->setValue( $orm->getReference() );
				} else {
					$node = new PandoraSDSObject( $this->getConfig()[ $key ][ 'type' ], $this->getConfig()[ $key ][ 'subject' ], $orm->getReference() );
					$this->root->setValue( $node );
				}
				$this->objects[ $orm->getId() ] = $orm;
				return true;
			}
		}
		return false;
	}

	protected function buildCollectionNode( $subject, $values ) {
		$node = new PandoraSDSObject( PandoraSDSObject::TYPE_COLLECTION, $subject );
		if ( is_array( $values ) ) {
			foreach ( $values as $value ) {
				$collectionNode = new PandoraSDSObject( PandoraSDSObject::TYPE_LITERAL, null, $value );
				$node->setValue( $collectionNode );
			}
		} else {
			$collectionNode = new PandoraSDSObject( PandoraSDSObject::TYPE_LITERAL, null, $values );
			$node->setValue( $collectionNode );
		}
		return $node;
	}

	protected function buildLiteralNode( $subject, $value ) {
		$node = new PandoraSDSObject( PandoraSDSObject::TYPE_LITERAL, $subject );
		if ( is_array( $value ) ) {
			$node->setValue( reset( $value ) );
		} else {
			$node->setValue( $value );
		}
		return $node;
	}

	protected function buildObjectORM( $type, $value ) {
		//reference to another object
		if ( $value instanceof PandoraORM ) {
			$orm = $value;
		} else {
			//create new orm
			//TODO: get id from $value
			if ( !isset( $value[ 'name' ] ) && !isset( $value[ 'id' ] ) ) {
				throw new WikiaException( 'Value must be supplied as array( "name" => ..., "id" => ...) or instance of PandoraORM.' );
			}
			if ( isset( $value[ 'id' ] ) && !empty( $value[ 'id' ] ) ) {
				//object already exists
				$orm = static::buildFromType( $type, $value[ 'id' ] );
				$orm->exist = true;
			} else {
				//create new one
				$orm = static::buildFromType( $type );
				$orm->name = $value[ 'name' ];
			}
			//insert data from value passed as 'config_key' => 'value to set'
			foreach ( $value as $key => $val ) {
				if ( $key !== 'id' ) {
					$orm->set( $key, $val );
				}
			}
		}
		//set type for database
		return $orm;
	}

	public function get( $key ) {
		if ( isset( $this->getConfig()[ $key ] ) ) {
			$item = $this->root->getItem( $this->getConfig()[ $key ][ 'subject' ] );
			if ( $item instanceof PandoraSDSObject ) {
				if ( !isset( $this->getConfig()[ $key ][ 'childType' ] ) ) {
					//litarals and collections
					$value = $item->getValue();
					if ( is_array( $value ) ) {
						foreach ( $value as $val ) {
							if ( $val->getType() === PandoraSDSObject::TYPE_LITERAL ) {
								$result[] = $val->getValue();
							}
						}
						return $result;
					} else {
						return $value;
					}
				} else {
					//objects and object collections
					if ( $item->getType() === PandoraSDSObject::TYPE_COLLECTION ) {
						$result = array();
						foreach ( $item->getValue() as $object ) {
							$id = $object->getValue( 'id', false );
							$obj = $this->getObject( $id );
							if ( $obj !== null ) {
								$result[] = $obj;
							}
						}
						return $result;
					} else {
						$id = $item->getValue( 'id', false );
						$obj = $this->getObject( $id );
						if ( $obj !== null ) {
							return $obj;
						}
					}
				}
			}
		}
		return null;
	}

	public function getObject( $id ) {
		if ( $id !== null ) {
			if ( !isset( $this->objects[ $id ] ) ) {
				//try lazy loading
				$this->objects[ $id ] = static::buildFromId( $id );
			}
			return $this->objects[ $id ];
		}
		return null;
	}

	protected function checkRequiredFields() {
		if ( $this->root->getItem( 'id' ) === null ) {
			if ( !isset( $this->id ) ) {
				throw new WikiaException( 'ID must be set before saving');
//				return false;
			}
			$this->set( 'id', $this->id );
		}
		if ( $this->root->getItem( 'type' ) === null ) {
			if ( !isset( $this->type ) && isset( $this->getConfig()[ 'type' ] ) ) {
				$this->type = $this->getConfig()[ 'type' ][ 'value' ];
			}
			if ( !isset( $this->type ) ) {
				throw new WikiaException( 'Type must be set before saving');
//				return false;
			}
			$this->set( 'type', $this->type );
		}
		if ( $this->root->getItem( 'schema:name' ) === null ) {
			if ( !isset( $this->name ) ) {
				throw new WikiaException( 'Name must be set before saving');
//				return false;
			}
			$this->set( 'name', $this->name );
		}
		return true;
	}

}