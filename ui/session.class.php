<?php

require_once(VPANEL_CORE . "/user.class.php");
require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

interface Session {
	public function generateToken($key);
	public function getFileTokenKey($file);
	public function validToken($key, $token);

	public function isSignedIn();
	public function setUser($user);
	public function getUser();

	public function getAllowedGliederungIDs($permission);
	public function isAllowed($permission, $gliederungid = null);

	public function getLang();
	public function getEncoding();

	public function getDefaultGliederungID();
	public function getDefaultDokumentKategorieID();
	public function getDefaultDokumentStatusID();

	public function addMitgliederMatcher($matcher);
	public function getMitgliederFilter($filterid);
	public function hasMitgliederMatcher($filterid);
	public function getMitgliederMatcher($filterid);

	public function hasVariable($name);
	public function hasFileVariable($name);
	public function getVariable($name);
	public function getDoubleVariable($name);
	public function getIntVariable($name);
	public function getBoolVariable($name);
	public function getListVariable($name);
	public function getFileVariable($name);

	public function getLink();
	public function getStorage();
}

abstract class AbstractSession implements Session {
	private $config;
	private $user;

	public function __construct($config) {
		$this->config = $config;
		$this->initialize();
	}

	abstract protected function initialize();
	abstract protected function hasSessionValue($key);
	abstract protected function getSessionValue($key);
	abstract protected function setSessionValue($key, $value);

	public function generateToken($key) {
		$token = md5($key . "-" . microtime(true) . "-" . rand(1000,9999));
		$this->setSessionValue("token_" . $key, $token);
		return $token;
	}
	public function getFileTokenKey($file) {
		return "file" . $file->getFileID();
	}
	public function validToken($key, $token) {
		return $this->hasSessionValue("token_" . $key) && $this->getSessionValue("token_" . $key) == $token;
	}

	public function isSignedIn() {
		return $this->getUser() != null;
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
	public function setUser($user) {
		if ($user != null) {
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
			$this->user = $user;
			$this->setSessionValue("user", $user);
			$this->setSessionValue("defaultgliederungid", $user->getDefaultGliederungID());
			$this->setSessionValue("defaultdokumentkategorieid", $user->getDefaultDokumentKategorieID());
			$this->setSessionValue("defaultdokumentstatusid", $user->getDefaultDokumentStatusID());
		} else {
			$this->clearPermissions();
			$this->user = null;
			$this->setSessionValue("user", null);
			$this->setSessionValue("defaultgliederungid", null);
			$this->setSessionValue("defaultdokumentkategorieid", null);
			$this->setSessionValue("defaultdokumentstatusid", null);
		}
	}
	public function getUser() {
		if ($this->user == null) {
			if ($this->hasSessionValue("user")) {
				$this->user = $this->getSessionValue("user");
				$this->user->setStorage($this->getStorage());
			}
		}
		return $this->user;
	}

	private function clearPermissions() {
		$this->setSessionValue("permissions", array());
	}
	private function addPermission($permission, $gliederungid) {
		$permissions = array();
		if ($this->hasSessionValue("permissions")) {
			$permissions = $this->getSessionValue("permissions");
		}
		$permissions[$permission->getPermission()->getLabel()][$gliederungid] = true;
		$this->setSessionValue("permissions", $permissions);
	}
	public function getAllowedGliederungIDs($permission) {
		if (! $this->hasSessionValue("permissions") ) {
			return array();
		}
		$permissions = $this->getSessionValue("permissions");
		if (! isset($permissions[$permission])) {
			return array();
		}
		return array_keys($permissions[$permission]);
	}
	public function isAllowed($permission, $gliederungid = null) {
		if ($gliederungid == null) {
			return count($this->getAllowedGliederungIDs($permission)) > 0;
		} else {
			return in_array($gliederungid, $this->getAllowedGliederungIDs($permission));
		}
	}

	public function setLang($lang) {
		$this->setSessionValue("lang", $lang);
	}
	public function getLang() {
		if (! $this->hasSessionValue("lang")) {
			return $this->config->getLang();
		}
		return $this->config->getLang($this->getSessionValue("lang"));
	}

	public function getDefaultGliederungID() {
		return $this->getSessionValue("defaultgliederungid");
	}
	public function getDefaultDokumentKategorieID() {
		return $this->getSessionValue("defaultdokumentkategorieid");
	}
	public function getDefaultDokumentStatusID() {
		return $this->getSessionValue("defaultdokumentstatusid");
	}

	public function addMitgliederMatcher($matcher) {
		$id = "custom" . substr(md5(microtime(true) . "-" . rand(1000,9999)),0,8);
		$this->setSessionValue("mitgliederfilter_" . $id, new MitgliederFilter($id, "Userdefined #" . $id, null, null, $matcher));
		return $this->getMitgliederFilter($id);
	}
	public function getMitgliederFilter($filterid) {
		if ($this->hasSessionValue("mitgliederfilter_" . $filterid)) {
			return $this->getSessionValue("mitgliederfilter_" . $filterid);
		}
		return $this->getStorage()->getMitgliederFilter($filterid);
	}
	public function hasMitgliederMatcher($filterid) {
		return $this->getMitgliederFilter($filterid) != null;
	}
	public function getMitgliederMatcher($filterid) {
		$filter = $this->getMitgliederFilter($filterid);
		if ($filter == null) {
			return null;
		}
		return $filter->getMatcher();
	}

	public function getLink() {
		$params = func_get_args();
		return call_user_func_array(array($this->config, "getLink"), $params);
	}
	public function getStorage() {
		return $this->config->getStorage();
	}
}

abstract class AbstractHTTPSession extends AbstractSession {
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
}

?>
