<?php

require_once(VPANEL_CORE . "/storage.class.php");

abstract class SQLStorage implements Storage {
	public function __construct() {}
	
	abstract protected function query($sql);
	abstract protected function fetchRow($result);
	abstract protected function getInsertID();

	protected function fetchAsArray($result, $keyfield = null, $field = null) {
		$rows = array();
		while ($row = $this->fetchRow($result)) {
			if ($field === null) {
				$item = $row;
			} else {
				$item = $row[$field];
			}
			if ($keyfield === null) {
				$rows[] = $item;
			} else {
				$rows[$row[$keyfield]] = $item;
			}
		}
		return $rows;
	}

	protected function escape($str) {
		return addslashes($str);
	}

	protected function hash($str) {
		return hash('sha256', $str);
	}

	/**
	 * Berechtigungen
	 */
	public function getPermissions() {
		$sql = "SELECT `permissionid`, `label`, `description` FROM `permissions`";
		return $this->fetchAsArray($this->query($sql), "permissionid");
	}

	public function getUserPermissions($userid) {
		$sql = "SELECT `permissions`.`permissionid` AS 'permissionid', `permissions`.`label` AS 'permission' FROM `userroles` LEFT JOIN `rolepermissions` USING (`roleid`) LEFT JOIN `permissions` USING (`permissionid`) WHERE `userroles`.`userid` = '" . $this->escape($userid) . "' GROUP BY `permissions`.`permissionid`";
		return $this->fetchAsArray($this->query($sql), "permissionid", "permission");
	}

	public function getRolePermissions($roleid) {
		$sql = "SELECT `permissions`.`permissionid` AS 'permissionid', `permissions`.`label` AS 'permission' FROM `rolepermissions` LEFT JOIN `permissions` USING (`permissionid`) WHERE `rolepermissions`.`roleid` = '" . $this->escape($roleid) . "'";
		return $this->fetchAsArray($this->query($sql), "permissionid", "permission");
	}

	public function addRolePermission($roleid, $permid) {
		$sql = "INSERT INTO `rolepermissions` (`roleid`, `permissionid`) VALUES (" . intval($roleid) . ", " . intval($permid) . ")";
		return $this->query($sql);
	}

	public function delRolePermission($roleid, $permid) {
		$sql = "DELETE FROM `rolepermissions` WHERE `roleid` = " . intval($roleid) . " and `permissionid` = " . intval($permid);
		return $this->query($sql);
	}

	/**
	 * Benutzer
	 */
	public function validLogin($username, $password) {
		$sql = "SELECT `userid`, `password` FROM `users` WHERE `username` = '" . $this->escape($username) . "'";
		$result = array_shift($this->fetchAsArray($this->query($sql), "userid"));
		return $result["password"] == $this->hash($password) ? $result["userid"] : false;
	}

	public function getUser($userid) {
		$sql = "SELECT `userid`, `username` FROM `users` WHERE `userid` = '" . intval($userid) . "'";
		return first($this->fetchAsArray($this->query($sql)));
	}
	
	public function getUserList($userid = null, $roleid = null) {
		$sql = "SELECT `userid`, `username` FROM `users`";
		if ($roleid !== null) {
			$sql .= " LEFT JOIN `userroles` USING (`userid`) WHERE `roleid` = " . intval($roleid);
		}
		if ($userid !== null) {
			$sql .= " WHERE `userid` = " . intval($userid);
		}
		return $this->fetchAsArray($this->query($sql), "userid");
	}

	public function addUser($username, $password) {
		$sql = "INSERT INTO `users` (`username`, `password`) VALUES ('" . $this->escape($username) . "', '" . $this->escape($this->hash($password)) . "')";
		$this->query($sql);
		return $this->getInsertID();
	}

	public function modUser($userid, $username) {
		$sql = "UPDATE `users` SET `username` = '" . $this->escape($username) . "' WHERE `userid` = " . intval($userid);
		return $this->query($sql);
	}

	public function delUser($userid) {
		$sql = "DELETE FROM `userroles` WHERE `userid` = " . intval($userid);
		$this->query($sql);
		$sql = "DELETE FROM `users` WHERE `userid` = " . intval($userid);
		return $this->query($sql);
	}

	public function changePassword($userid, $password) {
		$sql = "UPDATE `users` SET `password` = '" . $this->escape($this->hash($password)) . "' WHERE `userid` = " . intval($userid);
		return $this->query($sql);
	}

	/**
	 * Rollen
	 */
	public function getRoleList($roleid = null, $userid = null) {
		$sql = "SELECT `roleid`, `label`, `description` FROM `roles`";
		if ($userid !== null) {
			$sql .= " LEFT JOIN `userroles` USING (`roleid`) WHERE `userid` = " . intval($userid);
		}
		if ($roleid !== null) {
			$sql .= " WHERE `roleid` = " . intval($roleid);
		}
		return $this->fetchAsArray($this->query($sql), "roleid");
	}

	public function addRole($label, $description) {
		$sql = "INSERT INTO `roles` (`label`, `description`) VALUES ('" . $this->escape($label) . "', '" . $this->escape($description) . "')";
		$this->query($sql);
		return $this->getInsertID();
	}

	public function modRole($roleid, $label, $description) {
		$sql = "UPDATE `roles` SET `label` = '" . $this->escape($label) . "', `description` = '" . $this->escape($description) . "' WHERE `roleid` = " . intval($roleid);
		return $this->query($sql);
	}

	public function delRole($roleid) {
		$sql = "DELETE FROM `userroles` WHERE `roleid` = " . intval($roleid);
		$this->query($sql);
		$sql = "DELETE FROM `roles` WHERE `roleid` = " . intval($roleid);
		return $this->query($sql);
	}

	public function addUserRole($userid, $roleid) {
		$sql = "INSERT INTO `userroles` (`userid`, `roleid`) VALUES (" . intval($userid) . ", " . intval($roleid) . ")";
		$this->query($sql);
		return $this->getInsertID();
	}

	public function delUserRole($userid, $roleid) {
		$sql = "DELETE FROM `userroles` WHERE `userid` = " . intval($userid) . " and `roleid` = " . intval($roleid);
		return $this->query($sql);
	}

	/**
	 * Mitgliederverwaltung
	 **/
	public function getMitgliederList() {
		$sql = "SELECT `mitglieder`.`mitgliedid`, `mitglieder`.`eintritt`, `mitglieder`.`austritt`, `mitgliederrevisions`.`revisionid` as `latestrevisionid`, `mitgliederrevisions`.`timestamp` AS `latestrevisiontimestamp` FROM `mitglieder` LEFT JOIN `mitgliederrevisions` ON (`mitgliederrevisions`.`mitgliedid` = `mitglieder`.`mitgliedid`) HAVING `mitgliederrevisions`.`timestamp` = MAX(`mitgliederrevisions`.`timestamp`)";
		return $this->fetchAsArray($this->query($sql), "mitgliedid");
	}

	public function addMitglied($globalid, $eintritt, $austritt) {
		$sql = "INSERT INTO `mitglieder` (`globalid`, `eintritt`, `austritt`) VALUES ('" . $db->escape($globalid) . "', '" . date("Y-m-d", $eintritt) . "', " . ($austritt == null ? "NULL" : "'" . date("Y-m-d", $austritt) . "'") . ")";
		$this->query($sql);
		return $this->getInsertID();
	}

	public function modMitglied($mitgliedid, $globalid, $eintritt, $austritt) {
		$sql = "UPDATE `mitglieder` SET `globalid` = '" . $db->escape($globalid) . "', `eintritt` = '" . date("Y-m-d", $eintritt) . "', `austritt` = " . ($austritt == null ? "NULL" : "'" . date("Y-m-d", $austritt) . "'") . " WHERE `mitgliedid` = " . intval($mitgliedid);
		return $this->query($sql);
	}

	public function getMitgliederRevisionList($mitgliedid = null) {
		$sql = "SELECT `revisionid`, `globalid`, UNIX_TIMESTAMP(`timestamp`), `userid`, `mitgliedid`, `mitgliedschaftsid`, `gliederungsid`, `geloescht`, `mitglied_piraten`, `verteiler_eingetragen`, `beitrag`, `natpersonid`, `jurpersonid`, `kontaktid` FROM `mitgliederrevisions`";
		if ($mitgliedid != null) {
			$sql .= " WHERE `mitgliedid` = " . intval($mitgliedid);
		}
		$sql .= " ORDER BY `timestamp`";
		return $this->fetchAsArray($this->query($sql), "revisionid");
	}

	public function addMitgliederRevision() {
		
	}

	/**
	 * NatPerson
	 **/
	public function addNatPerson($name, $vorname, $geburtsdatum, $nationalitaet) {
		$sql = "INSERT INTO `natperson` (`name`, `vorname`, `geburtsdatum`, `nationalitaet`) VALUES ('" . $this->escape($name) . "', '" . $this->escape($vorname) . "', '" . date("Y-m-d", $geburtsdatum) . "', '" . $this->escape($nationalitaet) . "')";
		$this->query($sql);
		return $this->getInsertID();
	}

	public function delNatPerson($natpersonid) {
		$sql = "DELETE FROM `natperson` WHERE `natpersonid` = " . intval($natpersonid);
		return $this->query($sql);
	}

	/**
	 * JurPerson
	 **/
	public function addJurPerson($firma) {
		$sql = "INSERT INTO `jurperson` (`firma`) VALUES ('" . $this->escape($firma) . "')";
		$this->query($sql);
		return $this->getInsertID();
	}

	public function delJurPerson($jurpersonid) {
		$sql = "DELETE FROM `jurperson` WHERE `jurpersonid` = " . intval($jurpersonid);
		return $this->query($sql);
	}
}

?>
