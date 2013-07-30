<?php

class XerteAPIRequest
{
	private $data, $http_accept;
	public $method, $route, $parameters, $request_uri;

	public function __construct() {
		global $api_config;

		$this->data = array();
		$this->http_accept = (strpos($_SERVER["HTTP_ACCEPT"], "json")) ? "json" : "xml";
		$this->method = strtolower($_SERVER['REQUEST_METHOD']);

		// Store the URI as we might return it
		$this->request_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];

		$this->parameters = $this->get_parameters();
		if (!isset($this->parameters['format'])) {
			$this->parameters['format'] = $api_config->default_response_format;
		}
		$this->route = $this->get_route();
	}

   /**
	*	Convert querystring to assoc array
	*/
	private function get_parameters(){
		$params = explode('&', $_SERVER['QUERY_STRING']);
		$parameters = array();
		if (!empty($_SERVER['QUERY_STRING'])) {
			foreach ($params as $param) {
				$parts = explode('=', $param);
				$parameters[$parts[0]] = $parts[1];
			}
		}
		return $parameters;
	}

   /**
	*	Convert 'route' to an array
	*/
	private function get_route() {
		global $api_config;

		$path = substr($this->request_uri, 0, strlen($this->request_uri) - strlen($_SERVER['QUERY_STRING']) - ($_SERVER['QUERY_STRING']?1:0));
		$route = explode('/', substr($path, strpos($path, $api_config->base_url) + strlen($api_config->base_url)));

		// Remove index.php if we aren't using .htaccess
		if (strtolower($route[0]) == 'index.php') {
			$route = array_slice($route, 1);
		}

		return $route;
	}

   /**
	*	Convert headers to associative array
	*	Taken from : http://stackoverflow.com/questions/10589889/returning-header-as-array-using-curl
	*/

	/*private static function get_headers($request)
	{
		$headers = array();

		$header_text = substr($request, 0, strpos($request, "\r\n\r\n"));

		foreach (explode("\r\n", $header_text) as $i => $line)
			if ($i === 0)
				$headers['http_code'] = $line;
			else
			{
				list ($key, $value) = explode(': ', $line);

				$headers[$key] = $value;
			}

		return $headers;
	}*/
}