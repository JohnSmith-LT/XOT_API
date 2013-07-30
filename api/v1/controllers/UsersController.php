<?php

require_once 'XerteBaseController.php';

class UsersController extends XerteBaseController
{
	public function process_get() {
		global $xerte_toolkits_site;

		$params = array_slice($this->request->route, 1); // THIS IS A DUPLICATE - CAN WE ABSTRACT IT OUT??

		//CAN WE ACTUALLY ABSTRACT OUT THIS WHOLE FILE ALMOST??
		//IF WE PASS BACK SOME PARAMETERS THEN WE CAN...

		if (empty($params) || is_numeric($params[0])) {
			$strSQL = "SELECT * FROM {$xerte_toolkits_site->database_table_prefix}logindetails" . $this->offset_limit();
			if ($row = db_query($strSQL)) {
				$users = array();
				foreach ($row as $key => $value) {
					if (empty($params) || $params[0] == $value['login_id']) {
						$users[] = $value;
					}
				}

				if (empty($users)) {
					if (is_numeric($params[0])) {
						$this->response->status = 404;
					}
					else {
						$this->response->status = 200;
					}
					return null;
				}

				return $users;
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