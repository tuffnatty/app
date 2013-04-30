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

		public function __construct( $index, $type ) {

			$this->index = $index;
			$this->type = $type;
		}

		public function getItemUrl( $itemId ) {
			return $this->serviceUrl . '/' . $this->index . '/' . $this->type . '/' . $itemId;
		}

		public function getSearchUrl() {
			return $this->serviceUrl . '/' .$this->index . '/' . $this->type . '/_search';
		}

		public function call( $url, $method = null, $body = null ) {

			$options = array( 'method' => ( $method ) ? $method : 'GET' );
			//don't use wgHTTPProxy on devboxes, as cross-devbox calls will return 403
			//if ( !empty( $this->app->wg->develEnvironment ) )
			$options['noProxy'] = true;

			$httpRequest = MwHttpRequest::factory( $url,  $options );

			if ( $body ) {
				if ( is_array( $body) ) {
					$body = json_encode( $body );
				}
				$httpRequest->setData( $body );
			}
			$status = $httpRequest->execute();
			$statusCode = $httpRequest->getStatus();
			$response = $httpRequest->getContent();

			return array('status'=>$status, 'statusCode'=>$statusCode, 'response'=>$response );
		}
}