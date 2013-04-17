<?php

require_once(VPANEL_CORE . "/sendmailbackend.class.php");

class SleepSendMailBackend extends SendMailBackend {
	protected function sendMail(Mail $mail) {
		sleep(0.2);
	}
}

?>
