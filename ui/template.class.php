<?php

require_once("Smarty/Smarty.class.php");

class Template {
	private $smarty;
	private $session;
	
	public function __construct($session) {
		$this->session = $session;

		$this->smarty = new Smarty;
		$this->smarty->template_dir = dirname(__FILE__) . "/templates";
		$this->smarty->compile_dir = dirname(__FILE__) . "/templates_c";
		$this->smarty->register_modifier("__", array($this, "formatLang"));
	}

	public function formatLang($string) {
		$params = func_get_args();
		$string = array_shift($params);

		if ($this->session->getLang()->hasString($string)) {
			$string = $this->session->getLang()->getString($string);
		}

		// TODO rftm sprintf
		return sprintf($string, $params);
	}
	
	public function viewLogin() {
		$this->smarty->assign("loginaction", $this->session->getLink("login"));
		$this->smarty->display("login.html.tpl");
	}
}

?>
