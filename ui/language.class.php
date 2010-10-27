<?php

interface Language {
	public function hasString($str);
	public function getString($str);
}

class EmptyLanguage implements Language {
	public function hasString($str) {
		return false;
	}

	public function getString($str) {
		return null;
	}
}

?>
