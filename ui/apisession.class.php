<?php

require_once(VPANEL_UI . "/session.class.php");
require_once(VPANEL_UI . "/apitemplate.class.php");

class APISession extends AbstractHTTPSession {
	private $sessionid;
	private $challenge;
	private $data;

	protected function initialize() {
		$this->sessionid = $this->getVariable("sessionid");

		if ($this->isActive()) {
			$this->data = $this->getStorage()->getSessionData($this->sessionid);
		}
		$this->challenge = $this->getChallenge();
		if ($this->isValidSession()) {
			$this->generateNewChallenge();
		}
	}

	private function generateNewChallenge() {
		$this->setSessionValue("challenge", md5(rand(1000,9999) . $_SERVER["REMOTE_ADDR"] . microtime(true)));
	}

	public function initSession($user) {
		$this->sessionid = null;
		$this->data = array();
		$this->generateNewChallenge();
		$this->setUser($user);
	}

	private function isValidSession() {
		return hash_hmac("md5", $this->challenge, $this->getAPIKey()) == $this->getAuthhash();
	}

	public function isActive() {
		return $this->sessionid != null;
	}

	public function getSessionID() {
		return $this->sessionid;
	}

	private function getAPIKey() {
		if ($this->getUser() != null) {
			return $this->getUser()->getAPIKey();
		} else {
			if (!$this->hasSessionValue("apikey")) {
				$this->setSessionValue("apikey", "");
			}
		}
		return $this->data["apikey"];
	}

	public function getChallenge() {
		if ($this->hasSessionValue("challenge")) {
			return $this->getSessionValue("challenge");
		}
		return null;
	}

	private function getAuthhash() {
		return $this->getVariable("authhash");
	}

	protected function hasSessionValue($key) {
		return isset($this->data[$key]) && $this->getSessionValue($key) != null;
	}
	protected function getSessionValue($key) {
		return $this->data[$key];
	}
	protected function setSessionValue($key, $value) {
		$this->data[$key] = $value;
		$this->sessionid = $this->getStorage()->setSessionData($this->sessionid, time(), $this->data);
	}

	public function isSignedIn() {
		return parent::isSignedIn() && $this->isValidSession();
	}

	public function getEncoding() {
		return "UTF-8";
	}

	public function getTemplate() {
		return new APITemplate($this);
	}
}

?>
