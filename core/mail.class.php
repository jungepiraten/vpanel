<?php

class Mail {
	private $headers;
	private $body;
	private $attachments;

	private $boundary;
	private $mailfrom;
	private $fromlabel;

	public function __construct($headers = array(), $body = "", $attachments = array()) {
		$this->headers = $headers;
		$this->body = $body;
		$this->attachments = $attachments;
	}

	public function setRecipient($mail, $label = null) {
		$this->mailfrom = $mail;
		$this->fromlabel = $label;
	}

	public function getRecipient() {
		$rcpt = "";
		if ($this->fromlabel != null) {
			$rcpt .= $this->fromlabel . " ";
		}
		$rcpt .= "<" . $this->mailfrom . ">";
		return $rcpt;
	}

	public function setHeader($field, $value) {
		$this->headers[$field] = $value;
	}

	public function getHeaders() {
		return $this->headers;
	}

	public function setBody($body) {
		$this->body = $body;
	}

	public function getBody() {
		return $this->body;
	}

	public function addAttachment($attachment) {
		$this->attachments[] = $attachment;
	}

	public function getAttachments() {
		return $this->attachments;
	}

	public function isMultipart() {
		return count($this->getAttachments()) > 0;
	}

	public function getBoundary() {
		if ($this->boundary == null) {
			$this->boundary = "---vpanel" . md5(microtime());
		}
		return $this->boundary;
	}

	/**
	 * Gebe hier RFC-Kompatible Kodierung zurueck
	 * Zumindest fast. Weil einige Mailserver versagen, benutzen wir nur \n statt \r\n
	 **/
	public function getRaw() {
		$raw = "";
		$charset = "UTF-8";
		
		$raw .= "User-Agent: VPanel Mailer" . "\n";
		$raw .= "To: " . mb_encode_mimeheader($this->getRecipient(), $charset) . "\n";
		if ($this->isMultipart()) {
			$raw .= "MIME-Version: 1.0" . "\n";
			$raw .= "Content-Type: multipart/mixed; boundary=" . $this->getBoundary() . "\n";
		} else {
			$raw .= "Content-Type: text/plain; charset=" . $charset . "\n";
			$raw .= "Content-Transfer-Encoding: base64" . "\n";
		}

		$headers = $this->getHeaders();
		mb_internal_encoding("UTF-8");
		foreach ($headers as $key => $value) {
			$raw .= $key . ": " . mb_encode_mimeheader($value, $charset) . "\n";
		}
		$raw .= "\n";
		
		if ($this->isMultipart()) {
			$raw .= "This is a multipart message." . "\n";
			$raw .= "--" . $this->getBoundary() . "\n";
			$raw .= "Content-Type: text/plain; charset=" . $charset . "\n";
			$raw .= "Content-Transfer-Encoding: base64" . "\n";
			$raw .= "\n";
			$raw .= chunk_split(base64_encode($this->getBody()), 78, "\n");
			foreach ($this->getAttachments() as $attachment) {
				$raw .= "--" . $this->getBoundary() . "\n";
				$raw .= "Content-Type: " . $attachment->getMimeType() . "\n";
				$raw .= "Content-Disposition: inline; filename=\"" . addslashes(mb_encode_mimeheader($attachment->getExportFilename(), $charset)) . "\"\n";
				$raw .= "Content-Transfer-Encoding: base64" . "\n";
				$raw .= "\n";
				$raw .= chunk_split(base64_encode($attachment->getContent()), 78, "\n");
			}
			$raw .= "--" . $this->getBoundary() . "--" . "\n";
		} else {
			$raw .= chunk_split(base64_encode($this->getBody()), 78, "\n");
		}
		
		return $raw;
	}
}

?>
