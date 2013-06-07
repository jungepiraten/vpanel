<?php

require_once(VPANEL_CORE . "/textreplacer.class.php");

class MultipleTextReplacer extends TextReplacer {
	private $replacers;

	public function __construct($replacers) {
		if (is_array($replacers)) {
			$this->replacers = $replacers;
		} else {
			$this->replacers = func_get_args();
		}
	}

	public function replaceText($text) {
		foreach ($this->replacers as $replace) {
			$text = $replace->replaceText($text);
		}
		return $text;
	}
}
