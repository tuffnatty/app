<?php
/**
 * Find videos related to the given video title
 *
 * @author garth@wikia-inc.com
 * @ingroup Maintenance
 */

ini_set('display_errors', 'stderr');
ini_set('error_reporting', E_NOTICE);

require_once( dirname( __FILE__ ) . '/../../Maintenance.php' );
require_once( dirname( __FILE__ ) . '/../../../includes/wikia/services/VideoMetaService.class.php' );

class EditCLI extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Find videos related to the given video title";
		$this->addOption( 'title', 'Title', false, true, 't' );
		$this->addOption( 'freq', 'Frequency', false, true, 'f' );
	}

	public function execute() {
		$title = $this->getOption( 'title' );
		$freq  = $this->getOption( 'freq', 100 );

		// Make sure this is in DB format with underscores for spaces
		$title = preg_replace('/ +/', '_', $title);

		echo "\n";
		echo "Finding related videos for '$title'\n";

		$terms = VideoMetaService::termsForTitle( $title, $freq );

		if (empty($terms)) {
			die("Failed to retrieve terms for this title\n");
		}
		if (count($terms) == 0) {
			die("No terms returned for this title\n");
		}

		usort($terms, function($a, $b) {
			if ($a->frequency() > $b->frequency()) {
				return 1;
			} else if ($a->frequency() < $b->frequency()) {
				return -1;
			} else {
				return 0;
			}
		});

		echo "\n";
		echo "Found terms:\n";
		printf("\t%5s - %10s - %s\n", 'Freq', 'Type', 'Value');
		foreach ($terms as $term) {
			printf("\t%5d - %10s - %s\n", $term->frequency(), $term->type(), $term->value());
		}
		echo "\n";

		$videos = VideoMetaService::relatedVideos( $terms );

		foreach ($videos as $info) {
			$video = $info['video'];
			$relevancy = $info['relevancy'];

			printf( "%1.3f: %s\n", $relevancy, $video->title());
		}
		echo "\n";
	}
}

$maintClass = "EditCLI";
require_once( RUN_MAINTENANCE_IF_MAIN );

