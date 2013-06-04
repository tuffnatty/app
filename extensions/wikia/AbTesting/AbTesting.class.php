<?php
/**
 *
 * This is internal AB testing class.
 * For API for querying for running server-side
 * tests please see AbTests.class.php
 *
 * @author Sean Colombo
 * @author Kyle Florence
 * @author Władysław Bodzek
 * @author Piotr Bablok
 */

class AbTesting {

	const FLAG_GA_TRACKING = 1;
	const FLAG_DW_TRACKING = 2;
	const FLAG_FORCED_GA_TRACKING_ON_LOAD = 4;
	const FLAG_LIMIT_TO_SPECIAL_WIKIS = 8;
	const DEFAULT_FLAGS = 3;

	const STATUS_ACTIVE = 0;
	const STATUS_INACTIVE = 1;

	static public $flags = array(
		self::FLAG_GA_TRACKING => 'ga_tracking',
		self::FLAG_DW_TRACKING => 'dw_tracking',
		self::FLAG_FORCED_GA_TRACKING_ON_LOAD => 'forced_ga_tracking_on_load',
		self::FLAG_LIMIT_TO_SPECIAL_WIKIS => 'limit_to_special_wikis',
	);

	protected function __construct() {}

	static public function getFlagsInObject( $flags ) {
		$obj = new stdClass();
		foreach (self::$flags as $flag => $key) {
			$obj->$key = $flags & $flag ? 1 : 0;
		}
		return $obj;
	}

	public static function getTimestampForUTCDate( $date ) {
		return strtotime($date);
	}


	/**
	 * Given a string of ranges, returns an array of range hashes. For example:
	 * "0-10,15-25,40" => array(
	 *     array( "min" => 0, "max" => 10 ),
	 *     array( "min" => 15, "max" => 25 ),
	 *     array( "min" => 40, "max" => 40 )
	 * )
	 */
	public static function parseRanges( $ranges, $failOnError = false ) {
		$rangesArray = array();

		if ( strlen( $ranges ) ) {
			$min = $max = 0;
			foreach ( explode( ',', $ranges ) as $i => $range ) {
				$hasError = false;
				if ( preg_match( '/^(\d+)-(\d+)$/', $range, $matches ) ) {
					$min = intval( $matches[1] );
					$max = intval( $matches[2] );
				} elseif ( preg_match( '/^(\d+)$/', $range, $matches ) ) {
					$min = $max = intval( $matches[1] );
				} else {
					$hasError = true;
				}
				if ( $min < 0 || $min > 99 ) $hasError = true;
				if ( $max < 0 || $max > 99 ) $hasError = true;
				if ( $min > $max ) $hasError = true;

				if ( $hasError ) {
					if ( $failOnError ) {
						return false;
					}
					break;
				}

				$rangesArray[] = array(
					'min' => $min,
					'max' => $max
				);
			}
		}

		return $rangesArray;
	}

	/**
	 * Normalizes the names of experiments and treatment groups into an
	 * uppercased string with spaces replaced by underscores.
	 */
	static public function normalizeName( $name ) {
		$name = str_replace( ' ', '_', $name );
		$name = strtoupper( preg_replace( '/[^a-z0-9_]/i', '', $name ) );

		return $name;
	}

	static public function getCacheTTL() {
		return F::app()->wg->ResourceLoaderMaxage['unversioned'];
	}

	static public function getMaxStaleCache() {
		$cacheTTL = self::getCacheTTL();
		return
			$cacheTTL['server'] * 2 // DC varnish + edge varnish
			+ $cacheTTL['client'];  // user browser
	}

	static public function getFlagState( $flags, $flag ) {
		return ($flags & $flag) > 0;
	}

}
