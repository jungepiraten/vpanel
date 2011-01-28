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
		return str_replace(array_keys($this->replace), array_values($this->replace), $text);
	}

	public function getHeaders() {
		$headers = array();
		foreach ($this->template->getHeaders() as $header) {
			$headers[$header->getField()] = $this->replaceText($header->getValue());
		}
		return $headers;
	}

	public function getBody() {
		return $this->replaceText($this->template->getBody());
	}

	/**
	 * Gebe hier RFC-Kompatible Kodierung zurueck
	 **/
	public function getRawHeaders() {
		
	}

	public function getRawBody() {
		
	}
}

?>
