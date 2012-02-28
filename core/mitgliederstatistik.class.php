<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliederStatistik extends StorageClass {
	private $statistikid;
	private $timestamp;
	private $agegraphfileid;
	private $timegraphfileid;

	private $agegraphfile;
	private $timegraphfile;
	
	public static function factory(Storage $storage, $row) {
		$statistik = new MitgliederStatistik($storage);
		$statistik->setStatistikID($row["statistikid"]);
		$statistik->setTimestamp($row["timestamp"]);
		$statistik->setAgeGraphFileID($row["agegraphfileid"]);
		$statistik->setTimeGraphFileID($row["timegraphfileid"]);
		return $statistik;
	}

	public function setStatistikID($statistikid) {
		$this->statistikid = $statistikid;
	}

	public function getStatistikID() {
		return $this->statistikid;
	}

	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function getMitgliederCountScale() {
		return 24*60*60;
	}

	public function getMitgliederCountStart() {
		return $this->getMitgliederCountEnd() - 5*365*24*60*60;
	}

	public function getMitgliederCountEnd() {
		return floor($this->getTimestamp() / $this->getMitgliederCountScale()) * $this->getMitgliederCountScale();
	}

	public function getMitgliederAgeMinimum() {
		return 0;
	}

	public function getMitgliederAgeMaximum() {
		return 70;
	}

	public function setAgeGraphFileID($fileid) {
		if ($fileid != $this->agegraphfileid) {
			$this->agegraphfile = null;
		}
		$this->agegraphfileid = $fileid;
	}

	public function getAgeGraphFileID() {
		return $this->agegraphfileid;
	}

	public function setAgeGraphFile($file) {
		$this->setAgeGraphFileID($file->getFileID());
		$this->agegraphfile = $file;
	}
	
	public function getAgeGraphFile() {
		if ($this->agegraphfile == null) {
			$this->agegraphfile = $this->getStorage()->getFile($this->getAgeGraphFileID());
		}
		return $this->agegraphfile;
	}

	public function setTimeGraphFileID($fileid) {
		if ($fileid != $this->timegraphfileid) {
			$this->timegraphfile = null;
		}
		$this->timegraphfileid = $fileid;
	}

	public function getTimeGraphFileID() {
		return $this->timegraphfileid;
	}

	public function setTimeGraphFile($file) {
		$this->setTimeGraphFileID($file->getFileID());
		$this->timegraphfile = $file;
	}
	
	public function getTimeGraphFile() {
		if ($this->timegraphfile == null) {
			$this->timegraphfile = $this->getStorage()->getFile($this->getTimeGraphFileID());
		}
		return $this->timegraphfile;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setStatistikID($storage->setMitgliederStatistik(
			$this->getStatistikID(),
			$this->getTimestamp(),
			$this->getAgeGraphFileID(),
			$this->getTimeGraphFileID() ));
	}
}

?>
