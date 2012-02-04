<?php

interface Storage {
	public function getPermissionResult();
	public function getPermissionList();
	public function getPermission($permissionid);

	public function getUserResult();
	public function getUserList();
	public function getUser($userid);
	public function getUserByUsername($username);
	public function setUser($userid, $username, $password, $passwordsalt);
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

	public function getGliederungResult();
	public function getGliederungList();
	public function getGliederung($gliederungid);

	public function addMitgliedDokument($mitgliedid, $dokumentid);
	public function delMitgliedDokument($mitgliedid, $dokumentid);

	public function getMitgliederResult($filter = null, $limit = null, $offset = null);
	public function getMitgliederList($filter = null, $limit = null, $offset = null);
	public function getMitgliederByDokumentResult($dokumentid);
	public function getMitgliederByDokumentList($dokumentid);
	public function getMitglied($mitgliedid);
	public function getMitgliederCount($filter = null);
	public function setMitglied($mitgliedid, $globalid, $eintritt, $austritt);

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
	public function setMitgliederRevision($revisionid, $globalid, $timestamp, $userid, $mitgliedid, $mitgliedschaftid, $gliederungid, $geloescht, $mitgliedpiraten, $verteilereingetragen, $beitrag, $natpersonid, $jurpersonid, $kontaktid);

	public function getKontakt($kontaktid);
	public function setKontakt($kontaktid, $strasse, $hausnummer, $ortid, $telefon, $handy, $email);
	public function delKontakt($kontaktid);
	public function searchKontakt($strasse, $hausnummer, $ortid, $telefon, $handy, $email);

	public function getOrtResult();
	public function getOrtList();
	public function getOrtResultLimit($plz = null, $label = null, $stateid = null, $count = null);
	public function getOrtListLimit($plz = null, $label = null, $stateid = null, $count = null);
	public function getOrt($ortid);
	public function setOrt($ortid, $plz, $label, $stateid);
	public function delOrt($ortid);
	public function searchOrt($plz, $label, $stateid);

	public function getStateResult();
	public function getStateList();
	public function getState($stateid);
	public function setState($stateid, $label, $countryid);
	public function delState($stateid);

	public function getCountryResult();
	public function getCountryList();
	public function getCountry($countryid);
	public function setCountry($countryid, $label);
	public function delCountry($countryid);

	public function getMitgliedschaftResult();
	public function getMitgliedschaftList();
	public function getMitgliedschaft($mitgliedschaftid);
	public function setMitgliedschaft($mitgliedschaftid, $globalid, $label, $description, $defaultbeitrag, $defaultcreatemail);
	public function delMitgliedschaft($mitgliedschaftid);

	public function getNatPerson($natpersonid);
	public function setNatPerson($natpersonid, $name, $vorname, $geburtsdatum, $nationalitaet);
	public function delNatPerson($natpersonid);
	public function searchNatPerson($name, $vorname, $geburtsdatum, $nationalitaet);

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

	public function getDokumentResult($dokumentkategorieid = null, $dokumentstatus = null, $limit = null, $offset = null);
	public function getDokumentList($dokumentkategorieid = null, $dokumentstatus = null, $limit = null, $offset = null);
	public function getDokumentByMitgliedResult($mitgliedid);
	public function getDokumentByMitgliedList($mitgliedid);
	public function getDokumentSearchResult($query, $limit = null, $offset = null);
	public function getDokumentSearchList($query, $limit = null, $offset = null);
	public function getDokumentCount($dokumentkategorieid = null, $dokumentstatus = null);
	public function getDokument($dokumentid);
	public function setDokument($dokumentid, $dokumentkategorieid, $dokumentstatus, $identifier, $label, $content, $fileid);

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
	public function setTempFile($tempfileid, $userid, $fileid);
	public function delTempFile($tempfileid);
}

abstract class AbstractStorage implements Storage {
	public function getPermissionList() {
		return $this->getPermissionResult()->fetchAll();
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

	public function getGliederungList() {
		return $this->getGliederungResult()->fetchAll();
	}

	public function getMitgliederList($filter = null, $limit = null, $offset = null) {
		return $this->getMitgliederResult($filter, $limit, $offset)->fetchAll();
	}

	public function getMitgliederByDokumentList($dokumentid) {
		return $this->getMitgliederByDokumentResult($dokumentid)->fetchAll();
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

	public function getDokumentList($dokumentkategorieid = null, $dokumentstatusid = null, $limit = null, $offset = null) {
		return $this->getDokumentResult($dokumentkategorieid, $dokumentstatusid, $limit = null, $offset = null)->fetchAll();
	}

	public function getDokumentByMitgliedList($mitgliedid) {
		return $this->getDokumentByMitgliedResult($mitgliedid)->fetchAll();
	}

	public function getDokumentSearchList($query, $limit = null, $offset = null) {
		return $this->getDokumentSearchResult($query, $limit = null, $offset = null)->fetchAll();
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
}

interface StorageResult {
	public function fetchRow();
	public function fetchAll();
	public function getCount();
}

abstract class AbstractStorageResult implements StorageResult {}

?>
