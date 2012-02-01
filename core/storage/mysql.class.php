<?php

require_once(VPANEL_STORAGE . "/sql.class.php");

class MySQLStorage extends SQLStorage {
	private $mysqli;

	public function __construct($host, $user, $pass, $db) {
		parent::__construct();
		$this->mysqli = new MySQLi($host, $user, $pass, $db);
		$this->mysqli->set_charset("utf8");
	}

	public function getEncoding() {
		return $this->mysqli->character_set_name();
	}

	public function query($sql) {
		$ret = $this->mysqli->query($sql);
//		var_dump($this->mysqli->error);
		return $ret;
	}

	public function fetchRow($result) {
		return $result->fetch_assoc();
	}

	public function numRows($result) {
		return $result->num_rows;
	}

	public function getInsertID() {
		return $this->mysqli->insert_id;
	}
}

?>
