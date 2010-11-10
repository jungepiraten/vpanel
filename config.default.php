<?php

define("VPANEL_ROOT",		dirname(__FILE__));
define("VPANEL_CORE",		VPANEL_ROOT . "/core");
define("VPANEL_STORAGE",	VPANEL_CORE . "/storage");
define("VPANEL_UI",		VPANEL_ROOT . "/ui");
define("VPANEL_LANGUAGE",	VPANEL_UI . "/language");

require_once(VPANEL_UI . "/session.class.php");
require_once(VPANEL_UI . "/language.class.php");
require_once(VPANEL_STORAGE . "/mysql.class.php");

class DefaultConfig {
	public function getSession() {
		return new Session($this);
	}

	/** Fuer GlobaleIDs **/
	protected function getHostPart() {
		return "example.org";
	}
	public function generateGlobalID() {
		return uniqid("", true) . "@" . $this->getHostPart();
	}

	/** Mehrsprachen-Support **/
	private $langs;
	public function getLang($lang) {
		if (isset($this->langs[$lang])) {
			return $this->langs[$lang];
		}
		return current($this->langs);
	}
	public function registerLang($name, Language $lang) {
		$this->langs[$name] = $lang;
	}

	private $pages;
	public function getLink() {
		$params = func_get_args();
		$name = array_shift($params);
		return vsprintf($this->pages[$name], $params);
	}
	public function registerPage($name, $link) {
		$this->pages[$name] = $link;
	}

	private $storage;
	public function setStorage($storage) {
		$this->storage = $storage;
	}
	public function getStorage() {
		return $this->storage;
	}
}

?>
