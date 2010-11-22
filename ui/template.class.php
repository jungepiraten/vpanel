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
		$this->smarty->register_modifier("__", array($this, "translate"));
		$this->smarty->register_modifier("___", array($this, "link"));

		$this->smarty->assign("session", $this->session);
		$this->smarty->assign("charset", $this->session->getEncoding());
	}

	public function translate() {
		$params = func_get_args();
		$string = array_shift($params);

		if ($this->session->getLang()->hasString($string)) {
			$string = iconv($this->session->getLang()->getEncoding(), $this->session->getEncoding(), $this->session->getLang()->getString($string));
		}

		return vsprintf($string, $params);
	}
	public function link($name) {
		$params = func_get_args();
		return call_user_func_array(array($this->session, "getLink"), $params);
	}
	

	public function viewIndex() {
		$this->smarty->assign("", "");
		$this->smarty->display("index.html.tpl");
	}

	public function viewLogin($loginfailed = false) {
		$errors = array();
		if ($loginfailed) {
			$errors[] = $this->translate("Login failed");
		}
		$this->smarty->assign("errors", $errors);
		$this->smarty->display("login.html.tpl");
	}

	public function viewUserList($users) {
		$this->smarty->assign("users", $users);
		$this->smarty->display("userlist.html.tpl");
	}

	public function viewUserDetails($user, $userroles, $roles, $permissions) {
		$this->smarty->assign("user", $user);
		$this->smarty->assign("userroles", $userroles);
		$this->smarty->assign("roles", $roles);
		$this->smarty->assign("permissions", $permissions);
		$this->smarty->display("userdetails.html.tpl");
	}

	public function viewUserCreate() {
		$this->smarty->display("usercreate.html.tpl");
	}

	public function viewRoleList($roles) {
		$this->smarty->assign("roles", $roles);
		$this->smarty->display("rolelist.html.tpl");
	}

	public function viewRoleDetails($role, $roleusers, $users, $rolepermissions, $permissions) {
		$this->smarty->assign("role", $role);
		$this->smarty->assign("roleusers", $roleusers);
		$this->smarty->assign("users", $users);
		$this->smarty->assign("rolepermissions", $rolepermissions);
		$this->smarty->assign("permissions", $permissions);
		$this->smarty->display("roledetails.html.tpl");
	}

	public function viewRoleCreate() {
		$this->smarty->display("rolecreate.html.tpl");
	}

	public function viewMitgliederList() {
		
	}

	public function redirect($url = null) {
		if ($url === null) {
			$url = isset($_REQUEST["redirect"]) ? $_REQUEST["redirect"] : $_SERVER["HTTP_REFERER"];
		}
		header('Location: ' . $url);
		echo 'Sie werden weitergeleitet: <a href="'.$url.'">'.$url.'</a>';
	}
}

?>
