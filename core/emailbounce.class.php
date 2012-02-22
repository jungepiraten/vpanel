<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class EMailBounce extends StorageClass {
	private $bounceid;
	private $emailid;
	private $timestamp;
	private $message;

	private $email;

	public static function factory(Storage $storage, $row) {
		$bounce = new EMailBounce($storage);
		$bounce->setBounceID($row["bounceid"]);
		$bounce->setEMailID($row["emailid"]);
		$bounce->setTimestamp($row["timestamp"]);
		$bounce->setMessage($row["message"]);
		return $bounce;
	}

	public function getBounceID() {
		return $this->bounceid;
	}

	public function setBounceID($bounceid) {
		$this->bounceid = $bounceid;
	}

	public function getEMailID() {
		return $this->emailid;
	}

	public function setEMailID($emailid) {
		if ($this->emailid != $emailid) {
			$this->email = null;
		}
		$this->emailid = $emailid;
	}

	public function getEMail() {
		if ($this->email == null) {
			$this->email = $this->getStorage()->getEMail($this->emailid);
		}
		return $this->email;
	}

	public function setEMail($email) {
		$this->setEMailID($email->getEMailID());
		$this->email = $email;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	public function getMessage() {
		return $this->message;
	}

	public function setMessage($message) {
		$this->message = $message;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setBounceID( $storage->setEMailBounce(
			$this->getBounceID(),
			$this->getEMailID(),
			$this->getTimestamp(),
			$this->getMessage() ));
	}
}

?>
