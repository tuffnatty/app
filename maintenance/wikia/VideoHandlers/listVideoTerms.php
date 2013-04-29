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
		$this->addOption( 'thresh', 'Threshold', false, true, 't' );
		$this->addOption( 'limit', 'Limit', false, true, 'l' );
		$this->addOption( 'type', 'Type', false, true, 'y' );
		$this->addOption( 'dir', 'Direction', false, true, 'd' );
	}

	public function execute() {
		$limit  = $this->getOption( 'limit', 100 );
		$type   = $this->getOption( 'type' );
		$dir    = $this->getOption( 'dir' );

		if (!preg_match('/^(most|least)$/', $dir)) {
			die("Invalid value '$dir' for param --dir.  Options are 'most' or 'least'\n");
		}

		// Set some reasonable default values for threshold if not given based on the direction
		if ($dir == 'most') {
			$thresh = $this->getOption( 'thresh', 10000 );
		} else {
			$thresh = $this->getOption( 'thresh', 10 );
		}

		// Build up the method name we're going to use.  Since the params are so similar for all these
		// methods, this should hopefully eliminate bunch of confusing if/else's
		$method = $dir.'Used';

		if ($type == 'keyword') {
			$method .= 'Keywords';

			// Calling one of 'leastUsedKeywords' or 'mostUsedKeywords'
			$terms = VideoMetaService::$method($thresh, $limit);
		} else {
			$method .= 'Terms';

			// Calling one of 'leastUsedTerms' or 'mostUsedTerms'
			$terms = VideoMetaService::$method($thresh, $limit, $type);
		}

		printf("%5s - %8s - %s\n", 'Freq', 'Type', 'Term');
		foreach ($terms as $term) {
			printf("%5d - %8s - %s\n", $term->frequency(), $term->type(), $term->value());
		}
	}
}

$maintClass = "EditCLI";
require_once( RUN_MAINTENANCE_IF_MAIN );

