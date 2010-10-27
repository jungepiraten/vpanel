<?php

interface Language {
	public function getEncoding();
	public function hasString($str);
	public function getString($str);
}

class EmptyLanguage implements Language {
	public function getEncoding() {
		return "UTF-8";
	}

	public function hasString($str) {
		return false;
	}

	public function getString($str) {
		return null;
	}
}

?>
