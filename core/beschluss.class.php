<?php

require_once(dirname(__FILE__) . "/abstimmung.class.php");

class Beschluss {
	private $titel;
	private $abstimmung;

	public function __construct($titel) {
		$this->titel = $titel;
		$this->abstimmung = new Abstimmung;
	}
}

?>
