<?php

require_once(VPANEL_CORE . "/sendmailbackend.class.php");

class PHPSendMailBackend extends SendMailBackend {
	public function send(Mail $mail) {
		$raw = $mail->getRaw();
		list($header, $body) = preg_split("#\r?\n\r?\n#", $raw, 2);
		mail(null, null, $body, $header, "-f " . $mail->getBounceAddress());
	}
}

?>
