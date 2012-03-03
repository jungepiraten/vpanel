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
	public function setUser($userid, $username, $password, $passwordsalt, $apikey, $defaultdokumentkategorieid, $defaultdokumentstatusid);
	public function delUser($userid);
	public function getUserRoleResult($userid);
	public function getUserRoleList($userid);
	public function setUserRoleList($userid, $roles);

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
	public function setBeitrag($beitragid, $label, $hoehe);
	public function searchBeitrag($label, $hoehe);
	public function delBeitrag($beitragid);

	public function getMitgliederBeitragByMitgliedResult($mitgliedid);
	public function getMitgliederBeitragByMitgliedList($mitgliedid);
	public function setMitgliederBeitragByMitgliedList($mitgliedid, $beitragid, $hoehelist, $bezahltlist);
	public function delMitgliederBeitragByMitglied($mitgliedid);
	public function getMitgliederBeitragByBeitragCount($beitragid);
	public function getMitgliederBeitragByBeitragResult($beitragid, $pagesize = null, $offset = null);
	public function getMitgliederBeitragByBeitragList($beitragid, $pagesize = null, $offset = null);
	public function setMitgliederBeitragByBeitragList($beitragid, $mitgliedids, $hoehelist, $bezahltlist);
	public function delMitgliederBeitragByBeitrag($beitragid);
	public function getMitgliederBeitrag($mitgliedid, $beitragid);
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

	public function getMailTemplateResult();
	public function getMailTemplateList();
	public function getMailTemplate($mailtemplateid);
	public function setMailTemplate($mailtemplateid, $label, $body);
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
	public function setProcess($processid, $type, $typedata, $progess, $queued, $started, $finished, $finishedpage);
	public function delProcess($processid);

	public function getDokumentResult($gliederungids, $gliederungid = null, $dokumentkategorieid = null, $dokumentstatus = null, $limit = null, $offset = null);
	public function getDokumentList($gliederungids, $gliederungid = null, $dokumentkategorieid = null, $dokumentstatus = null, $limit = null, $offset = null);
	public function getDokumentByMitgliedResult($mitgliedid);
	public function getDokumentByMitgliedList($mitgliedid);
	public function getDokumentIdentifierMaxNumber($identifierPrefix, $identifierNumberLength);
	public function getDokumentSearchResult($gliederungids, $query, $limit = null, $offset = null);
	public function getDokumentSearchList($gliederungids, $query, $limit = null, $offset = null);
	public function getDokumentCount($gliederungids, $gliederungid = null, $dokumentkategorieid = null, $dokumentstatus = null);
	public function getDokument($dokumentid);
	public function setDokument($dokumentid, $gliederungid, $dokumentkategorieid, $dokumentstatus, $identifier, $label, $content, $data, $fileid);

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

	public function getDokumentNotizResult($dokumentid = null);
	public function getDokumentNotizList($dokumentid = null);
	public function getDokumentNotiz($dokumentnotizid);
	public function setDokumentNotiz($dokumentnotizid, $dokumentid, $author, $timestamp, $nextState, $nextKategorie, $kommentar);
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

	public function getMitgliederStatistikResult();
	public function getMitgliederStatistikList();
	public function getMitgliederStatistik($tempfileid);
	public function setMitgliederStatistik($statistikid, $userid, $timestamp, $agegraphfileid, $timegraphfileid, $timebalancegraphfileid, $gliederungchartfileid, $statechartfileid, $mitgliedschaftchartfileid);
	public function delMitgliederStatistik($statistikid);

	public function getMitgliedTemplateResult($gliederungids = null);
	public function getMitgliedTemplateList($gliederungids = null);
	public function getMitgliedTemplate($templateid);
	public function setMitgliedTemplate($templateid, $label, $gliederungid, $mitgliedschaftid, $beitrag, $createmailtemplateid);
	public function delMitgliedTemplate($templateid);

	public function getMitgliederFilterList($gliederungids);
	public function hasMitgliederFilter($filterid);
	public function getMitgliederFilter($filterid);

	public function getDokumentTemplateList($gliederungids);
	public function hasDokumentTemplate($templateid);
	public function getDokumentTemplate($templateid);
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

	public function getMailTemplateList() {
		return $this->getMailTemplateResult()->fetchAll();
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

	public function getDokumentList($gliederungids, $gliederungid = null, $dokumentkategorieid = null, $dokumentstatusid = null, $limit = null, $offset = null) {
		return $this->getDokumentResult($gliederungids, $gliederungid, $dokumentkategorieid, $dokumentstatusid, $limit = null, $offset = null)->fetchAll();
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

	public function getDokumentNotizList($dokumentid = null) {
		return $this->getDokumentNotizResult($dokumentid)->fetchAll();
	}

	public function getFileList() {
		return $this->getFileResult()->fetchAll();
	}

	public function getTempFileList() {
		return $this->getTempFileResult()->fetchAll();
	}

	public function getMitgliederStatistikList() {
		return $this->getMitgliederStatistikResult()->fetchAll();
	}

	public function getMitgliedTemplateList($gliederungids = null) {
		return $this->getMitgliedTemplateResult($gliederungids)->fetchAll();
	}

	/** Filter **/
	private $mitgliederfilters = array();
	public function getMitgliederFilterList($gliederungids) {
		$filters = array();
		foreach ($this->mitgliederfilters as $filter) {
			if ($filter->getGliederungID() == null || in_array($filter->getGliederungID(), $gliederungids)) {
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
		$this->mitgliederfilters[$filter->getFilterID()] = $filter;
	}

	/** DokumentTemplates **/
	private $dokumenttemplates = array();
	public function getDokumentTemplateList($gliederungids) {
		$templates = array();
		foreach ($this->dokumenttemplates as $template) {
			if ($template->getGliederungID() == null || in_array($template->getGliederungID(), $gliederungids)) {
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
		$this->dokumenttemplates[$template->getDokumentTemplateID()] = $template;
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
