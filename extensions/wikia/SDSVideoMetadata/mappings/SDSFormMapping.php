<?php
class SDSFormMapping {

	const type = null;

	protected $contextValues = array();
	protected $objects = array();

	public function setContextValues( $contextValues ) {
		$this->contextValues = $contextValues;
	}

	public function getContextValues() {
		return $this->contextValues;
	}

	protected static $mappings = array(

		'VideoClipTVVideo',
		'VideoClipGamingVideo',
		'VideoClipMovieTrailersVideo',
		'VideoClipCookingVideo',
		'VideoClipMusicVideo',
		'VideoClipTravelVideo',
		'VideoClipCraftVideo',
		'VideoClipHowToVideo'
	);

	public static function canHandle( PandoraSDSObject $object ) {
		return true;
	}

	protected function getMapArray( $mapType = 'main' ) {

		$contextValues = $this->getContextValues();

		$map = array();

		$map['main'] = array();
		$map['main']['videoObject_name'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );

		if ( isset( $contextValues['name'] ) ) {
			$map['main']['videoObject_name']['value'] = $contextValues['name'];
		}

		$map['main']['videoObject_description'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:description' );
		$map['main']['videoObject_datePublished'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:datePublished' );
		$map['main']['videoObject_inLanguage']= array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:inLanguage' );
		$map['main']['videoObject_subTitleLanguage'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:subTitleLanguage' );

		if ( isset( $contextValues['contentURL'] ) ) {
			$map['main']['contentUrl'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:contentURL', 'value' => $contextValues['contentURL'] );
		}

		return isset( $map[ $mapType ] ) ? $map[ $mapType ] : array();
	}

	protected function generateId() {
		//TODO: generate unique ID for new object
		$generatedId = "http://sds.wikia.com/sds/~" . base64_encode(microtime(true) . rand());
		return $generatedId;
	}

	protected function getLiteralValue( $subject, $fieldData, $element = 0, $value = null ) {
		$item = new PandoraSDSObject();
		$item->setType( PandoraSDSObject::TYPE_LITERAL );
		if ( $subject !== null) {
			$item->setSubject( $subject );
		}
		if ( $value !== null ) {
			$item->setValue( $value );
			return $item;
		}
		if ( is_array( $fieldData ) ) {
			$returnValue = ( isset( $fieldData[ $element ] ) ) ? $fieldData[ $element ] : null;
		} else {
			$returnValue = $fieldData;
		}
		if ( $subject === 'id' ) {
			if ( empty( $returnValue ) ) {
				$id = $this->generateId();
				$item->setValue( $id );
				return $item;
			}
		}
		$item->setValue( $returnValue );
		return $item;
	}

	protected function getCollectionValue( $params, $formData, $fieldName ) {
		$item = new PandoraSDSObject();
		$item->setType( PandoraSDSObject::TYPE_COLLECTION );
		if ( isset( $params[ 'subject' ] ) ) {
			$item->setSubject( $params[ 'subject' ] );
		}
		foreach ( $formData[ $fieldName ] as $i => $field ) {
			if ( isset( $params['childType'] ) ) {
				$subItem = $this->getObjectValue( $params, $formData, $i );
			} else {
				$subItem = $this->getLiteralValue( null, $field );
			}
			$item->setValue( $subItem );
		}
		return $item;
	}

	protected function getObjectValue( $params, $fieldData, $element = 0 ) {
		$subItem = new PandoraSDSObject();
		$subItem->setType( PandoraSDSObject::TYPE_OBJECT );
		$childMap = $this->getMapArray( $params['childType'] );
//		$subItemType = count( $childMap ) > 1 ? PandoraSDSObject::TYPE_COLLECTION : PandoraSDSObject::TYPE_OBJECT;
		foreach ( $childMap as $childMapKey => $childMapValue ) {
			$mapperValue = (isset( $childMapValue[ 'value' ] ) ) ? $childMapValue[ 'value' ] : null;
			$formItemData = isset( $fieldData[ $childMapKey ] ) ? $fieldData[ $childMapKey ] : '';
			//find if id is there
			if ( $childMapValue[ 'subject' ] === 'id' ) {
				//and has value
				if ( is_array( $formItemData ) ) {
					$value = ( isset( $formItemData[ $element ] ) ) ? $formItemData[ $element ] : null;
				} else {
					$value = $formItemData;
				}
				if ( !empty( $value ) ) {
					//reset item
					$subItem->setType( PandoraSDSObject::TYPE_OBJECT );
					$subItem->setValue( $this->getItem( $childMapValue, array( $childMapKey => $formItemData  ), $childMapKey, $element, $subItem ), $mapperValue );
					//stop processing this node
					break;
				}
			}
			$subItem->setValue( $this->getItem( $childMapValue, array( $childMapKey => $formItemData  ), $childMapKey, $element, $subItem ), $mapperValue );
		}
		$this->objects[ $params[ 'childType' ] ][] = $subItem;
		return $subItem;
	}

	protected function getItem( $params, $formData, $fieldName, $element = 0, $parent ) {
			if ( $params['type'] === PandoraSDSObject::TYPE_LITERAL ) {
				$mapperValue = (isset( $params[ 'value' ] ) ) ? $params[ 'value' ] : null;
				$item = $this->getLiteralValue( $params[ 'subject' ], $formData[ $fieldName ], $element, $mapperValue );
			}
			elseif ( $params['type'] === PandoraSDSObject::TYPE_COLLECTION ) {
				$item = $this->getCollectionValue( $params, $formData, $fieldName );
				//check for parent item looking for subject, if exist continue on adding items to previous one
				$parentItem = $parent->getItem( $params[ 'subject' ] );
				if ( $parentItem !== null ) {
					$parentItem->setValue( $item->getValue() );
					//return empty string, so the item is not added again
					return '';
				}
			}
			return $item;
	}

	public function newPandoraSDSObjectFromFormData( $formData, $mapName = 'main', &$objects = false) {
		//reset objects array
		$this->objects = array();

		$this->sanitizeFormData( $formData );
		$map = $this->getMapArray( $mapName );

		$root = new PandoraSDSObject();

		$titleObj = Title::newFromText( $formData['video'], NS_FILE );
		$articleId = (int) $titleObj->getArticleID();

		if ( $articleId == 0 ) {
			throw new Exception('Unknown video');
		}

		$itemIdObject = new PandoraSDSObject();
		$itemIdObject->setType( PandoraSDSObject::TYPE_LITERAL );
		$itemIdObject->setSubject('id');

		//TODO: move to external class
		$itemIdObject->setValue( 'http://sds.wikia.com/video151/'.$articleId );
		$root->setValue( $itemIdObject );

		foreach ( $map as $fieldName => $params ) {

			if ( isset( $params[ 'value' ] ) ) {
				$formData[ $fieldName ] = $params[ 'value' ];
			}

			if ( empty( $formData[ $fieldName ] ) ||
				( is_array( $formData[ $fieldName ] ) && count($formData[ $fieldName ] )==1 && $formData[ $fieldName ][0]=="" ) ) {
					continue;
			}

			$item = $this->getItem( $params, $formData, $fieldName, 0, $root );

			$root->setValue( $item );
		}
		//set objects array if needed
		if ( $objects !== false ) {
			$objects = $this->objects;
		}
		return $root;
	}

	public function sanitizeFormData( &$formData ) {
		foreach ( $formData as $key => $field ) {
			if ( is_array( $field ) && !empty( $field ) ) {
				$this->sanitizeFormData( $formData[ $key ] );
				if ( empty ( $formData[ $key ] ) ) {
					unset ( $formData[ $key ] );
				}
			} else {
				if ( empty( $field ) ) {
					unset ( $formData[ $key ] );
				}
			}
		}
	}

	public function toFormDataFromPandoraSDSObject( PandoraSDSObject $data, $map = 'main' ) {
		$result = array();
		$map = $this->getMapArray( $map );

		foreach( $map as $mapField => $params ) {
			if ( $params[ 'type' ] === PandoraSDSObject::TYPE_LITERAL ) {
				//if is array (incosistent with server) then get value of the first element
				$value = $data->getValue( $params[ 'subject' ] );
				if ( is_array( $value ) ) {
					$parsedValue = reset( $value );
					$value = $parsedValue->getValue();
				}
				$result[ $mapField ] = $value;
			} elseif ( $params[ 'type' ] === PandoraSDSObject::TYPE_COLLECTION ) {
				if ( isset( $params[ 'childType' ] ) ) {
					//object
					$childData =  $data->getItem( $params[ 'subject' ] );
					if ( $childData instanceof PandoraSDSObject ) {
						if ( $childData->getType() === PandoraSDSObject::TYPE_COLLECTION ) {
							foreach ( $childData->getValue() as $sub ) {
								$this->appendFormData( $result, $mapField, $sub, $params );
							}
						} elseif ( $childData->getType() === PandoraSDSObject::TYPE_OBJECT ) {
							$this->appendFormData( $result, $mapField, $childData, $params );
						}
					}
				} else {
					$stringCollection = $data->getValue( $params[ 'subject' ] );
					if ( !empty( $stringCollection ) ) {
						if ( is_array( $stringCollection ) ) {
							foreach ( $stringCollection as $item ) {
								$result[ $mapField ][] = $item->getValue();
							}
						} else {
							$result[ $mapField ] = array( $stringCollection );
						}
					}
				}
			}
		}
		return $result;
	}

	protected function appendFormData ( &$result, $formField, $data, $formFieldParams ) {
		//load mapping array for this child type
		$childMap = $this->getMapArray( $formFieldParams[ 'childType' ] );
		//and get params for search field
		$childParams = $childMap[ $formField ];

		//lazy loading for references
		$childData = $data->getValue();
		//check for type if set add only correct
		if ( isset( $childMap[ 'type' ] ) && isset( $childMap[ 'type' ][ 'value' ] ) ) {
			if ( $childMap[ 'type' ][ 'value' ] === $data->getValue( 'type' ) ) {
				$result[ $formField ][] = array( 'name' => $data->getValue( $childParams[ 'subject' ] ), 'id' => $data->getValue( 'id' ) );
			}
		} else {
			//if type not set just add data
			//get "name" and "id" for $formField
			$result[ $formField ][] = array( 'name' => $data->getValue( $childParams[ 'subject' ] ), 'id' => $data->getValue( 'id' ) );
		}

		//check childs for deeper connections
		foreach( $childMap as $childMapField => $childMapParams ) {
			if ( isset( $childMapParams[ 'childType' ] ) ) {
				$subChildData = $data->getItem( $childMapParams[ 'subject' ] );
				if ( $subChildData->getType() === PandoraSDSObject::TYPE_COLLECTION ) {
					foreach ( $subChildData->getValue() as $sub ) {
						$this->appendFormData( $result, $childMapField, $sub, $childMapParams );
					}
				} elseif ( $subChildData->getType() === PandoraSDSObject::TYPE_OBJECT ) {
					$this->appendFormData( $result, $childMapField, $subChildData, $childMapParams );
				}
			}
		}
	}

	public static function newFormDataFromPandoraSDSObject( PandoraSDSObject $object, $contextValues=null ) {

		$additionalType = $object->getValue( 'schema:additionalType' );

		$mappings = static::$mappings;
		//add this class as default fallback
		$mappings[] = get_class();
		if ( $additionalType !== null ) {
			foreach ( $mappings as $mappingHandler ) {
				if ( $additionalType === $mappingHandler::type ) {
					$handler = new $mappingHandler();
					break;
				}
			}
		}

		if ( !isset( $handler ) ) {
			foreach ( $mappings as $mappingHandler ) { /* @var $mappingHandler SDSFormMapping */

				if ( $mappingHandler::canHandle( $object ) ) {
					$handler = new $mappingHandler();
					break;
				}
			}
		}

		if ( isset( $handler ) ) {
			if ( $contextValues !== null ) {
				$handler->setContextValues( $contextValues );
			}

			$result = $handler->toFormDataFromPandoraSDSObject( $object );
			$result[ 'vcType' ] = $mappingHandler;
			return $result;
		}
	}

	public static function getSubjectType( PandoraSDSObject $data, $subject = 'schema:about' ) {
		$about = $data->getItem( $subject );
		//lazy load
		if ( $about !== null ) {
			$about->getValue();
			if ( $about->getType() === PandoraSDSObject::TYPE_COLLECTION ) {
				foreach ( $about->getValue() as $aboutItem ) {
					$type[] = $aboutItem->getValue( 'type' );
				}
				return array_unique( $type );
			} else {
				return array( $about->getValue( 'type' ) );
			}
		}
		return array();
	}
}
