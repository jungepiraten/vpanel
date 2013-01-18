<?php

interface Storage {
	public function getSessionData($sessionid);
	public function setSessionData($sessionid, $timestamp, $data);
	public function delSessionDataBefore($timestamp);

	public function getPermissionResult();
	public function getPermissionList();
	public function getPermissionGlobalResult();
	public function getPermissionGlobalList();
	public function getPermissionLocalResult();
	public function getPermissionLocalList();
	public function getPermission($permissionid);

	public function getUserResult();
	public function getUserList();
	public function getUser($userid);
	public function getUserByUsername($username);
	public function setUser($userid, $username, $password, $passwordsalt, $apikey, $aktiv, $defaultgliederungid, $defaultdokumentkategorieid, $defaultdokumentstatusid);
	public function delUser($userid);
	public function getUserRoleResult($userid);
	public function getUserRoleList($userid);
	public function setUserRoleList($userid, $roles);

	public function getDashboardWidgetResult($userid);
	public function getDashboardWidgetList($userid);
	public function getDashboardWidget($userid);
	public function setDashboardWidget($widgetid, $userid, $column, $type, $typedata);
	public function delDashboardWidget($widgetid);

	public function getRoleResult();
	public function getRoleList();
	public function getRole($roleid);
	public function setRole($roleid, $label, $description);
	public function delRole($roleid);
	public function getRolePermissionResult($roleid);
	public function getRolePermissionList($roleid);
	public function setRolePermissionList($roleid, $permissions);
	public function getRoleUserResult($roleid);
	public function getRoleUserList($roleid);
	public function setRoleUserList($roleid, $userids);

	public function getGliederungResult($gliederungids = null);
	public function getGliederungList($gliederungids = null);
	public function getGliederung($gliederungid);
	public function setGliederung($gliederungid, $label, $parentid);

	public function getBeitragTimeFormatResult();
	public function getBeitragTimeFormatList();
	public function getBeitragTimeFormat($beitragtimeformatid);
	public function setBeitragTimeFormat($beitragtimeformatid, $label, $format);
	public function delBeitragTimeFormat($beitragtimeformatid);

	public function getBeitragResult();
	public function getBeitragList();
	public function getBeitrag($beitragid);
	public function setBeitrag($beitragid, $label, $hoehe, $mailtemplateid);
	public function searchBeitrag($label);
	public function delBeitrag($beitragid);

	public function getMitgliederBeitragByMitgliedResult($mitgliedid);
	public function getMitgliederBeitragByMitgliedList($mitgliedid);
	public function getMitgliederBeitragByBeitragCount($beitragid);
	public function getMitgliederBeitragByBeitragResult($beitragid, $pagesize = null, $offset = null);
	public function getMitgliederBeitragByBeitragList($beitragid, $pagesize = null, $offset = null);
	public function getMitgliederBeitrag($beitragid);
	public function setMitgliederBeitrag($beitragid, $mitgliedid, $beitragid, $hoehe);
	public function delMitgliederBeitrag($beitragid);

	public function getMitgliederBeitragBuchungResultTimeline($gliederungids, $start, $count);
	public function getMitgliederBeitragBuchungListTimeline($gliederungids, $start, $count);
	public function getMitgliederBeitragBuchungByBeitragResult($beitragid);
	public function getMitgliederBeitragBuchungByBeitragList($beitragid);
	public function getMitgliederBeitragBuchungByMitgliederBeitragResult($beitragid);
	public function getMitgliederBeitragBuchungByMitgliederBeitragList($beitragid);
	public function getMitgliederBeitragBuchung($buchungid);
	public function setMitgliederBeitragBuchung($buchungid, $beitragid, $gliederungid, $userid, $timestamp, $vermerk, $hoehe);
	public function delMitgliederBeitragBuchung($buchungid);

	public function addMitgliedDokument($mitgliedid, $dokumentid);
	public function delMitgliedDokument($mitgliedid, $dokumentid);

	public function getMitgliederResult($filter = null, $limit = null, $offset = null);
	public function getMitgliederList($filter = null, $limit = null, $offset = null);
	public function getMitglied($mitgliedid);
	public function getMitgliederCount($filter = null);
	public function setMitglied($mitgliedid, $globalid, $eintritt, $austritt);

	public function getMitgliedFlagResult();
	public function getMitgliedFlagList();
	public function getMitgliedFlag($flagid);
	public function setMitgliedFlag($flagid, $label);

	public function getMitgliedTextFieldResult();
	public function getMitgliedTextFieldList();
	public function getMitgliedTextField($flagid);
	public function setMitgliedTextField($flagid, $label);

	public function getMitgliederRevisionResultTimeline($gliederungids, $start, $count);
	public function getMitgliederRevisionListTimeline($gliederungids, $start, $count);
	public function getMitgliederRevisionResult();
	public function getMitgliederRevisionList();
	public function getMitgliederRevisionsByMitgliedIDResult($mitgliedid);
	public function getMitgliederRevisionsByMitgliedIDList($mitgliedid);
	public function getMitgliederRevision($revisionid);
	public function setMitgliederRevision($revisionid, $globalid, $timestamp, $userid, $mitgliedid, $mitgliedschaftid, $gliederungid, $geloescht, $beitrag, $beitragtimeformatid, $natpersonid, $jurpersonid, $kontaktid, $kommentar);

	public function getMitgliederRevisionFlagResult($revisionid);
	public function getMitgliederRevisionFlagList($revisionid);
	public function setMitgliederRevisionFlagList($revisionid, $flags);

	public function getMitgliederRevisionTextFieldResult($revisionid);
	public function getMitgliederRevisionTextFieldList($revisionid);
	public function setMitgliederRevisionTextFieldList($revisionid, $textfieldids, $textfieldvalues);

	public function getKontakt($kontaktid);
	public function setKontakt($kontaktid, $adresszusatz, $strasse, $hausnummer, $ortid, $telefon, $handy, $email, $iban);
	public function delKontakt($kontaktid);
	public function searchKontakt($strasse, $adresszusatz, $hausnummer, $ortid, $telefon, $handy, $email, $iban);

	public function getEMail($emailid);
	public function setEMail($emailid, $email);
	public function delEMail($emailid);
	public function searchEMail($email);

	public function getEMailBounceResultTimeline($start, $count);
	public function getEMailBounceListTimeline($start, $count);
	public function getEMailBounceResultByEMail($emailid);
	public function getEMailBounceListByEMail($emailid);
	public function getEMailBounce($bounceid);
	public function setEMailBounce($bounceid, $emailid, $timestamp, $message);
	public function delEMailBounce($bounceid);

	public function getOrtResult();
	public function getOrtList();
	public function getOrtResultLimit($plz = null, $label = null, $stateid = null, $count = null);
	public function getOrtListLimit($plz = null, $label = null, $stateid = null, $count = null);
	public function getOrt($ortid);
	public function setOrt($ortid, $plz, $label, $latitude, $longitude, $stateid);
	public function delOrt($ortid);
	public function searchOrt($plz, $label, $stateid);

	public function getStateResult();
	public function getStateList();
	public function getState($stateid);
	public function setState($stateid, $label, $population, $countryid);
	public function delState($stateid);

	public function getCountryResult();
	public function getCountryList();
	public function getCountry($countryid);
	public function setCountry($countryid, $label);
	public function delCountry($countryid);

	public function getMitgliedschaftResult();
	public function getMitgliedschaftList();
	public function getMitgliedschaft($mitgliedschaftid);
	public function setMitgliedschaft($mitgliedschaftid, $globalid, $label, $description);
	public function delMitgliedschaft($mitgliedschaftid);

	public function getNatPerson($natpersonid);
	public function setNatPerson($natpersonid, $anrede, $name, $vorname, $geburtsdatum, $nationalitaet);
	public function delNatPerson($natpersonid);
	public function searchNatPerson($anrede, $name, $vorname, $geburtsdatum, $nationalitaet);

	public function getJurPerson($jurpersonid);
	public function setJurPerson($jurpersonid, $firma);
	public function delJurPerson($jurpersonid);
	public function searchJurPerson($firma);

	public function getMailTemplateResult($gliederungid);
	public function getMailTemplateList($gliederungids);
	public function getMailTemplate($mailtemplateid);
	public function setMailTemplate($mailtemplateid, $gliederungid, $label, $body);
	public function delMailTemplate($mailtemplateid);
	public function getMailTemplateHeaderResult($mailtemplateid);
	public function getMailTemplateHeaderList($mailtemplateid);
	public function setMailTemplateHeaderList($mailtemplateid, $headerids, $values);
	public function getMailTemplateAttachmentResult($mailtemplateid);
	public function getMailTemplateAttachmentList($mailtemplateid);
	public function setMailTemplateAttachmentList($mailtemplateid, $files);

	public function getProcessResult();
	public function getProcessList();
	public function getProcess($processid);
	public function setProcess($processid, $userid, $type, $typedata, $progess, $queued, $started, $finished, $finishedpage);
	public function delProcess($processid);

	public function getDokumentResult($matcher, $limit = null, $offset = null);
	public function getDokumentList($matcher, $limit = null, $offset = null);
	public function getDokumentIdentifierMaxNumber($identifierPrefix, $identifierNumberLength);
	public function getDokumentCount($matcher);
	public function getDokument($dokumentid);
	public function setDokument($dokumentid);
	public function delDokument($dokumentid);

	public function getDokumentNotifyResult($gliederungid = null, $dokumentkategorieid = null, $dokumentstatusid = null);
	public function getDokumentNotifyList($gliederungid = null, $dokumentkategorieid = null, $dokumentstatusid = null);
	public function getDokumentNotify($dokumentnotifyid);
	public function setDokumentNotify($dokumentnotifyid, $gliederungid, $dokumentkategorieid, $dokumentstatusid, $emailid);

	public function getDokumentRevisionResultTimeline($gliederungids, $start, $count);
	public function getDokumentRevisionListTimeline($gliederungids, $start, $count);
	public function getDokumentRevisionResult($dokumentid = null);
	public function getDokumentRevisionList($dokumentid = null);
	public function getDokumentRevision($revisionid);
	public function setDokumentRevision($revisionid, $timestamp, $userid, $dokumentid, $gliederungid, $kategorieid, $statusid, $identifier, $label, $content, $data, $fileid, $kommentar);
	public function delDokumentRevision($revisionid);

	public function getDokumentRevisionFlagResult($revisionid);
	public function getDokumentRevisionFlagList($revisionid);
	public function setDokumentRevisionFlagList($revisionid, $flags);

	public function getDokumentKategorieResult();
	public function getDokumentKategorieList();
	public function getDokumentKategorie($dokumentkategorieid);
	public function setDokumentKategorie($dokumentkategorieid, $label);
	public function delDokumentKategorie($dokumentkategorieid);

	public function getDokumentStatusResult();
	public function getDokumentStatusList();
	public function getDokumentStatus($dokumentstatusid);
	public function setDokumentStatus($dokumentstatusid, $label);
	public function delDokumentStatus($dokumentstatusid);

	public function getDokumentFlagResult();
	public function getDokumentFlagList();
	public function getDokumentFlag($flagid);
	public function setDokumentFlag($flagid, $status);
	public function delDokumentFlag($flagid);

	public function getFileResult();
	public function getFileList();
	public function getFile($fileid);
	public function setFile($fileid, $filename, $exportfilename, $mimetype);
	public function delFile($fileid);

	public function getTempFileResult();
	public function getTempFileList();
	public function getTempFile($tempfileid);
	public function setTempFile($tempfileid, $userid, $timestamp, $fileid);
	public function delTempFile($tempfileid);

	public function getMitgliederTemplateList($session = null);
	public function hasMitgliederTemplate($templateid);
	public function getMitgliederTemplate($templateid);

	public function getMitgliederFilterList($session = null);
	public function hasMitgliederFilter($filterid);
	public function getMitgliederFilter($filterid);

	public function getMitgliederFilterActionList($session = null);
	public function hasMitgliederFilterAction($actionid);
	public function getMitgliederFilterAction($actionid);

	public function getDokumentTemplateList($session = null);
	public function hasDokumentTemplate($templateid);
	public function getDokumentTemplate($templateid);

	public function getDokumentFilterList($session = null);
	public function hasDokumentFilter($filterid);
	public function getDokumentFilter($filterid);

	public function getSingleDokumentTransitionList($session, $dokument);
	public function getMultiDokumentTransitionList($session, $kategorieid, $statusid);
	public function hasDokumentTransition($transitionid);
	public function getDokumentTransition($transitionid);

	public function getMitgliederBadgeList();
	public function hasMitgliederBadge($badgeid);
	public function getMitgliederBadge($badgeid);

	public function getDokumentBadgeList();
	public function hasDokumentBadge($badgeid);
	public function getDokumentBadge($badgeid);
}

abstract class AbstractStorage implements Storage {
	public function getPermissionList() {
		return $this->getPermissionResult()->fetchAll();
	}

	public function getPermissionGlobalList() {
		return $this->getPermissionGlobalResult()->fetchAll();
	}

	public function getPermissionLocalList() {
		return $this->getPermissionLocalResult()->fetchAll();
	}

	public function getUserList() {
		return $this->getUserResult()->fetchAll();
	}

	public function getUserRoleList($userid) {
		return $this->getUserRoleResult($userid)->fetchAll();
	}

	public function getDashboardWidgetList($userid) {
		return $this->getDashboardWidgetResult($userid)->fetchAll();
	}

	public function getRoleList() {
		return $this->getRoleResult()->fetchAll();
	}

	public function getRolePermissionList($roleid) {
		return $this->getRolePermissionResult($roleid)->fetchAll();
	}

	public function getRoleUserList($roleid) {
		return $this->getRoleUserResult($roleid)->fetchAll();
	}

	public function getGliederungList($gliederungids = null) {
		return $this->getGliederungResult($gliederungids)->fetchAll();
	}

	public function getBeitragTimeFormatList() {
		return $this->getBeitragTimeFormatResult()->fetchAll();
	}

	public function getBeitragList() {
		return $this->getBeitragResult()->fetchAll();
	}

	public function getMitgliederBeitragByMitgliedList($mitgliedid) {
		return $this->getMitgliederBeitragByMitgliedResult($mitgliedid)->fetchAll();
	}

	public function getMitgliederBeitragByBeitragList($beitragid, $pagesize = null, $offset = null) {
		return $this->getMitgliederBeitragByBeitragResult($beitragid, $pagesize, $offset)->fetchAll();
	}

	public function getMitgliederBeitragBuchungListTimeline($gliederungids, $start, $count) {
		return $this->getMitgliederBeitragBuchungResultTimeline($gliederungids, $start, $count)->fetchAll();
	}

	public function getMitgliederBeitragBuchungByBeitragList($beitragid) {
		return $this->getMitgliederBeitragBuchungByBeitragResult($beitragid)->fetchAll();
	}

	public function getMitgliederBeitragBuchungByMitgliederBeitragList($beitragid) {
		return $this->getMitgliederBeitragBuchungByMitgliederBeitragResult($beitragid)->fetchAll();
	}

	public function getMitgliederList($filter = null, $limit = null, $offset = null) {
		return $this->getMitgliederResult($filter, $limit, $offset)->fetchAll();
	}

	public function getMitgliedFlagList() {
		return $this->getMitgliedFlagResult()->fetchAll();
	}

	public function getMitgliedTextFieldList() {
		return $this->getMitgliedTextFieldResult()->fetchAll();
	}

	public function getMitgliederRevisionListTimeline($gliederungids, $start, $count) {
		return $this->getMitgliederRevisionResultTimeline($gliederungids, $start, $count)->fetchAll();
	}

	public function getMitgliederRevisionList() {
		return $this->getMitgliederRevisionResult()->fetchAll();
	}

	public function getMitgliederRevisionsByMitgliedIDList($mitgliedid) {
		return $this->getMitgliederRevisionsByMitgliedIDResult($mitgliedid)->fetchAll();
	}

	public function getMitgliederRevisionFlagList($revisionid) {
		return $this->getMitgliederRevisionFlagResult($revisionid)->fetchAll();
	}

	public function getMitgliederRevisionTextFieldList($revisionid) {
		return $this->getMitgliederRevisionTextFieldResult($revisionid)->fetchAll();
	}

	public function getEMailBounceListTimeline($start, $count) {
		return $this->getEMailBounceResultTimeline($start, $count)->fetchAll();
	}

	public function getEMailBounceListByEMail($emailid) {
		return $this->getEMailBounceResultByEMail($emailid)->fetchAll();
	}

	public function getOrtList() {
		return $this->getOrtResult()->fetchAll();
	}

	public function getOrtListLimit($plz = null, $label = null, $stateid = null, $count = null) {
		return $this->getOrtResultLimit($plz, $label, $stateid, $count)->fetchAll();
	}

	public function getStateList() {
		return $this->getStateResult()->fetchAll();
	}

	public function getCountryList() {
		return $this->getCountryResult()->fetchAll();
	}

	public function getMitgliedschaftList() {
		return $this->getMitgliedschaftResult()->fetchAll();
	}

	public function getMailTemplateList($gliederungid) {
		return $this->getMailTemplateResult($gliederungid)->fetchAll();
	}

	public function getMailTemplateHeaderList($mailtemplateid) {
		return $this->getMailTemplateHeaderResult($mailtemplateid)->fetchAll();
	}

	public function getMailTemplateAttachmentList($mailtemplateid) {
		return $this->getMailTemplateAttachmentResult($mailtemplateid)->fetchAll();
	}

	public function getProcessList() {
		return $this->getProcessResult()->fetchAll();
	}

	public function getDokumentList($matcher, $limit = null, $offset = null) {
		return $this->getDokumentResult($matcher, $limit, $offset)->fetchAll();
	}

	public function getDokumentDokumentFlagList($dokumentid) {
		return $this->getDokumentDokumentFlagResult($dokumentid)->fetchAll();
	}

	public function getDokumentNotifyList($gliederungid = null, $dokumentkategorieid = null, $dokumentstatusid = null) {
		return $this->getDokumentNotifyResult($gliederungid, $dokumentkategorieid, $dokumentstatusid)->fetchAll();
	}

	public function getDokumentKategorieList() {
		return $this->getDokumentKategorieResult()->fetchAll();
	}

	public function getDokumentStatusList() {
		return $this->getDokumentStatusResult()->fetchAll();
	}

	public function getDokumentFlagList() {
		return $this->getDokumentFlagResult()->fetchAll();
	}

	public function getDokumentRevisionListTimeline($gliederungids, $start, $count) {
		return $this->getDokumentRevisionResultTimeline($gliederungids, $start, $count)->fetchAll();
	}

	public function getDokumentRevisionList($dokumentid = null) {
		return $this->getDokumentRevisionResult($dokumentid)->fetchAll();
	}

	public function getDokumentRevisionFlagList($revisionid) {
		return $this->getDokumentRevisionFlagResult($revisionid)->fetchAll();
	}

	public function getFileList() {
		return $this->getFileResult()->fetchAll();
	}

	public function getTempFileList() {
		return $this->getTempFileResult()->fetchAll();
	}

	/** MitgliederTemplate **/
	private $mitgliedertemplates = array();
	public function getMitgliederTemplateList($session = null) {
		if ($session == null) {
			return $this->mitgliedertemplates;
		}
		$templates = array();
		foreach ($this->getMitgliederTemplateList() as $template) {
			if ($template->isAllowed($session)) {
				$templates[] = $template;
			}
		}
		return $templates;
	}
	public function hasMitgliederTemplate($templateid) {
		return isset($this->mitgliedertemplates[$templateid]);
	}
	public function getMitgliederTemplate($templateid) {
		if (!$this->hasMitgliederTemplate($templateid)) {
			return null;
		}
		return $this->mitgliedertemplates[$templateid];
	}
	public function registerMitgliederTemplate($template) {
		$template->setStorage($this);
		$this->mitgliedertemplates[$template->getMitgliedTemplateID()] = $template;
	}

	/** MitgliederFilter **/
	private $mitgliederfilters = array();
	public function getMitgliederFilterList($session = null) {
		if ($session == null) {
			return $this->mitgliederfilters;
		}
		$filters = array();
		foreach ($this->getMitgliederFilterList() as $filter) {
			if ($filter->isAllowed($session)) {
				$filters[] = $filter;
			}
		}
		return $filters;
	}
	public function hasMitgliederFilter($filterid) {
		return isset($this->mitgliederfilters[$filterid]);
	}
	public function getMitgliederFilter($filterid) {
		if (!$this->hasMitgliederFilter($filterid)) {
			return null;
		}
		return $this->mitgliederfilters[$filterid];
	}
	public function registerMitgliederFilter($filter) {
		$filter->setStorage($this);
		$this->mitgliederfilters[$filter->getFilterID()] = $filter;
	}

	/** MitgliederFilterAction **/
	private $mitgliederfilteractions = array();
	public function getMitgliederFilterActionList($session = null) {
		if ($session == null) {
			return $this->mitgliederfilteractions;
		}
		$actions = array();
		foreach ($this->getMitgliederFilterActionList() as $action) {
			if ($action->isAllowed($session)) {
				$actions[] = $action;
			}
		}
		return $actions;
	}
	public function hasMitgliederFilterAction($actionid) {
		return isset($this->mitgliederfilteractions[$actionid]);
	}
	public function getMitgliederFilterAction($actionid) {
		if (!$this->hasMitgliederFilterAction($actionid)) {
			return null;
		}
		return $this->mitgliederfilteractions[$actionid];
	}
	public function registerMitgliederFilterAction($action) {
		$action->setStorage($this);
		$this->mitgliederfilteractions[$action->getActionID()] = $action;
	}

	/** DokumentFilter **/
	private $dokumentfilters = array();
	public function getDokumentFilterList($session = null) {
		if ($session == null) {
			return $this->dokumentfilters;
		}
		$filters = array();
		foreach ($this->getDokumentFilterList() as $filter) {
			if ($filter->isAllowed($session)) {
				$filters[] = $filter;
			}
		}
		return $filters;
	}
	public function hasDokumentFilter($filterid) {
		return isset($this->dokumentfilters[$filterid]);
	}
	public function getDokumentFilter($filterid) {
		if (!$this->hasDokumentFilter($filterid)) {
			return null;
		}
		return $this->dokumentfilters[$filterid];
	}
	public function registerDokumentFilter($filter) {
		$filter->setStorage($this);
		$this->dokumentfilters[$filter->getFilterID()] = $filter;
	}

	/** DokumentTemplates **/
	private $dokumenttemplates = array();
	public function getDokumentTemplateList($session = null) {
		if ($session == null) {
			return $this->dokumenttemplates;
		}
		$templates = array();
		foreach ($this->getDokumentTemplateList() as $template) {
			if ($template->isAllowed($session)) {
				$templates[] = $template;
			}
		}
		return $templates;
	}
	public function hasDokumentTemplate($templateid) {
		return isset($this->dokumenttemplates[$templateid]);
	}
	public function getDokumentTemplate($templateid) {
		if (!$this->hasDokumentTemplate($templateid)) {
			return null;
		}
		return $this->dokumenttemplates[$templateid];
	}
	public function registerDokumentTemplate($template) {
		$template->setStorage($this);
		$this->dokumenttemplates[$template->getDokumentTemplateID()] = $template;
	}

	/** DokumentTransitionen **/
	private $dokumenttransitionen = array();
	public function getSingleDokumentTransitionList($session, $dokument) {
		$transitionen = array();
		foreach ($this->dokumenttransitionen as $transition) {
			if ($transition instanceof SingleDokumentTransition && $transition->isMatching($session, $dokument->getLatestRevision()->getKategorieID(), $dokument->getLatestRevision()->getStatusID())) {
				$transitionen[] = $transition;
			}
		}
		return $transitionen;
	}
	public function getMultiDokumentTransitionList($session, $kategorieid, $statusid) {
		if ($kategorieid instanceof DokumentKategorie) {
			$kategorieid = $kategorieid->getDokumentKategorieID();
		}
		if ($statusid instanceof DokumentStatus) {
			$statusid = $statusid->getDokumentStatusID();
		}

		$transitionen = array();
		foreach ($this->dokumenttransitionen as $transition) {
			if ($transition instanceof MultiDokumentTransition && $transition->isMatching($session, $kategorieid, $statusid)) {
				$transitionen[] = $transition;
			}
		}
		return $transitionen;
	}
	public function hasDokumentTransition($transitionid) {
		return isset($this->dokumenttransitionen[$transitionid]);
	}
	public function getDokumentTransition($transitionid) {
		if (!$this->hasDokumentTransition($transitionid)) {
			return null;
		}
		return $this->dokumenttransitionen[$transitionid];
	}
	public function registerDokumentTransition($transition) {
		$transition->setStorage($this);
		$this->dokumenttransitionen[$transition->getDokumentTransitionID()] = $transition;
	}

	/** MitgliederBadge **/
	private $mitgliederbadges = array();
	public function getMitgliederBadgeList() {
		return $this->mitgliederbadges;
	}
	public function hasMitgliederBadge($badgeid) {
		return isset($this->mitgliederbadges[$badgeid]);
	}
	public function getMitgliederBadge($badgeid) {
		return $this->mitgliederbadges[$badgeid];
	}
	public function registerMitgliederBadge($badge) {
		$this->mitgliederbadges[$badge->getBadgeID()] = $badge;
	}

	/** DokumentBadge **/
	private $dokumentbadges = array();
	public function getDokumentBadgeList() {
		return $this->dokumentbadges;
	}
	public function hasDokumentBadge($badgeid) {
		return isset($this->dokumentbadges[$badgeid]);
	}
	public function getDokumentBadge($badgeid) {
		return $this->dokumentbadges[$badgeid];
	}
	public function registerDokumentBadge($badge) {
		$this->dokumentbadges[$badge->getBadgeID()] = $badge;
	}
}

interface StorageResult {
	public function fetchRow();
	public function fetchAll();
	public function getCount();
}

abstract class AbstractStorageResult implements StorageResult {}

class EmptyStorageResult extends AbstractStorageResult {
	public function fetchRow() {
		return null;
	}

	public function fetchAll() {
		return array();
	}

	public function getCount() {
		return 0;
	}
}

?>
