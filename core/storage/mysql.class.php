<?php

require_once(VPANEL_STORAGE . "/sql.class.php");

class MySQLStorage extends SQLStorage {
	private $mysqli;
	
	public function __construct($host, $user, $pass, $db) {
		parent::__construct();
		$this->mysqli = new MySQLi($host, $user, $pass, $db);
	}

	public function query($sql) {
		return $this->mysqli->query($sql);
	}

	public function fetchRow($result) {
		return $result->fetch_assoc();
	}

	public function getInsertID() {
		return $this->mysqli->insert_id;
	}
}

?>
