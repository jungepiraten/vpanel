<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliederStatistik extends StorageClass {
	private $statistikid;
	private $userid;
	private $timestamp;
	private $agegraphfileid;
	private $timegraphfileid;
	private $timebalancegraphfileid;
	private $gliederungchartfileid;
	private $statechartfileid;
	private $mitgliedschaftchartfileid;

	private $user;
	private $agegraphfile;
	private $timegraphfile;
	private $timebalancegraphfile;
	private $gliederungchartfile;
	private $statechartfile;
	private $mitgliedschaftchartfile;

	public static function factory(Storage $storage, $row) {
		$statistik = new MitgliederStatistik($storage);
		$statistik->setStatistikID($row["statistikid"]);
		$statistik->setUserID($row["userid"]);
		$statistik->setTimestamp($row["timestamp"]);
		$statistik->setAgeGraphFileID($row["agegraphfileid"]);
		$statistik->setTimeGraphFileID($row["timegraphfileid"]);
		$statistik->setTimeBalanceGraphFileID($row["timebalancegraphfileid"]);
		$statistik->setGliederungChartFileID($row["gliederungchartfileid"]);
		$statistik->setStateChartFileID($row["statechartfileid"]);
		$statistik->setMitgliedschaftChartFileID($row["mitgliedschaftchartfileid"]);
		return $statistik;
	}

	public function setStatistikID($statistikid) {
		$this->statistikid = $statistikid;
	}

	public function getStatistikID() {
		return $this->statistikid;
	}

	public function setUserID($userid) {
		if ($this->userid != $userid) {
			$this->user = null;
		}
		$this->userid = $userid;
	}

	public function getUserID() {
		return $this->userid;
	}

	public function setUser($user) {
		$this->setUserID($user->getUserID());
		$this->user = $user;
	}

	public function getUser() {
		if ($this->user == null) {
			$this->user = $this->getStorage()->getUser($this->getUserID());
		}
		return $this->user;
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
		return 100;
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

	public function setTimeBalanceGraphFileID($fileid) {
		if ($fileid != $this->timebalancegraphfileid) {
			$this->timebalancegraphfile = null;
		}
		$this->timebalancegraphfileid = $fileid;
	}

	public function getTimeBalanceGraphFileID() {
		return $this->timebalancegraphfileid;
	}

	public function setTimeBalanceGraphFile($file) {
		$this->setTimeBalanceGraphFileID($file->getFileID());
		$this->timebalancegraphfile = $file;
	}
	
	public function getTimeBalanceGraphFile() {
		if ($this->timebalancegraphfile == null) {
			$this->timebalancegraphfile = $this->getStorage()->getFile($this->getTimeBalanceGraphFileID());
		}
		return $this->timebalancegraphfile;
	}

	public function setGliederungChartFileID($fileid) {
		if ($fileid != $this->gliederungchartfileid) {
			$this->gliederungchartfile = null;
		}
		$this->gliederungchartfileid = $fileid;
	}

	public function getGliederungChartFileID() {
		return $this->gliederungchartfileid;
	}

	public function setGliederungChartFile($file) {
		$this->setGliederungChartFileID($file->getFileID());
		$this->gliederungchartfile = $file;
	}
	
	public function getGliederungChartFile() {
		if ($this->gliederungchartfile == null) {
			$this->gliederungchartfile = $this->getStorage()->getFile($this->getGliederungChartFileID());
		}
		return $this->gliederungchartfile;
	}

	public function setStateChartFileID($fileid) {
		if ($fileid != $this->statechartfileid) {
			$this->statechartfile = null;
		}
		$this->statechartfileid = $fileid;
	}

	public function getStateChartFileID() {
		return $this->statechartfileid;
	}

	public function setStateChartFile($file) {
		$this->setStateChartFileID($file->getFileID());
		$this->statechartfile = $file;
	}
	
	public function getStateChartFile() {
		if ($this->statechartfile == null) {
			$this->statechartfile = $this->getStorage()->getFile($this->getStateChartFileID());
		}
		return $this->statechartfile;
	}

	public function setMitgliedschaftChartFileID($fileid) {
		if ($fileid != $this->mitgliedschaftchartfileid) {
			$this->mitgliedschaftchartfile = null;
		}
		$this->mitgliedschaftchartfileid = $fileid;
	}

	public function getMitgliedschaftChartFileID() {
		return $this->mitgliedschaftchartfileid;
	}

	public function setMitgliedschaftChartFile($file) {
		$this->setMitgliedschaftChartFileID($file->getFileID());
		$this->mitgliedschaftchartfile = $file;
	}
	
	public function getMitgliedschaftChartFile() {
		if ($this->mitgliedschaftchartfile == null) {
			$this->mitgliedschaftchartfile = $this->getStorage()->getFile($this->getMitgliedschaftChartFileID());
		}
		return $this->mitgliedschaftchartfile;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setStatistikID($storage->setMitgliederStatistik(
			$this->getStatistikID(),
			$this->getUserID(),
			$this->getTimestamp(),
			$this->getAgeGraphFileID(),
			$this->getTimeGraphFileID(),
			$this->getTimeBalanceGraphFileID(),
			$this->getGliederungChartFileID(),
			$this->getStateChartFileID(),
			$this->getMitgliedschaftChartFileID() ));
	}
}

?>
