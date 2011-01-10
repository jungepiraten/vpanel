<?php

interface Storage {
	public function getPermissionList();
	public function getPermission($permissionid);

	public function getUserList();
	public function getUser($userid);
	public function getUserByUsername($username);
	public function setUser($userid, $username, $password);
	public function delUser($userid);
	public function getUserRoleList($userid);
	public function setUserRoleList($userid, $permissions);

	public function getRoleList();
	public function getRole($roleid);
	public function setRole($roleid, $label, $description);
	public function delRole($roleid);
	public function getRolePermissionList($roleid);
	public function setRolePermissionList($roleid, $permissions);
	public function getRoleUserList($roleid);
	public function setRoleUserList($roleid, $userids);

	public function getGliederungList();
	public function getGliederung($gliederungid);

	public function getMitgliederList($limit = null, $offset = null);
	public function getMitgliederCount();
	public function getMitglied($mitgliedid);
	public function setMitglied($mitgliedid, $globalid, $eintritt, $austritt);

	public function getMitgliederRevisionList($mitgliedid = null);
	public function getMitgliederRevision($revisionid);
	public function setMitgliederRevision($revisionid, $globalid, $timestamp, $userid, $mitgliedid, $mitgliedschaftid, $gliederungid, $geloescht, $mitgliedpiraten, $verteilereingetragen, $beitrag, $natpersonid, $jurpersonid, $kontaktid);

	public function getKontakt($kontaktid);
	public function setKontakt($kontaktid, $strasse, $hausnummer, $ortid, $telefon, $handy, $email);
	public function delKontakt($kontaktid);
	public function searchKontakt($strasse, $hausnummer, $ortid, $telefon, $handy, $email);

	public function getOrtList();
	public function getOrtListLimit($plz = null, $label = null, $stateid = null, $count = null);
	public function getOrt($ortid);
	public function setOrt($ortid, $plz, $label, $stateid);
	public function delOrt($ortid);
	public function searchOrt($plz, $label, $stateid);

	public function getStateList();
	public function getState($stateid);
	public function setState($stateid, $label, $countryid);
	public function delState($stateid);

	public function getCountryList();
	public function getCountry($countryid);
	public function setCountry($countryid, $label);
	public function delCountry($countryid);

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

	public function getMailTemplateList();
	public function getMailTemplate($mailtemplateid);
	public function setMailTemplate($mailtemplateid, $body);
	public function delMailTemplate($mailtemplateid);
	public function getMailTemplateHeaderList($mailtemplateid);
	public function setMailTemplateHeaderList($mailtemplateid, $headerids, $values);
	public function getMailTemplateAttachmentList($mailtemplateid);
	public function setMailTemplateAttachmentList($mailtemplateid, $attachments);

	public function getMailHeaderList();
	public function getMailHeader($headerid);
	public function setMailHeader($headerid, $label);
	public function delMailHeader($headerid);

	public function getMailAttachmentList();
	public function getMailAttachment($attachmentid);
	public function setMailAttachment($attachmentid, $filename, $mimetype, $content);
	public function delMailAttachment($attachmentid);
}

?>
