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
	public function getMitgliederByDokumentResult($dokumentid);
	public function getMitgliederByDokumentList($dokumentid);
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

	public function getMitgliedNotizResult($mitgliedid = null);
	public function getMitgliedNotizList($mitgliedid = null);
	public function getMitgliedNotiz($mitgliednotizid);
	public function setMitgliedNotiz($mitgliednotizid, $mitgliedid, $author, $timestamp, $kommentar);
	public function delMitgliedNotiz($mitgliednotizid);

	public function getMitgliederRevisionResultTimeline($gliederungids, $start, $count);
	public function getMitgliederRevisionListTimeline($gliederungids, $start, $count);
	public function getMitgliederRevisionResult();
	public function getMitgliederRevisionList();
	public function getMitgliederRevisionsByMitgliedIDResult($mitgliedid);
	public function getMitgliederRevisionsByMitgliedIDList($mitgliedid);
	public function getMitgliederRevision($revisionid);
	public function setMitgliederRevision($revisionid, $globalid, $timestamp, $userid, $mitgliedid, $mitgliedschaftid, $gliederungid, $geloescht, $beitrag, $natpersonid, $jurpersonid, $kontaktid);

	public function getMitgliederRevisionFlagResult($revisionid);
	public function getMitgliederRevisionFlagList($revisionid);
	public function setMitgliederRevisionFlagList($revisionid, $flags);

	public function getMitgliederRevisionTextFieldResult($revisionid);
	public function getMitgliederRevisionTextFieldList($revisionid);
	public function setMitgliederRevisionTextFieldList($revisionid, $textfieldids, $textfieldvalues);

	public function getKontakt($kontaktid);
	public function setKontakt($kontaktid, $adresszusatz, $strasse, $hausnummer, $ortid, $telefon, $handy, $email);
	public function delKontakt($kontaktid);
	public function searchKontakt($strasse, $adresszusatz, $hausnummer, $ortid, $telefon, $handy, $email);

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

	public function getDokumentResult($gliederungids, $dokumentkategorieid = null, $dokumentstatus = null, $limit = null, $offset = null);
	public function getDokumentList($gliederungids, $dokumentkategorieid = null, $dokumentstatus = null, $limit = null, $offset = null);
	public function getDokumentByMitgliedResult($mitgliedid);
	public function getDokumentByMitgliedList($mitgliedid);
	public function getDokumentIdentifierMaxNumber($identifierPrefix, $identifierNumberLength);
	public function getDokumentSearchResult($gliederungids, $query, $limit = null, $offset = null);
	public function getDokumentSearchList($gliederungids, $query, $limit = null, $offset = null);
	public function getDokumentCount($gliederungids, $dokumentkategorieid = null, $dokumentstatus = null);
	public function getDokument($dokumentid);
	public function setDokument($dokumentid, $gliederungid, $dokumentkategorieid, $dokumentstatus, $identifier, $label, $content, $data, $fileid);
	public function delDokument($dokumentid);

	public function getDokumentNotifyResult($gliederungid = null, $dokumentkategorieid = null, $dokumentstatusid = null);
	public function getDokumentNotifyList($gliederungid = null, $dokumentkategorieid = null, $dokumentstatusid = null);
	public function getDokumentNotify($dokumentnotifyid);
	public function setDokumentNotify($dokumentnotifyid, $gliederungid, $dokumentkategorieid, $dokumentstatusid, $emailid);

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

	public function getDokumentNotizResultTimeline($gliederungids, $start, $count);
	public function getDokumentNotizListTimeline($gliederungids, $start, $count);
	public function getDokumentNotizResult($dokumentid = null);
	public function getDokumentNotizList($dokumentid = null);
	public function getDokumentNotiz($dokumentnotizid);
	public function setDokumentNotiz($dokumentnotizid, $dokumentid, $author, $timestamp, $nextState, $nextKategorie, $nextLabel, $nextIdentifier, $kommentar);
	public function delDokumentNotiz($dokumentnotizid);

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

	public function getSingleDokumentTransitionList($session, $dokument);
	public function getMultiDokumentTransitionList($session, $kategorieid, $statusid);
	public function hasDokumentTransition($transitionid);
	public function getDokumentTransition($transitionid);
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

	public function getMitgliederByDokumentList($dokumentid) {
		return $this->getMitgliederByDokumentResult($dokumentid)->fetchAll();
	}

	public function getMitgliedFlagList() {
		return $this->getMitgliedFlagResult()->fetchAll();
	}

	public function getMitgliedTextFieldList() {
		return $this->getMitgliedTextFieldResult()->fetchAll();
	}

	public function getMitgliedNotizList($mitgliedid = null) {
		return $this->getMitgliedNotizResult($mitgliedid)->fetchAll();
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

	public function getDokumentList($gliederungids, $dokumentkategorieid = null, $dokumentstatusid = null, $limit = null, $offset = null) {
		return $this->getDokumentResult($gliederungids, $dokumentkategorieid, $dokumentstatusid, $limit, $offset)->fetchAll();
	}

	public function getDokumentByMitgliedList($mitgliedid) {
		return $this->getDokumentByMitgliedResult($mitgliedid)->fetchAll();
	}

	public function getDokumentSearchList($gliederungids, $query, $limit = null, $offset = null) {
		return $this->getDokumentSearchResult($gliederungids, $query, $limit = null, $offset = null)->fetchAll();
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

	public function getDokumentNotizListTimeline($gliederungids, $start, $count) {
		return $this->getDokumentNotizResultTimeline($gliederungids, $start, $count)->fetchAll();
	}

	public function getDokumentNotizList($dokumentid = null) {
		return $this->getDokumentNotizResult($dokumentid)->fetchAll();
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

	/** Filter **/
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

	/** FilterAction **/
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
			if ($transition instanceof SingleDokumentTransition && $transition->isMatching($session, $dokument->getDokumentKategorieID(), $dokument->getDokumentStatusID())) {
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
