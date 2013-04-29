<?php

/**
 * Represents the result of calling SD REST service. It contains the status of the call and the response.
 */
class PandoraResponse {
	/**
	 * @param $status Status
	 * @param $statusCode int
	 * @param $response String
	 */
	public function __construct($status, $statusCode, $response) {
		$this->status = $status;
		$this->statusCode = $statusCode;
		$this->response = $response;
	}

	/**
	 * @return int
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * @return bool
	 */
	public function isOK() {
		return $this->status->isOK();
	}

	/**
	 * @return String
	 */
	public function getMessage() {
		return $this->status->getMessage();
	}

	private $jsonResponse = null;

	/**
	 * return JSON response decoded into PHP variable
	 * @return mixed|null
	 */
	public function asJson() {
		if ( is_null( $this->jsonResponse ) ) {
			$this->jsonResponse = json_decode($this->response);
		}
		return $this->jsonResponse;
	}

}