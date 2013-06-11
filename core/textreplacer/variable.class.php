<?php

require_once(VPANEL_CORE . "/textreplacer.class.php");

abstract class VariableTextReplacer extends TextReplacer {
	public function replaceText($text) {
		// Suche alle vorkommenden Variablen ab
		preg_match_all('/\\{(.*?)\\}/', $text, $matches);
		$keywords = array_unique($matches[1]);
		foreach ($keywords as $keyword) {
			// {VORNAME/FIRMA} prüft zuerst {VORNAME} und dannach {FIRMA}
			$words = explode("/", $keyword);
			$replace = null;
			for ($i=0; $i<count($words) && $replace == null; $i++) {
				$replace = $this->getVariableValue($words[$i]);
			}
			if ($replace !== null) {
				$text = str_replace("{" . $keyword . "}", $replace, $text);
			}
		}
		return $text;
	}

	abstract protected function getVariableValue($keyword);
}
