<?php

   /**
	*
	*  Xerte API Library functions
	*
	*  @author John Smith, Glasgow Caledonian University
	*  @version 1.0
	*
	*/



   /**
	*
	*  Authorise the signed request based on OAuth 2-legged consumer/secret request
	*
	*  @return true/false
	*
	*/
	function authorised() {

		global $api_config;

		$request = OAuthRequest::from_request();

		$api_config->oauth_parameters = $request->get_parameters();

		if (	!isset($api_config->oauth_parameters['oauth_consumer_key']) ||
				!isset($api_config->oauth_parameters['oauth_signature']) ||
				!isset($api_config->oauth_parameters['oauth_timestamp']) ||
				!isset($api_config->oauth_parameters['oauth_nonce'])
		) {
			return false;
		}

		$consumer_key = $api_config->oauth_parameters['oauth_consumer_key'];
		$consumer_secret = lookup_secret($consumer_key);
		$consumer = new OAuthConsumer($consumer_key, $consumer_secret);

		$signature_method = new OAuthSignatureMethod_HMAC_SHA1;
		$signature = $api_config->oauth_parameters['oauth_signature'];

		$timestamp = $api_config->oauth_parameters['oauth_timestamp'];
		$request_expired = (abs(time() - $timestamp) > 300);

		return !$request_expired && valid_nonce() && $signature_method->check_signature($request, $consumer, null, $signature);
	}

   /**
	*
	*  Lookup the secret key based on the passed consumer key
	*
	*  @param string consumer key passed with the request
	*
	*  @return string secret key or null if consumer key not found
	*
	*/
	function lookup_secret($key) {
		global $xerte_toolkits_site;

		$strSQL = "SELECT consumer_secret FROM {$xerte_toolkits_site->database_table_prefix}api_keys WHERE consumer_key = ? AND active = 1";
		if ($rows = db_query($strSQL, array($key))) {
			$row = $rows[0];
			$consumer_secret = $row['consumer_secret'];

			$strSQL = "UPDATE {$xerte_toolkits_site->database_table_prefix}api_keys SET last_used = NOW(), uses_count = uses_count + 1 WHERE consumer_key = ? AND consumer_secret = ?";
			db_query($strSQL, array($key,$consumer_secret));

			return $consumer_secret;
		}
		return null;
	}

   /**
	*
	*  Lookup the nonce to ensure that it hasn't been used during the valid window period
	*
	*  @return true/false
	*
	*  TODO - Ensure that current timestamp is AFTER any previous timestamps from same consumer
	*
	*/
	function valid_nonce() {
		global $api_config;
		global $xerte_toolkits_site;

		$consumer_key = $api_config->oauth_parameters['oauth_consumer_key'];
		$nonce = $api_config->oauth_parameters['oauth_nonce'];
		$timestamp = $api_config->oauth_parameters['oauth_timestamp'];

		$strSQL = "SELECT * FROM {$xerte_toolkits_site->database_table_prefix}api_nonces WHERE consumer_key = ? AND nonce = ?";
		if ($rows = db_query($strSQL, array($consumer_key,$nonce))) {
			return false;
		}
		return true;
	}

   /**
	*
	*  Store the nonce to ensure that it can't be re-used during the valid window to prevent replay attacks
	*  Also cleans up nonce table, removing any nonces that have expired
	*
	*/
	function store_nonce() {
		global $api_config;
		global $xerte_toolkits_site;

		$consumer_key = $api_config->oauth_parameters['oauth_consumer_key'];
		$nonce = $api_config->oauth_parameters['oauth_nonce'];
		$timestamp = $api_config->oauth_parameters['oauth_timestamp'];
		$expired = $timestamp - $api_config->nonce_expiry;

		// Delete any expired nonces from this consumer
		$strSQL = "DELETE FROM {$xerte_toolkits_site->database_table_prefix}api_nonces WHERE consumer_key = ? AND timestamp < $expired";
		db_query($strSQL, array($consumer_key));

		// Save this nonce
		$strSQL = "INSERT INTO {$xerte_toolkits_site->database_table_prefix}api_nonces (consumer_key,nonce,timestamp) VALUES (?,?,?)";
		db_query($strSQL, array($consumer_key,$nonce,$timestamp));
	}