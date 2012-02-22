<?php

require_once("Net/SMTP.php");

require_once(VPANEL_CORE . "/sendmailbackend.class.php");

class NetSMTPSendMailBackend extends SendMailBackend {
	private $conn;

	public function __construct($host = null, $port = null, $helo = null) {
		$this->conn = new Net_SMTP($host, $port, $helo);
	}
	
	public function send(Mail $mail) {
		$conn->connect();
		$conn->mailFrom($mail->getBounceAddress());
		$conn->rcptTo($mail->getRecipient()->getEMail());
		$conn->data($mail->getRaw());
		$conn->disconnect();
	}
}

?>
