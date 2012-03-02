<?php

require_once(VPANEL_CORE . "/user.class.php");
require_once(VPANEL_CORE . "/mitgliederfilter.class.php");
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
	private function buildGliederungChildList($gliederung, &$gliederungChilds) {
		$gliederungid = $gliederung->getGliederungID();
		$parentid = $gliederung->getParentID();

		$gliederungen[$gliederungid] = $gliederung;
		if (!isset($gliederungChilds[$gliederungid])) {
			$gliederungChilds[$gliederungid] = array();
		}
		if ($parentid != null) {
			if (!isset($gliederungChilds[$parentid])) {
				$gliederungChilds[$parentid] = array();
			}
			$gliederungChilds[$parentid][] = $gliederung->getGliederungID();
			$this->buildGliederungChildList($gliederung->getParent(), $gliederungChilds);
		}
	}
	public function login($username, $password) {
		$user = $this->getStorage()->getUserByUsername($username);
		if ($user == null or !$user->isValidPassword($password)) {
			throw new Exception("Login failed.");
		}
		$gliederungen = array();
		$gliederungChilds = array(NULL => array());
		foreach ($this->getStorage()->getGliederungList() as $gliederung) {
			$gliederungen[$gliederung->getGliederungID()] = $gliederung;
			$this->buildGliederungChildList($gliederung, $gliederungChilds);
		}
		$roles = $user->getRoles();
		$permissions = array();
		foreach ($roles as $role) {
			foreach ($role->getPermissions() as $permission) {
				if ($permission->isTransitive()) {
					foreach ($gliederungChilds[$permission->getGliederungID()] as $childid) {
						$this->addPermission($permission, $childid);
					}
				}
				$this->addPermission($permission, $permission->getGliederungID());
			}
		}
		$this->setUser($user);
		$this->setDefaultDokumentKategorieID($user->getDefaultDokumentKategorieID());
		$this->setDefaultDokumentStatusID($user->getDefaultDokumentStatusID());
	}
	public function logout() {
		$this->setUser(null);
		$this->stor["permissions"] = array();
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

	private function addPermission($permission, $gliederungid) {
		if (!isset($this->stor["permissions"])) {
			$this->stor["permissions"] = array();
		}
		$this->stor["permissions"][$permission->getPermission()->getLabel()][$gliederungid] = true;
	}
	public function getAllowedGliederungIDs($permission) {
		if (!isset($this->stor["permissions"][$permission])) {
			return array();
		}
		return array_keys($this->stor["permissions"][$permission]);
	}
	public function isAllowed($permission, $gliederungid = null) {
		if ($gliederungid == null) {
			return isset($this->stor["permissions"][$permission]) && count($this->stor["permissions"][$permission]) > 0;
		} else {
			return isset($this->stor["permissions"][$permission][$gliederungid]);
		}
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

	public function addMitgliederMatcher($matcher) {
		$id = "custom" . substr(md5(microtime(true) . "-" . rand(1000,9999)),0,8);
		$this->stor["mitgliederfilter"][$id] = serialize(new MitgliederFilter($id, "Userdefined #" . $id, null, $matcher));
		return $this->getMitgliederFilter($id);
	}
	public function getMitgliederFilter($filterid) {
		if (isset($this->stor["mitgliederfilter"][$filterid])) {
			return unserialize($this->stor["mitgliederfilter"][$filterid]);
		}
		return $this->getStorage()->getMitgliederFilter($filterid);
	}
	public function getMitgliederMatcher($filterid) {
		$filter = $this->getMitgliederFilter($filterid);
		if ($filter == null) {
			return null;
		}
		return $filter->getMatcher();
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
		if (!$this->hasVariable($name)) {
			return null;
		}
		return iconv($this->getEncoding(), "UTF-8", stripslashes($_REQUEST[$name]));
	}
	public function getDoubleVariable($name) {
		if (!$this->hasVariable($name)) {
			return null;
		}
		return doubleval($_REQUEST[$name]);
	}
	public function getIntVariable($name) {
		if (!$this->hasVariable($name)) {
			return null;
		}
		return intval($_REQUEST[$name]);
	}
	public function getBoolVariable($name) {
		return isset($_REQUEST[$name]) && $_REQUEST[$name];
	}
	public function getListVariable($name) {
		if (!$this->hasVariable($name)) {
			return array();
		}
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
