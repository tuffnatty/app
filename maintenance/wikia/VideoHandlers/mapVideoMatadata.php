<?php

/**
* Maintenance script to map video metadata by provider
* This is one time use script
* @author Saipetch Kongkatong
*/

/**
 * map metadata
 * @global int $skip
 * @global array $categories
 * @global array $extraMapping
 * @param string $videoTitle
 * @param VideoFeedIngester $ingester
 * @param array $data
 * @return array|false $metadata
 */
function mapMetadata( $videoTitle, $ingester, $data ) {
	global $skip, $categories, $extraMapping;

	// default page categories
	if ( empty( $data['pageCategories'] ) ) {
		$pageCategories = array();
	} else {
		$pageCategories = array_map( 'trim', explode( ',', $data['pageCategories'] ) );
		$pageCategories = $ingester->getUniqueArray( $pageCategories );
	}

	// get name
	if ( empty( $data['name'] ) && !empty( $data['keywords'] ) ) {
		$keywords = array();
		foreach ( explode( ',' , $data['keywords'] ) as $keyword ) {
			$keyword = trim( $keyword );
			if ( empty( $keyword ) ) {
				continue;
			}

			$keywords[] = $keyword;

			// remove page categories from keywords (for ooyala, iva)
			if ( !empty( $data['series'] ) ) {
				$categories[] = $data['series'];
			}
			if ( !empty( $data['provider'] ) ) {
				$categories[] = $data['provider'];
			}

			foreach ( $categories as $category) {
				if ( strcasecmp( $keyword, $category ) == 0 ) {
					array_pop( $keywords );
					if ( !in_array( $category, $pageCategories ) ) {
						$pageCategories[] = $category;
					}
					break;
				}
			}
		}

		// use series if keywords is empty
		if ( empty( $keywords ) && !empty( $data['series'] ) ) {
			$keywords[] = $data['series'];
		}


		if ( count( $keywords ) == 1 ) {
			$data['name'] = array_pop( $keywords );
		} else {
			$skip++;
			echo "$videoTitle...SKIPPED. (cannot map keywords to name (Keywords: $data[keywords])).\n";
			return false;
		}
	}

	// get page categories
	if ( !empty( $pageCategories ) ) {
		$data['pageCategories'] = implode( ', ', $ingester->getUniqueArray( $pageCategories ) );
	}

	// get keywords
	if ( !empty( $data['tags'] ) ) {
		if ( !empty( $keywords ) ) {
			$tags = array_map( 'trim', explode( ',', $data['tags'] ) );
			$keywords = array_merge( $keywords, $tags );
			$data['keywords'] = implode( ', ', $keywords );
		} else {
			$data['keywords'] = preg_replace( '/,([^\s])/', ', $1', $data['tags'] );
		}
	} else {
		$data['keywords'] = '';
	}

	// get rating
	if ( !empty( $data['trailerRating'] ) && strcasecmp( $data['trailerRating'], 'not rated') != 0 && strcasecmp( $data['trailerRating'], 'nr') != 0 ) {
		$data['industryRating'] = $ingester->getIndustryRating( $data['trailerRating'] );
	} else if ( !empty( $data['industryRating'] ) ) {
		$data['industryRating'] = $ingester->getIndustryRating( $data['industryRating'] );
	}

	// get age required
	if ( empty( $data['ageRequired'] ) && !empty( $data['ageGate'] ) && is_numeric( $data['ageGate'] ) ) {
		if ( $data['ageGate'] > 1 ) {
			$data['ageRequired'] = $data['ageGate'];
		} else if ( !empty( $data['industryRating'] ) ) {
			$data['ageRequired'] = $ingester->getAgeRequired( $data['industryRating'] );
		} else {
			$data['ageRequired'] = 18;	// default value for age required
		}
	}

	// get age gate
	$data['ageGate'] = empty( $data['ageRequired'] ) ? 0 : 1;

	$errorMsg = '';
	$metadata = $ingester->generateMetadata( $data, $errorMsg );
	if ( !empty( $errorMsg ) ) {
			$skip++;
			echo "$videoTitle...SKIPPED. ($errorMsg).\n";
			return false;
	}

	// add required fields
	$metadata['title'] = $data['title'];
	$metadata['canEmbed'] = $data['canEmbed'];

	// map additional metadata (per provider)
	if ( in_array( $metadata['provider'], $extraMapping ) ) {
		$function = 'mapMetadata'.ucfirst( $metadata['provider'] );
		$function( $ingester, $data, $metadata );
	}

	return $metadata;
}

/**
 * mapping additional metadata for RealGravity
 * @param VideoFeedIngester $ingester
 * @param array $data
 * @param array $metadata
 */
function mapMetadataRealgravity( $ingester, $data, &$metadata ) {
	$metadata['name'] = '';
	$metadata['category'] = $ingester->getCategory( $metadata['category'] );
}

/**
 * mapping additional metadata for IVA
 * @param VideoFeedIngester $ingester
 * @param array $data
 * @param array $metadata
 */
function mapMetadataIva( $ingester, $data, &$metadata ) {
	global $countryNames, $languageNames;

	// get language
	if ( !empty( $metadata['language'] ) && !empty( $languageNames ) && array_key_exists( $metadata['language'], $languageNames ) ) {
		$metadata['language'] = $languageNames[$metadata['language']];
	}

	// get subtitle
	if ( !empty( $metadata['subtitle'] ) && !empty( $languageNames ) && array_key_exists( $metadata['subtitle'], $languageNames ) ) {
		$metadata['subtitle'] = $languageNames[$metadata['subtitle']];
	}

	// get country code
	if ( !empty( $metadata['targetCountry'] ) && !empty( $countryNames ) && array_key_exists( $metadata['targetCountry'], $countryNames ) ) {
		$metadata['targetCountry'] = $countryNames[$metadata['targetCountry']];
	}

	$metadata['category'] = $ingester->getCategory( $metadata['category'] );
	$metadata['type'] = $ingester->getStdType( $metadata['category'] );

	// add page categories to metadata
	$metadata['pageCategories'] = empty( $data['pageCategories'] ) ? '' : $data['pageCategories'];
	if ( empty( $data['pageCategories'] ) ) {
		$metadata['pageCategories'] = '';
	} else {
		$categories = array_map( 'trim', explode( ',', $data['pageCategories'] ) );
		$categories = $ingester->generateCategories( $metadata, $categories );
		$metadata['pageCategories'] = implode( ', ', $categories );
	}
}

/**
 * mapping additional metadata for IGN
 * @param VideoFeedIngester $ingester
 * @param array $data
 * @param array $metadata
 */
function mapMetadataIgn( $ingester, $data, &$metadata ) {
	if ( !empty( $metadata['category'] ) ) {
		$metadata['type'] = $ingester->getStdType( $metadata['category'] );
	}
	$metadata['category'] = '';
}

/**
 * mapping additional metadata for Anyclip
 * @param VideoFeedIngester $ingester
 * @param array $data
 * @param array $metadata
 */
function mapMetadataAnyclip( $ingester, $data, &$metadata ) {
	global $languageNames;
	if ( !empty( $metadata['category'] ) && strtolower( $metadata['category'] == 'Movies' ) ) {
		$metadata['type'] = 'Clip';
	}

	// get language
	if ( !empty( $metadata['language'] ) && !empty( $languageNames ) && array_key_exists( $metadata['language'], $languageNames ) ) {
		$metadata['language'] = $languageNames[$metadata['language']];
	}
}

// ----------------------------- Main ------------------------------------

ini_set( "include_path", dirname( __FILE__ )."/../../" );

require_once( "commandLine.inc" );

if ( isset($options['help']) ) {
	die( "Usage: php mapVideoMatadata.php [--help] [--dry-run] [--provider=abc] [--limit=123]
	--dry-run                      dry run
	--provider                     video provider (required)
	--limit                        limit number of videos (required)
	--name                         video title (optional)
	--help                         you are reading it right now\n\n" );
}

if ( empty($wgCityId) ) {
	die( "Error: Invalid wiki id.\n" );
}

$dryRun = isset( $options['dry-run'] );
$provider = isset( $options['provider'] ) ? $options['provider'] : '';
$limit = isset( $options['limit'] ) ? $options['limit'] : 0 ;
$videoTitle = isset( $options['name'] ) ? $options['name'] : '';

if ( empty( $provider ) ) {
	die( "Error: Invalid provider.\n" );
}

if ( !is_numeric( $limit ) ) {
	die( "Error: Invalid limit.\n" );
}

echo "Wiki: $wgCityId ($wgDBname)\n";

$ingester = VideoFeedIngester::getInstance( $provider );
// get WikiFactory data
$ingestionData = $ingester->getWikiIngestionData();
if ( empty( $ingestionData ) ) {
	die( "No ingestion data found in wikicities. Aborting.\n" );
}

// keywords for page categories
$categories = array( 'International', 'Foreign', 'Movies', 'TV', 'Gaming', 'Games', 'Others' );

// providers that require extra mapping
$extraMapping = array( 'iva', 'realgravity', 'ign', 'anyclip' );

// include cldr extension for language code ($languageNames), country code ($countryNames)
include( dirname( __FILE__ ).'/../../../extensions/cldr/CldrNames/CldrNamesEn.php' );

$dbw = wfGetDB( DB_MASTER );

$sqlWhere = array(
	'img_media_type' => 'VIDEO',
	'img_minor_mime' => $provider,
);

if ( !empty( $videoTitle ) ) {
	$sqlWhere[] = "img_name >= ".$dbw->addQuotes( $videoTitle );
}

$result = $dbw->select(
	array( 'image' ),
	array( 'img_name', 'img_metadata' ),
	$sqlWhere,
	__METHOD__,
	array( 'LIMIT' => $limit, 'ORDER BY' => 'img_name' )
);

$total = $result->numRows();
$success = 0;
$failed = 0;
$skip = 0;
while ( $row = $dbw->fetchObject($result) ) {
	$name = $row->img_name;
	echo "Name: $name";

	$title = Title::newFromText( $name, NS_FILE );
	if ( !$title instanceof Title ) {
		$failed++;
		echo "...FAILED. (Error: Title NOT found)\n";
		continue;
	}

	$file = wfFindFile( $title );
	if ( empty($file) ) {
		$failed++;
		echo "...FAILED. (Error: File NOT found)\n";
		continue;
	}

	$metadata = unserialize( $row->img_metadata );
	if ( !$metadata ) {
		$failed++;
		echo "...FAILED. (Error: Cannot unserialized metadata)\n";
		continue;
	}

	// check for videoId
	if ( empty( $metadata['videoId'] ) ) {
		$skip++;
		echo "...SKIPPED. (empty videoId in metadata).\n";
		continue;
	}

	// check for title
	if ( empty( $metadata['title'] ) ) {
		$skip++;
		echo "...SKIPPED. (empty title in metadata).\n";
		continue;
	}

	// check for title
	if ( !isset( $metadata['canEmbed'] ) ) {
		$skip++;
		echo "...SKIPPED. (canEmbed field NOT found in metadata).\n";
		continue;
	}

	// check if the metadata already mapped
	if ( isset( $metadata['name'] ) ) {
		$skip++;
		echo "...SKIPPED. ('name' field found in metadata. metadata already mapped).\n";
		continue;
	}

	// set provider
	if ( empty( $metadata['provider'] ) ) {
		$metadata['provider'] = $provider;
	}

	// map metadata
	$newMetadata = mapMetadata( $name, $ingester, $metadata );
	if ( $newMetadata === false ) {
		continue;
	}

	// for debugging
	//echo "\n\tMetadata:\n";
	//foreach ( $metadata as $key => $value ) {
	//	echo "\t\t$key: $value\n";
	//}

	if ( !$dryRun ) {
		$serializedMeta = serialize( $newMetadata );

		if ( wfReadOnly() ) {
			die( "Read only mode.\n" );
		}

		$dbw->begin();

		// update database
		$dbw->update( 'image',
			array( 'img_metadata' => $serializedMeta ),
			array( 'img_name' => $name ),
			__METHOD__
		);

		$dbw->commit();

		// clear cache
		$file->purgeEverything();
	}

	echo "...DONE.\n";

	echo "\tNEW Metadata:\n";
	$fields = array_unique( array_merge( array_keys( $newMetadata ), array_keys( $metadata ) ) );
	foreach ( $fields as $field ) {
		if ( !isset( $newMetadata[$field] ) ) {
			echo "\t\t[DELETED] $field: $metadata[$field]\n";
		} else if ( !isset( $metadata[$field] ) ) {
			echo "\t\t[NEW] $field: $newMetadata[$field]\n";
		} else if ( strcasecmp( $metadata[$field], $newMetadata[$field] ) == 0 ) {
			echo "\t\t$field: $newMetadata[$field]\n";
		} else {
			echo "\t\t$field: $newMetadata[$field] (Old value: $metadata[$field])\n";
		}
	}

	$success++;
}

echo "Total Videos: $total, Success: $success, Failed: $failed, Skipped: $skip.\n\n";