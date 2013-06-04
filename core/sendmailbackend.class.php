<?php

abstract class SendMailBackend {
	public function send(Mail $mail) {
		$this->sendMail($mail);
	}

	abstract protected function sendMail(Mail $mail);
}

?>
