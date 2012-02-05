<?php

require_once(VPANEL_CORE . "/user.class.php");
require_once(VPANEL_UI . "/template.class.php");

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
		$this->santisize();
	}

	public function santisize() {
		if (isset($this->stor["user"]) and $this->stor["user"] != null) {
			$this->stor["user"]->setStorage($this->getStorage());
		}
	}

	public function generateToken($key) {
		if (!isset($this->stor["tokens"])) {
			$this->stor["tokens"] = array();
		}
		$token = md5($key . "-" . microtime(true) . "-" . rand(1000,9999));
		$this->stor["tokens"][$key] = $token;
		return $token;
	}
	public function validToken($key, $token) {
		return (isset($this->stor["tokens"][$key]) && $this->stor["tokens"][$key] == $token);
	}

	public function isSignedIn() {
		return isset($this->stor["user"]) and $this->stor["user"] != null;
	}
	public function login($username, $password) {
		$user = $this->getStorage()->getUserByUsername($username);
		if ($user == null or !$user->isValidPassword($password)) {
			throw new Exception("Login failed.");
		}
		$roles = $user->getRoles();
		$permissions = array();
		foreach ($roles as $role) {
			foreach ($role->getPermissions() as $permission) {
				$permissions[$permission->getPermissionID()] = $permission;
			}
		}
		$this->setUser($user);
		$this->setPermissions($permissions);
		$this->setDefaultDokumentKategorieID($user->getDefaultDokumentKategorieID());
		$this->setDefaultDokumentStatusID($user->getDefaultDokumentStatusID());
	}
	public function logout() {
		$this->setUser(null);
		$this->setPermissions(null);
	}
	public function setUser($user) {
		$this->stor["user"] = $user;
	}
	public function getUser() {
		if ($this->isSignedIn()) {
			return $this->stor["user"];
		}
		// TODO anonymous auth
		return new User($this->getStorage());
	}

	public function getPermissions() {
		if (!isset($this->stor["permissions"])) {
			return array();
		}
		return $this->stor["permissions"];
	}
	public function setPermissions($permissions) {
		$this->stor["permissions"] = $permissions;
	}
	public function isAllowed($permission) {
		if (!is_array($this->getPermissions())) {
			return false;
		}
		foreach ($this->getPermissions() as $perm) {
			if ($perm->getLabel() == $permission) {
				return true;
			}
		}
		return false;
	}

	public function setLang($lang) {
		$this->stor["lang"] = $lang;
	}
	public function getLang() {
		if (!isset($this->stor["lang"])) {
			return $this->config->getLang();
		}
		return $this->config->getLang($this->stor["lang"]);
	}

	public function setDefaultDokumentKategorieID($kategorieid) {
		$this->stor["defaultdokumentkategorieid"] = $kategorieid;
	}
	public function getDefaultDokumentKategorieID() {
		return $this->stor["defaultdokumentkategorieid"];
	}
	public function setDefaultDokumentStatusID($statusid) {
		$this->stor["defaultdokumentstatusid"] = $statusid;
	}
	public function getDefaultDokumentStatusID() {
		return $this->stor["defaultdokumentstatusid"];
	}

	public function getEncoding() {
		return "UTF-8";
	}
	public function hasVariable($name) {
		return isset($_REQUEST[$name]);
	}
	public function hasFileVariable($name) {
		return isset($_FILES[$name]) && $_FILES[$name]["error"] == 0;
	}
	public function getVariable($name) {
		return iconv($this->getEncoding(), "UTF-8", stripslashes($_REQUEST[$name]));
	}
	public function getDoubleVariable($name) {
		return doubleval($_REQUEST[$name]);
	}
	public function getIntVariable($name) {
		return intval($_REQUEST[$name]);
	}
	public function getBoolVariable($name) {
		return isset($_REQUEST[$name]) && $_REQUEST[$name];
	}
	public function getListVariable($name) {
		return $_REQUEST[$name];
	}
	public function getFileVariable($name) {
		if (!$this->hasFileVariable($name)) {
			return null;
		}
		$file = new File($this->getStorage());
		$file->setExportFilename($_FILES[$name]["name"]);
		$file->setMimeType($_FILES[$name]["type"]);
		if (!move_uploaded_file($_FILES[$name]["tmp_name"], $file->getAbsoluteFilename())) {
			return null;
		}
		$file->save();
		return $file;
	}
	public function encodeString($string) {
		return iconv("UTF-8", $this->getEncoding(), $string);
	}

	public function getLink() {
		$params = func_get_args();
		return call_user_func_array(array($this->config, "getLink"), $params);
	}
	public function getStorage() {
		return $this->config->getStorage();
	}
	public function getTemplate() {
		return new Template($this);
	}
}

?>
