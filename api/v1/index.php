<?php

   /**
	*
	*  Xerte API main entry page
	*
	*  @author John Smith, Glasgow Caledonian University
	*  @version 1.0
	*
	*/

	global $xerte_toolkits_site;
	$xerte_toolkits_site = new StdClass();
	require_once dirname(__FILE__) . '/../../database.php';
	require_once dirname(__FILE__) . '/../../website_code/php/database_library.php';
	require_once(dirname(__FILE__) . '/../../functions.php');

   /**
	*
	*  Request comes in
	*  We pass the request into the controller
	*  We pass the controller into the response
	*
	*  		COLLECTION:		/api/v1/noun?query
	* 		ITEM:			/api/v1/noun/id?query
	*
	*  Nouns:			templates, users
	*
	*  Formats:			json(p), xml
	*
	*  Query params:	offset (default=0),
	*					limit (default=all),
	*					format (default=json),
	*					callback (default=none)
	*/

	require_once 'XerteAPIConfig.php';
	require_once 'XerteAPILibrary.php';
	require_once 'XerteAPIRequest.php';
	require_once 'XerteAPIResponse.php';
	require_once 'vendor/OAUTH.php';

   /**
	*  Read in our options
	*/
	$api_options = array();
	$strSQL = "SELECT * FROM {$xerte_toolkits_site->database_table_prefix}api_options";
	if ($rows = db_query($strSQL)) {
		foreach ($rows as $key => $value) {
			$api_options[$value['name']] = $value['value'];
		}
	}

   /**
	*  Build our request object
	*/
	$request = new XerteAPIRequest();

   /**
	*  Is the api enabled??
	*/
	if (isset($api_options['enabled']) && $api_options['enabled'] == 'yes') {

		if (authorised()) {

		   /**
			*  Decode request and load correct controller
			*/
			$controller_name = ucwords($request->route[0]) . 'Controller';
			if (file_exists('controllers/' . $controller_name . '.php')) {
				require_once 'controllers/' . $controller_name . '.php';

			   /**
				*  So far so good, build OK response
				*/
				$response = new XerteAPIResponse(200);
				$response->request = $request;
				$response->noun = $request->route[0];

			   /**
				*  Pass control to the controller
				*/
				$controller = new $controller_name($request, $response);
				$controller->execute();
			}
			else {
			   /**
				*  Controller wasn't found so send BAD REQUEST as noun probably wrong
				*/
				$response = new XerteAPIResponse(400);
				$response->request = $request;
				$response->parameters = $request->parameters;
			}

		   /**
			*  If we get here then the request was accepted so retain the nonce
			*/
			store_nonce();
		}
		else {
			   /**
				*  Send UNAUTHORISED response
				*/
				$response = new XerteAPIResponse(401);
				$response->request = $request;
				$response->parameters = $request->parameters;
		}
	}
	else {
	   /**
		*  Send SERVICE UNAVAILABLE response
		*/
		$response = new XerteAPIResponse(503);
		$response->request = $request;
		$response->parameters = $request->parameters;
	}

   /**
	*  Send the response
	*/
	$response->sendResponse();
