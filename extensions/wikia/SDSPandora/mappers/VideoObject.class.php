<?php
/**
 * Created by adam
 * Date: 14.03.13
 */

class VideoObject extends PandoraORM {

	public static $config = array(
		'id' => array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' ),
		'name' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' ),
		'description' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:description' ),
		'duration' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:duration' ),
		'datePublished' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:datePublished' ),
		'thumbnailUrl' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:thumbnailUrl' ),
		'contentURL' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:contentURL' ),
		'inLanguage' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:inLanguage' ),
		'subTitleLanguage' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:subTitleLanguage' ),
		'videoQuality' => array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:videoQuality' ),
		'regionsAllowed' => array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:regionsAllowed' ),
	);
	//TODO: Aspect ratio
	//TODO: Flight Start date
	//TODO: flight end date
}