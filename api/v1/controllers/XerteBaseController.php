<?php

abstract class XerteBaseController
{
	public $request, $response;

	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
	}

	public function execute() {
		global $api_config;

		// Transfer the parameters from request to response
		// PERHAPS THIS SHOULD ONLY BE THE ALLOWED PARAMETERS??
		$this->response->parameters = $this->request->parameters;

		// DONT THINK THIS IS NEEDED
		//if (is_null($this->request->parameters['format'])) {
		//	$this->response->parameters['format'] = $api_config->default_response_format;
		//}

		// Detect if a get/post/put/delete handler has been added to the route controller
		$method = 'process_'.$this->request->method;
		if (method_exists ( $this , $method )) {
			$this->response->payload = $this->$method();
		}
		else {
			$this->response->status = 400;
			$this->response->payload = null;
		}
	}

	public function offset_limit() {
		$limit = null; $offset = null; $appendSQL = '';

		if (isset($this->request->parameters['limit']) && is_numeric($this->request->parameters['limit'])) {
			$limit = $this->request->parameters['limit'];
		}

		if (isset($this->request->parameters['offset']) && is_numeric($this->request->parameters['offset'])) {
			$offset = $this->request->parameters['offset'];
			if (is_null($limit)) $limit = '18446744073709551615'; // MAXIMUM
		}

		if (!is_null($limit)) $appendSQL = $appendSQL . ' LIMIT ' . $limit;
		if (!is_null($offset)) $appendSQL = $appendSQL . ' OFFSET ' . $offset;

		return $appendSQL;
	}
}