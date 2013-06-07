<?php

require_once(VPANEL_CORE . "/textreplacer.class.php");

class NullTextReplacer extends TextReplacer {
	public function replaceText($text) {
		return $text;
	}
}
