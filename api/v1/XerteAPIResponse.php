<?php

class XerteAPIResponse {

	public $status, $payload, $paramaters, $noun, $request;

	public function __construct(
								$status = 200,
								$payload = null,
								$parameters = array(),
								$noun = '',
								$request = null
	) {
		global $api_config;

		$this->status = $status;
		$this->payload = $payload;
		$this->parameters = $parameters;
	}

	public function sendResponse() {

		header("HTTP/1.0 " . $this->status . XerteAPIResponse::status_message($this->status), true, $this->status);
		header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		global $api_config;
		if (!isset($this->parameters['format'])) {
			$this->parameters['format'] = $api_config->default_response_format;
		}

		switch ($this->parameters['format']) {
			case 'json':
				header('Content-type: application/json; charset=utf-8');
				$this->json();
				break;
			default:
				header('Content-type: application/xml; charset=utf-8');
				$this->xml();
		}
	}

	private function xml() {

		$status_code = $this->status;
		$status_message = $this->status_message($status_code);

		$final = array(
			'status' => array(
				'code' => $status_code,
				'message' => $status_message,
				'uri' => $this->request->request_uri
			)
		);

		// If we have a valid query then send back the data (may be empty)
		if ($status_code == 200) {
			// We need to mung the data for xml format
			$munged = array();
			$noun = $this->noun;
			$singular = (isset($noun) && strlen($noun)>0 && substr($noun, strlen($noun)-1)=='s') ? substr($noun, 0, strlen($noun)-1) : 'item';
			if (!is_null($this->payload)) {
				foreach($this->payload as $key => $value) {
					array_push($munged, array("$singular" => $value));
				}
			}
			$final['data'] = $munged;
		}

		$result = new SimpleXMLElement('<'.'?xml version="1.0" encoding="UTF-8"?'.'><response></response>');
		XerteAPIResponse::array_to_xml($final, $result);
		echo $result->saveXML();
	}

	private function json() {

		$status_code = $this->status;
		$status_message = $this->status_message($status_code);

		// Build the array which will form the structure of the response
		$final = array(
			'status' => array(
				'code' => $status_code,
				'message' => $status_message,
				'uri' => $this->request->request_uri
			)
		);

		// If we have a valid query then send back the data (may be empty)
		if ($status_code == 200) {
			if (is_null($this->payload))
				$final['data'] = array();
			else
				$final['data'] = $this->payload;
		}

		// If we have a callback parameter its JSONP so add the callback name
		if (isset($this->parameters['callback']) && strlen($this->parameters['callback']) > 0) {
			echo $this->parameters['callback'] . '(' . json_encode($final) . ')';
		}
		else {
			echo json_encode($final);
		}
	}

	private static function status_message($status_code) {
		$status_messages = Array(
		    100 => "Continue",
		    101 => "Switching Protocols",
		    200 => "OK",
		    201 => "Created",
		    202 => "Accepted",
		    203 => "Non-Authoritative Information",
		    204 => "No Content",
		    205 => "Reset Content",
		    206 => "Partial Content",
		    300 => "Multiple Choices",
		    301 => "Moved Permanently",
		    302 => "Found",
		    303 => "See Other",
		    304 => "Not Modified",
		    305 => "Use Proxy",
		    306 => "(Unused)",
		    307 => "Temporary Redirect",
		    400 => "Bad Request",
		    401 => "Unauthorized",
		    402 => "Payment Required",
		    403 => "Forbidden",
		    404 => "Not Found",
		    405 => "Method Not Allowed",
		    406 => "Not Acceptable",
		    407 => "Proxy Authentication Required",
		    408 => "Request Timeout",
		    409 => "Conflict",
		    410 => "Gone",
		    411 => "Length Required",
		    412 => "Precondition Failed",
		    413 => "Request Entity Too Large",
		    414 => "Request-URI Too Long",
		    415 => "Unsupported Media Type",
		    416 => "Requested Range Not Satisfiable",
		    417 => "Expectation Failed",
		    500 => "Internal Server Error",
		    501 => "Not Implemented",
		    502 => "Bad Gateway",
		    503 => "Service Unavailable",
		    504 => "Gateway Timeout",
		    505 => "HTTP Version Not Supported"
		);

		return (isset($status_messages[$status_code])) ? $status_messages[$status_code] : "";
	}

	private static function array_to_xml($student_info, &$xml_student_info) {
		foreach($student_info as $key => $value) {
			if(is_array($value)) {
				if(!is_numeric($key)){
					$subnode = $xml_student_info->addChild("$key");
					XerteAPIResponse::array_to_xml($value, $subnode);
				}
				else{
					XerteAPIResponse::array_to_xml($value, $xml_student_info);
				}
			}
			else {
				$xml_student_info->addAttribute("$key","$value");
			}
		}
	}

}