<?php

require_once(VPANEL_CORE . "/sendmailbackend.class.php");

class SleepSendMailBackend extends SendMailBackend {
	public function send(Mail $mail) {
		sleep(0.2);
	}
}

?>
