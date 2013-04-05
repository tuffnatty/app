<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 */
class VideoClipGamingVideo extends VideoObject {

	const type = 'http://sds.wikia.com/vocabs/VideoClipGamingVideo';

	public static $config = array (
		'game' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType' => 'wikia:VideoGame' ),
		'additional_type' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:additionalType', 'value'=> 'http://sds.wikia.com/vocabs/VideoClipGamingVideo' ),
		'isFamilyFriendly' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:isFamilyFriendly' ),
		'keywords' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:keywords' ),
		'setting' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:setting' ),
		'type' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:VideoObject' ),
		'content_url' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:contentURL' ),
	    'soundtrack' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:encodesCreativeWork', 'childType' => 'schema:MusicRecording' ),
	    'characters' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:character', 'childType' => 'wikia:Character' ),
	);

	protected function getMapArray( $mapType = 'main' ) {

		$map = array();
		$map['main'] = parent::getMapArray( 'main' );
		$map['main']['videoObject_name'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['main']['videoObject_description'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:description' );
		$map['main']['videoObject_datePublished'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:datePublished' );
		$map['main']['videoObject_inLanguage']= array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:inLanguage' );
		$map['main']['videoObject_subTitleLanguage'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:subTitleLanguage' );
		$map['main']['about_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType' => 'about_name' );
		$map['main']['videoObject_isFamilyFriendly'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:isFamilyFriendly' );
		$map['main']['videoObject_associatedMedia'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:associatedMedia', 'childType' => 'schema:MediaObject' );
		$map['main']['videoObject_keywords'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:keywords' );
		$map['main']['videoObject_setting'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:setting' );
		$map['main']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:VideoObject' );
		$map['main']['additionalType'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:additionalType', 'value'=>static::type );

		$map['about_name'] = array();
		$map['about_name']['about_name'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['about_name']['about_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['about_name']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'wikia:VideoGame' );

		$map['schema:MediaObject'] = array();
		$map['schema:MediaObject']['videoObject_associatedMedia'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['schema:MediaObject']['videoObject_associatedMedia_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['schema:MediaObject']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:MediaObject' );

		return $map[ $mapType ];
	}

	public static function canHandle( PandoraSDSObject $data ) {

		$type = static::getSubjectType( $data );
		if ( in_array( 'wikia:VideoGame', $type, true ) ) {
			return true;
		}
		return false;
	}
}
