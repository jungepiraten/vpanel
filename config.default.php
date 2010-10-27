<?php

define("VPANEL_ROOT", dirname(__FILE__));
define("VPANEL_CORE", VPANEL_ROOT . "/core");
define("VPANEL_UI", VPANEL_ROOT . "/ui");

require_once(VPANEL_UI . "/session.class.php");
require_once(VPANEL_UI . "/language.class.php");

class DefaultConfig {
	public function getSession() {
		return new Session($this);
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

	public function getLink($name) {
		return $this->pages[$name];
	}
	public function registerPage($name, $link) {
		$this->pages[$name] = $link;
	}
}

?>
