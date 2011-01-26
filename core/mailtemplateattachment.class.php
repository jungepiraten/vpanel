<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MailTemplateAttachment extends StorageClass {
	private $templateid;
	private $attachmentid;
	private $attachment;

	public static function factory(Storage $storage, $row) {
		$attachment = new MailTemplateHeader($storage);
		$attachment->setTemplateID($row["templateid"]);
		$attachment->setAttachmentID($row["attachmentid"]);
		return $attachment;
	}

	public function __construct(Storage $storage, $templateid = null, $attachmentid = null) {
		parent::__construct($storage);
		if ($templateid != null) {
			$this->setTemplateID($templateid);
		}
		if ($attachmentid != null) {
			$this->setAttachmentID($attachmentid);
		}
	}

	public function getTemplateID() {
		return $this->templateid;
	}

	public function setTemplateID($templateid) {
		$this->templateid = $templateid;
	}

	public function getAttachmentID() {
		return $this->attachmentid;
	}

	public function setField($attachmentid) {
		if ($this->attachmentid != $attachmentid) {
			$this->attachment = null;
		}
		$this->attachmentid = $attachmentid;
	}

	public function getAttachment() {
		if ($this->attachment == null) {
			$this->attachment = $this->getStorage()->getAttachment($this->getAttachmentID());
		}
		return $this->attachment;
	}

	public function setAttachment(MailAttachment $attachment) {
		$this->setAttachmentID($attachment->getAttachmentID());
		$this->attachment = $attachment;
	}
}

?>
