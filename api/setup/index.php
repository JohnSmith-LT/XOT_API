<?php

	global $xerte_toolkits_site;
	$xerte_toolkits_site = new StdClass();
	require_once dirname(__FILE__) . '/../../database.php';
	require_once dirname(__FILE__) . '/../../website_code/php/database_library.php';
	require_once(dirname(__FILE__) . '/../../functions.php');

    $temp = explode(";", file_get_contents("basic.sql"));
	foreach ($temp as $key => $sql) {

        $query = str_replace("$", $xerte_toolkits_site->database_table_prefix, ltrim($sql));

        if ($query != "") {
            $query_response = db_query($query);

            echo $query_response . "<br />";
        }
    }