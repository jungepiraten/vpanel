<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MailAttachment extends StorageClass {
	private $attachmentid;
	private $filename;
	private $mimetype;
	private $content;

	public static function factory(Storage $storage, $row) {
		$attachment = new MailAttachment($storage);
		$attachment->setAttachmentID($row["attachmentid"]);
		$attachment->setFilename($row["filename"]);
		$attachment->setMimetype($row["mimetype"]);
		$attachment->setContent($row["content"]);
		return $attachment;
	}

	public function getAttachmentID() {
		return $this->attachmentid;
	}

	public function setAttachmentID($attachmentid) {
		$this->attachmentid = $attachmentid;
	}

	public function getFilename() {
		return $this->filename;
	}

	public function setFilename($filename) {
		$this->filename = $filename;
	}

	public function getMimeType() {
		return $this->mimetype;
	}

	public function setMimeType($mimetype) {
		$this->mimetype = $mimetype;
	}

	public function getContent() {
		return $this->content;
	}

	public function setContent($content) {
		$this->content = $content;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setAttachmentID( $storage->setMailAttachment(
			$this->getAttachmentID(),
			$this->getFilename(),
			$this->getMimeType(),
			$this->getContent() ));
	}
}

?>
