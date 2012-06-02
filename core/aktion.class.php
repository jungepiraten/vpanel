<?php

abstract class Aktion {
	private $label;
	private $permission;

	private $storage;

	public function __construct($label, $permission) {
		$this->label = $label;
		$this->permission = $permission;
	}

	public function getStorage() {
		return $this->storage;
	}

	public function setStorage($storage) {
		$this->storage = $storage;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getPermission() {
		return $this->permission;
	}

	public function isAllowed($session, $gliederungid = null) {
		return $session->isAllowed($this->getPermission(), $gliederungid);
	}
}

abstract class GliederungAktion extends Aktion {
	private $gliederungid;

	private $gliederung;

	public function __construct($label, $permission, $gliederungid) {
		parent::__construct($label, $permission);
		$this->gliederungid = $gliederungid;
	}

	public function isAllowed($session) {
		return parent::isAllowed($session, $this->getGliederungID());
	}

	public function getGliederungID() {
		return $this->gliederungid;
	}

	public function getGliederung() {
		if ($this->gliederung == null) {
			$this->gliederung = $this->getStorage()->getGliederung($this->getGliederungID());
		}
		return $this->gliederung;
	}
}

?>
