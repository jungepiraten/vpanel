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
	
	protected function parseUser($user) {
		$row = array();
		$row["userid"] = $user->getUserID();
		$row["username"] = $user->getUsername();
		return $row;
	}

	protected function parseUsers($rows) {
		return array_map(array($this, parseUser), $rows);
	}

	protected function parseRole($role) {
		$row = array();
		$row["roleid"] = $role->getRoleID();
		$row["label"] = $role->getLabel();
		$row["description"] = $role->getDescription();
		return $row;
	}

	protected function parseRoles($rows) {
		return array_map(array($this, parseRole), $rows);
	}

	protected function parsePermission($permission) {
		$row = array();
		$row["permissionid"] = $permission->getPermissionID();
		$row["label"] = $permission->getLabel();
		$row["description"] = $permission->getDescription();
		return $row;
	}

	protected function parsePermissions($rows) {
		return array_map(array($this, parsePermission), $rows);
	}

	protected function parseMitglied($mitglied) {
		$row = array();
		$row["mitgliedid"] = $mitglied->getMitgliedID();
		$row["globalid"] = $mitglied->getGlobalID();
		$row["eintritt"] = $mitglied->getEintrittsdatum();
		$row["austritt"] = $mitglied->getAustrittsdatum();
		$row["latest"] = $this->parseMitgliedRevision($mitglied->getLatestRevision());
		return $row;
	}

	protected function parseMitglieder($rows) {
		return array_map(array($this, parseMitglied), $rows);
	}

	protected function parseMitgliedRevision($revision) {
		$row = array();
		$row["revisionid"] = $revision->getRevisionID();
		$row["globalid"] = $revision->getGlobalID();
		$row["timestamp"] = $revision->getTimestamp();
		$row["user"] = $this->parseUser($revision->getUser());
		$row["mitgliedschaft"] = $this->parseMitgliedschaft($revision->getMitgliedschaft());
		if ($revision->getNatPersonID() != null) {
			$row["natperson"] = $this->parseNatPerson($revision->getNatPerson());
		}
		if ($revision->getJurPersonID() != null) {
			$row["jurperson"] = $this->parseJurPerson($revision->getJurPerson());
		}
		$row["kontakt"] = $this->parseKontakt($revision->getKontakt());
		$row["beitrag"] = $revision->getBeitrag();
		$row["mitglied_piraten"] = $revision->isMitgliedPiraten();
		$row["verteiler_eingetragen"] = $revision->isVerteilerEingetragen();
		$row["geloescht"] = $revision->isGeloescht();
		return $row;
	}

	protected function parseMitgliedRevisions($rows) {
		return array_map(array($this, parseMitgliedRevision), $rows);
	}

	protected function parseNatPerson($natperson) {
		$row = array();
		$row["natpersonid"] = $natperson->getNatPersonID();
		$row["vorname"] = $natperson->getVorname();
		$row["name"] = $natperson->getName();
		$row["geburtsdatum"] = $natperson->getGeburtsdatum();
		$row["nationalitaet"] = $natperson->getNationalitaet();
		return $row;
	}

	protected function parseJurPerson($jurperson) {
		$row = array();
		$row["jurpersonid"] = $jurperson->getJurPersonID();
		$row["label"] = $jurperson->getLabel();
		return $row;
	}

	protected function parseKontakt($kontakt) {
		$row = array();
		$row["kontaktid"] = $kontakt->getKontaktID();
		$row["strasse"] = $kontakt->getStrasse();
		$row["hausnummer"] = $kontakt->getHausnummer();
		$row["ort"] = $this->parseOrt($kontakt->getOrt());
		$row["telefon"] = $kontakt->getTelefonnummer();
		$row["handy"] = $kontakt->getHandynummer();
		$row["email"] = $kontakt->getEMail();
		return $row;
	}

	protected function parseMitgliedschaft($mitgliedschaft) {
		$row = array();
		$row["mitgliedschaftid"] = $mitgliedschaft->getMitgliedschaftID();
		$row["label"] = $mitgliedschaft->getLabel();
		$row["description"] = $mitgliedschaft->getDescription();
		$row["defaultbeitrag"] = $mitgliedschaft->getDefaultBeitrag();
		$row["defaultmailcreate"] = $mitgliedschaft->getDefaultCreateMail();
		return $row;
	}

	protected function parseMitgliedschaften($rows) {
		return array_map(array($this, parseMitgliedschaft), $rows);
	}

	protected function parseOrt($ort) {
		$row = array();
		$row["ortid"] = $ort->getOrtID();
		$row["label"] = $ort->getLabel();
		$row["plz"] = $ort->getPLZ();
		$row["state"] = $this->parseState($ort->getState());
		return $row;
	}

	protected function parseOrte($rows) {
		return array_map(array($this, parseOrt), $rows);
	}

	protected function parseState($state) {
		$row = array();
		$row["stateid"] = $state->getStateID();
		$row["label"] = $state->getLabel();
		$row["country"] = $this->parseCountry($state->getCountry());
		return $row;
	}

	protected function parseStates($rows) {
		return array_map(array($this, parseState), $rows);
	}

	protected function parseCountry($country) {
		$row = array();
		$row["countryid"] = $country->getCountryID();
		$row["label"] = $country->getLabel();
		return $row;
	}

	protected function parseCountries($rows) {
		return array_map(array($this, parseCountry), $rows);
	}

	public function viewIndex() {
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
		$this->smarty->assign("users", $this->parseUsers($users));
		$this->smarty->display("userlist.html.tpl");
	}

	public function viewUserDetails($user, $roles) {
		$this->smarty->assign("user", $this->parseUser($user));
		$this->smarty->assign("userroles", $this->parseRoles($user->getRoles()));
		$this->smarty->assign("roles", $this->parseRoles($roles));
		$this->smarty->display("userdetails.html.tpl");
	}

	public function viewUserCreate() {
		$this->smarty->display("usercreate.html.tpl");
	}

	public function viewRoleList($roles) {
		$this->smarty->assign("roles", $this->parseRoles($roles));
		$this->smarty->display("rolelist.html.tpl");
	}

	public function viewRoleDetails($role, $users, $permissions) {
		$this->smarty->assign("role", $this->parseRole($role));
		$this->smarty->assign("roleusers", $this->parseUsers($role->getUsers()));
		$this->smarty->assign("users", $this->parseUsers($users));
		$this->smarty->assign("rolepermissions", $this->parsePermissions($role->getPermissions()));
		$this->smarty->assign("permissions", $this->parsePermissions($permissions));
		$this->smarty->display("roledetails.html.tpl");
	}

	public function viewRoleCreate() {
		$this->smarty->display("rolecreate.html.tpl");
	}

	public function viewMitgliederList($mitglieder, $mitgliedschaften) {
		$this->smarty->assign("mitglieder", $this->parseMitglieder($mitglieder));
		$this->smarty->assign("mitgliedschaften", $this->parseMitgliedschaften($mitgliedschaften));
		$this->smarty->display("mitgliederlist.html.tpl");
	}

	public function viewMitgliedDetails($mitglied, $orte, $states) {
		$this->smarty->assign("mitglied", $this->parseMitglied($mitglied));
		$this->smarty->assign("mitgliedrevision", $this->parseMitgliedRevision($mitglied->getLatestRevision()));
		$this->smarty->assign("orte", $this->parseOrte($orte));
		$this->smarty->assign("states", $this->parseStates($states));
		$this->smarty->display("mitgliederdetails.html.tpl");
	}

	public function viewMitgliedCreate($mitgliedschaft, $orte, $states) {
		$this->smarty->assign("mitgliedschaft", $this->parseMitgliedschaft($mitgliedschaft));
		$this->smarty->assign("orte", $this->parseOrte($orte));
		$this->smarty->assign("states", $this->parseStates($states));
		$this->smarty->display("mitgliedercreate.html.tpl");
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
