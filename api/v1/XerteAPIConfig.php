<?php

   /**
	*
	*  Xerte API configuration page
	*
	*  @author John Smith, Glasgow Caledonian University
	*  @version 1.0
	*
	*/

	global $api_config;
	$api_config = new stdClass();

	// Set the base_url - used to work out the route
	$api_config->base_url = '/api/v1/';

	// Set the default format
	$api_config->default_response_format = 'json';

	// Set allowed formats
	$api_config->allowed_response_formats = array('xml', 'json');

	// Set nonce expiry time (seconds)
	$api_config->nonce_expiry = 300; // 5 minutes