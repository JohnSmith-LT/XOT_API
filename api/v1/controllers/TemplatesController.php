<?php

require_once 'XerteBaseController.php';

class TemplatesController extends XerteBaseController
{
	public function process_get() {
		global $xerte_toolkits_site;

		$params = array_slice($this->request->route, 1); // THIS IS A DUPLICATE - CAN WE ABSTRACT IT OUT??

		//CAN WE ACTUALLY ABSTRACT OUT THIS WHOLE FILE ALMOST??
		//IF WE PASS BACK SOME PARAMETERS THEN WE CAN...

		if (empty($params) || is_numeric($params[0])) {
			$strSQL = "SELECT * FROM {$xerte_toolkits_site->database_table_prefix}templatedetails" . $this->offset_limit();
			//print($strSQL);
			if ($row = db_query($strSQL)) {
				$templates = array();
				foreach ($row as $key => $value) {
					if (empty($params) || $params[0] == $value['template_id']) {
						$templates[] = $value;
					}
				}

				if (empty($templates)) {
					if (is_numeric($params[0])) {
						$this->response->status = 404;
					}
					else {
						$this->response->status = 200;
					}
					return null;
				}

				return $templates;
			}
			else {
				$this->response->status = 200;
				return null;
			}
		}
		else {
			$this->response->status = 400;
			return null;
		}
	}
}