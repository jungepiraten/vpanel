<?php

abstract class SendMailBackend {
	abstract public function send(Mail $mail);
}

?>
