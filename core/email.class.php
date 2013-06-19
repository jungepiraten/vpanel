<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class EMail extends StorageClass {
	private $emailid;
	private $email;
	private $gpgfingerprint;
	private $lastSend;

	private $bounces;

	public static function factory(Storage $storage, $row) {
		$email = new EMail($storage);
		$email->setEMailID($row["emailid"]);
		$email->setEMail($row["email"]);
		$email->setGPGFingerprint($row["gpgfingerprint"]);
		$email->setLastSend($row["lastSend"]);
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

	public function getGPGFingerprint() {
		return $this->gpgfingerprint;
	}

	public function setGPGFingerprint($gpgfingerprint) {
		$this->gpgfingerprint = $gpgfingerprint;
	}

	public function getLastSend() {
		return $this->lastSend;
	}

	public function setLastSend($lastSend) {
		$this->lastSend = $lastSend;
	}

	public function getBounces() {
		if ($this->bounces == null) {
			$this->bounces = $this->getStorage()->getEMailBounceListByEMail($this->getEMailID());
		}
		return $this->bounces;
	}

	public function getNewBounces() {
		return array_filter($this->getBounces(), create_function('$bounce', ($this->getLastSend() == null ? 'return true;' : 'return $bounce->getTimestamp() > ' . $this->getLastSend() . ';')));
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setEMailID( $storage->setEMail(
			$this->getEMailID(),
			$this->getEMail(),
			$this->getGPGID(),
			$this->getLastSend() ));
	}

	public function delete(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$bounces = $storage->getEMailBounceListByEMail($this->getEMailID());
		foreach ($bounces as $bounce) {
			$bounce->delete($storage);
		}
		$storage->delEMail($this->getEMailID());
	}
}

?>
