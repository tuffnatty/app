<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adam
 * Date: 05.02.13
 * Time: 16:51
 * To change this template use File | Settings | File Templates.
 */

class VideoClipTVVideo extends VideoObject {

	const type = 'http://sds.wikia.com/vocabs/VideoClipTVVideo';

	public static $config = array (
	    'tvSeries' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType' => 'schema:TVSeries' ),
	    'tvSeason' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType' => 'schema:TVSeason' ),
	    'keywords' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:keywords' ),
	    'actors' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:contributor', 'childType' => 'schema:Person' ),
	    'character' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:character', 'childType' => 'wikia:Character' ),
	    'additional_type' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:additionalType', 'value'=> 'http://sds.wikia.com/vocabs/VideoClipTVVideo' ),
		'setting' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:setting' ),
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
		$map['main']['series_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType' =>'series_name' );
		$map['main']['season_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType'=>'season_name' );
		$map['main']['videoObject_keywords'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:keywords' );
		$map['main']['videoObject_setting'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:setting' );
		$map['main']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:VideoObject' );
		$map['main']['additionalType'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:additionalType', 'value'=>static::type );

		$map['series_name'] = array();
		$map['series_name']['series_name'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['series_name']['series_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['series_name']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:TVSeries' );

		$map['season_name'] = array();
		$map['season_name']['season_name'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['season_name']['season_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['season_name']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:TVSeason' );

		return $map[ $mapType ];
	}

	public static function canHandle( PandoraSDSObject $data ) {

		$type = static::getSubjectType( $data );
		if ( in_array( 'schema:TVSeries', $type, true ) ) {
			return true;
		}
		return false;

	}
}