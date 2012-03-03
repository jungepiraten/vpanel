<?php

class APITemplate {
	private $session;

	public function __construct($session) {
		$this->session = $session;
	}

	public function output($result = null) {
		$data = array();
		if ($this->session->isActive()) {
			$data["sessionid"] = $this->session->getSessionID();
			$data["challenge"] = $this->session->getChallenge();
		}
		if ($result != null) {
			$data["result"] = $result;
		}
		print(json_encode($data));
	}
}

?>
