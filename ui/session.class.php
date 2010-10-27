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
		$userid = $this->getStorage()->validLogin($username, $password);
		if ($userid == false) {
			throw new Exception("Login failed.");
		}
		$permissions = $this->getStorage()->getPermissions($userid);
		$this->setAuth(new Auth($userid, $username, $permissions));
	}
	public function logout() {
		$this->setAuth(null);
	}
	public function setAuth($auth) {
		$this->stor["auth"] = $auth;
	}
	public function getAuth() {
		if ($this->stor["auth"] !== null) {
			return $this->stor["auth"];
		}
		// TODO Anonymous-Auth
		return new Auth(null, null, array());
	}

	public function setLang($lang) {
		$this->stor["lang"] = $lang;
	}
	public function getLang() {
		return $this->config->getLang($this->stor["lang"]);
	}

	public function getEncoding() {
		return "UTF-8";
	}

	public function getLink() {
		$params = func_get_args();
		return call_user_func_array(array($this->config, "getLink"), $params);
	}
	public function getStorage() {
		return $this->config->getStorage();
	}
}

?>
