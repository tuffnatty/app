<?php
/**
 * Created by adam
 * Date: 14.02.13
 */

class VideoClipCookingVideo extends VideoObject {

	const type = 'http://sds.wikia.com/vocabs/VideoClipCookingVideo';

	public static $config = array (
	    'provider' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:provider', 'childType' => 'schema:Organization' ),
	    'publisher' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:publisher', 'childType' => 'schema:Organization' ),
	    'recipe' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType' => 'schema:Recipe' ),
	    'genre' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:genre' ),
	    'celebrity' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:contributor', 'childType' => 'schema:Person' ),
	    'additional_type' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:additionalType', 'value'=> 'http://sds.wikia.com/vocabs/VideoClipCookingVideo' ),
	);

	protected function getMapArray( $mapType = 'main' ) {

		$map = array();
		$map['main'] = parent::getMapArray( 'main' );
		$map['main']['videoObject_name'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['main']['videoObject_description'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:description' );
		$map['main']['provider_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:provider', 'childType'=>'schema_provider' );
		$map['main']['publisher_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:publisher', 'childType'=>'schema_organization' );
		$map['main']['videoObject_datePublished'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:datePublished' );
		$map['main']['videoObject_inLanguage']= array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:inLanguage' );
		$map['main']['videoObject_subTitleLanguage'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:subTitleLanguage' );
		$map['main']['videoObject_genre'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:genre' );
		$map['main']['recipe_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType'=>'schema_recipe' );
		$map['main']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:VideoObject' );
		$map['main']['additionalType'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:additionalType', 'value'=>static::type );

		$map['schema_provider'] = array();
		$map['schema_provider']['provider_name'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['schema_provider']['provider_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['schema_provider']['type'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'type', 'value'=>'schema:Organization' );

		$map['schema_organization'] = array();
		$map['schema_organization']['publisher_name'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['schema_organization']['publisher_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['schema_organization']['type'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'type', 'value'=>'schema:Organization' );

		$map['schema_recipe'] = array();
		$map['schema_recipe']['recipe_name'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['schema_recipe']['recipe_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['schema_recipe']['type'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'type', 'value'=>'schema:Recipe' );


		return $map [ $mapType ];
	}

	public static function canHandle( PandoraSDSObject $data ) {

		$type = static::getSubjectType( $data );
		if ( in_array( 'schema:Recipe', $type, true ) ) {
			return true;
		}
		return false;
	}
}