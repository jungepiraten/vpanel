<?php

require_once(VPANEL_CORE . "/textreplacer.class.php");

abstract class VariableTextReplacer extends TextReplacer {
	public function replaceText($text) {
		// Suche alle vorkommenden Variablen ab
		preg_match_all('/\\{(.*?)\\}/', $text, $matches);
		$keywords = array_unique($matches[1]);
		foreach ($keywords as $keyword) {
			$text = str_replace("{" . $keyword . "}", $this->getVariableValue($keyword), $text);
		}
		return $text;
	}

	abstract protected function getVariableValue($keyword);
}
