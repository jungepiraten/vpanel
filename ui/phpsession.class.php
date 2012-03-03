<?php

require_once(VPANEL_UI . "/session.class.php");
require_once(VPANEL_UI . "/template.class.php");

class PHPSession extends AbstractHTTPSession {
	protected function initialize() {
		session_start();
		if (!isset($_SESSION["vpanel"])) {
			$_SESSION["vpanel"] = array();
		}
	}

	protected function hasSessionValue($key) {
		return isset($_SESSION["vpanel"][$key]) && $this->getSessionValue($key) != null;
	}
	protected function getSessionValue($key) {
		return unserialize($_SESSION["vpanel"][$key]);
	}
	protected function setSessionValue($key, $value) {
		$_SESSION["vpanel"][$key] = serialize($value);
	}

	public function getTemplate() {
		return new Template($this);
	}
}

?>
