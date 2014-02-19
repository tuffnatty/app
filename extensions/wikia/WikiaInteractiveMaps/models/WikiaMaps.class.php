<?php
class WikiaMaps extends WikiaModel {

	public function getAllMaps( $master = false ) {
		if( wfReadOnly() ) {
			throw new Exception( 'DB in read-only mode' );
		}

		$db = $this->getDB( ( $master ? DB_MASTER : DB_SLAVE ) );
		$results = $db->select( 'page', [ 'page_id' ], [ 'page_namespace' => NS_WIKIA_MAP ], __METHOD__ );
		if( $db->numRows( $results ) > 0 ) {
			while( ( $map = $db->fetchObject( $results ) ) ) {
				$mapId = $map->page_id;
				$mapTitle = Title::newFromID( $mapId );
				if( !is_null( $mapTitle ) ) {
					$mapObj = new WikiaMap( $mapTitle );
					$mapParams = $mapObj->getMapsParameters();

					$mapParams->type = ( $mapParams->type === 2 ) ? 'Custom' : 'Real world';
					$mapParams->status = ( $mapParams->status === 1 ) ? 'Done' : 'In progress';

					$maps[] = $mapParams;
				}
			}
		} else {
			$maps = [];
		}

		return $maps;
	}

}
