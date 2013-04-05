<?php
/**
 * Created by adam
 * Date: 06.02.13
 */

class VideoClipMovieTrailersVideo extends VideoObject {

	const type = 'http://sds.wikia.com/vocabs/VideoClipMovieTrailersVideo';

	public static $config = array (
	    'movie' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType' => 'schema:Movie' ),
	    'trailerRating' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:contentRating' ),
	    'keywords' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:keywords' ),
	    'isFamilyFriendly' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:isFamilyFriendly' ),
	    'videoQuality' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:videoQuality' ),
	    'actors' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:contributor', 'childType' => 'schema:Person' ),
	    'characters' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:character', 'childType' => 'wikia:Character' ),
	    'additional_type' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:additionalType', 'value'=> 'http://sds.wikia.com/vocabs/VideoClipMovieTrailersVideo' ),
		'setting' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:setting' ),
	);


	protected function getMapArray( $mapType = 'main' ) {

		$map = array();
		$map['main'] = parent::getMapArray( 'main' );
		$map['main']['videoObject_name'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['main']['videoObject_description'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:description' );
		$map['main']['videoObject_datePublished'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:datePublished' );
		$map['main']['videoObject_inLanguage']= array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:inLanguage' );
		$map['main']['videoObject_subTitleLanguage'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:subTitleLanguage' );
		$map['main']['movie_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType' => 'movie_name' );
		$map['main']['videoObject_rating'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:contentRating' );
		$map['main']['videoObject_isFamilyFriendly'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:isFamilyFriendly' );
		$map['main']['videoObject_setting'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'wikia:setting' );
		$map['main']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:VideoObject' );
		$map['main']['additionalType'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:additionalType', 'value'=>static::type );

		$map['movie_name'] = array();
		$map['movie_name']['movie_name'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['movie_name']['movie_id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['movie_name']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:Movie' );

		return $map[ $mapType ];
	}

	public static function canHandle( PandoraSDSObject $data ) {

		$type = static::getSubjectType( $data );
		if ( in_array( 'schema:Movie', $type, true ) ) {
			return true;
		}
		return false;
	}
}