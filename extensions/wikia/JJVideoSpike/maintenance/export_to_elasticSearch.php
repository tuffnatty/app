<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 29.04.13 13:15
 *
 */

require_once( dirname( __FILE__ ) . '/../../../../maintenance/Maintenance.php' );


class exportToElasticSearch extends Maintenance {

	protected $providers = array('anyclip', 'dailymotion', 'ign', 'movieclips', 'realgravity', 'screenplay');

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Import video metadata to elastic search engine";
		$this->addOption( 'provider', 'Provider name', true, true, 'p' );
	}

	/**
	 * Do the actual work. All child classes will need to implement this
	 */
	public function execute() {

		$provider = $this->getOption( 'provider', '' );
		if ( !in_array( $provider, $this->providers ) ) {
			die( " You need to choose provider: " . implode( ", ", $this->providers ) );
		}

		$db = wfGetDB( DB_SLAVE, array(), "video151" );
		$elems = $db->query("SELECT i.*, p.page_id
							 FROM image i LEFT JOIN page p ON i.img_name = p.page_title
							 WHERE i.img_media_type='VIDEO' AND p.page_id > 0 AND i.img_minor_mime = '{$provider}' LIMIT 500");


		$elastic = new ElasticSearchQuery('testing', 'test');

		while ( $r = $elems->fetchObject() ) {

			$metadata = unserialize( $r->img_metadata );
			$keywords = array();
			if ( isset($metadata['keywords']) ) {
				$keywordsA = explode(",", $metadata['keywords']);
				foreach ( $keywordsA as $keyword ) {
					$keywords[] = trim( $keyword );
				}
			}

			$toIndex = array(
				'video_id' => $r->page_id,
				'title' => $r->img_name,
				'description' => isset( $metadata['description'] ) ? $metadata['description'] : '',
				'keywords' => $keywords
			);

			$resp = $elastic->indexData( $r->page_id, $toIndex );

			print_r( $toIndex );
			print_r( $resp );
			echo "================================\n";
		}

	}

}

$maintClass = "exportToElasticSearch";
require_once( RUN_MAINTENANCE_IF_MAIN );




