<?php

abstract class SendMailBackend {
	public function send(Mail $mail) {
		// To detect if we get new bounces
		$mail->getRecipient()->setLastSend(time());
		$mail->getRecipient()->save();
		$this->sendMail($mail);
	}

	abstract protected function sendMail(Mail $mail);
}

?>
