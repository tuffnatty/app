<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 29.04.13 13:09
 *
 */

class ElasticSearchClient {

		protected $serviceUrl = 'http://db-sds-s1:9200';
		protected $collection;
		protected $index;

		public function __construct( $index = 'video151', $type = 'videos' ) {

			$this->index = $index;
			$this->type = $type;
		}

		public function getItemUrl( $itemId ) {
			return $this->serviceUrl . $this->index . '/' . $this->type . '/' . $itemId;
		}

		public function getSearchUrl() {
			return $this->serviceUrl . $this->index . '/' . $this->type . '/_search';
		}

		public function call( $method, $url, $jsonData ) {


		}
}