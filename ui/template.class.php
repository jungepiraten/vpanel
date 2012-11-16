<?php

require_once("Smarty/Smarty.class.php");

require_once(VPANEL_MITGLIEDERMATCHER . "/mitglied.class.php");

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
		$this->smarty->register_modifier("file_size", array($this, "formatFileSize"));

		if ($this->session->getUser() != null) {
			$this->smarty->assign("sessionUser", $this->parseUser($this->session->getUser()));
		}
		$this->smarty->assign("session", $this->session);
		$this->smarty->assign("charset", $this->session->getEncoding());
		$this->smarty->assign("sidebars", array());
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
	public function formatFileSize($bytes) {
		$mb = 1024*1024;
		if ($bytes > $mb) {
			$output = sprintf ("%01.2f",$bytes/$mb) . " MB";
		} elseif ( $bytes >= 1024 ) {
			$output = sprintf ("%01.0f",$bytes/1024) . " Kb";
		} else {
			$output = $bytes . " bytes";
		}

		return $output;
	}

	private function getStorage() {
		return $this->session->getConfig()->getStorage();
	}

	protected function parseUser($user) {
		$row = array();
		$row["userid"] = $user->getUserID();
		$row["username"] = $user->getUsername();
		$row["apikey"] = $user->getAPIKey();
		$row["aktiv"] = $user->isAktiv();
		$row["defaultgliederungid"] = $user->getDefaultGliederungID();
		$row["defaultdokumentkategorieid"] = $user->getDefaultDokumentKategorieID();
		$row["defaultdokumentstatusid"] = $user->getDefaultDokumentStatusID();
		return $row;
	}

	protected function parseUsers($rows) {
		return array_map(array($this, 'parseUser'), $rows);
	}

	protected function parseDashboardWidget($widget) {
		$row = array();
		$row["widgetid"] = $widget->getWidgetID();
		$row["user"] = $this->parseUser($widget->getUser());
		$row["column"] = $widget->getColumn();
		return $row;
	}

	protected function parseDashboardWidgets($rows) {
		return array_map(array($this, 'parseDashboardWidget'), $rows);
	}

	protected function parseRole($role) {
		$row = array();
		$row["roleid"] = $role->getRoleID();
		$row["label"] = $role->getLabel();
		$row["description"] = $role->getDescription();
		return $row;
	}

	protected function parseRoles($rows) {
		return array_map(array($this, 'parseRole'), $rows);
	}

	protected function parsePermission($permission) {
		$row = array();
		$row["permissionid"] = $permission->getPermissionID();
		$row["label"] = $permission->getLabel();
		$row["description"] = $permission->getDescription();
		$row["global"] = $permission->isGlobal();
		return $row;
	}

	protected function parsePermissions($rows) {
		return array_map(array($this, 'parsePermission'), $rows);
	}

	protected function parseRolePermission($permission) {
		$row = array();
		$row["role"] = $this->parseRole($permission->getRole());
		$row["permission"] = $this->parsePermission($permission->getPermission());
		if ($permission->getGliederung() != null) {
			$row["gliederung"] = $this->parseGliederung($permission->getGliederung());
		}
		$row["transitive"] = $permission->isTransitive();
		return $row;
	}

	protected function parseRolePermissions($rows) {
		return array_map(array($this, 'parseRolePermission'), $rows);
	}

	protected function parseGliederung($gliederung) {
		$row = array();
		$row["gliederungid"] = $gliederung->getGliederungID();
		$row["label"] = $gliederung->getLabel();
		return $row;
	}

	protected function parseGliederungen($rows) {
		return array_map(array($this, 'parseGliederung'), $rows);
	}

	protected function parseMitgliedTemplate($mitgliedtemplate) {
		$row = array();
		$row["mitgliedtemplateid"] = $mitgliedtemplate->getMitgliedTemplateID();
		$row["label"] = $mitgliedtemplate->getLabel();
		if ($mitgliedtemplate->getGliederungID() != null) {
			$row["gliederung"] = $this->parseGliederung($mitgliedtemplate->getGliederung());
		}
		if ($mitgliedtemplate->getMitgliedschaftID() != null) {
			$row["mitgliedschaft"] = $this->parseMitgliedschaft($mitgliedtemplate->getMitgliedschaft());
		}
		$row["beitrag"] = $mitgliedtemplate->getBeitrag();
		if ($mitgliedtemplate->getCreateMailTemplateID() != null) {
			$row["createmailtemplate"] = $this->parseMailTemplate($mitgliedtemplate->getCreateMailTemplate());
		}
		return $row;
	}

	protected function parseMitgliedTemplates($rows) {
		return array_map(array($this, 'parseMitgliedTemplate'), $rows);
	}

	protected function parseMail($mail) {
		$row = array();
		$row["headers"] = array();
		foreach ($mail->getHeaders() as $headerfield => $headervalue) {
			$row["headers"][$headerfield] = $headervalue;
		}
		$row["body"] = $mail->getBody();
		return $row;
	}

	protected function parseMails($rows) {
		return array_map(array($this, 'parseMail'), $rows);
	}

	protected function parseMailTemplate($template) {
		$row = array();
		$row["templateid"] = $template->getTemplateID();
		if ($template->getGliederungID() != null) {
			$row["gliederung"] = $this->parseGliederung($template->getGliederung());
		}
		$row["label"] = $template->getLabel();
		$row["body"] = $template->getBody();
		$row["headers"] = array();
		foreach ($template->getHeaders() as $header) {
			$row["headers"][$header->getField()] = $header->getValue();
		}
		$row["attachments"] = $this->parseFiles($template->getAttachments());
		return $row;
	}

	protected function parseMailTemplates($rows) {
		return array_map(array($this, 'parseMailTemplate'), $rows);
	}

	protected function parseBeitrag($beitrag) {
		$row = array();
		$row["beitragid"] = $beitrag->getBeitragID();
		$row["label"] = $beitrag->getLabel();
		$row["hoehe"] = $beitrag->getHoehe();
		if ($beitrag->getMailTemplate() != null) {
			$row["mailtemplate"] = $this->parseMailTemplate($beitrag->getMailTemplate());
		}
		return $row;
	}

	protected function parseBeitragList($rows) {
		return array_map(array($this, 'parseBeitrag'), $rows);
	}

	protected function parseMitgliedBeitrag($mitgliedbeitrag, &$mitglied = null) {
		$row = array();
		if ($mitglied == null) {
			$mitglied = $this->parseMitglied($mitgliedbeitrag->getMitglied());
		}
		$row["mitgliederbeitragid"] = $mitgliedbeitrag->getMitgliederBeitragID();
		$row["mitglied"] = $mitglied;
		$row["beitrag"] = $this->parseBeitrag($mitgliedbeitrag->getBeitrag());
		$row["hoehe"] = $mitgliedbeitrag->getHoehe();
		$row["buchungen"] = $this->parseMitgliedBeitragBuchungList($mitgliedbeitrag->getBuchungen());
		$row["bezahlt"] = 0;
		foreach ($row["buchungen"] as $buchung) {
			$row["bezahlt"] += $buchung["hoehe"];
		}
		return $row;
	}

	protected function parseMitgliedBeitragList($rows, &$mitglied = null) {
		return array_map(array($this, 'parseMitgliedBeitrag'), $rows, count($rows) > 0 ? array_fill(0, count($rows), $mitglied) : array());
	}

	protected function parseMitgliedBeitragBuchung($buchung) {
		$row = array();
		$row["buchungid"] = $buchung->getBuchungID();
		$row["gliederung"] = $this->parseGliederung($buchung->getGliederung());
		$row["vermerk"] = $buchung->getVermerk();
		if ($buchung->getUser() != null) {
			$row["user"] = $this->parseUser($buchung->getUser());
		}
		if ($buchung->getTimestamp() != null) {
			$row["timestamp"] = $buchung->getTimestamp();
		}
		$row["hoehe"] = $buchung->getHoehe();
		return $row;
	}

	protected function parseMitgliedBeitragBuchungList($rows) {
		return array_map(array($this, 'parseMitgliedBeitragBuchung'), $rows);
	}

	protected function parseMitgliederFilter($filter) {
		$row = array();
		$row["filterid"] = $filter->getFilterID();
		$row["label"] = $filter->getLabel();
		return $row;
	}

	protected function parseMitgliederFilters($rows) {
		return array_map(array($this, 'parseMitgliederFilter'), $rows);
	}

	protected function parseMitgliederFilterAction($action) {
		$row = array();
		$row["actionid"] = $action->getActionID();
		$row["label"] = $action->getLabel();
		$row["permission"] = $action->getPermission();
		return $row;
	}

	protected function parseMitgliederFilterActions($rows) {
		return array_map(array($this, 'parseMitgliederFilterAction'), $rows);
	}

	protected function parseMitglied($mitglied) {
		$row = array();
		$row["mitgliedid"] = $mitglied->getMitgliedID();
		$row["filterid"] = $this->session->addMitgliederMatcher(new MitgliedMitgliederMatcher($mitglied->getMitgliedID()))->getFilterID();
		$row["globalid"] = $mitglied->getGlobalID();
		$row["eintritt"] = $mitglied->getEintrittsdatum();
		if ($mitglied->isAusgetreten()) {
			$row["austritt"] = $mitglied->getAustrittsdatum();
		}
		$row["beitraege"] = $this->parseMitgliedBeitragList($mitglied->getBeitragList(), $row);
		$row["beitraege_hoehe"] = 0;
		$row["beitraege_bezahlt"] = 0;
		foreach ($row["beitraege"] as $beitrag) {
			$row["beitraege_hoehe"] += $beitrag["hoehe"];
			$row["beitraege_bezahlt"] += $beitrag["bezahlt"];
		}
		$row["latest"] = $this->parseMitgliedRevision($mitglied->getLatestRevision());
		return $row;
	}

	protected function parseMitglieder($rows) {
		return array_map(array($this, 'parseMitglied'), $rows);
	}

	protected function parseMitgliederFlag($flag) {
		$row = array();
		$row["flagid"] = $flag->getFlagID();
		$row["label"] = $flag->getLabel();
		return $row;
	}

	protected function parseMitgliederFlags($rows) {
		return array_map(array($this, 'parseMitgliederFlag'), $rows);
	}

	protected function parseMitgliederTextField($textfield) {
		$row = array();
		$row["textfieldid"] = $textfield->getTextFieldID();
		$row["label"] = $textfield->getLabel();
		return $row;
	}

	protected function parseMitgliederTextFields($rows) {
		return array_map(array($this, 'parseMitgliederTextField'), $rows);
	}

	protected function parseMitgliederRevisionTextField($revisiontextfield) {
		$row = array();
		$row["textfield"] = $this->parseMitgliederTextField($revisiontextfield->getTextField());
		// Infinite Loop else
		// $row["revision"] = $this->parseMitgliederRevision($revisiontextfield->getRevision());
		$row["value"] = $revisiontextfield->getValue();
		return $row;
	}

	protected function parseMitgliederRevisionTextFields($rows) {
		return array_map(array($this, 'parseMitgliederRevisionTextField'), $rows);
	}

	protected function parseMitgliedNotiz($notiz) {
		$row = array();
		$row["mitgliednotizid"] = $notiz->getMitgliedNotizID();
		$row["mitgliedid"] = $notiz->getMitgliedID();
		if ($notiz->getAuthor() != null) {
			$row["author"] = $this->parseUser($notiz->getAuthor());
		}
		$row["timestamp"] = $notiz->getTimestamp();
		$row["kommentar"] = $notiz->getKommentar();
		return $row;
	}

	protected function parseMitgliedNotizen($rows) {
		return array_map(array($this, 'parseMitgliedNotiz'), $rows);
	}

	protected function parseMitgliedRevision($revision) {
		$row = array();
		$row["revisionid"] = $revision->getRevisionID();
		$row["globalid"] = $revision->getGlobalID();
		$row["timestamp"] = $revision->getTimestamp();
		if ($revision->getUser() != null) {
			$row["user"] = $this->parseUser($revision->getUser());
		}
		$row["gliederung"] = $this->parseGliederung($revision->getGliederung());
		$row["mitgliedschaft"] = $this->parseMitgliedschaft($revision->getMitgliedschaft());
		if ($revision->getNatPersonID() != null) {
			$row["bezeichnung"] = $revision->getNatPerson()->getVorname() . " " . $revision->getNatPerson()->getName();
			$row["natperson"] = $this->parseNatPerson($revision->getNatPerson());
		}
		if ($revision->getJurPersonID() != null) {
			$row["bezeichnung"] = $revision->getJurPerson()->getLabel();
			$row["jurperson"] = $this->parseJurPerson($revision->getJurPerson());
		}
		$row["kontakt"] = $this->parseKontakt($revision->getKontakt());
		$row["beitrag"] = $revision->getBeitrag();
		$row["flags"] = $this->parseMitgliederFlags($revision->getFlags());
		$row["textfields"] = $this->parseMitgliederRevisionTextFields($revision->getTextFields());
		$row["geloescht"] = $revision->isGeloescht();
		return $row;
	}

	protected function parseMitgliedRevisions($rows) {
		return array_map(array($this, 'parseMitgliedRevision'), $rows);
	}

	protected function parseNatPerson($natperson) {
		$row = array();
		$row["natpersonid"] = $natperson->getNatPersonID();
		$row["anrede"] = $natperson->getAnrede();
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
		$row["adresszusatz"] = $kontakt->getAdresszusatz();
		$row["strasse"] = $kontakt->getStrasse();
		$row["hausnummer"] = $kontakt->getHausnummer();
		$row["ort"] = $this->parseOrt($kontakt->getOrt());
		$row["telefon"] = $kontakt->getTelefonnummer();
		$row["handy"] = $kontakt->getHandynummer();
		$row["email"] = $this->parseEMail($kontakt->getEMail());
		return $row;
	}

	protected function parseEMail($email) {
		$row = array();
		$row["emailid"] = $email->getEMailID();
		$row["email"] = $email->getEMail();
		$row["bounces"] = $this->parseEMailBounces($email->getBounces(), &$row);
		return $row;
	}

	protected function parseEMailBounce($bounce, &$email = null) {
		$row = array();
		if ($email == null) {
			$email = $this->parseEMail($bounce->getEMail());
		}
		$row["bounceid"] = $bounce->getBounceID();
		$row["email"] = $email;
		$row["timestamp"] = $bounce->getTimestamp();
		$row["message"] = $bounce->getMessage();
		return $row;
	}

	protected function parseEMailBounces($rows, &$email = null) {
		return array_map(array($this, "parseEMailBounce"), $rows, count($rows) > 0 ? array_fill(0, count($rows), $email) : array());
	}

	protected function parseMitgliedschaft($mitgliedschaft) {
		$row = array();
		$row["mitgliedschaftid"] = $mitgliedschaft->getMitgliedschaftID();
		$row["label"] = $mitgliedschaft->getLabel();
		$row["description"] = $mitgliedschaft->getDescription();
		return $row;
	}

	protected function parseMitgliedschaften($rows) {
		return array_map(array($this, 'parseMitgliedschaft'), $rows);
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
		return array_map(array($this, 'parseOrt'), $rows);
	}

	protected function parseState($state) {
		$row = array();
		$row["stateid"] = $state->getStateID();
		$row["label"] = $state->getLabel();
		$row["population"] = $state->getPopulation();
		$row["country"] = $this->parseCountry($state->getCountry());
		return $row;
	}

	protected function parseStates($rows) {
		return array_map(array($this, 'parseState'), $rows);
	}

	protected function parseCountry($country) {
		$row = array();
		$row["countryid"] = $country->getCountryID();
		$row["label"] = $country->getLabel();
		return $row;
	}

	protected function parseCountries($rows) {
		return array_map(array($this, 'parseCountry'), $rows);
	}

	protected function parseProcess($process) {
		$row = array();
		$row["processid"] = $process->getProcessID();
		$row["progress"] = $process->getProgress();
		$row["queued"] = $process->getQueued();
		$row["started"] = $process->getStarted();
		$row["finished"] = $process->getFinished();
		$row["iswaiting"] = $process->isWaiting();
		$row["isrunning"] = $process->isRunning();
		$row["isfinished"] = $process->isFinished();
		return $row;
	}

	protected function parseProcesses($rows) {
		return array_map(array($this, 'parseProcess'), $rows);
	}

	protected function parseDokument($dokument) {
		$row = array();
		$row["dokumentid"] = $dokument->getDokumentID();
		$row["gliederung"] = $this->parseGliederung($dokument->getGliederung());
		$row["dokumentkategorie"] = $this->parseDokumentKategorie($dokument->getDokumentKategorie());
		$row["dokumentstatus"] = $this->parseDokumentStatus($dokument->getDokumentStatus());
		$row["identifier"] = $dokument->getIdentifier();
		$row["label"] = $dokument->getLabel();
		$row["content"] = $dokument->getContent();
		$row["file"] = $this->parseFile($dokument->getFile());
		return $row;
	}

	protected function parseDokumente($rows) {
		return array_map(array($this, 'parseDokument'), $rows);
	}

	protected function parseDokumentTemplate($template) {
		$row = array();
		$row["dokumenttemplateid"] = $template->getDokumentTemplateID();
		$row["label"] = $template->getLabel();
		return $row;
	}

	protected function parseDokumentTemplates($rows) {
		return array_map(array($this, 'parseDokumentTemplate'), $rows);
	}

	protected function parseDokumentTransition($transition) {
		$row = array();
		$row["dokumenttransitionid"] = $transition->getDokumentTransitionID();
		$row["label"] = $transition->getLabel();
		return $row;
	}

	protected function parseDokumentTransitionen($rows) {
		return array_map(array($this, 'parseDokumentTransition'), $rows);
	}

	protected function parseDokumentKategorie($kategorie) {
		$row = array();
		$row["dokumentkategorieid"] = $kategorie->getDokumentKategorieID();
		$row["label"] = $kategorie->getLabel();
		return $row;
	}

	protected function parseDokumentKategorien($rows) {
		return array_map(array($this, 'parseDokumentKategorie'), $rows);
	}

	protected function parseDokumentStatus($status) {
		$row = array();
		$row["dokumentstatusid"] = $status->getDokumentStatusID();
		$row["label"] = $status->getLabel();
		return $row;
	}

	protected function parseDokumentStatusList($rows) {
		return array_map(array($this, 'parseDokumentStatus'), $rows);
	}

	protected function parseDokumentFlag($flag) {
		$row = array();
		$row["flagid"] = $flag->getFlagID();
		$row["label"] = $flag->getLabel();
		return $row;
	}

	protected function parseDokumentFlags($rows) {
		return array_map(array($this, 'parseDokumentFlag'), $rows);
	}

	protected function parseDokumentNotiz($notiz) {
		$row = array();
		$row["dokumentnotizid"] = $notiz->getDokumentNotizID();
		$row["author"] = $this->parseUser($notiz->getAuthor());
		$row["timestamp"] = $notiz->getTimestamp();
		if ($notiz->getNextKategorieID() != null) {
			$row["nextkategorie"] = $this->parseDokumentKategorie($notiz->getNextKategorie());
		}
		if ($notiz->getNextStatusID() != null) {
			$row["nextstatus"] = $this->parseDokumentStatus($notiz->getNextStatus());
		}
		if ($notiz->getNextLabel() != null) {
			$row["nextlabel"] = $notiz->getNextLabel();
		}
		if ($notiz->getNextIdentifier() != null) {
			$row["nextidentifier"] = $notiz->getNextIdentifier();
		}
		$row["kommentar"] = $notiz->getKommentar();
		return $row;
	}

	protected function parseDokumentNotizen($rows) {
		return array_map(array($this, 'parseDokumentNotiz'), $rows);
	}

	protected function parseFile($file) {
		$row = array();
		$row["fileid"] = $file->getFileID();
		$row["exportfilename"] = $file->getExportFilename();
		$row["mimetype"] = $file->getMimeType();
		$row["filesize"] = $file->getFileSize();
		return $row;
	}

	protected function parseFiles($rows) {
		return array_map(array($this, 'parseFile'), $rows);
	}

	protected function parseTempFile($tempfile) {
		$row = array();
		$row["tempfileid"] = $tempfile->getTempFileID();
		$row["file"] = $this->parseFile($tempfile->getFile());
		return $row;
	}

	protected function parseTempFiles($rows) {
		return array_map(array($this, 'parseTempFile'), $rows);
	}


	public function viewDashboardWidget($widget) {
		$this->smarty->assign("widget", $this->parseDashboardWidget($widget));
		if ($widget instanceof StaticDashboardWidget) {
			$this->smarty->assign("text", $widget->getText());
			$this->smarty->display("dashboardwidget_static.block.tpl");
		}
		if ($widget instanceof MitgliederBeitragBuchungTimelineDashboardWidget) {
			if ($widget->hasReload()) {
				$this->smarty->assign("reload", $widget->getReload());
			}
			$this->smarty->display("dashboard_mitgliederbeitragbuchung_timeline.block.tpl");
		}
		if ($widget instanceof MitgliederRevisionTimelineDashboardWidget) {
			if ($widget->hasReload()) {
				$this->smarty->assign("reload", $widget->getReload());
			}
			$this->smarty->display("dashboard_mitgliederrevision_timeline.block.tpl");
		}
		if ($widget instanceof DokumentNotizenTimelineDashboardWidget) {
			if ($widget->hasReload()) {
				$this->smarty->assign("reload", $widget->getReload());
			}
			$this->smarty->display("dashboard_dokumentnotizen_timeline.block.tpl");
		}
	}

	public function viewDashboard($user, $widgets) {
		$columns = array();
		foreach ($widgets as $widget) {
			if (!isset($columns[$widget->getColumn()])) {
				$columns[$widget->getColumn()] = array();
			}
			$columns[$widget->getColumn()][] = $this->parseDashboardWidget($widget);
		}

		$this->smarty->assign("user", $this->parseUser($user));
		$this->smarty->assign("columns", $columns);
		$this->smarty->display("dashboard.html.tpl");
	}

	public function viewEinstellungen($success = false, $wrongpw = false, $pwsnotequal = false, $pwtooshort = false) {
		$errors = array();
		if ($success) {
			$errors[] = $this->translate("Änderungen erfolgreich");
		}
		if ($wrongpw) {
			$errors[] = $this->translate("Das Passwort ist falsch");
		}
		if ($pwsnotequal) {
			$errors[] = $this->translate("Die Passwörter stimmen nicht überein");
		}
		if ($pwtooshort) {
			$errors[] = $this->translate("Das gewählte Passwort ist zu kurz");
		}
		$this->smarty->assign("errors", $errors);
		$this->smarty->display("einstellungen.html.tpl");
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

	public function viewUserDetails($user, $roles, $gliederungen, $dokumentkategorien, $dokumentstatuslist) {
		$this->smarty->assign("user", $this->parseUser($user));
		$this->smarty->assign("userroles", $this->parseRoles($user->getRoles()));
		$this->smarty->assign("gliederungen", $this->parseGliederungen($gliederungen));
		$this->smarty->assign("dokumentkategorien", $this->parseDokumentKategorien($dokumentkategorien));
		$this->smarty->assign("dokumentstatuslist", $this->parseDokumentStatusList($dokumentstatuslist));
		$this->smarty->assign("roles", $this->parseRoles($roles));
		$this->smarty->display("userdetails.html.tpl");
	}

	public function viewUserCreate($gliederungen, $dokumentkategorien, $dokumentstatuslist) {
		$this->smarty->assign("gliederungen", $this->parseGliederungen($gliederungen));
		$this->smarty->assign("dokumentkategorien", $this->parseDokumentKategorien($dokumentkategorien));
		$this->smarty->assign("dokumentstatuslist", $this->parseDokumentStatusList($dokumentstatuslist));
		$this->smarty->display("usercreate.html.tpl");
	}

	public function viewRoleList($roles) {
		$this->smarty->assign("roles", $this->parseRoles($roles));
		$this->smarty->display("rolelist.html.tpl");
	}

	public function viewRoleDetails($role, $users, $permissions_global, $permissions_local, $gliederungen) {
		$this->smarty->assign("role", $this->parseRole($role));
		$this->smarty->assign("roleusers", $this->parseUsers($role->getUsers()));
		$this->smarty->assign("users", $this->parseUsers($users));
		$this->smarty->assign("rolepermissions", $this->parseRolePermissions($role->getPermissions()));
		$this->smarty->assign("permissions_global", $this->parsePermissions($permissions_global));
		$this->smarty->assign("permissions_local", $this->parsePermissions($permissions_local));
		$this->smarty->assign("gliederungen", $this->parseGliederungen($gliederungen));
		$this->smarty->display("roledetails.html.tpl");
	}

	public function viewRoleCreate() {
		$this->smarty->display("rolecreate.html.tpl");
	}

	public function viewBeitragList($beitraege) {
		$this->smarty->assign("beitraege", $this->parseBeitragList($beitraege));
		$this->smarty->display("beitraglist.html.tpl");
	}

	public function viewBeitragCreate($mailtemplates) {
		$this->smarty->assign("mailtemplates", $this->parseMailTemplates($mailtemplates));
		$this->smarty->display("beitragcreate.html.tpl");
	}

	public function viewBeitragDetails($beitrag, $mitgliederbeitraglist, $page, $pagecount, $mailtemplates) {
		$this->smarty->assign("beitrag", $this->parseBeitrag($beitrag));
		$this->smarty->assign("mitgliederbeitraglist", $this->parseMitgliedBeitragList($mitgliederbeitraglist));
		$this->smarty->assign("mitgliederbeitraglist_page", $page);
		$this->smarty->assign("mitgliederbeitraglist_pagecount", $pagecount);
		$this->smarty->assign("mailtemplates", $this->parseMailTemplates($mailtemplates));
		$this->smarty->display("beitragdetails.html.tpl");
	}

	public function viewMailTemplateList($mailtemplates, $gliederungen, $gliederung) {
		if ($gliederung != null) {
			$this->smarty->assign("gliederung", $this->parseGliederung($gliederung));
		}
		$this->smarty->assign("gliederungen", $this->parseGliederungen($gliederungen));
		$this->smarty->assign("mailtemplates", $this->parseMailTemplates($mailtemplates));
		$this->smarty->display("mailtemplatelist.html.tpl");
	}

	public function viewMailTemplateDetails($mailtemplate) {
		$this->smarty->assign("mailtemplate", $this->parseMailTemplate($mailtemplate));
		$this->smarty->display("mailtemplatedetails.html.tpl");
	}

	public function viewMailTemplateCreate($gliederungen, $gliederung) {
		if ($gliederung != null) {
			$this->smarty->assign("gliederung", $this->parseGliederung($gliederung));
		}
		$this->smarty->assign("gliederungen", $this->parseGliederungen($gliederungen));
		$this->smarty->display("mailtemplatecreate.html.tpl");
	}

	public function viewMailTemplateCreateAttachment($mailtemplate) {
		$this->smarty->assign("mailtemplate", $this->parseMailTemplate($mailtemplate));
		$this->smarty->display("mailtemplatecreateattachment.html.tpl");
	}

	public function viewMitgliederComposeFilter($filters, $flags) {
		$this->smarty->assign("filters", $this->parseMitgliederFilters($filters));
		$this->smarty->assign("mitgliederflags", $this->parseMitgliederFlags($flags));
		$this->smarty->display("mitgliedercomposefilter.html.tpl");
	}

	public function viewMitgliederList($mitglieder, $mitgliedtemplates, $filteractions, $filters, $filter, $page, $pagecount, $mitgliedercount) {
		if ($filter != null) {
			$this->smarty->assign("filter", $this->parseMitgliederFilter($filter));
		}
		$this->smarty->assign("page", $page);
		$this->smarty->assign("pagecount", $pagecount);
		$this->smarty->assign("mitgliedercount", $mitgliedercount);
		$this->smarty->assign("mitglieder", $this->parseMitglieder($mitglieder));
		$this->smarty->assign("mitgliedtemplates", $this->parseMitgliedTemplates($mitgliedtemplates));
		$this->smarty->assign("filters", $this->parseMitgliederFilters($filters));
		$this->smarty->assign("filteractions", $this->parseMitgliederFilterActions($filteractions));
		$this->smarty->display("mitgliederlist.html.tpl");
	}

	public function viewMitgliedDetails($mitglied, $revisions, $revision, $notizen, $dokumente, $gliederungen, $mitgliedschaften, $mailtemplates, $filteractions, $states, $mitgliederflags, $mitgliedertextfields, $beitraege) {
		$this->smarty->assign("mitglied", $this->parseMitglied($mitglied));
		$this->smarty->assign("mitgliedrevisions", $this->parseMitgliedRevisions($revisions));
		$this->smarty->assign("mitgliedrevision", $this->parseMitgliedRevision($revision));
		$this->smarty->assign("mitgliednotizen", $this->parseMitgliedNotizen($notizen));
		$this->smarty->assign("dokumente", $this->parseDokumente($dokumente));
		$this->smarty->assign("gliederungen", $this->parseGliederungen($gliederungen));
		$this->smarty->assign("mitgliedschaften", $this->parseMitgliedschaften($mitgliedschaften));
		$this->smarty->assign("mailtemplates", $this->parseMailTemplates($mailtemplates));
		$this->smarty->assign("filteractions", $this->parseMitgliederFilterActions($filteractions));
		$this->smarty->assign("states", $this->parseStates($states));
		$this->smarty->assign("flags", $this->parseMitgliederFlags($mitgliederflags));
		$this->smarty->assign("textfields", $this->parseMitgliederTextFields($mitgliedertextfields));
		$this->smarty->assign("beitraege", $this->parseBeitragList($beitraege));
		$this->smarty->display("mitgliederdetails.html.tpl");
	}

	public function viewMitgliedCreate($mitgliedtemplate, $dokument, $data, $gliederungen, $mitgliedschaften, $mailtemplates, $states, $mitgliederflags, $mitgliedertextfields) {
		if ($mitgliedtemplate != null) {
			$this->smarty->assign("mitgliedtemplate", $this->parseMitgliedTemplate($mitgliedtemplate));
		}
		if ($dokument != null) {
			$this->smarty->assign("dokument", $this->parseDokument($dokument));
		}
		if ($data != null) {
			$this->smarty->assign("data", $data);
		}
		$this->smarty->assign("gliederungen", $this->parseGliederungen($gliederungen));
		$this->smarty->assign("mitgliedschaften", $this->parseMitgliedschaften($mitgliedschaften));
		$this->smarty->assign("mailtemplates", $this->parseMailTemplates($mailtemplates));
		$this->smarty->assign("states", $this->parseStates($states));
		$this->smarty->assign("flags", $this->parseMitgliederFlags($mitgliederflags));
		$this->smarty->assign("textfields", $this->parseMitgliederTextFields($mitgliedertextfields));
		$this->smarty->display("mitgliedercreate.html.tpl");
	}

	public function viewMitgliederFilterAction($action, $filter, $matcher, $result) {
		if (isset($result["redirect"])) {
			return $this->redirect($result["redirect"]);
		}
		$this->smarty->assign("action", $this->parseMitgliederFilterAction($action));
		if ($filter != null) {
			$this->smarty->assign("filterid", $filter->getFilterID());
			$this->smarty->assign("filter", $this->parseMitgliederFilter($filter));
		} else {
			$this->smarty->assign("filterid", null);
		}
		if ($action instanceof DeleteMitgliederFilterAction) {
			$this->smarty->display("mitgliederdeleteform.html.tpl");
		}
		if ($action instanceof SendMailMitgliederFilterAction) {
			switch ($result["sendmail"]) {
			case "select":
				$this->smarty->assign("mailtemplates", $this->parseMailTemplates($result["templates"]));
				if ($result["mailtemplate"] != null) {
					$this->smarty->assign("mailtemplate", $this->parseMailTemplate($result["mailtemplate"]));
				}
				$this->smarty->display("mitgliedersendmailform.html.tpl");
				return;
			case "preview":
				$this->smarty->assign("mail", $this->parseMail($result["mail"]));
				$this->smarty->assign("mailtemplatecode", $result["mailtemplatecode"]);
				$this->smarty->display("mitgliedersendmailpreview.html.tpl");
				return;
			}
		}
		if ($action instanceof ExportMitgliederFilterAction) {
			$this->smarty->assign("predefinedfields", $result["predefinedfields"]);
			$this->smarty->display("mitgliederexportform.html.tpl");
			return;
		}
		if ($action instanceof SetBeitragMitgliederFilterAction) {
			$this->smarty->assign("beitraglist", $this->parseBeitragList($result["beitraglist"]));
			$this->smarty->display("mitgliedersetbeitragselect.html.tpl");
			return;
		}
		if ($action instanceof CalculateBeitragMitgliederFilterAction) {
			$this->smarty->assign("beitraglist", $this->parseBeitragList($result["beitraglist"]));
			$this->smarty->assign("gliederungen", $this->parseGliederungen($result["gliederungen"]));
			$this->smarty->display("mitgliedercalculatebeitragselect.html.tpl");
			return;
		}
	}

	public function viewMitgliederFilterProcess($action, $process, $result) {
		if (isset($result["redirect"])) {
			return $this->redirect($result["redirect"]);
		}
		$this->smarty->assign("action", $this->parseMitgliederFilterAction($action));
		$this->smarty->assign("process", $this->parseProcess($process));
		if ($action instanceof StatistikMitgliederFilterAction) {
			$this->smarty->assign("tempfiles", $this->parseTempFiles($result["tempfiles"]));
			$this->smarty->display("mitgliederstatistik.html.tpl");
			return;
		}
		if ($action instanceof CalculateBeitragMitgliederFilterAction) {
			$this->smarty->assign("beitrag", $this->parseBeitrag($result["beitrag"]));
			$this->smarty->assign("gliederungen", $this->parseGliederungen($result["gliederungen"]));
			$this->smarty->assign("anteile", $result["anteile"]);
			$this->smarty->assign("istHoehe", $result["gliederungsBeitragHoehe"]);
			$this->smarty->assign("sollHoehe", $result["gliederungsMitgliedHoehe"]);
			$this->smarty->assign("sumhoehe", $result["sumhoehe"]);
			$this->smarty->display("mitgliedercalculatebeitrag.html.tpl");
			return;
		}
	}

	public function viewStatistik($mitgliedercount, $mitgliedschaften, $countPerMitgliedschaft, $states, $countPerState) {
		$this->smarty->assign("mitgliedercount", $mitgliedercount);
		$this->smarty->assign("mitgliedschaften", $this->parseMitgliedschaften($mitgliedschaften));
		$this->smarty->assign("mitgliedercountPerMitgliedschaft", $countPerMitgliedschaft);
		$this->smarty->assign("states", $this->parseStates($states));
		$this->smarty->assign("mitgliedercountPerState", $countPerState);
		$this->smarty->display("statistik.html.tpl");
	}

	public function viewProcess($process) {
		$this->smarty->assign("process", $this->parseProcess($process));
		$this->smarty->display("process.html.tpl");
	}

	public function viewDokumentTemplate($dokumenttemplate, $link, $title, $options = null) {
		if ($options == null) {
			$options = array();
		}
		$this->smarty->assign("title", $title);
		$this->smarty->assign("link", $link);
		$this->smarty->assign("showupload", ! in_array("hideupload", $options));
		$this->smarty->assign("dokumenttemplate", $this->parseDokumentTemplate($dokumenttemplate));
		if ($dokumenttemplate instanceof NatPersonDokumentTemplate) {
			$this->smarty->display("dokumentcreate_person.html.tpl");
		} else if ($dokumenttemplate instanceof DefaultDateDokumentTemplate) {
			$this->smarty->display("dokumentcreate_defaultdate.html.tpl");
		} else if ($dokumenttemplate instanceof DefaultDokumentTemplate) {
			$this->smarty->display("dokumentcreate_default.html.tpl");
		} else if ($dokumenttemplate instanceof SelectPrefixDateDokumentTemplate) {
			$this->smarty->assign("options", $dokumenttemplate->getPrefixOptions());
			$this->smarty->display("dokumentcreate_selectprefixdate.html.tpl");
		}
	}

	public function viewDokumentList($dokumente, $templates, $transitionen, $gliederungen, $gliederung, $dokumentkategorien, $dokumentkategorie, $dokumentstatuslist, $dokumentstatus, $page, $pagecount) {
		if ($gliederung != null) {
			$this->smarty->assign("gliederung", $this->parseGliederung($gliederung));
		}
		if ($dokumentkategorie != null) {
			$this->smarty->assign("dokumentkategorie", $this->parseDokumentKategorie($dokumentkategorie));
		}
		if ($dokumentstatus != null) {
			$this->smarty->assign("dokumentstatus", $this->parseDokumentStatus($dokumentstatus));
		}
		$this->smarty->assign("page", $page);
		$this->smarty->assign("pagecount", $pagecount);
		$this->smarty->assign("dokumente", $this->parseDokumente($dokumente));
		$this->smarty->assign("dokumenttemplates", $this->parseDokumentTemplates($templates));
		$this->smarty->assign("dokumenttransitionen", $this->parseDokumentTransitionen($transitionen));
		$this->smarty->assign("gliederungen", $this->parseGliederungen($gliederungen));
		$this->smarty->assign("dokumentkategorien", $this->parseDokumentKategorien($dokumentkategorien));
		$this->smarty->assign("dokumentstatuslist", $this->parseDokumentStatusList($dokumentstatuslist));
		$this->smarty->display("dokumentlist.html.tpl");
	}

	public function viewDokumentCreate($dokumenttemplate) {
		$this->viewDokumentTemplate($dokumenttemplate, $this->link("dokumente_create", $dokumenttemplate->getDokumentTemplateID()), $this->translate("%s anlegen", $dokumenttemplate->getLabel()) );
	}

	public function viewDokumentDetails($dokument, $dokumentnotizen, $mitglieder, $transitionen, $dokumentkategorien, $dokumentstatuslist, $flags, $mitgliedtemplates) {
		$this->smarty->assign("dokument", $this->parseDokument($dokument));
		$this->smarty->assign("dokumentnotizen", $this->parseDokumentNotizen($dokumentnotizen));
		$this->smarty->assign("mitglieder", $this->parseMitglieder($mitglieder));
		$this->smarty->assign("dokumenttransitionen", $this->parseDokumentTransitionen($transitionen));
		$this->smarty->assign("dokumentkategorien", $this->parseDokumentKategorien($dokumentkategorien));
		$this->smarty->assign("dokumentstatuslist", $this->parseDokumentStatusList($dokumentstatuslist));
		$this->smarty->assign("flags", $this->parseDokumentFlags($flags));
		$this->smarty->assign("mitgliedtemplates", $this->parseMitgliedTemplates($mitgliedtemplates));
		$this->smarty->display("dokumentdetails.html.tpl");
	}

	public function viewSingleDokumentTransition($transition, $result, $dokumentid) {
		return $this->viewDokumentTransition($transition, $result, $this->link("dokumente_transitionaction", $transition->getDokumentTransitionID(), $dokumentid));
	}

	public function viewMultiDokumentTransition($transition, $result, $gliederungid, $kategorieid, $statusid) {
		return $this->viewDokumentTransition($transition, $result, $this->link("dokumente_transitionactionmulti", $transition->getDokumentTransitionID(), $gliederungid, $kategorieid, $statusid));
	}

	private function viewDokumentTransition($transition, $result, $link) {
		if (isset($result["redirect"])) {
			return $this->redirect($result["redirect"]);
		}
		$this->smarty->assign("transition", $this->parseDokumentTransition($transition));
		$this->smarty->assign("link", $link);
		if ($transition instanceof RenameDokumentTransition) {
			if (isset($result["selectTransition"])) {
				$this->smarty->assign("dokumenttemplates", $this->parseDokumentTemplates($result["templates"]));
				$this->smarty->display("dokumenttransition_rename.html.tpl");
			} else {
				$link .= "&templateid=" . urlencode($result["template"]->getDokumentTemplateID());
				$this->viewDokumentTemplate($transition->getDokumentTemplate($this->session), $link, $this->translate("%s umbenennen", $transition->getDokumentTemplate($this->session)->getLabel()), array("hideupload"));
			}
		}
	}

	public function viewDokumentTransitionProcess($transition, $process, $result) {
		if (isset($result["redirect"])) {
			return $this->redirect($result["redirect"]);
		}
		$this->smarty->assign("transition", $this->parseDokumentTransition($transition));
		$this->smarty->assign("process", $this->parseProcess($process));
	}

	public function viewMitgliedDokumentForm($mitglied, $dokument) {
		if ($mitglied != null) {
			$this->smarty->assign("mitglied", $this->parseMitglied($mitglied));
		}
		if ($dokument != null) {
			$this->smarty->assign("dokument", $this->parseDokument($dokument));
		}
		$this->smarty->assign("mode", $_REQUEST["mode"]);
		$this->smarty->display("mitglieddokument.html.tpl");
	}

	public function viewFileImagePreview($file, $token) {
		$this->smarty->assign("file", $this->parseFile($file));
		$this->smarty->assign("token", $token);
		$this->smarty->display("fileimagepreview.html.tpl");
	}

	public function viewFileTextPreview($file, $token) {
		$this->smarty->assign("file", $this->parseFile($file));
		$this->smarty->assign("content", $file->getContent());
		$this->smarty->display("filetextpreview.html.tpl");
	}

	public function viewFilePDFPreview($file, $token) {
		$this->smarty->assign("file", $this->parseFile($file));
		$this->smarty->assign("token", $token);
		$this->smarty->display("filepdfpreview.html.tpl");
	}

	public function viewEMailBounceList($bounces) {
		$this->smarty->assign("bounces", $this->parseEMailBounces($bounces));
		$this->smarty->display("emailbounces.html.tpl");
	}

	public function getRedirectURL() {
		return isset($_REQUEST["redirect"]) ? $_REQUEST["redirect"] : $_SERVER["HTTP_REFERER"];
	}

	public function redirect($url = null) {
		if ($url === null) {
			// TODO $session->getVariable nutzen
			$url = $this->getRedirectURL();
		}
		header('Location: ' . $url);
		echo 'Sie werden weitergeleitet: <a href="'.$url.'">'.$url.'</a>';
	}
}

?>
