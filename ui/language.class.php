<?php

interface Language {
	public function getEncoding();
	public function hasString($str);
	public function getString($str);
}

abstract class AbstractLanguage implements Language {
	private $lang = array();

	public function getEncoding() {
		return "UTF-8";
	}

	public function hasString($str) {
		return isset($this->lang[$str]);
	}

	public function getString($str) {
		return $this->lang[$str];
	}

	protected function setLang($lang) {
		$this->lang = $lang;
	}
}

class EmptyLanguage extends AbstractLanguage {
}

class PHPLanguage extends AbstractLanguage {
	public function __construct($filename) {
		$lang = array();
		include($filename);
		$this->setLang($lang);
	}
}

?>
