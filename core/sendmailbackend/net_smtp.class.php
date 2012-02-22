<?php

require_once("Net/SMTP.php");

require_once(VPANEL_CORE . "/sendmailbackend.class.php");

class NetSMTPSendMailBackend extends SendMailBackend {
	private $conn;

	public function __construct($host = null, $port = null, $helo = null) {
		$this->conn = new Net_SMTP($host, $port, $helo);
	}
	
	public function send(Mail $mail) {
		$this->conn->connect();
		$this->conn->mailFrom($mail->getBounceAddress());
		$this->conn->rcptTo($mail->getRecipient()->getEMail());
		$this->conn->data($mail->getRaw());
		$this->conn->disconnect();
	}
}

?>
