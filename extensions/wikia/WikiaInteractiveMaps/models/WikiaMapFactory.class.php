<?php
/**
 * Created by PhpStorm.
 * User: aquilax
 * Date: 2/20/14
 * Time: 11:00 AM
 */

class WikiaMapFactory {

	const MAP_TYPE_EARTH_OPEN_MAPS = 1;
	const MAP_TYPE_CUSTOM = 2;
	const MAP_TYPE_EARTH_GOOGLE_MAPS = 3;

	public static function build( Title $title) {
		switch ( self::getType( $title ) ) {
			case self::MAP_TYPE_EARTH_OPEN_MAPS:
				return new WikiaEarthOpenMap( $title, self::MAP_TYPE_EARTH_OPEN_MAPS );
			break;
			case self::MAP_TYPE_CUSTOM:
				return new WikiaCustomMap( $title, self::MAP_TYPE_CUSTOM );
			break;
		}
		// FIXME: in case of unknown map
		return null;
	}

	public static function getType( Title $title ) {
		$rev = self::getRevision( $title );
		if ( is_null( $rev ) ) {
			return null;
		}
		$text = $rev->getText();

		if( stripos( $text, '__MAP_TYPE_CUSTOM__' ) !== false ) {
			return self::MAP_TYPE_CUSTOM;
		} else {
			return self::MAP_TYPE_EARTH_OPEN_MAPS;
		}
	}

	public static function getRevision( Title $title ) {
		return Revision::newFromId( $title->getLatestRevID() );
	}
}