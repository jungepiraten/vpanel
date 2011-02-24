<?php

abstract class Storage {
	abstract public function getPermissionResult();
	public function getPermissionList() {
		return $this->getPermissionResult()->fetchAll();
	}
	abstract public function getPermission($permissionid);

	abstract public function getUserResult();
	public function getUserList() {
		return $this->getUserResult()->fetchAll();
	}
	abstract public function getUser($userid);
	abstract public function getUserByUsername($username);
	abstract public function setUser($userid, $username, $password, $passwordsalt);
	abstract public function delUser($userid);
	abstract public function getUserRoleResult($userid);
	public function getUserRoleList($userid) {
		return $this->getUserRoleResult($userid)->fetchAll();
	}
	abstract public function setUserRoleList($userid, $roles);

	abstract public function getRoleResult();
	public function getRoleList() {
		return $this->getRoleResult()->fetchAll();
	}
	abstract public function getRole($roleid);
	abstract public function setRole($roleid, $label, $description);
	abstract public function delRole($roleid);
	abstract public function getRolePermissionResult($roleid);
	public function getRolePermissionList($roleid) {
		return $this->getRolePermissionResult($roleid)->fetchAll();
	}
	abstract public function setRolePermissionList($roleid, $permissions);
	abstract public function getRoleUserResult($roleid);
	public function getRoleUserList($roleid) {
		return $this->getRoleUserResult($roleid)->fetchAll();
	}
	abstract public function setRoleUserList($roleid, $userids);

	abstract public function getGliederungResult();
	public function getGliederungList() {
		return $this->getGliederungResult()->fetchAll();
	}
	abstract public function getGliederung($gliederungid);

	abstract public function getMitgliederResult($filter = null, $limit = null, $offset = null);
	public function getMitgliederList($filter = null, $limit = null, $offset = null) {
		return $this->getMitgliederResult($filter, $limit, $offset)->fetchAll();
	}
	abstract public function getMitgliederCount($filter = null);
	abstract public function getMitglied($mitgliedid);
	abstract public function setMitglied($mitgliedid, $globalid, $eintritt, $austritt);

	abstract public function getMitgliederRevisionResult($mitgliedid = null);
	public function getMitgliederRevisionList($mitgliedid = null) {
		return $this->getMitgliederRevisionResult($mitgliedid)->fetchAll();
	}
	abstract public function getMitgliederRevision($revisionid);
	abstract public function setMitgliederRevision($revisionid, $globalid, $timestamp, $userid, $mitgliedid, $mitgliedschaftid, $gliederungid, $geloescht, $mitgliedpiraten, $verteilereingetragen, $beitrag, $natpersonid, $jurpersonid, $kontaktid);

	abstract public function getKontakt($kontaktid);
	abstract public function setKontakt($kontaktid, $strasse, $hausnummer, $ortid, $telefon, $handy, $email);
	abstract public function delKontakt($kontaktid);
	abstract public function searchKontakt($strasse, $hausnummer, $ortid, $telefon, $handy, $email);

	abstract public function getOrtResult();
	public function getOrtList() {
		return $this->getOrtResult()->fetchAll();
	}
	abstract public function getOrtResultLimit($plz = null, $label = null, $stateid = null, $count = null);
	public function getOrtListLimit($plz = null, $label = null, $stateid = null, $count = null) {
		return $this->getOrtResult($plz, $label, $stateid, $count)->fetchAll();
	}
	abstract public function getOrt($ortid);
	abstract public function setOrt($ortid, $plz, $label, $stateid);
	abstract public function delOrt($ortid);
	abstract public function searchOrt($plz, $label, $stateid);

	abstract public function getStateResult();
	public function getStateList() {
		return $this->getStateResult()->fetchAll();
	}
	abstract public function getState($stateid);
	abstract public function setState($stateid, $label, $countryid);
	abstract public function delState($stateid);

	abstract public function getCountryResult();
	public function getCountryList() {
		return $this->getCountryResult()->fetchAll();
	}
	abstract public function getCountry($countryid);
	abstract public function setCountry($countryid, $label);
	abstract public function delCountry($countryid);

	abstract public function getMitgliedschaftResult();
	public function getMitgliedschaftList() {
		return $this->getMitgliedschaftResult()->fetchAll();
	}
	abstract public function getMitgliedschaft($mitgliedschaftid);
	abstract public function setMitgliedschaft($mitgliedschaftid, $globalid, $label, $description, $defaultbeitrag, $defaultcreatemail);
	abstract public function delMitgliedschaft($mitgliedschaftid);

	abstract public function getNatPerson($natpersonid);
	abstract public function setNatPerson($natpersonid, $name, $vorname, $geburtsdatum, $nationalitaet);
	abstract public function delNatPerson($natpersonid);
	abstract public function searchNatPerson($name, $vorname, $geburtsdatum, $nationalitaet);

	abstract public function getJurPerson($jurpersonid);
	abstract public function setJurPerson($jurpersonid, $firma);
	abstract public function delJurPerson($jurpersonid);
	abstract public function searchJurPerson($firma);

	abstract public function getMailTemplateResult();
	public function getMailTemplateList() {
		return $this->getMailTemplateResult()->fetchAll();
	}
	abstract public function getMailTemplate($mailtemplateid);
	abstract public function setMailTemplate($mailtemplateid, $label, $body);
	abstract public function delMailTemplate($mailtemplateid);
	abstract public function getMailTemplateHeaderResult($mailtemplateid);
	public function getMailTemplateHeaderList($mailtemplateid) {
		return $this->getMailTemplateHeaderResult($mailtemplateid)->fetchAll();
	}
	abstract public function setMailTemplateHeaderList($mailtemplateid, $headerids, $values);
	abstract public function getMailTemplateAttachmentResult($mailtemplateid);
	public function getMailTemplateAttachmentList($mailtemplateid) {
		return $this->getMailTemplateAttachmentResult($mailtemplateid)->fetchAll();
	}
	abstract public function setMailTemplateAttachmentList($mailtemplateid, $attachments);

	abstract public function getMailAttachmentResult();
	public function getMailAttachmentList() {
		return $this->getMailAttachmentResult()->fetchAll();
	}
	abstract public function getMailAttachment($attachmentid);
	abstract public function setMailAttachment($attachmentid, $filename, $mimetype, $content);
	abstract public function delMailAttachment($attachmentid);

	abstract public function getProcessResult();
	public function getProcessList() {
		return $this->getProcessResult()->fetchAll();
	}
	abstract public function getProcess($processid);
	abstract public function setProcess($processid, $type, $typedata, $progess, $queued, $started, $finished);
	abstract public function delProcess($processid);
}

abstract class StorageResult {
	abstract public function fetchRow();
	abstract public function fetchAll();
	abstract public function getCount();
}

?>
