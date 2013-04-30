<?php
/**
 * User: artur
 * Date: 23.04.13
 * Time: 16:18
 */

class VideoInformationProvider {

	public function get( $title ) {
		$title = Title::newFromText( $title, NS_FILE );
		$file = wfFindFile($title);
		if( !$file ) return false;
		$meta = $file->getMetadata();
		$resultMeta = array();
		if( $meta ) {
			$meta= unserialize($meta);
			if ( isset($meta['keywords']) )
				$resultMeta['keywords'] = $meta['keywords'];
			if ( isset($meta['tags']) )
				$resultMeta['tags'] = $meta['tags'];
			if ( isset($meta['description']) )
				$resultMeta['description'] = $meta['description'];
			if ( isset($meta['category']) )
				$resultMeta['category'] = $meta['category'];
			if ( isset($meta['title']) )
				$resultMeta['title'] = $meta['title'];
			if ( isset($meta['actors']) ) {
				$resultMeta['actors'] = $meta['actors'];
			}
			if ( isset($meta['genres']) ) {
				$resultMeta['genres'] = $meta['genres'];
			}
			return new VideoInformation( $resultMeta );
		} else {
			return false;
		}
	}

	public function getExpanded( $title, $metadata=null ) {
		print_r( '<pre>' );

		if ( empty( $metadata ) ) {
			$metadata = $this->get( $title );
		}

		$keywords =  isset( $metadata[ 'keywords' ] ) ? explode( ',', $metadata[ 'keywords' ] ) : array();
		$tags =  isset( $metadata[ 'tags' ] ) ? explode( ',', $metadata[ 'tags' ] ) : array();

		//join keywords and tags
		$joinedKeywords = array_merge( $keywords, $tags );
		//trim all keywords, just in case
		foreach ( $joinedKeywords as $key => $word ) {
			$joinedKeywords[ $key ] = trim( $word );
		}
		//filter out common tags
		$dbr = wfGetDB( DB_SLAVE, array(), F::app()->wg->ExternalDatawareDB );
		$query = 'select * from vid_term_freq f, vid_term t where f.term_id = t.id order by count desc limit 100;';
		$dbResult = $dbr->query( $query, __METHOD__ );
		//get all forbidden words
		$exludeTerms = array();
		while ($row = $dbResult->fetchObject()) {
			$exludeTerms[] = $row->term;
		}

		//ask freebase for types and titles
		$fbClient = new FreebaseClient();
		$score = 120;
		$about = array();
		$matched = array();

		foreach ( $joinedKeywords as $tag ) {
//			$fbResult = $fbClient->queryWithTypeFilter( $tag, 'getAllTypes' );
			if ( !in_array( $tag, $exludeTerms ) ) {
				$fbResult = $fbClient->query( $tag );

				if ( isset( $fbResult->result ) ) {
					foreach ( $fbResult->result as $res ) {
						if ( isset( $res->notable ) ) {
							$type = $fbClient->getTypeMapping( $res->notable->id );
							if ( $type !== null && $res->score > $score ) {
								$about[ $tag ][] = array( 'title' => $res->name, 'type' => $type, 'score' => $res->score );
								continue;
							}
						}
						if ( trim( strtolower( $res->name ) ) === str_replace( '-', ' ', strtolower( $tag ) ) ) {
							$type = ( isset( $res->notable ) ) ? $res->notable->id : '';
							$matched[ $tag ][] = array( 'title' => $res->name, 'type' => $type, 'score' => $res->score );
						}
					}
				}
			} else {
				var_dump( 'Excluded: '.$tag );
			}
		}
		//refine about tags (those matching score and filtered by type)
		foreach( $about as $tag => $list) {
			$maxScore = array( 'score' => 0 );
			$exact = null;
			foreach( $list as $element ) {
				if ( trim( strtolower( $element[ 'title' ] ) ) === str_replace( '-', ' ', strtolower( $tag ) ) ) {
					$exact = $element;
					break;
				}
				$maxScore = ( $maxScore[ 'score' ] < $element[ 'score' ] ) ? $element : $maxScore;
			}
			$about[ $tag ] = ( $exact !== null ) ? $exact : $maxScore;
		}
		//refine matched tags (those matching score and filtered by type)
		foreach( $matched as $tag => $list) {
			$maxScore = array( 'score' => 0 );
			$exact = null;
			foreach( $list as $element ) {
				if ( trim( strtolower( $element[ 'title' ] ) ) === str_replace( '-', ' ', strtolower( $tag ) ) ) {
					$exact = $element;
					break;
				}
				$maxScore = ( $maxScore[ 'score' ] < $element[ 'score' ] ) ? $element : $maxScore;
			}
			$choosed = ( $exact !== null ) ? $exact : $maxScore;
			if  ( $choosed[ 'score' ] > $score ) {
				$matched[ $tag ] = $choosed;
			} else {
				unset( $matched[ $tag ] );
			}
		}
		var_dump( $about );
		var_dump( $matched );

		//czary mary dla titla - wiecej coli


		$metadata[ 'expanded' ] = array(
			'keywords' => array(), //odfiltrowane keywordy i tagi
			'about' => array(), //keyword name => array( type => game, title => game_title ) - freebase validation
		);

		return $metadata;

	}

}
