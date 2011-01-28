<?php

class Mail {
	private $template;
	private $replace;

	public function __construct(MailTemplate $template) {
		$this->template = $template;
		$this->replace = array();
	}

	public function addReplace($pattern, $text) {
		$this->replace[$pattern] = $text;
	}

	private function replaceText($text) {
		return str_replace($text, $this->replace);
	}

	/**
	 * Gebe hier RFC-Kompatible Kodierung zurueck
	 **/
	public function getRawHeaders() {
		
	}

	public function getRawBody() {
		return $this->replaceText($this->template->getBody());
	}
}

?>
