<?php

require_once(VPANEL_CORE . "/auth.class.php");

class Session {
	private $config;
	private $stor;

	public function __construct($config) {
		session_start();
		$this->config = $config;

		if (!isset($_SESSION["vpanel"])) {
			$_SESSION["vpanel"] = array();
		}
		$this->stor = &$_SESSION["vpanel"];
	}

	public function login($username, $password) {
		$this->setAuth(new Auth($username, $password));
	}
	public function setAuth($auth) {
		$this->stor["auth"] = $auth;
	}
	public function getAuth() {
		return $this->stor["auth"];
	}

	public function setLang($lang) {
		$this->stor["lang"] = $lang;
	}
	public function getLang() {
		return $this->config->getLang($this->stor["lang"]);
	}

	public function getLink($name) {
		return $this->config->getLink($name);
	}
}

?>
