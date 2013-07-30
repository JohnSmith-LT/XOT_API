<html>
<head>
</head>
<body>
<form action="" method="POST">

Route:<select name="noun">
<option value="templates"<?php if (isset($_POST['noun']) && $_POST['noun'] == 'templates') echo ' selected' ?>>templates</option>
<option value="users"<?php if (isset($_POST['noun']) && $_POST['noun'] == 'users') echo ' selected' ?>>users</option>
</select>
/
<input name="id" value="<?php if (isset($_POST['id']) && $_POST['id'] != '') echo $_POST['id'] ?>" size="4" />(this can be left empty)<br />

Offset:<input name="offset" value="<?php if (isset($_POST['offset']) && $_POST['offset'] != '') echo $_POST['offset'] ?>" />(this can be left empty)<br />

Limit:<input name="limit" value="<?php if (isset($_POST['limit']) && $_POST['limit'] != '') echo $_POST['limit'] ?>" />(this can be left empty)<br />

Format:<select name="format">
<option value="json"<?php if (isset($_POST['format']) && $_POST['format'] == 'json') echo ' selected' ?>>json</option>
<option value="xml"<?php if (isset($_POST['format']) && $_POST['format'] == 'xml') echo ' selected' ?>>xml</option>
</select><br />

JSON Callback:<input name="callback" value="<?php if (isset($_POST['callback']) && $_POST['callback'] != '') echo $_POST['callback'] ?>" />(this can be left empty)<br /><br />

<input type="submit" name='submit' text="Submit" />
</form>
<hr />
<?php

	if (isset($_POST['submit']) && isset($_POST['noun'])) {

		require("v1/vendor/OAuth.php");

		$noun = $_POST['noun'];
		$id = ''; if (isset($_POST['id']) && $_POST['id'] != '') $id = '/' . $_POST['id'];

		$base_url = 'http://'. $_SERVER['SERVER_NAME'] . str_replace('/api/test.php', '', $_SERVER['REQUEST_URI']) . '/api/v1/';

		$consumer_key  = "IQKbtAYlXLripLGPWd0HUA";
		$consumer_secret = "GgDYlkSvaPxGxC4X8liwpUoqKwwr3lCADbz8A7ADU";

		$url = $base_url . $noun . $id;

		$args = array();
		if (isset($_POST['offset']) && $_POST['offset'] != '') $args["offset"] = intval($_POST['offset']);
		if (isset($_POST['limit']) && $_POST['limit'] != '') $args["limit"] = intval($_POST['limit']);
		$args["format"] = "json"; if (isset($_POST['format'])) $args["format"] = $_POST['format'];
		if (isset($_POST['callback']) && $_POST['callback'] != '') $args["callback"] = $_POST['callback'];

		// ** Sign the request and send **
		$consumer = new OAuthConsumer($consumer_key, $consumer_secret);
		$request = OAuthRequest::from_consumer_and_token($consumer, NULL,"GET", $url, $args);
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
		$url = sprintf("%s?%s", $url, OAuthUtil::build_http_query($args));
		$ch = curl_init();
		$headers = array($request->to_header());
		echo 'Request made to ' . $url . '<br /><br />';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$rsp = curl_exec($ch);

		echo '<hr />';

		if ($args["format"] == 'xml')
			print(str_replace('<', '&lt;', $rsp));
		else
			print($rsp);
	}
?>
</body>
</html>