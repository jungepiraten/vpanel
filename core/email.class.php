<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class EMail extends StorageClass {
	private $emailid;
	private $email;

	private $bounces;

	public static function factory(Storage $storage, $row) {
		$email = new EMail($storage);
		$email->setEMailID($row["emailid"]);
		$email->setEMail($row["email"]);
		return $email;
	}

	public function getEMailID() {
		return $this->emailid;
	}

	public function setEMailID($emailid) {
		$this->emailid = $emailid;
	}

	public function getEMail() {
		return $this->email;
	}

	public function setEMail($email) {
		$this->email = $email;
	}

	public function getBounces() {
		if ($this->bounces == null) {
			$this->bounces = $this->getStorage()->getEMailBounceListByEMail($this->getEMailID());
		}
		return $this->bounces;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setEMailID( $storage->setEMail(
			$this->getEMailID(),
			$this->getEMail() ));
	}
}

?>
