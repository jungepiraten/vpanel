<?php

require_once(VPANEL_TEXTREPLACER . "/variable.class.php");

class StaticTextReplacer extends VariableTextReplacer {
	private $lookup;

	public function __construct($lookup = array()) {
		$this->lookup = $lookup;
	}

	protected function getVariableValue($keyword) {
		return $this->lookup[$keyword];
	}
}
