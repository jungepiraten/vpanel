<?php

require_once(VPANEL_CORE . "/storageobject.class.php");
require_once(VPANEL_CORE . "/beitrag.class.php");

class BeitragTimeFormat extends StorageClass {
	private $beitragtimeformatid;
	private $label;
	private $format;

	public static function factory(Storage $storage, $row) {
		$beitragtimeformat = new BeitragTimeFormat($storage);
		$beitragtimeformat->setBeitragTimeFormatID($row["beitragtimeformatid"]);
		$beitragtimeformat->setLabel($row["label"]);
		$beitragtimeformat->setFormat($row["format"]);
		return $beitragtimeformat;
	}

	public function getBeitragTimeFormatID() {
		return $this->beitragtimeformatid;
	}

	public function setBeitragTimeFormatID($beitragtimeformatid) {
		$this->beitragtimeformatid = $beitragtimeformatid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getFormat() {
		return $this->format;
	}

	public function setFormat($format) {
		$this->format = $format;
	}

	public function getBeitrag($timestamp = null) {
		if ($timestamp == null) {
			$timestamp = time();
		}
		$beitrag = $this->getStorage()->searchBeitrag(strftime($this->getFormat(), $timestamp));
		if ($beitrag == null) {
			$beitrag = new Beitrag($this->getStorage());
			$beitrag->setLabel(strftime($this->getFormat(), $timestamp));
			$beitrag->setHoehe(null);
			$beitrag->save();
		}
		return $beitrag;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setBeitragTimeFormatID( $storage->setBeitragTimeFormat(
			$this->getBeitragTimeFormatID(),
			$this->getLabel(),
			$this->getFormat() ));
	}

	public function delete(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$storage->delBeitragTimeFormat($this->getBeitragTimeFormatID());
	}
}

?>
