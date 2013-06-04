<?php

class AbTestingController extends WikiaController {

	protected $abTesting;
	protected $externalData;
	protected $externalDataLoaded = false;

	public function externalData() {
		// parse GET request
		$request = $this->getVal('ids');
		$request = !empty($request) && is_string($request) ? explode(',',$request) : array();

		// prepare request to config provider
		$dataRequests = array();
		foreach ($request as $groupSpec) {
			$parts = explode('.',$groupSpec);
			if ( count($parts) != 3 ) {
				continue;
			}
			$dataRequests[$groupSpec] = $parts;
		}

		// fetch external data from config provider
		$config = AbTestingConfig::getInstance();
		$external = $config->getExternalData( $dataRequests );

		// pass the data
		foreach ($external as $groupSpec => $data) {
			$data = $this->processData($data);
			$this->setVal($groupSpec,$data);
		}

		// force output format to be JSONP
		$response = $this->getResponse();
		$response->setFormat(WikiaResponse::FORMAT_JSONP);

		// set appropriate cache TTL
		$cacheTTL = AbTesting::getCacheTTL();
		$response->setCacheValidity(null,$cacheTTL['client'],array(WikiaResponse::CACHE_TARGET_BROWSER));
		$response->setCacheValidity(null,$cacheTTL['server'],array(WikiaResponse::CACHE_TARGET_VARNISH));
	}

	protected function processData( $input ) {
		$result = array();
		if ( isset($input['styles']) ) {
			$styles = $input['styles'];
			try {
				$sassService = SassService::newFromString($input['styles'],0,'');
				$sassService->setFilters(
					  SassService::FILTER_IMPORT_CSS
					| SassService::FILTER_CDN_REWRITE
					| SassService::FILTER_BASE64
					| SassService::FILTER_JANUS
					| SassService::FILTER_MINIFY
				);
				$styles = '/*SASS*/' . $sassService->getCss();
			} catch (Exception $e) {
				$styles = "/* SASS processing failed */\n\n";
				$styles .= $input['styles'];
			}
			$result['styles'] = $styles;
		}
		if ( isset($input['scripts']) ) {
			$result['scripts'] = $input['scripts'];
		}
		return $result;
	}

}