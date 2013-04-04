<?php
/**
 * Created by adam
 * Date: 14.02.13
 */

class VideoClipMusicVideo extends VideoObject {

	const type = 'http://sds.wikia.com/vocabs/VideoClipMusicVideo';

	public static $config = array (
	    'musicRecording' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType' => 'schema:MusicRecording' ),
	    'musicGroup' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:contributor', 'childType' => 'schema:MusicGroup' ),
	    'organisation' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:contributor', 'childType' => 'schema:Organization' ),
		'setting' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:setting' ),
	    'genre' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:genre' ),
	    'celebrity' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:contributor', 'childType' => 'schema:Person' ),
	    'characters' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:character', 'childType' => 'wikia:Character' ),
	    'additional_type' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:additionalType', 'value'=> 'http://sds.wikia.com/vocabs/VideoClipMusicVideo' ),
	);
	//TODO: Setting


	protected function getMapArray( $mapType = 'main' ) {

		$map = array();
		$map['main'] = parent::getMapArray( 'main' );
		$map['main']['videoObject_name'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['main']['videoObject_description'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:description' );
		$map['main']['videoObject_datePublished'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:datePublished' );
		$map['main']['videoObject_inLanguage']= array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:inLanguage' );
		$map['main']['videoObject_subTitleLanguage'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:subTitleLanguage' );
		$map['main']['videoObject_setting'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:setting' );
		$map['main']['videoObject_contentFormat'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:encodingFormat' );
		$map['main']['track_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType'=>'schema_musicRecording' );
		$map['main']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:VideoObject' );
		$map['main']['musicGroup_name'] = array( 'type' => PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:contributor', 'childType'=>'schema_musicGroup' );
		$map['main']['musicRecording_musicLabel'] = array( 'type' => PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:contributor', 'childType'=>'schema_organization' );
		$map['main']['videoObject_genre'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:genre' );
		$map['main']['additionalType'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:additionalType', 'value'=>static::type );

		$map['schema_musicRecording'] = array();
		$map['schema_musicRecording']['track_name'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['schema_musicRecording']['track_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['schema_musicRecording']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:MusicRecording' );

		$map['schema_musicGroup'] = array();
		$map['schema_musicGroup']['musicGroup_name'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['schema_musicGroup']['musicGroup_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['schema_musicGroup']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:MusicGroup' );

		$map['schema_organization'] = array();
		$map['schema_organization']['musicRecording_musicLabel'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['schema_organization']['musicRecording_musicLabel_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['schema_organization']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:Organization' );

		return $map[ $mapType ];
	}

	public static function canHandle( PandoraSDSObject $data ) {

		$type = static::getSubjectType( $data );
		if ( in_array( 'schema:MusicRecording', $type, true ) ) {
			return true;
		}
		return false;
	}

}