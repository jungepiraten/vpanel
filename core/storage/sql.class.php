<?php

require_once(VPANEL_CORE . "/storage.class.php");
require_once(VPANEL_CORE . "/user.class.php");
require_once(VPANEL_CORE . "/dashboardwidget.class.php");
require_once(VPANEL_CORE . "/role.class.php");
require_once(VPANEL_CORE . "/permission.class.php");
require_once(VPANEL_CORE . "/rolepermission.class.php");
require_once(VPANEL_CORE . "/mitglied.class.php");
require_once(VPANEL_CORE . "/mitgliedflag.class.php");
require_once(VPANEL_CORE . "/mitgliedtextfield.class.php");
require_once(VPANEL_CORE . "/mitgliedrevisiontextfield.class.php");
require_once(VPANEL_CORE . "/mitgliednotiz.class.php");
require_once(VPANEL_CORE . "/beitrag.class.php");
require_once(VPANEL_CORE . "/mitgliedbeitrag.class.php");
require_once(VPANEL_CORE . "/mitgliedbeitragbuchung.class.php");
require_once(VPANEL_CORE . "/mitgliedrevision.class.php");
require_once(VPANEL_CORE . "/mitgliedschaft.class.php");
require_once(VPANEL_CORE . "/natperson.class.php");
require_once(VPANEL_CORE . "/jurperson.class.php");
require_once(VPANEL_CORE . "/kontakt.class.php");
require_once(VPANEL_CORE . "/email.class.php");
require_once(VPANEL_CORE . "/emailbounce.class.php");
require_once(VPANEL_CORE . "/mailtemplate.class.php");
require_once(VPANEL_CORE . "/mailtemplateheader.class.php");
require_once(VPANEL_CORE . "/process.class.php");
require_once(VPANEL_CORE . "/dokument.class.php");
require_once(VPANEL_CORE . "/dokumentnotify.class.php");
require_once(VPANEL_CORE . "/dokumentkategorie.class.php");
require_once(VPANEL_CORE . "/dokumentstatus.class.php");
require_once(VPANEL_CORE . "/dokumentflag.class.php");
require_once(VPANEL_CORE . "/dokumentnotiz.class.php");
require_once(VPANEL_CORE . "/file.class.php");
require_once(VPANEL_CORE . "/tempfile.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/mitglied.class.php");

abstract class SQLStorage extends AbstractStorage {
	public function __construct() {}

	abstract protected function query($sql);
	abstract protected function getEncoding();
	abstract public function fetchRow($result);
	abstract public function numRows($result);
	abstract protected function getInsertID();

	protected function escape($str) {
		return addslashes($str);
	}

	protected function parseRow($row, $field = null, $class = null) {
		if ($field === null) {
			$item = $row;
		} else {
			$item = $row[$field];
		}
		if ($class !== null) {
			if (is_array($class)) {
				$item = array();
				$values = array();
				foreach ($row as $col => $val) {
					if (!strpos($col, "_")) {
						$item[$col] = $val;
					} else {
						list($prefix, $col) = explode("_", $col, 2);
						$values[$prefix][$col] = iconv($this->getEncoding(), "UTF-8", $val);
					}
				}
				foreach ($class as $l => $c) {
					$item[$l] = call_user_func(array($c, "factory"), $this, $values[$l]);
				}
			} else {
				$item = call_user_func(array($class, "factory"), $this, $row);
			}
		}
		return $item;
	}

	protected function getResult($sql, $callback = null) {
		return new SQLStorageResult($this, $this->query($sql), $callback);
	}

	/**
	 * SessionData
	 **/
	public function getSessionData($sessionid) {
		$sql = "SELECT `data` FROM `sessions` WHERE `sessionid` = " . intval($sessionid);
		return unserialize(array_shift($this->getResult($sql)->fetchRow()));
	}
	public function setSessionData($sessionid, $timestamp, $data) {
		if ($sessionid == null) {
			$sql = "INSERT INTO `sessions` (`timestamp`, `data`) VALUES ('" . date("Y-m-d H:i:s", $timestamp) . "', '" . $this->escape(serialize($data)) . "')";
		} else {
			$sql = "UPDATE `sessions` SET `timestamp` = '" . date("Y-m-d H:i:s", $timestamp) . "', `data` = '" . $this->escape(serialize($data)) . "' WHERE `sessionid` = " . intval($sessionid);
		}
		$this->query($sql);
		if ($sessionid == null) {
			$sessionid = $this->getInsertID();
		}
		return $sessionid;
	}
	public function delSessionDataBefore($timestamp) {
		$sql = "DELETE FROM `sessions` WHERE `timestamp` <= '" . date("Y-m-d H:i:s", $timestamp) . "'";
		$this->query($sql);
	}

	/**
	 * Berechtigungen
	 */
	public function parsePermission($row) {
		return $this->parseRow($row, null, "Permission");
	}
	public function getPermissionResult() {
		$sql = "SELECT `permissionid`, `label`, `description`, `global` FROM `permissions`";
		return $this->getResult($sql, array($this, "parsePermission"));
	}
	public function getPermissionGlobalResult() {
		$sql = "SELECT `permissionid`, `label`, `description`, `global` FROM `permissions` WHERE `global` = 1";
		return $this->getResult($sql, array($this, "parsePermission"));
	}
	public function getPermissionLocalResult() {
		$sql = "SELECT `permissionid`, `label`, `description`, `global` FROM `permissions` WHERE `global` = 0";
		return $this->getResult($sql, array($this, "parsePermission"));
	}
	public function getPermission($permissionid) {
		$sql = "SELECT `permissionid`, `label`, `description`, `global` FROM `permissions` WHERE `permissionid` = " . intval($permissionid);
		return $this->getResult($sql, array($this, "parsePermission"))->fetchRow();
	}

	public function parseRolePermission($row) {
		return $this->parseRow($row, null, "RolePermission");
	}
	public function getRolePermissionResult($roleid) {
		$sql = "SELECT `roleid`, `permissionid`, `gliederungid`, `transitive` FROM `rolepermissions` WHERE `roleid` = '" . $this->escape($roleid) . "'";
		return $this->getResult($sql, array($this, "parseRolePermission"));
	}
	public function setRolePermissionList($roleid, $permissions) {
		$sql = "DELETE FROM `rolepermissions` WHERE `roleid` = " . intval($roleid);
		$this->query($sql);
		if (count($permissions) > 0) {
			$permissionssql = array();
			foreach ($permissions as $perm) {
				$permissionssql[] = "(" . intval($roleid) . ", " . intval($perm->getPermissionID()) . ", " . ($perm->getGliederungID() == null ? "NULL" : intval($perm->getGliederungID())) . ", " . ($perm->isTransitive() ? 1 : 0) . ")";
			}
			$sql = "INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `gliederungid`, `transitive`) VALUES " . implode(",", $permissionssql);
			$this->query($sql);
		}
		return true;
	}

	/**
	 * Benutzer
	 */
	public function parseUser($row) {
		return $this->parseRow($row, null, "User");
	}
	public function getUserResult() {
		$sql = "SELECT `userid`, `username`, `password`, `passwordsalt`, `apikey`, `aktiv`, `defaultgliederungid`, `defaultdokumentkategorieid`, `defaultdokumentstatusid` FROM `users`";
		return $this->getResult($sql, array($this, "parseUser"));
	}
	public function getUser($userid) {
		$sql = "SELECT `userid`, `username`, `password`, `passwordsalt`, `apikey`, `aktiv`, `defaultgliederungid`, `defaultdokumentkategorieid`, `defaultdokumentstatusid` FROM `users` WHERE `userid` = " . intval($userid);
		return $this->getResult($sql, array($this, "parseUser"))->fetchRow();
	}
	public function getUserByUsername($username) {
		$sql = "SELECT `userid`, `username`, `password`, `passwordsalt`, `apikey`, `aktiv`, `defaultgliederungid`, `defaultdokumentkategorieid`, `defaultdokumentstatusid` FROM `users` WHERE `username` = '" . $this->escape($username) . "'";
		return $this->getResult($sql, array($this, "parseUser"))->fetchRow();
	}
	public function setUser($userid, $username, $password, $passwordsalt, $apikey, $aktiv, $defaultgliederungid, $defaultdokumentkategorieid, $defaultdokumentstatusid) {
		if ($userid == null) {
			$sql = "INSERT INTO `users` (`username`, `password`, `passwordsalt`, `apikey`, `aktiv`, `defaultgliederungid`, `defaultdokumentkategorieid`, `defaultdokumentstatusid`) VALUES ('" . $this->escape($username) . "', '" . $this->escape($password) . "', '" . $this->escape($passwordsalt) . "', " . ($apikey == null ? "NULL" : "'" . $this->escape($apikey) . "'") . ", " . ($aktiv ? "1" : "0") . ", " . ($defaultgliederungid == null ? "NULL" : intval($defaultgliederungid)) . ", " . ($defaultdokumentkategorieid == null ? "NULL" : intval($defaultdokumentkategorieid)) . ", " . ($defaultdokumentstatusid == null ? "NULL" : intval($defaultdokumentstatusid)) . ")";
		} else {
			$sql = "UPDATE `users` SET `username` = '" . $this->escape($username) . "', `password` = '" . $this->escape($password) . "', `passwordsalt` = '" . $this->escape($passwordsalt) . "', `apikey` = " . ($apikey == null ? "NULL" : "'" . $this->escape($apikey) . "'") . ", `aktiv` = " . ($aktiv ? "1" : "0") . ", `defaultgliederungid` = " . ($defaultgliederungid == null ? "NULL" : intval($defaultgliederungid)) . ", `defaultdokumentkategorieid` = " . ($defaultdokumentkategorieid == null ? "NULL" : intval($defaultdokumentkategorieid)) . ", `defaultdokumentstatusid` = " . ($defaultdokumentstatusid == null ? "NULL" : intval($defaultdokumentstatusid)) . " WHERE `userid` = " . intval($userid);
		}
		$this->query($sql);
		if ($userid == null) {
			$userid = $this->getInsertID();
		}
		return $userid;
	}
	public function delUser($userid) {
		$sql = "DELETE FROM `dashboardwidgets` WHERE `userid` = " . intval($userid);
		return $this->query($sql);
		$sql = "DELETE FROM `users` WHERE `userid` = " . intval($userid);
		return $this->query($sql);
	}

	public function getRoleUserResult($roleid) {
		$sql = "SELECT `userid`, `username`, `password`, `passwordsalt`, `apikey`, `aktiv`, `defaultgliederungid`, `defaultdokumentkategorieid`, `defaultdokumentstatusid` FROM `users` LEFT JOIN `userroles` USING (`userid`) WHERE `roleid` = " . intval($roleid);
		return $this->getResult($sql, array($this, "parseUser"));
	}
	public function setRoleUserList($roleid, $userids) {
		$sql = "DELETE FROM `userroles` WHERE `roleid` = " . intval($roleid);
		$this->query($sql);
		if (count($userids) > 0) {
			$userssql = array();
			foreach ($userids as $userid) {
				$userssql[] = "(" . intval($roleid) . ", " . intval($userid) . ")";
			}
			$sql = "INSERT INTO `userroles` (`roleid`, `userid`) VALUES " . implode(",", $userssql);
			$this->query($sql);
		}
		return true;
	}

	/**
	 * Widgets
	 **/

	public function parseDashboardWidget($row) {
		return $this->parseRow($row, null, "DashboardWidget");
	}
	public function getDashboardWidgetResult($userid) {
		$sql = "SELECT `widgetid`,`userid`,`column`,`type`,`typedata` FROM `dashboardwidgets` WHERE `userid` = " . intval($userid);
		return $this->getResult($sql, array($this, "parseDashboardWidget"));
	}
	public function getDashboardWidget($widgetid) {
		$sql = "SELECT `widgetid`,`userid`,`column`,`type`,`typedata` FROM `dashboardwidgets` WHERE `widgetid` = " . intval($widgetid);
		return $this->getResult($sql, array($this, "parseDashboardWidget"))->fetchRow();
	}
	public function setDashboardWidget($widgetid, $userid, $column, $type, $typedata) {
		if ($widgetid == null) {
			$sql = "INSERT INTO `dashboardwidgets` (`userid`,`column`,`type`,`typedata`) VALUES (" . intval($userid) . ", " . intval($column) . ", '" . $this->escape($type) . "', '" . $this->escape($typedata) . "')";
		} else {
			$sql = "UPDATE `dashboardwidgets` SET `userid` = " . intval($userid) . ", `column` = " . intval($column) . ", `type` = '" . $this->escape($type) . "', `typedata` = '" . $this->escape($typedata) . "' WHERE `widgetid` = " . intval($widgetid);
		}
		$this->query($sql);
		if ($widgetid == null) {
			return $this->getInsertID();
		} else {
			return $widgetid;
		}
	}
	public function delDashboardWidget($widgetid) {
		$sql = "DELETE FROM `dashboardwidgets` WHERE `widgetid` = " . intval($widgetid);
		return $this->query($sql);
	}

	/**
	 * Rollen
	 */
	public function parseRole($row) {
		return $this->parseRow($row, null, "Role");
	}
	public function getRoleResult() {
		$sql = "SELECT `roleid`, `label`, `description` FROM `roles`";
		return $this->getResult($sql, array($this, "parseRole"));
	}
	public function getRole($roleid) {
		$sql = "SELECT `roleid`, `label`, `description` FROM `roles` WHERE `roleid` = " . intval($roleid);
		return $this->getResult($sql, array($this, "parseRole"))->fetchRow();
	}
	public function setRole($roleid, $label, $description) {
		if ($roleid == null) {
			$sql = "INSERT INTO `roles` (`label`, `description`) VALUES ('" . $this->escape($label) . "', '" . $this->escape($description) . "')";
		} else {
			$sql = "UPDATE `roles` SET `label` = '" . $this->escape($label) . "', `description` = '" . $this->escape($description) . "' WHERE `roleid` = " . intval($roleid);
		}
		$this->query($sql);
		if ($roleid == null) {
			return $this->getInsertID();
		} else {
			return $roleid;
		}
	}
	public function delRole($roleid) {
		$sql = "DELETE FROM `userroles` WHERE `roleid` = " . intval($roleid);
		$this->query($sql);
		$sql = "DELETE FROM `roles` WHERE `roleid` = " . intval($roleid);
		return $this->query($sql);
	}

	public function getUserRoleResult($userid) {
		$sql = "SELECT `roleid`, `label`, `description` FROM `roles` LEFT JOIN `userroles` USING (`roleid`) WHERE `userid` = " . intval($userid);
		return $this->getResult($sql, array($this, "parseRole"));
	}
	public function setUserRoleList($userid, $roleids) {
		$sql = "DELETE FROM `userroles` WHERE `userid` = " . intval($userid);
		$this->query($sql);
		if (count($roleids) > 0) {
			$rolesql = array();
			foreach ($roleids as $roleid) {
				$rolesql[] = "(" . intval($userid) . ", " . intval($roleid) . ")";
			}
			$sql = "INSERT INTO `userroles` (`userid`, `roleid`) VALUES " . implode(",", $rolesql);
			$this->query($sql);
		}
		return true;
	}

	/**
	 * Gliederung
	 **/
	public function parseGliederung($row) {
		return $this->parseRow($row, null, "Gliederung");
	}
	public function getGliederungResult($gliederungids = null) {
		$sql = "SELECT `gliederungsid`, `label`, `parentid` FROM `gliederungen`";
		if ($gliederungids != null) {
			$sql .= " WHERE `gliederungsid` IN (" . implode(",", array_map("intval", $gliederungids)) . ")";
		}
		return $this->getResult($sql, array($this, "parseGliederung"));
	}
	public function getGliederung($gliederungid) {
		$sql = "SELECT `gliederungsid`, `label`, `parentid` FROM `gliederungen` WHERE `gliederungsid` = " . intval($gliederungid);
		return $this->getResult($sql, array($this, "parseGliederung"))->fetchRow();
	}
	public function setGliederung($gliederungid, $label, $parentid) {
		if ($gliederungid == null) {
			$sql = "INSERT INTO `gliederungen` (`label`, `parentid`) VALUES ('" . $this->escape($label) . "', " . ($parentid == null ? "NULL" : intval($parentid)) . ")";
		} else {
			$sql = "UPDATE `gliederungen` SET `label` = '" . $this->escape($label) . "', `parentid` = " . ($parentid == null ? "NULL" : intval($parentid)) . " WHERE `gliederungid` = " . intval($gliederungid);
		}
		$this->query($sql);
		if ($gliederungid == null) {
			$gliederungid = $this->getInsertID();
		}
		return $gliederungid;
	}

	/**
	 * Beitrag
	 **/
	public function parseBeitrag($row) {
		return $this->parseRow($row, null, "Beitrag");
	}
	public function getBeitragResult() {
		$sql = "SELECT `beitragid`, `label`, `hoehe`, `mailtemplateid` FROM `beitraege`";
		return $this->getResult($sql, array($this, "parseBeitrag"));
	}
	public function getBeitrag($beitragid) {
		$sql = "SELECT `beitragid`, `label`, `hoehe`, `mailtemplateid` FROM `beitraege` WHERE `beitragid` = " . intval($beitragid);
		return $this->getResult($sql, array($this, "parseBeitrag"))->fetchRow();
	}
	public function setBeitrag($beitragid, $label, $hoehe, $mailtemplateid) {
		if ($beitragid == null) {
			$sql = "INSERT INTO `beitraege` (`label`, `hoehe`, `mailtemplateid`) VALUES ('" . $this->escape($label) . "', " . ($hoehe == null ? "NULL" : floatval($hoehe)) . ", " . ($mailtemplateid == null ? "NULL" : intval($mailtemplateid)) . ")";
		} else {
			$sql = "UPDATE `beitraege` SET `label` = '" . $this->escape($label) . "', `hoehe` = " . ($hoehe == null ? "NULL" : doubleval($hoehe)) . ", `mailtemplateid` = " . ($mailtemplateid == null ? "NULL" : intval($mailtemplateid)) . " WHERE `beitragid` = " . intval($beitragid);
		}
		$this->query($sql);
		if ($beitragid == null) {
			$beitragid = $this->getInsertID();
		}
		return $beitragid;
	}
	public function searchBeitrag($label) {
		$sql = "SELECT `beitragid`, `label`, `hoehe`, `mailtemplateid` FROM `beitraege` WHERE `label` = '" . $this->escape($label) . "'";
		$result = $this->getResult($sql, array($this, "parseBeitrag"));
		if ($result->getCount() > 0) {
			return $result->fetchRow();
		}
		return null;
	}
	public function delBeitrag($beitragid) {
		$sql = "DELETE FROM `beitraege` WHERE `beitragid` = " . intval($beitragid);
		return $this->query($sql);
	}

	/**
	 * MitgliederBeitrag
	 **/
	public function parseMitgliedBeitrag($row) {
		$o = $this->parseRow($row, null, array("mb" => 'MitgliedBeitrag', "b" => 'Beitrag'));
		$o["mb"]->setBeitrag($o["b"]);
		return $o["mb"];
	}
	public function getMitgliederBeitragByMitgliedResult($mitgliedid) {
		$sql = "SELECT `mb`.`mitgliederbeitragid` AS `mb_mitgliederbeitragid`, `mb`.`mitgliedid` AS `mb_mitgliedid`, `mb`.`beitragid` AS `mb_beitragid`, `mb`.`hoehe` AS `mb_hoehe`, `b`.`beitragid` as `b_beitragid`, `b`.`label` AS `b_label`, `b`.`hoehe` AS `b_hoehe`, `b`.`mailtemplateid` AS `b_mailtemplateid` FROM `mitgliederbeitrag` `mb` LEFT JOIN `beitraege` `b` USING (`beitragid`) WHERE `mb`.`mitgliedid` = " . intval($mitgliedid);
		return $this->getResult($sql, array($this, "parseMitgliedBeitrag"));
	}
	public function getMitgliederBeitragByBeitragCount($beitragid) {
		$sql = "SELECT COUNT(`mb`.`beitragid`) FROM `mitgliederbeitrag` `mb` LEFT JOIN `beitraege` `b` USING (`beitragid`) WHERE `mb`.`beitragid` = " . intval($beitragid);
		return reset($this->getResult($sql)->fetchRow());
	}
	public function getMitgliederBeitragByBeitragResult($beitragid, $limit = null, $offset = null) {
		$sql = "SELECT `mb`.`mitgliederbeitragid` AS `mb_mitgliederbeitragid`, `mb`.`mitgliedid` AS `mb_mitgliedid`, `mb`.`beitragid` AS `mb_beitragid`, `mb`.`hoehe` AS `mb_hoehe`, `b`.`beitragid` as `b_beitragid`, `b`.`label` AS `b_label`, `b`.`hoehe` AS `b_hoehe`, `b`.`mailtemplateid` AS `b_mailtemplateid` FROM `mitgliederbeitrag` `mb` LEFT JOIN `beitraege` `b` USING (`beitragid`) WHERE `mb`.`beitragid` = " . intval($beitragid);
		if ($limit !== null or $offset !== null) {
			$sql .= " LIMIT ";
			if ($offset !== null) {
				$sql .= $offset . ",";
			}
			if ($limit !== null) {
				$sql .= $limit;
			}
		}
		return $this->getResult($sql, array($this, "parseMitgliedBeitrag"));
	}
	public function getMitgliederBeitrag($beitragid) {
		$sql = "SELECT `mb`.`mitgliederbeitragid` AS `mb_mitgliederbeitragid`, `mb`.`mitgliedid` AS `mb_mitgliedid`, `mb`.`beitragid` AS `mb_beitragid`, `mb`.`hoehe` AS `mb_hoehe`, `b`.`beitragid` AS `b_beitragid`, `b`.`label` AS `b_label`, `b`.`hoehe` AS `b_hoehe`, `b`.`mailtemplateid` AS `b_mailtemplateid` FROM `mitgliederbeitrag` `mb` LEFT JOIN `beitraege` `b` USING (`beitragid`) WHERE `mb`.`mitgliederbeitragid` = " . intval($beitragid);
		return $this->getResult($sql, array($this, "parseMitgliedBeitrag"))->fetchRow();
	}
	public function setMitgliederBeitrag($mitgliederbeitragid, $mitgliedid, $beitragid, $hoehe) {
		if ($mitgliederbeitragid == null) {
			$sql = "INSERT INTO `mitgliederbeitrag` (`mitgliedid`, `beitragid`, `hoehe`) VALUES (" . intval($mitgliedid) . ", " . intval($beitragid) . ", " . floatval($hoehe) . ")";
		} else {
			$sql = "UPDATE `mitgliederbeitrag` SET `mitgliedid` = " . intval($mitgliedid) . ", `beitragid` = " . intval($beitragid) . ", `hoehe` = " . floatval($hoehe) . " WHERE `mitgliederbeitragid` = " . intval($mitgliederbeitragid);
		}
		$this->query($sql);
		if ($mitgliederbeitragid == null) {
			$mitgliederbeitragid = $this->getInsertID();
		}
		return $mitgliederbeitragid;
	}
	public function delMitgliederBeitrag($beitragid) {
		$sql = "DELETE FROM `mitgliederbeitrag` WHERE `mitgliederbeitragid` = " . intval($beitragid);
		return $this->query($sql);
	}

	/**
	 * MitgliederBeitragBuchung
	 **/
	public function parseMitgliederBeitragBuchung($row) {
		return $this->parseRow($row, null, "MitgliedBeitragBuchung");
	}
	public function getMitgliederBeitragBuchungResultTimeline($gliederungids, $start, $count) {
		$sql = "SELECT	`mbb`.`buchungid`, `mbb`.`beitragid`, `mbb`.`gliederungid`, `mbb`.`userid`, UNIX_TIMESTAMP(`mbb`.`timestamp`) AS `timestamp`, `mbb`.`vermerk`, `mbb`.`hoehe`
			FROM	`mitgliederbeitragbuchung` `mbb`
			LEFT JOIN `mitgliederbeitrag` `mb` ON (`mb`.`mitgliederbeitragid` = `mbb`.`beitragid`)
			LEFT JOIN `mitgliederrevisions` `r` ON (`r`.`mitgliedid` = `mb`.`mitgliedid`)
			WHERE	`r`.`timestamp` = (	SELECT	MAX(`rmax`.`timestamp`)
							FROM	`mitgliederrevisions` `rmax`
							WHERE   `r`.`mitgliedid` = `rmax`.`mitgliedid`)
				AND `r`.`gliederungsid` IN (" . implode(",", array_map("intval", $gliederungids)) . ") ORDER BY `mbb`.`timestamp` DESC LIMIT " . intval($start) . "," . intval($count);
		return $this->getResult($sql, array($this, "parseMitgliederBeitragBuchung"));
	}
	public function getMitgliederBeitragBuchungByBeitragResult($beitragid) {
		$sql = "SELECT `mbb`.`buchungid`, `mbb`.`beitragid`, `mbb`.`gliederungid`, `mbb`.`userid`, UNIX_TIMESTAMP(`mbb`.`timestamp`) AS `timestamp`, `mbb`.`vermerk`, `mbb`.`hoehe` FROM `mitgliederbeitragbuchung` `mbb` LEFT JOIN `mitgliederbeitrag` `mb` ON (`mb`.`mitgliederbeitragid` = `mbb`.`beitragid`) WHERE `mb`.`beitragid` = " . intval($beitragid) . " ORDER BY `mbb`.`timestamp`";
		return $this->getResult($sql, array($this, "parseMitgliederBeitragBuchung"));
	}
	public function getMitgliederBeitragBuchungByMitgliederBeitragResult($mitgliederbeitragid) {
		$sql = "SELECT `buchungid`, `beitragid`, `gliederungid`, `userid`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `vermerk`, `hoehe` FROM `mitgliederbeitragbuchung` WHERE `beitragid` = " . intval($mitgliederbeitragid) . " ORDER BY `timestamp`";
		return $this->getResult($sql, array($this, "parseMitgliederBeitragBuchung"));
	}
	public function getMitgliederBeitragBuchung($buchungid) {
		$sql = "SELECT `buchungid`, `beitragid`, `gliederungid`, `userid`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `vermerk`, `hoehe` FROM `mitgliederbeitragbuchung` WHERE `buchungid` = " . intval($buchungid) . " ORDER BY `timestamp`";
		return $this->getResult($sql, array($this, "parseMitgliederBeitragBuchung"))->fetchRow();
	}
	public function setMitgliederBeitragBuchung($buchungid, $beitragid, $gliederungid, $userid, $timestamp, $vermerk, $hoehe) {
		if ($buchungid == null) {
			$sql = "INSERT INTO `mitgliederbeitragbuchung` (`beitragid`, `gliederungid`, `userid`, `timestamp`, `vermerk`, `hoehe`) VALUES (" . intval($beitragid) . ", " . intval($gliederungid) . ", " . intval($userid) . ", '" . date("Y-m-d", $timestamp) . "', '" . $this->escape($vermerk) . "', " . floatval($hoehe) . ")";
		} else {
			$sql = "UPDATE `mitgliederbeitragbuchung` SET `beitragid` = " . intval($beitragid) . ", `gliederungid` = " . intval($gliederungid) . ", `userid` = " . intval($userid) . ", `timestamp` = '" . date("Y-m-d", $timestamp) . "', `vermerk` = '" . $this->escape($vermerk) . "', `hoehe` = " . floatval($hoehe) . " WHERE `buchungid` = " . intval($buchungid);
		}
		$this->query($sql);
		if ($buchungid == null) {
			$buchungid = $this->getInsertID();
		}
		return $buchungid;
	}
	public function delMitgliederBeitragBuchung($buchungid) {
		$sql = "DELETE FROM `mitgliederbeitragbuchung` WHERE `buchungid` = " . intval($buchungid);
		return $this->query($sql);
	}

	/**
	 * MitgliedDokument
	 **/
	public function addMitgliedDokument($mitgliedid, $dokumentid) {
		$sql = "INSERT INTO `mitglieddokument` (`mitgliedid`, `dokumentid`) VALUES (" . intval($mitgliedid) . ", " . intval($dokumentid) . ")";
		return $this->query($sql);
	}
	public function delMitgliedDokument($mitgliedid, $dokumentid) {
		$sql = "DELETE FROM `mitglieddokument` WHERE `mitgliedid` = " . intval($mitgliedid) . " AND `dokumentid` = " . intval($dokumentid);
		return $this->query($sql);
	}

	/**
	 * Mitglieder
	 **/
	protected function parseMitgliederMatcher($matcher) {
		if ($matcher instanceof TrueMitgliederMatcher || $matcher == null) {
			return "1";
		}
		if ($matcher instanceof FalseMitgliederMatcher) {
			return "0";
		}
		if ($matcher instanceof AndMitgliederMatcher) {
			return "(" . implode(" AND ",array_map(array($this,'parseMitgliederMatcher'), $matcher->getConditions())) . ")";
		}
		if ($matcher instanceof OrMitgliederMatcher) {
			return "(" . implode(" OR ",array_map(array($this,'parseMitgliederMatcher'), $matcher->getConditions())) . ")";
		}
		if ($matcher instanceof NotMitgliederMatcher) {
			return "NOT (" . $this->parseMitgliederMatcher($matcher->getCondition()) . ")";
		}
		if ($matcher instanceof GliederungMitgliederMatcher) {
			return "`r`.`gliederungsid` IN (" . implode(",", array_map("intval", $matcher->getGliederungIDs())) . ")";
		}
		if ($matcher instanceof MitgliedschaftMitgliederMatcher) {
			return "`r`.`mitgliedschaftid` = " . intval($matcher->getMitgliedschaftID());
		}
		if ($matcher instanceof StateMitgliederMatcher) {
			return "`o`.`stateid` = " . intval($matcher->getStateID());
		}
		if ($matcher instanceof OrtMitgliederMatcher) {
			return "`k`.`ortid` = " . intval($matcher->getOrtID());
		}
		if ($matcher instanceof NatPersonMitgliederMatcher) {
			return "`r`.`natpersonid` IS NOT NULL";
		}
		if ($matcher instanceof NatPersonAgeMitgliederMatcher) {
			return "`r`.`natpersonid` IS NOT NULL AND ADDDATE(`n`.`geburtsdatum`, INTERVAL " . intval($matcher->getAge()) . " YEAR) <= CURRENT_DATE()";
		}
		if ($matcher instanceof EintrittAgeNatPersonMitgliederMatcher) {
			return "`r`.`natpersonid` IS NOT NULL AND ADDDATE(`n`.`geburtsdatum`, INTERVAL " . intval($matcher->getAge()) . " YEAR) <= `m`.`eintritt`";
		}
		if ($matcher instanceof JurPersonMitgliederMatcher) {
			return "`r`.`jurpersonid` IS NOT NULL";
		}
		if ($matcher instanceof AusgetretenMitgliederMatcher) {
			return "`m`.`austritt` IS NOT NULL";
		}
		if ($matcher instanceof IBANMitgliederMatcher) {
			return "`k`.`iban` IS NOT NULL";
		}
		if ($matcher instanceof MitgliedMitgliederMatcher) {
			return "`m`.`mitgliedid` = " . intval($matcher->getMitgliedID());
		}
		if ($matcher instanceof EMailBounceCountAboveMitgliederMatcher) {
			return "`e`.`emailid` IN (SELECT `emailid` FROM `emailbounces` GROUP BY `emailid` HAVING COUNT(`bounceid`) > " . intval($matcher->getCountLimit()) . ")";
		}
		if ($matcher instanceof EMailBounceCountBelowMitgliederMatcher) {
			return "`e`.`emailid` IN (SELECT `emailid` FROM `emailbounces` GROUP BY `emailid` HAVING COUNT(`bounceid`) <= " . intval($matcher->getCountLimit()) . ")";
		}
		if ($matcher instanceof RevisionFlagMitgliederMatcher) {
			return "`r`.`revisionid` IN (SELECT `revisionid` FROM `mitgliederrevisionflags` WHERE `flagid` = " . intval($matcher->getFlagID()) . ")";
		}
		if ($matcher instanceof RevisionTextFieldMitgliederMatcher) {
			return "`r`.`revisionid` IN (SELECT `revisionid` FROM `mitgliederrevisiontextfields` WHERE `textfieldid` = " . intval($matcher->getTextFieldID()) . " AND `value` = '" . $this->escape($matcher->getValue()) . "')";
		}
		if ($matcher instanceof OrtDistanceMitgliederMatcher) {
			return "2*6371*ASIN(SQRT(POW(SIN(radians(`o`.`latitude`-".doubleval($matcher->getLatitude()).")/2),2)+cos(radians(`o`.`latitude`))*cos(radians(".doubleval($matcher->getLatitude())."))*pow(sin(radians(`o`.`longitude`-".doubleval($matcher->getLongitude()).")/2),2))) <= " . doubleval($matcher->getDistance());
		}
		if ($matcher instanceof BeitragMitgliederMatcher) {
			return "`m`.`mitgliedid` IN (SELECT `mitgliedid` FROM `mitgliederbeitrag` WHERE `beitragid` = ".intval($matcher->getBeitragID()).")";
		}
		if ($matcher instanceof BeitragPaidMitgliederMatcher) {
			return "`m`.`mitgliedid` IN (SELECT `mitgliedid` FROM 
			                            (SELECT `mitgliedid`, `mitgliederbeitrag`.`hoehe`
			                                   FROM `mitgliederbeitrag` LEFT JOIN `mitgliederbeitragbuchung` `buchung` ON (`buchung`.`beitragid` = `mitgliederbeitrag`.`mitgliederbeitragid`)
			                                   WHERE `mitgliederbeitrag`.`beitragid` = ".intval($matcher->getBeitragID())."
			                                   GROUP BY `mitgliederbeitrag`.`mitgliederbeitragid`
			                                   HAVING `mitgliederbeitrag`.`hoehe` - IFNULL(SUM(`buchung`.`hoehe`),0) <= 0) AS `tmp`)";
		}
		if ($matcher instanceof BeitragPaidBelowMitgliederMatcher) {
			return "`m`.`mitgliedid` IN (SELECT `mitgliedid`
			                                   FROM `mitglieder`
			                                   LEFT JOIN `mitgliederbeitrag` USING (`mitgliedid`)
			                                   LEFT JOIN `mitgliederbeitragbuchung` `buchung` ON (`buchung`.`beitragid` = `mitgliederbeitrag`.`mitgliederbeitragid`)
			                                   GROUP BY `mitglieder`.`mitgliedid`
			                                   HAVING IFNULL(SUM(`buchung`.`hoehe`),0) <= " . floatval($matcher->getBeitragMark()) . ")";
		}
		if ($matcher instanceof BeitragPaidAboveMitgliederMatcher) {
			return "`m`.`mitgliedid` IN (SELECT `mitgliedid`
			                                   FROM `mitglieder`
			                                   LEFT JOIN `mitgliederbeitrag` USING (`mitgliedid`)
			                                   LEFT JOIN `mitgliederbeitragbuchung` `buchung` ON (`buchung`.`beitragid` = `mitgliederbeitrag`.`mitgliederbeitragid`)
			                                   GROUP BY `mitglieder`.`mitgliedid`
			                                   HAVING IFNULL(SUM(`buchung`.`hoehe`),0) > " . floatval($matcher->getBeitragMark()) . ")";
		}
		if ($matcher instanceof BeitragMissingMitgliederMatcher) {
			return "`m`.`mitgliedid` IN (SELECT `mitgliedid` FROM 
			                            (SELECT `mitgliedid`, `mitgliederbeitrag`.`hoehe`
			                                   FROM `mitgliederbeitrag`
			                                   LEFT JOIN `mitgliederbeitragbuchung` `buchung` ON (`buchung`.`beitragid` = `mitgliederbeitrag`.`mitgliederbeitragid`)
			                                   WHERE `mitgliederbeitrag`.`beitragid` = ".intval($matcher->getBeitragID())."
			                                   GROUP BY `mitgliederbeitrag`.`mitgliederbeitragid`
			                                   HAVING `mitgliederbeitrag`.`hoehe` - IFNULL(SUM(`buchung`.`hoehe`),0) > 0) AS `tmp`)";
		}
		if ($matcher instanceof BeitragMissingBelowMitgliederMatcher) {
			return "`m`.`mitgliedid` IN (SELECT `mitgliedid` FROM 
			                            (SELECT `mitgliedid`, SUM(IFNULL(`mitgliederbeitrag`.`hoehe`,0)) - IFNULL(SUM(`buchung`.`hoehe`),0) AS `missing`
			                                   FROM `mitglieder`
			                                   LEFT JOIN `mitgliederbeitrag` USING (`mitgliedid`)
			                                   LEFT JOIN `mitgliederbeitragbuchung` `buchung` ON (`buchung`.`beitragid` = `mitgliederbeitrag`.`mitgliederbeitragid`)
			                                   GROUP BY `mitglieder`.`mitgliedid`) AS `tmp`
			                             WHERE `tmp`.`missing` <= " . floatval($matcher->getBeitragMark()) . ")";
		}
		if ($matcher instanceof BeitragMissingAboveMitgliederMatcher) {
			return "`m`.`mitgliedid` IN (SELECT `mitgliedid` FROM 
			                            (SELECT `mitgliedid`, SUM(IFNULL(`mitgliederbeitrag`.`hoehe`,0)) - IFNULL(SUM(`buchung`.`hoehe`),0) AS `missing`
			                                   FROM `mitglieder`
			                                   LEFT JOIN `mitgliederbeitrag` USING (`mitgliedid`)
			                                   LEFT JOIN `mitgliederbeitragbuchung` `buchung` ON (`buchung`.`beitragid` = `mitgliederbeitrag`.`mitgliederbeitragid`)
			                                   GROUP BY `mitglieder`.`mitgliedid`) AS `tmp`
			                             WHERE `tmp`.`missing` > " . floatval($matcher->getBeitragMark()) . ")";
		}
		if ($matcher instanceof DokumentMitgliederMatcher) {
			return "`m`.`mitgliedid` IN (SELECT `mitgliedid` FROM `mitglieddokument` WHERE `dokumentid` = " . intval($matcher->getDokumentID()) . ")";
		}
		if ($matcher instanceof DokumentKategorieMitgliederMatcher) {
			return "`m`.`mitgliedid` IN (SELECT `mitgliedid` FROM `mitglieddokument` `mdok` LEFT JOIN `dokument` `dok` USING (`dokumentid`) WHERE `dok`.`dokumentkategorieid` = " . intval($matcher->getDokumentKategorieID()) . ")";
		}
		if ($matcher instanceof DokumentStatusMitgliederMatcher) {
			return "`m`.`mitgliedid` IN (SELECT `mitgliedid` FROM `mitglieddokument` `mdok` LEFT JOIN `dokument` `dok` USING (`dokumentid`) WHERE `dok`.`dokumentstatusid` = " . intval($matcher->getDokumentStatusID()) . ")";
		}
		if ($matcher instanceof EintrittsdatumAfterMitgliederMatcher) {
			return "`m`.`eintritt` >= '" . $this->escape(date("Y-m-d", $matcher->getTimestamp())) . "'";
		}
		if ($matcher instanceof AustrittsdatumAfterMitgliederMatcher) {
			return "`m`.`austritt` >= '" . $this->escape(date("Y-m-d", $matcher->getTimestamp())) . "'";
		}
		if ($matcher instanceof SearchMitgliederMatcher) {
			$fields = array("`m`.`mitgliedid`", "`m`.`globalid`", "`r`.`revisionid`", "`r`.`globaleid`", "`r`.`userid`", "`r`.`mitgliedid`", "`r`.`mitgliedschaftid`", "`r`.`gliederungsid`", "`r`.`geloescht`", "`r`.`beitrag`", "`n`.`natpersonid`", "`n`.`anrede`", "`n`.`name`", "`n`.`vorname`", "`n`.`nationalitaet`", "`j`.`jurpersonid`", "`j`.`label`", "`k`.`kontaktid`", "`k`.`adresszusatz`", "`k`.`strasse`", "`k`.`hausnummer`", "`k`.`telefonnummer`", "`k`.`handynummer`", "`k`.`iban`", "`e`.`email`", "`o`.`ortid`", "`o`.`plz`", "`o`.`label`", "`o`.`stateid`");
			$wordclauses = array();
			$escapedwords = array();
			foreach ($matcher->getWords() as $word) {
				$escapedwords[] = $this->escape($word);
				$clauses = array();
				foreach ($fields as $field) {
					$clauses[] = $field . " LIKE '%" . $this->escape($word) . "%'";
				}
				$wordclauses[] = implode(" OR ", $clauses);
			}
			return "( (" . implode(") AND (", $wordclauses) . ") OR `m`.`mitgliedid` IN (SELECT `mitgliedid` FROM `mitgliedernotizen` WHERE `kommentar` LIKE '%" . implode("%' AND `kommentar` LIKE '%", $escapedwords) . "%') )";
		}
		throw new Exception("Not implemented: ".get_class($matcher));
	}
	public function getMitgliederCount($matcher = null) {
		if ($matcher instanceof MitgliederFilter) {
			$matcher = $matcher->getMatcher();
		}
		$sql = "SELECT	COUNT(`r`.`revisionid`) as `count`
			FROM	`mitgliederrevisions` `r`
			LEFT JOIN `mitglieder` `m` USING (`mitgliedid`)
			LEFT JOIN `natperson` `n` USING (`natpersonid`)
			LEFT JOIN `jurperson` `j` USING (`jurpersonid`)
			LEFT JOIN `kontakte` `k` USING (`kontaktid`)
			LEFT JOIN `orte` `o` USING (`ortid`)
			LEFT JOIN `emails` `e` USING (`emailid`)
			WHERE	`r`.`timestamp` = (
				SELECT	MAX(`rmax`.`timestamp`)
				FROM	`mitgliederrevisions` `rmax`
				WHERE	`r`.`mitgliedid` = `rmax`.`mitgliedid`)
					AND ".$this->parseMitgliederMatcher($matcher);
		return reset($this->getResult($sql)->fetchRow());
	}
	public function parseMitglied($row) {
		$o = $this->parseRow($row, null, array(	"r" => 'MitgliedRevision', "g" => 'Gliederung', "n" => 'NatPerson', "j" => 'JurPerson', "k" => 'Kontakt', "o" => 'Ort',
							"s" => 'State', "c" => 'Country', "e" => 'EMail', "m" => 'Mitglied', "t" => 'Mitgliedschaft', "u" => 'User'));
		$o["s"]->setCountry($o["c"]);
		$o["o"]->setState($o["s"]);
		$o["k"]->setOrt($o["o"]);
		$o["k"]->setEMail($o["e"]);
		if ($o["r"]->getNatPersonID() !== null) {
			$o["r"]->setNatPerson($o["n"]);
		}
		if ($o["r"]->getJurPersonID() !== null) {
			$o["r"]->setJurPerson($o["j"]);
		}
		$o["r"]->setKontakt($o["k"]);
		$o["r"]->setGliederung($o["g"]);
		$o["r"]->setMitgliedschaft($o["t"]);
		$o["r"]->setUser($o["u"]);
		$o["m"]->setLatestRevision($o["r"]);
		return $o["m"];
	}
	public function getMitgliederResult($matcher = null, $limit = null, $offset = null) {
		if ($matcher instanceof MitgliederFilter) {
			$matcher = $matcher->getMatcher();
		}
		$sql = "SELECT	`r`.`timestamp` AS `null`,
				`m`.`mitgliedid` as `m_mitgliedid`,
				`m`.`globalid` as `m_globalid`,
				UNIX_TIMESTAMP(`m`.`eintritt`) as `m_eintritt`,
				UNIX_TIMESTAMP(`m`.`austritt`) as `m_austritt`,
				`r`.`revisionid` AS `r_revisionid`,
				`r`.`globaleid` AS `r_globaleid`,
				UNIX_TIMESTAMP(`r`.`timestamp`) AS `r_timestamp`,
				`r`.`userid` AS `r_userid`,
				`r`.`mitgliedid` AS `r_mitgliedid`,
				`r`.`mitgliedschaftid` AS `r_mitgliedschaftid`,
				`r`.`gliederungsid` AS `r_gliederungsid`,
				`r`.`geloescht` AS `r_geloescht`,
				`r`.`beitrag` AS `r_beitrag`,
				`r`.`natpersonid` AS `r_natpersonid`,
				`r`.`jurpersonid` AS `r_jurpersonid`,
				`r`.`kontaktid` AS `r_kontaktid`,
				`n`.`natpersonid` AS `n_natpersonid`,
				`n`.`anrede` AS `n_anrede`,
				`n`.`name` AS `n_name`,
				`n`.`vorname` AS `n_vorname`,
				`n`.`geburtsdatum` AS `n_geburtsdatum`,
				`n`.`nationalitaet` AS `n_nationalitaet`,
				`j`.`jurpersonid` AS `j_jurpersonid`,
				`j`.`label` AS `j_label`,
				`k`.`kontaktid` AS `k_kontaktid`,
				`k`.`adresszusatz` AS `k_adresszusatz`,
				`k`.`strasse` AS `k_strasse`,
				`k`.`hausnummer` AS `k_hausnummer`,
				`k`.`ortid` AS `k_ortid`,
				`k`.`telefonnummer` AS `k_telefonnummer`,
				`k`.`handynummer` AS `k_handynummer`,
				`k`.`emailid` AS `k_emailid`,
				`k`.`iban` AS `k_iban`,
				`e`.`emailid` AS `e_emailid`,
				`e`.`email` AS `e_email`,
				`o`.`ortid` AS `o_ortid`,
				`o`.`plz` AS `o_plz`,
				`o`.`label` AS `o_label`,
				`o`.`latitude` AS `o_latitude`,
				`o`.`longitude` AS `o_longitude`,
				`o`.`stateid` AS `o_stateid`,
				`s`.`stateid` AS `s_stateid`,
				`s`.`label` AS `s_label`,
				`s`.`population` AS `s_population`,
				`s`.`countryid` AS `s_countryid`,
				`c`.`countryid` AS `c_countryid`,
				`c`.`label` AS `c_label`,
				`g`.`gliederungsid` AS `g_gliederungsid`,
				`g`.`label` AS `g_label`,
				`g`.`parentid` AS `g_parentid`,
				`t`.`mitgliedschaftid` AS `t_mitgliedschaftid`,
				`t`.`label` AS `t_label`,
				`t`.`description` AS `t_description`,
				`u`.`userid` AS `u_userid`,
				`u`.`username` AS `u_username`,
				`u`.`password` AS `u_password`,
				`u`.`passwordsalt` AS `u_passwordsalt`,
				`u`.`apikey` AS `u_apikey`,
				`u`.`aktiv` AS `u_aktiv`,
				`u`.`defaultgliederungid` AS `u_defaultgliederungid`,
				`u`.`defaultdokumentkategorieid` AS `u_defaultdokumentkategorieid`,
				`u`.`defaultdokumentstatusid` AS `u_defaultdokumentstatusid`
			FROM	`mitglieder` `m`
			LEFT JOIN `mitgliederrevisions` `r` USING (`mitgliedid`)
			LEFT JOIN `natperson` `n` USING (`natpersonid`)
			LEFT JOIN `jurperson` `j` USING (`jurpersonid`)
			LEFT JOIN `kontakte` `k` USING (`kontaktid`)
			LEFT JOIN `emails` `e` USING (`emailid`)
			LEFT JOIN `orte` `o` USING (`ortid`)
			LEFT JOIN `states` `s` USING (`stateid`)
			LEFT JOIN `countries` `c` USING (`countryid`)
			LEFT JOIN `gliederungen` `g` USING (`gliederungsid`)
			LEFT JOIN `mitgliedschaften` `t` USING (`mitgliedschaftid`)
			LEFT JOIN `users` `u` USING (`userid`)
			WHERE	`r`.`timestamp` = (
				SELECT	MAX(`rmax`.`timestamp`)
				FROM	`mitgliederrevisions` `rmax`
				WHERE	`r`.`mitgliedid` = `rmax`.`mitgliedid`)
					AND " . $this->parseMitgliederMatcher($matcher) . "
			ORDER BY `m`.`mitgliedid`";
		if ($limit !== null or $offset !== null) {
			$sql .= " LIMIT ";
			if ($offset !== null) {
				$sql .= $offset . ",";
			}
			if ($limit !== null) {
				$sql .= $limit;
			}
		}
		return $this->getResult($sql, array($this, "parseMitglied"));
	}
	public function getMitgliederByDokumentResult($dokumentid) {
		$sql = "SELECT	`r`.`timestamp` AS `null`,
				`m`.`mitgliedid` as `m_mitgliedid`,
				`m`.`globalid` as `m_globalid`,
				UNIX_TIMESTAMP(`m`.`eintritt`) as `m_eintritt`,
				UNIX_TIMESTAMP(`m`.`austritt`) as `m_austritt`,
				`r`.`revisionid` AS `r_revisionid`,
				`r`.`globaleid` AS `r_globaleid`,
				UNIX_TIMESTAMP(`r`.`timestamp`) AS `r_timestamp`,
				`r`.`userid` AS `r_userid`,
				`r`.`mitgliedid` AS `r_mitgliedid`,
				`r`.`mitgliedschaftid` AS `r_mitgliedschaftid`,
				`r`.`gliederungsid` AS `r_gliederungsid`,
				`r`.`geloescht` AS `r_geloescht`,
				`r`.`beitrag` AS `r_beitrag`,
				`r`.`natpersonid` AS `r_natpersonid`,
				`r`.`jurpersonid` AS `r_jurpersonid`,
				`r`.`kontaktid` AS `r_kontaktid`,
				`n`.`natpersonid` AS `n_natpersonid`,
				`n`.`anrede` AS `n_anrede`,
				`n`.`name` AS `n_name`,
				`n`.`vorname` AS `n_vorname`,
				`n`.`geburtsdatum` AS `n_geburtsdatum`,
				`n`.`nationalitaet` AS `n_nationalitaet`,
				`j`.`jurpersonid` AS `j_jurpersonid`,
				`j`.`label` AS `j_label`,
				`k`.`kontaktid` AS `k_kontaktid`,
				`k`.`adresszusatz` AS `k_adresszusatz`,
				`k`.`strasse` AS `k_strasse`,
				`k`.`hausnummer` AS `k_hausnummer`,
				`k`.`ortid` AS `k_ortid`,
				`k`.`telefonnummer` AS `k_telefonnummer`,
				`k`.`handynummer` AS `k_handynummer`,
				`k`.`emailid` AS `k_emailid`,
				`k`.`iban` AS `k_iban`,
				`e`.`emailid` AS `e_emailid`,
				`e`.`email` AS `e_email`,
				`o`.`ortid` AS `o_ortid`,
				`o`.`plz` AS `o_plz`,
				`o`.`label` AS `o_label`,
				`o`.`latitude` AS `o_latitude`,
				`o`.`longitude` AS `o_longitude`,
				`o`.`stateid` AS `o_stateid`,
				`s`.`stateid` AS `s_stateid`,
				`s`.`label` AS `s_label`,
				`s`.`population` AS `s_population`,
				`s`.`countryid` AS `s_countryid`,
				`c`.`countryid` AS `c_countryid`,
				`c`.`label` AS `c_label`,
				`g`.`gliederungsid` AS `g_gliederungsid`,
				`g`.`label` AS `g_label`,
				`g`.`parentid` AS `g_parentid`,
				`t`.`mitgliedschaftid` AS `t_mitgliedschaftid`,
				`t`.`label` AS `t_label`,
				`t`.`description` AS `t_description`,
				`u`.`userid` AS `u_userid`,
				`u`.`username` AS `u_username`,
				`u`.`password` AS `u_password`,
				`u`.`passwordsalt` AS `u_passwordsalt`,
				`u`.`apikey` AS `u_apikey`,
				`u`.`aktiv` AS `u_aktiv`,
				`u`.`defaultgliederungid` AS `u_defaultgliederungid`,
				`u`.`defaultdokumentkategorieid` AS `u_defaultdokumentkategorieid`,
				`u`.`defaultdokumentstatusid` AS `u_defaultdokumentstatusid`
			FROM	`mitglieddokument`
			LEFT JOIN `mitglieder` `m` USING (`mitgliedid`)
			LEFT JOIN `mitgliederrevisions` `r` USING (`mitgliedid`)
			LEFT JOIN `natperson` `n` ON (`n`.`natpersonid` = `r`.`natpersonid`)
			LEFT JOIN `jurperson` `j` ON (`j`.`jurpersonid` = `r`.`jurpersonid`)
			LEFT JOIN `kontakte` `k` ON (`k`.`kontaktid` = `r`.`kontaktid`)
			LEFT JOIN `emails` `e` USING (`emailid`)
			LEFT JOIN `orte` `o` USING (`ortid`)
			LEFT JOIN `states` `s` USING (`stateid`)
			LEFT JOIN `countries` `c` USING (`countryid`)
			LEFT JOIN `gliederungen` `g` USING (`gliederungsid`)
			LEFT JOIN `mitgliedschaften` `t` USING (`mitgliedschaftid`)
			LEFT JOIN `users` `u` USING (`userid`)
			WHERE	`r`.`timestamp` = (
				SELECT	MAX(`rmax`.`timestamp`)
				FROM	`mitgliederrevisions` `rmax`
				WHERE	`r`.`mitgliedid` = `rmax`.`mitgliedid`)
					AND	`dokumentid` = " . intval($dokumentid) . "
			ORDER BY `m`.`mitgliedid`";
		return $this->getResult($sql, array($this, "parseMitglied"));
	}
	public function getMitglied($mitgliedid) {
		return $this->getMitgliederResult(new MitgliedMitgliederMatcher($mitgliedid))->fetchRow();
	}
	public function setMitglied($mitgliedid, $globalid, $eintritt, $austritt) {
		if ($mitgliedid == null) {
			$sql = "INSERT INTO `mitglieder` (`globalid`, `eintritt`, `austritt`) VALUES ('" . $this->escape($globalid) . "', '" . date("Y-m-d", $eintritt) . "', " . ($austritt == null ? "NULL" : "'" . date("Y-m-d", $austritt) . "'") . ")";
		} else {
			$sql = "UPDATE `mitglieder` SET `globalid` = '" . $this->escape($globalid) . "', `eintritt` = '" . date("Y-m-d", $eintritt) . "', `austritt` = " . ($austritt == null ? "NULL" : "'" . date("Y-m-d", $austritt) . "'") . " WHERE `mitgliedid` = " . intval($mitgliedid);
		}
		$this->query($sql);
		if ($mitgliedid == null) {
			return $this->getInsertID();
		} else {
			return $mitgliedid;
		}
	}

	/**
	 * MitgliedFlag
	 **/
	public function parseMitgliedFlag($row) {
		return $this->parseRow($row, null, "MitgliedFlag");
	}
	public function getMitgliedFlagResult() {
		$sql = "SELECT `flagid`, `label` FROM `mitgliederflags`";
		return $this->getResult($sql, array($this, "parseMitgliedFlag"));
	}
	public function getMitgliedFlag($flagid) {
		$sql = "SELECT `flagid`, `label` FROM `mitgliederflags` WHERE `flagid` = " . intval($flagid);
		return $this->getResult($sql, array($this, "parseMitgliedFlag"))->fetchRow();
	}
	public function setMitgliedFlag($flagid, $label) {
		if ($flagid == null) {
			$sql = "INSERT INTO `mitgliederflags`
				(`label`) VALUES
				('" . $this->escape($label) . "')";
		} else {
			$sql = "UPDATE	`mitgliederflags`
				SET	`label` = '" . $this->escape($label) . "'
				WHERE `flagid` = " . intval($flagid);
		}
		$this->query($sql);
		if ($flagid == null) {
			$flagid = $this->getInsertID();
		}
		return $flagid;
	}
	public function delMitgliedFlag($flagid) {
		$sql = "DELETE FROM `mitgliederflags` WHERE `flagid` = " . intval($flagid);
		return $this->query($sql);
	}
	public function getMitgliederRevisionFlagResult($revisionid) {
		$sql = "SELECT `flagid`, `label` FROM `mitgliederrevisionflags` LEFT JOIN `mitgliederflags` USING (`flagid`) WHERE `revisionid` = " . intval($revisionid);
		return $this->getResult($sql, array($this, "parseMitgliedFlag"));
	}
	public function setMitgliederRevisionFlagList($revisionid, $flags) {
		$sql = "DELETE FROM `mitgliederrevisionflags` WHERE `revisionid` = " . intval($revisionid);
		$this->query($sql);
		$sqlinserts = array();
		while (count($flags) > 0) {
			$sqlinserts[] = "(" . intval($revisionid) . ", " . intval(array_shift($flags)) . ")";
		}
		if (count($sqlinserts) > 0) {
			$sql = "INSERT INTO `mitgliederrevisionflags` (`revisionid`, `flagid`) VALUES " . implode(", ", $sqlinserts);
			$this->query($sql);
		}
	}

	/**
	 * MitgliedTextField
	 **/
	public function parseMitgliedTextField($row) {
		return $this->parseRow($row, null, "MitgliedTextField");
	}
	public function getMitgliedTextFieldResult() {
		$sql = "SELECT `textfieldid`, `label` FROM `mitgliedertextfields`";
		return $this->getResult($sql, array($this, "parseMitgliedTextField"));
	}
	public function getMitgliedTextField($textfieldid) {
		$sql = "SELECT `textfieldid`, `label` FROM `mitgliedertextfields` WHERE `textfieldid` = " . intval($textfieldid);
		return $this->getResult($sql, array($this, "parseMitgliedTextField"))->fetchRow();
	}
	public function setMitgliedTextField($textfieldid, $label) {
		if ($textfieldid == null) {
			$sql = "INSERT INTO `mitgliedertextfields`
				(`label`) VALUES
				('" . $this->escape($label) . "')";
		} else {
			$sql = "UPDATE	`mitgliedertextfields`
				SET	`label` = '" . $this->escape($label) . "'
				WHERE `textfieldid` = " . intval($textfieldid);
		}
		$this->query($sql);
		if ($textfieldid == null) {
			$textfieldid = $this->getInsertID();
		}
		return $textfieldid;
	}
	public function delMitgliedTextField($textfieldid) {
		$sql = "DELETE FROM `mitgliedertextfields` WHERE `textfieldid` = " . intval($textfieldid);
		return $this->query($sql);
	}

	public function parseMitgliedRevisionTextField($row) {
		$o = $this->parseRow($row, null, array("rtx" => "MitgliedRevisionTextField", "tx" => "MitgliedTextField"));
		$o["rtx"]->setTextField($o["tx"]);
		return $o["rtx"];
	}
	public function getMitgliederRevisionTextFieldResult($revisionid) {
		$sql = "SELECT `textfieldid` AS `rtx_textfieldid`, `revisionid` as `rtx_revisionid`, `value` as `rtx_value`, `textfieldid` as `tx_textfieldid`, `label` as `tx_label` FROM `mitgliederrevisiontextfields` LEFT JOIN `mitgliedertextfields` USING (`textfieldid`) WHERE `revisionid` = " . intval($revisionid);
		return $this->getResult($sql, array($this, "parseMitgliedRevisionTextField"));
	}
	public function setMitgliederRevisionTextFieldList($revisionid, $textfieldids, $textfieldvalues) {
		$sql = "DELETE FROM `mitgliederrevisiontextfields` WHERE `revisionid` = " . intval($revisionid);
		$this->query($sql);
		$sqlinserts = array();
		while (count($textfieldids) > 0) {
			$sqlinserts[] = "(" . intval($revisionid) . ", " . intval(array_shift($textfieldids)) . ", '" . $this->escape(array_shift($textfieldvalues)) . "')";
		}
		if (count($sqlinserts) > 0) {
			$sql = "INSERT INTO `mitgliederrevisiontextfields` (`revisionid`, `textfieldid`, `value`) VALUES " . implode(", ", $sqlinserts);
			$this->query($sql);
		}
	}

	/**
	 * MitgliedNotiz
	 **/
	public function parseMitgliedNotiz($row) {
		return $this->parseRow($row, null, "MitgliedNotiz");
	}
	public function getMitgliedNotizResult($mitgliedid = null) {
		$sql = "SELECT `mitgliednotizid`, `mitgliedid`, `author`, `timestamp`, `kommentar` FROM `mitgliedernotizen` WHERE 1=1";
		if ($mitgliedid != null) {
			$sql .= " AND `mitgliedid` = " . intval($mitgliedid);
		}
		return $this->getResult($sql, array($this, "parseMitgliedNotiz"));
	}
	public function getMitgliedNotiz($mitgliednotizid) {
		$sql = "SELECT `mitgliednotizid`, `mitgliedid`, `author`, `timestamp`, `kommentar` FROM `mitgliedernotizen` WHERE `mitgliednotizid` = " . intval($mitgliednotizid);
		return $this->getResult($sql, array($this, "parseMitgliedNotiz"))->fetchRow();
	}
	public function setMitgliedNotiz($mitgliednotizid, $mitgliedid, $author, $timestamp, $kommentar) {
		if ($mitgliednotizid == null) {
			$sql = "INSERT INTO `mitgliedernotizen`
				(`mitgliedid`, `author`, `timestamp`, `kommentar`) VALUES
				(" . intval($mitgliedid) . ",
				 " . ($author == NULL ? "NULL" : intval($author)) . ",
				 '" . date("Y-m-d H:i:s", $timestamp) . "',
				 '" . $this->escape($kommentar) . "')";
		} else {
			$sql = "UPDATE	`mitgliedernotizen`
				SET	`mitgliedid` = " . intval($mitgliedid) . ",
					`author` = " . ($author == NULL ? "NULL" : intval($author)) . ",
					`timestamp` = " . date("Y-m-d H:i:s", $timestamp) . ",
					`kommentar` = '" . $this->escape($kommentar) . "'
				WHERE `mitgliednotizid` = " . intval($mitgliednotizid);
		}
		$this->query($sql);
		if ($mitgliednotizid == null) {
			$mitgliednotizid = $this->getInsertID();
		}
		return $mitgliednotizid;
	}
	public function delMitgliedNotiz($mitgliednotizid) {
		$sql = "DELETE FROM `mitgliedernotizen` WHERE `mitgliednotizid` = " . intval($mitgliednotizid);
		return $this->query($sql);
	}

	/**
	 * MitgliederRevisions
	 **/
	public function parseMitgliederRevision($row) {
		$o = $this->parseRow($row, null, array(	"r" => 'MitgliedRevision', "n" => 'NatPerson', "j" => 'JurPerson', "k" => 'Kontakt', "o" => "Ort", "s" => "State", "c" => "Country",
							"g" => "Gliederung", "e" => "EMail", "t" => "Mitgliedschaft", "u" => "User"));
		$o["s"]->setCountry($o["c"]);
		$o["o"]->setState($o["s"]);
		$o["k"]->setOrt($o["o"]);
		$o["k"]->setEMail($o["e"]);
		$o["r"]->setNatPerson($o["n"]);
		$o["r"]->setJurPerson($o["j"]);
		$o["r"]->setKontakt($o["k"]);
		$o["r"]->setGliederung($o["g"]);
		$o["r"]->setMitgliedschaft($o["t"]);
		$o["r"]->setUser($o["u"]);
		return $o["r"];
	}
	public function getMitgliederRevisionResultTimeline($gliederungids, $start, $count) {
		$sql = "SELECT	`r`.`revisionid` AS `r_revisionid`,
				`r`.`globaleid` AS `r_globaleid`,
				UNIX_TIMESTAMP(`r`.`timestamp`) AS `r_timestamp`,
				`r`.`userid` AS `r_userid`,
				`r`.`mitgliedid` AS `r_mitgliedid`,
				`r`.`mitgliedschaftid` AS `r_mitgliedschaftid`,
				`r`.`gliederungsid` AS `r_gliederungsid`,
				`r`.`geloescht` AS `r_geloescht`,
				`r`.`beitrag` AS `r_beitrag`,
				`r`.`natpersonid` AS `r_natpersonid`,
				`r`.`jurpersonid` AS `r_jurpersonid`,
				`r`.`kontaktid` AS `r_kontaktid`,
				`n`.`natpersonid` AS `n_natpersonid`,
				`n`.`anrede` AS `n_anrede`,
				`n`.`name` AS `n_name`,
				`n`.`vorname` AS `n_vorname`,
				`n`.`geburtsdatum` AS `n_geburtsdatum`,
				`n`.`nationalitaet` AS `n_nationalitaet`,
				`j`.`jurpersonid` AS `j_jurpersonid`,
				`j`.`label` AS `j_label`,
				`k`.`kontaktid` AS `k_kontaktid`,
				`k`.`adresszusatz` AS `k_adresszusatz`,
				`k`.`strasse` AS `k_strasse`,
				`k`.`hausnummer` AS `k_hausnummer`,
				`k`.`ortid` AS `k_ortid`,
				`k`.`telefonnummer` AS `k_telefonnummer`,
				`k`.`handynummer` AS `k_handynummer`,
				`k`.`emailid` AS `k_emailid`,
				`k`.`iban` AS `k_iban`,
				`e`.`emailid` AS `e_emailid`,
				`e`.`email` AS `e_email`,
				`o`.`ortid` AS `o_ortid`,
				`o`.`plz` AS `o_plz`,
				`o`.`label` AS `o_label`,
				`o`.`latitude` AS `o_latitude`,
				`o`.`longitude` AS `o_longitude`,
				`o`.`stateid` AS `o_stateid`,
				`s`.`stateid` AS `s_stateid`,
				`s`.`label` AS `s_label`,
				`s`.`population` AS `s_population`,
				`s`.`countryid` AS `s_countryid`,
				`c`.`countryid` AS `c_countryid`,
				`c`.`label` AS `c_label`,
				`g`.`gliederungsid` AS `g_gliederungsid`,
				`g`.`label` AS `g_label`,
				`g`.`parentid` AS `g_parentid`,
				`t`.`mitgliedschaftid` AS `t_mitgliedschaftid`,
				`t`.`label` AS `t_label`,
				`t`.`description` AS `t_description`,
				`u`.`userid` AS `u_userid`,
				`u`.`username` AS `u_username`,
				`u`.`password` AS `u_password`,
				`u`.`passwordsalt` AS `u_passwordsalt`,
				`u`.`apikey` AS `u_apikey`,
				`u`.`aktiv` AS `u_aktiv`,
				`u`.`defaultgliederungid` AS `u_defaultgliederungid`,
				`u`.`defaultdokumentkategorieid` AS `u_defaultdokumentkategorieid`,
				`u`.`defaultdokumentstatusid` AS `u_defaultdokumentstatusid`
			FROM	`mitgliederrevisions` `r`
			LEFT JOIN `natperson` `n` USING (`natpersonid`)
			LEFT JOIN `jurperson` `j` USING (`jurpersonid`)
			LEFT JOIN `kontakte` `k` USING (`kontaktid`)
			LEFT JOIN `orte` `o` USING (`ortid`)
			LEFT JOIN `states` `s` USING (`stateid`)
			LEFT JOIN `countries` `c` USING (`countryid`)
			LEFT JOIN `gliederungen` `g` USING (`gliederungsid`)
			LEFT JOIN `emails` `e` USING (`emailid`)
			LEFT JOIN `mitgliedschaften` `t` USING (`mitgliedschaftid`)
			LEFT JOIN `users` `u` USING (`userid`)
			WHERE	`r`.`timestamp` = (	SELECT	MAX(`rmax`.`timestamp`)
							FROM	`mitgliederrevisions` `rmax`
							WHERE   `r`.`mitgliedid` = `rmax`.`mitgliedid`)
			ORDER BY `r`.`timestamp` DESC
			LIMIT " . intval($start) . ",". intval($count);
		return $this->getResult($sql, array($this, "parseMitgliederRevision"));
	}
	public function getMitgliederRevisionResult() {
		$sql = "SELECT	`r`.`revisionid` AS `r_revisionid`,
				`r`.`globaleid` AS `r_globaleid`,
				UNIX_TIMESTAMP(`r`.`timestamp`) AS `r_timestamp`,
				`r`.`userid` AS `r_userid`,
				`r`.`mitgliedid` AS `r_mitgliedid`,
				`r`.`mitgliedschaftid` AS `r_mitgliedschaftid`,
				`r`.`gliederungsid` AS `r_gliederungsid`,
				`r`.`geloescht` AS `r_geloescht`,
				`r`.`beitrag` AS `r_beitrag`,
				`r`.`natpersonid` AS `r_natpersonid`,
				`r`.`jurpersonid` AS `r_jurpersonid`,
				`r`.`kontaktid` AS `r_kontaktid`,
				`n`.`natpersonid` AS `n_natpersonid`,
				`n`.`anrede` AS `n_anrede`,
				`n`.`name` AS `n_name`,
				`n`.`vorname` AS `n_vorname`,
				`n`.`geburtsdatum` AS `n_geburtsdatum`,
				`n`.`nationalitaet` AS `n_nationalitaet`,
				`j`.`jurpersonid` AS `j_jurpersonid`,
				`j`.`label` AS `j_label`,
				`k`.`kontaktid` AS `k_kontaktid`,
				`k`.`adresszusatz` AS `k_adresszusatz`,
				`k`.`strasse` AS `k_strasse`,
				`k`.`hausnummer` AS `k_hausnummer`,
				`k`.`ortid` AS `k_ortid`,
				`k`.`telefonnummer` AS `k_telefonnummer`,
				`k`.`handynummer` AS `k_handynummer`,
				`k`.`emailid` AS `k_emailid`,
				`k`.`iban` AS `k_iban`,
				`e`.`emailid` AS `e_emailid`,
				`e`.`email` AS `e_email`,
				`o`.`ortid` AS `o_ortid`,
				`o`.`plz` AS `o_plz`,
				`o`.`label` AS `o_label`,
				`o`.`latitude` AS `o_latitude`,
				`o`.`longitude` AS `o_longitude`,
				`o`.`stateid` AS `o_stateid`,
				`s`.`stateid` AS `s_stateid`,
				`s`.`label` AS `s_label`,
				`s`.`population` AS `s_population`,
				`s`.`countryid` AS `s_countryid`,
				`c`.`countryid` AS `c_countryid`,
				`c`.`label` AS `c_label`,
				`g`.`gliederungsid` AS `g_gliederungsid`,
				`g`.`label` AS `g_label`,
				`g`.`parentid` AS `g_parentid`,
				`t`.`mitgliedschaftid` AS `t_mitgliedschaftid`,
				`t`.`label` AS `t_label`,
				`t`.`description` AS `t_description`,
				`u`.`userid` AS `u_userid`,
				`u`.`username` AS `u_username`,
				`u`.`password` AS `u_password`,
				`u`.`passwordsalt` AS `u_passwordsalt`,
				`u`.`apikey` AS `u_apikey`,
				`u`.`aktiv` AS `u_aktiv`,
				`u`.`defaultgliederungid` AS `u_defaultgliederungid`,
				`u`.`defaultdokumentkategorieid` AS `u_defaultdokumentkategorieid`,
				`u`.`defaultdokumentstatusid` AS `u_defaultdokumentstatusid`
			FROM	`mitgliederrevisions` `r`
			LEFT JOIN `natperson` `n` USING (`natpersonid`)
			LEFT JOIN `jurperson` `j` USING (`jurpersonid`)
			LEFT JOIN `kontakte` `k` USING (`kontaktid`)
			LEFT JOIN `orte` `o` USING (`ortid`)
			LEFT JOIN `states` `s` USING (`stateid`)
			LEFT JOIN `countries` `c` USING (`countryid`)
			LEFT JOIN `gliederungen` `g` USING (`gliederungsid`)
			LEFT JOIN `emails` `e` USING (`emailid`)
			LEFT JOIN `mitgliedschaften` `t` USING (`mitgliedschaftid`)
			LEFT JOIN `users` `u` USING (`userid`)
			ORDER BY `r`.`timestamp`";
		return $this->getResult($sql, array($this, "parseMitgliederRevision"));
	}
	public function getMitgliederRevisionsByMitgliedIDResult($mitgliedid) {
		$sql = "SELECT	`r`.`revisionid` AS `r_revisionid`,
				`r`.`globaleid` AS `r_globaleid`,
				UNIX_TIMESTAMP(`r`.`timestamp`) AS `r_timestamp`,
				`r`.`userid` AS `r_userid`,
				`r`.`mitgliedid` AS `r_mitgliedid`,
				`r`.`mitgliedschaftid` AS `r_mitgliedschaftid`,
				`r`.`gliederungsid` AS `r_gliederungsid`,
				`r`.`geloescht` AS `r_geloescht`,
				`r`.`beitrag` AS `r_beitrag`,
				`r`.`natpersonid` AS `r_natpersonid`,
				`r`.`jurpersonid` AS `r_jurpersonid`,
				`r`.`kontaktid` AS `r_kontaktid`,
				`n`.`natpersonid` AS `n_natpersonid`,
				`n`.`anrede` AS `n_anrede`,
				`n`.`name` AS `n_name`,
				`n`.`vorname` AS `n_vorname`,
				`n`.`geburtsdatum` AS `n_geburtsdatum`,
				`n`.`nationalitaet` AS `n_nationalitaet`,
				`j`.`jurpersonid` AS `j_jurpersonid`,
				`j`.`label` AS `j_label`,
				`k`.`kontaktid` AS `k_kontaktid`,
				`k`.`adresszusatz` AS `k_adresszusatz`,
				`k`.`strasse` AS `k_strasse`,
				`k`.`hausnummer` AS `k_hausnummer`,
				`k`.`ortid` AS `k_ortid`,
				`k`.`telefonnummer` AS `k_telefonnummer`,
				`k`.`handynummer` AS `k_handynummer`,
				`k`.`emailid` AS `k_emailid`,
				`k`.`iban` AS `k_iban`,
				`e`.`emailid` AS `e_emailid`,
				`e`.`email` AS `e_email`,
				`o`.`ortid` AS `o_ortid`,
				`o`.`plz` AS `o_plz`,
				`o`.`label` AS `o_label`,
				`o`.`latitude` AS `o_latitude`,
				`o`.`longitude` AS `o_longitude`,
				`o`.`stateid` AS `o_stateid`,
				`s`.`stateid` AS `s_stateid`,
				`s`.`label` AS `s_label`,
				`s`.`population` AS `s_population`,
				`s`.`countryid` AS `s_countryid`,
				`c`.`countryid` AS `c_countryid`,
				`c`.`label` AS `c_label`,
				`g`.`gliederungsid` AS `g_gliederungsid`,
				`g`.`label` AS `g_label`,
				`g`.`parentid` AS `g_parentid`,
				`t`.`mitgliedschaftid` AS `t_mitgliedschaftid`,
				`t`.`label` AS `t_label`,
				`t`.`description` AS `t_description`,
				`u`.`userid` AS `u_userid`,
				`u`.`username` AS `u_username`,
				`u`.`password` AS `u_password`,
				`u`.`passwordsalt` AS `u_passwordsalt`,
				`u`.`apikey` AS `u_apikey`,
				`u`.`aktiv` AS `u_aktiv`,
				`u`.`defaultgliederungid` AS `u_defaultgliederungid`,
				`u`.`defaultdokumentkategorieid` AS `u_defaultdokumentkategorieid`,
				`u`.`defaultdokumentstatusid` AS `u_defaultdokumentstatusid`
			FROM	`mitgliederrevisions` `r`
			LEFT JOIN `natperson` `n` USING (`natpersonid`)
			LEFT JOIN `jurperson` `j` USING (`jurpersonid`)
			LEFT JOIN `kontakte` `k` USING (`kontaktid`)
			LEFT JOIN `orte` `o` USING (`ortid`)
			LEFT JOIN `states` `s` USING (`stateid`)
			LEFT JOIN `countries` `c` USING (`countryid`)
			LEFT JOIN `gliederungen` `g` USING (`gliederungsid`)
			LEFT JOIN `emails` `e` USING (`emailid`)
			LEFT JOIN `mitgliedschaften` `t` USING (`mitgliedschaftid`)
			LEFT JOIN `users` `u` USING (`userid`)
			WHERE `r`.`mitgliedid` = " . intval($mitgliedid) . "
			ORDER BY `r`.`timestamp`";
		return $this->getResult($sql, array($this, "parseMitgliederRevision"));
	}
	public function getMitgliederRevision($revisionid) {
		$sql = "SELECT	`r`.`revisionid` AS `r_revisionid`,
				`r`.`globaleid` AS `r_globaleid`,
				UNIX_TIMESTAMP(`r`.`timestamp`) AS `r_timestamp`,
				`r`.`userid` AS `r_userid`,
				`r`.`mitgliedid` AS `r_mitgliedid`,
				`r`.`mitgliedschaftid` AS `r_mitgliedschaftid`,
				`r`.`gliederungsid` AS `r_gliederungsid`,
				`r`.`geloescht` AS `r_geloescht`,
				`r`.`beitrag` AS `r_beitrag`,
				`r`.`natpersonid` AS `r_natpersonid`,
				`r`.`jurpersonid` AS `r_jurpersonid`,
				`r`.`kontaktid` AS `r_kontaktid`,
				`n`.`natpersonid` AS `n_natpersonid`,
				`n`.`anrede` AS `n_anrede`,
				`n`.`name` AS `n_name`,
				`n`.`vorname` AS `n_vorname`,
				`n`.`geburtsdatum` AS `n_geburtsdatum`,
				`n`.`nationalitaet` AS `n_nationalitaet`,
				`j`.`jurpersonid` AS `j_jurpersonid`,
				`j`.`label` AS `j_label`,
				`k`.`kontaktid` AS `k_kontaktid`,
				`k`.`adresszusatz` AS `k_adresszusatz`,
				`k`.`strasse` AS `k_strasse`,
				`k`.`hausnummer` AS `k_hausnummer`,
				`k`.`ortid` AS `k_ortid`,
				`k`.`telefonnummer` AS `k_telefonnummer`,
				`k`.`handynummer` AS `k_handynummer`,
				`k`.`emailid` AS `k_emailid`,
				`k`.`iban` AS `k_iban`,
				`e`.`emailid` AS `e_emailid`,
				`e`.`email` AS `e_email`,
				`o`.`ortid` AS `o_ortid`,
				`o`.`plz` AS `o_plz`,
				`o`.`label` AS `o_label`,
				`o`.`latitude` AS `o_latitude`,
				`o`.`longitude` AS `o_longitude`,
				`o`.`stateid` AS `o_stateid`,
				`s`.`stateid` AS `s_stateid`,
				`s`.`label` AS `s_label`,
				`s`.`population` AS `s_population`,
				`s`.`countryid` AS `s_countryid`,
				`c`.`countryid` AS `c_countryid`,
				`c`.`label` AS `c_label`,
				`g`.`gliederungsid` AS `g_gliederungsid`,
				`g`.`label` AS `g_label`,
				`g`.`parentid` AS `g_parentid`,
				`t`.`mitgliedschaftid` AS `t_mitgliedschaftid`,
				`t`.`label` AS `t_label`,
				`t`.`description` AS `t_description`,
				`u`.`userid` AS `u_userid`,
				`u`.`username` AS `u_username`,
				`u`.`password` AS `u_password`,
				`u`.`passwordsalt` AS `u_passwordsalt`,
				`u`.`apikey` AS `u_apikey`,
				`u`.`aktiv` AS `u_aktiv`,
				`u`.`defaultgliederungid` AS `u_defaultgliederungid`,
				`u`.`defaultdokumentkategorieid` AS `u_defaultdokumentkategorieid`,
				`u`.`defaultdokumentstatusid` AS `u_defaultdokumentstatusid`
			FROM	`mitgliederrevisions` `r`
			LEFT JOIN `natperson` `n` USING (`natpersonid`)
			LEFT JOIN `jurperson` `j` USING (`jurpersonid`)
			LEFT JOIN `kontakte` `k` USING (`kontaktid`)
			LEFT JOIN `orte` `o` USING (`ortid`)
			LEFT JOIN `states` `s` USING (`stateid`)
			LEFT JOIN `countries` `c` USING (`countryid`)
			LEFT JOIN `gliederungen` `g` USING (`gliederungsid`)
			LEFT JOIN `emails` `e` USING (`emailid`)
			LEFT JOIN `mitgliedschaften` `t` USING (`mitgliedschaftid`)
			LEFT JOIN `users` `u` USING (`userid`)
			WHERE	`r`.`revisionid` = " . intval($revisionid);
		return $this->getResult($sql, array($this, "parseMitgliederRevision"))->fetchRow();
	}
	public function setMitgliederRevision($revisionid, $globalid, $timestamp, $userid, $mitgliedid, $mitgliedschaftid, $gliederungid, $geloescht, $beitrag, $natpersonid, $jurpersonid, $kontaktid) {
		if ($revisionid == null) {
			$sql = "INSERT INTO `mitgliederrevisions` (`globaleid`, `timestamp`, `userid`, `mitgliedid`, `mitgliedschaftid`, `gliederungsid`, `geloescht`, `beitrag`, `natpersonid`, `jurpersonid`, `kontaktid`) VALUES ('" . $this->escape($globalid) . "', '" . date("Y-m-d H:i:s", $timestamp) . "', " . ($userid === null ? "NULL" : intval($userid)) . ", " . intval($mitgliedid) . ", " . intval($mitgliedschaftid) . ", " . intval($gliederungid) . ", " . ($geloescht ? 1 : 0) . ", " . doubleval($beitrag) . ", " . ($natpersonid == null ? "NULL" : intval($natpersonid)) . ", " . ($jurpersonid == null ? "NULL" : intval($jurpersonid)) . ", " . intval($kontaktid) . ")";
		} else {
			$sql = "UPDATE `mitgliederrevisions` SET `globaleid` = '" . $this->escape($globalid) . "', `timestamp` = '" . date("Y-m-d H:i:s", $timestamp) . "', `userid` = " . ($userid === NULL ? "NULL" : intval($userid)) . ", `mitgliedid` = " . intval($mitgliedid) . ", `mitgliedschaftid` = " . intval($mitgliedschaftid) . ", `gliederungsid` = " . intval($gliederungid) . ", `geloescht` = " . ($geloescht ? 1 : 0) . ", " . doubleval($beitrag) . ", `natpersonid` = " . ($natpersonid == null ? "NULL" : intval($natpersonid)) . ",`jurpersonid` = " . ($jurpersonid == null ? "NULL" : intval($jurpersonid)) . ", `kontaktid` = " . intval($kontaktid) . " WHERE `revisionid` = " . intval($revisionid);
		}
		$this->query($sql);
		if ($revisionid == null) {
			$revisionid = $this->getInsertID();
		}
		return $revisionid;
	}

	/**
	 * Kontakt
	 **/
	public function parseKontakt($row) {
		return $this->parseRow($row, null, "Kontakt");
	}
	public function getKontakt($kontaktid) {
		$sql = "SELECT `kontaktid`, `adresszusatz`, `strasse`, `hausnummer`, `ortid`, `telefonnummer`, `handynummer`, `emailid`, `iban` FROM `kontakt` WHERE `kontaktid` = " . intval($kontaktid);
		return $this->getResult($sql, array($this, "parseKontakt"))->fetchRow();
	}
	public function setKontakt($kontaktid, $adresszusatz, $strasse, $hausnummer, $ortid, $telefon, $handy, $emailid, $iban) {
		if ($kontaktid == null) {
			$sql = "INSERT INTO `kontakte` (`adresszusatz`, `strasse`, `hausnummer`, `ortid`, `telefonnummer`, `handynummer`, `emailid`, `iban`) VALUES ('" . $this->escape($adresszusatz) . "', '" . $this->escape($strasse) . "', '" . $this->escape($hausnummer) . "', " . intval($ortid) . ", '" . $this->escape($telefon) . "', '" . $this->escape($handy) . "', " . intval($emailid) . ", '" . $this->escape($iban) . "')";
		} else {
			$sql = "UPDATE `kontakte` SET `adresszusatz` = '" . $this->escape($adresszusatz) . "', `strasse` = '" . $this->escape($strasse) . "', `hausnummer` = '" . $this->escape($hausnummer) . "', `ortid` = " . intval($ortid) . ", `telefonnummer` = '" . $this->escape($telefon) . "', `handynummer` = '" . $this->escape($handy) . "', `emailid` = " . intval($emailid) . ", `iban` = '" . $this->escape($iban) . "' WHERE `kontaktid` = " . intval($kontaktid);
		}
		$this->query($sql);
		if ($kontaktid == null) {
			$kontaktid = $this->getInsertID();
		}
		return $kontaktid;
	}
	function delKontakt($kontaktid) {
		$sql = "DELETE FROM `kontakte` WHERE `kontaktid` = " . intval($kontaktid);
		return $this->query($sql);
	}
	public function searchKontakt($adresszusatz, $strasse, $hausnummer, $ortid, $telefon, $handy, $emailid, $iban) {
		$sql = "SELECT `kontaktid`, `adresszusatz`, `strasse`, `hausnummer`, `ortid`, `telefonnummer`, `handynummer`, `emailid`, `iban` FROM `kontakte` WHERE `adresszusatz` = '" . $this->escape($adresszusatz) . "' AND `strasse` = '" . $this->escape($strasse) . "' AND `hausnummer` = '" . $this->escape($hausnummer) . "' AND `ortid` = " . intval($ortid) . " AND `telefonnummer` = '" . $this->escape($telefon) . "' AND `handynummer` = '" . $this->escape($handy) . "' AND `emailid` = '" . intval($emailid) . "' AND `iban` = '" . $this->escape($iban) . "'";
		$result = $this->getResult($sql, array($this, "parseKontakt"));
		if ($result->getCount() > 0) {
			return $result->fetchRow();
		}
		$kontakt = new Kontakt($this);
		$kontakt->setAdresszusatz($adresszusatz);
		$kontakt->setStrasse($strasse);
		$kontakt->setHausnummer($hausnummer);
		$kontakt->setOrtID($ortid);
		$kontakt->setTelefonnummer($telefon);
		$kontakt->setHandynummer($handy);
		$kontakt->setEMailID($emailid);
		$kontakt->setIBan($iban);
		$kontakt->save();
		return $kontakt;
	}

	/**
	 * EMail
	 **/
	public function parseEMail($row) {
		return $this->parseRow($row, null, "EMail");
	}
	public function getEMail($emailid) {
		$sql = "SELECT `emailid`, `email` FROM `emails` WHERE `emailid` = " . intval($emailid);
		return $this->getResult($sql, array($this, "parseEMail"))->fetchRow();
	}
	public function setEMail($emailid, $email) {
		if ($emailid == null) {
			$sql = "INSERT INTO `emails` (`email`) VALUES ('" . $this->escape($email) . "')";
		} else {
			$sql = "UPDATE `emails` SET `email` = '" . $this->escape($email) . "' WHERE `emailid` = " . intval($emailid);
		}
		$this->query($sql);
		if ($emailid == null) {
			$emailid = $this->getInsertID();
		}
		return $emailid;
	}
	public function delEMail($emailid) {
		$sql = "DELETE FROM `emails` WHERE `emailid` = " . intval($emailid);
		return $this->query($sql);
	}
	public function searchEMail($address) {
		$sql = "SELECT `emailid`, `email` FROM `emails` WHERE `email` = '" . $this->escape($address) . "'";
		$result = $this->getResult($sql, array($this, "parseEMail"));
		if ($result->getCount() > 0) {
			return $result->fetchRow();
		}
		$email = new EMail($this);
		$email->setEMail($address);
		$email->save();
		return $email;
	}

	/**
	 * EMailBounce
	 **/
	public function parseEMailBounce($row) {
		return $this->parseRow($row, null, "EMailBounce");
	}
	public function getEMailBounceResultTimeline($start, $count) {
		$sql = "SELECT `bounceid`, `emailid`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `message` FROM `emailbounces` ORDER BY `timestamp` DESC LIMIT " . intval($start) . "," . intval($count);
		return $this->getResult($sql, array($this, "parseEMailBounce"));
	}
	public function getEMailBounceResultByEMail($emailid) {
		$sql = "SELECT `bounceid`, `emailid`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `message` FROM `emailbounces` WHERE `emailid` = " . intval($emailid);
		return $this->getResult($sql, array($this, "parseEMailBounce"));
	}
	public function getEMailBounce($bounceid) {
		$sql = "SELECT `bounceid`, `emailid`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `message` FROM `emailbounces` WHERE `bounceid` = " . intval($bounceid);
		return $this->getResult($sql, array($this, "parseEMailBounce"))->fetchRow();
	}
	public function setEMailBounce($bounceid, $emailid, $timestamp, $message) {
		if ($bounceid == null) {
			$sql = "INSERT INTO `emailbounces` (`emailid`, `timestamp`, `message`) VALUES (" . intval($emailid) . ", '" . date("Y-m-d H:i:s", $timestamp) . "', '" . $this->escape($message) . "')";
		} else {
			$sql = "UPDATE `emailbounces` SET `emailid` = " . intval($emailid) . ", `timestamp` = '" . date("Y-m-d H:i:s", $timestamp) . "', `message` = '" . $this->escape($message) . "' WHERE `bounceid` = " . intval($bounceid);
		}
		$this->query($sql);
		if ($bounceid == null) {
			$bounceid = $this->getInsertID();
		}
		return $bounceid;
	}
	public function delEMailBounce($bounceid) {
		$sql = "DELETE FROM `emailbounces` WHERE `bounceid` = " . intval($bounceid);
		return $this->query($sql);
	}

	/**
	 * Ort
	 **/
	public function parseOrt($row) {
		return $this->parseRow($row, null, "Ort");
	}
	public function getOrtResult() {
		$sql = "SELECT `ortid`, `plz`, `label`, `latitude`, `longitude`, `stateid` FROM `orte`";
		return $this->getResult($sql, array($this, "parseOrt"));
	}
	public function getOrtResultLimit($plz = null, $label = null, $stateid = null, $count = null) {
		$sql = "SELECT `ortid`, `plz`, `label`, `latitude`, `longitude`, `stateid` FROM `orte` WHERE 1";
		if ($plz != null) {
			$sql .= " and `plz` LIKE '" . $this->escape($plz) . "%'";
		}
		if ($label != null) {
			$sql .= " and `label` LIKE '%" . $this->escape($label) . "%'";
		}
		if ($stateid != null) {
			$sql .= " and `stateid` = " . intval($stateid);
		}
		if ($count != null) {
			$sql .= " LIMIT " . intval($count);
		}
		return $this->getResult($sql, array($this, "parseOrt"));
	}
	public function getOrt($ortid) {
		$sql = "SELECT `ortid`, `plz`, `label`, `latitude`, `longitude`, `stateid` FROM `orte` WHERE `ortid` = " . intval($ortid);
		return $this->getResult($sql, array($this, "parseOrt"))->fetchRow();
	}
	public function setOrt($ortid, $plz, $label, $latitude, $longitude, $stateid) {
		if ($ortid == null) {
			$sql = "INSERT INTO `orte` (`plz`, `label`, `latitude`, `longitude`, `stateid`) VALUES ('" . $this->escape($plz) . "', '" . $this->escape($label) . "', " . ($latitude == null ? "NULL" : doubleval($latitude)) . ", " . ($longitude == null ? "NULL" : doubleval($longitude)) . ", " . intval($stateid) . ")";
		} else {
			$sql = "UPDATE `orte` SET `plz` = '" . $this->escape($plz) . "', `label` = '" . $this->escape($label) . "', `latitude` = " . ($latitude == null ? "NULL" : doubleval($latitude)) . ", `longitude` = " . ($longitude == null ? "NULL" : doubleval($longitude)) . ", `stateid` = '" . $this->escape($stateid) . "' WHERE `ortid` = " . intval($ortid);
		}
		$this->query($sql);
		if ($ortid == null) {
			$ortid = $this->getInsertID();
		}
		return $ortid;
	}
	public function delOrt($ortid) {
		$sql = "DELETE FROM `orte` WHERE `ortid` = " . intval($ortid);
		return $this->query($sql);
	}
	public function searchOrt($plz, $label, $stateid) {
		$sql = "SELECT `ortid`, `plz`, `label`, `latitude`, `longitude`, `stateid` FROM `orte` WHERE `plz` = '" . $this->escape($plz) . "' AND `label` = '" . $this->escape($label) . "' AND `stateid` = " . intval($stateid);
		$result = $this->getResult($sql, array($this, "parseOrt"));
		if ($result->getCount() > 0) {
			return $result->fetchRow();
		}
		$ort = new Ort($this);
		$ort->setPLZ($plz);
		$ort->setLabel($label);
		$ort->setStateID($stateid);
		$ort->save();
		return $ort;
	}

	/**
	 * State
	 **/
	public function parseState($row) {
		return $this->parseRow($row, null, "State");
	}
	public function getStateResult() {
		$sql = "SELECT `stateid`, `label`, `population`, `countryid` FROM `states`";
		return $this->getResult($sql, array($this, "parseState"));
	}
	public function getState($stateid) {
		$sql = "SELECT `stateid`, `label`, `population`, `countryid` FROM `states` WHERE `stateid` = " . intval($stateid);
		return $this->getResult($sql, array($this, "parseState"))->fetchRow();
	}
	public function setState($stateid, $label, $population, $countryid) {
		if ($stateid == null) {
			$sql = "INSERT INTO `states` (`label`, `population`, `countryid`) VALUES ('" . $this->escape($label) . "', " . ($population == null ? "NULL" : intval($population)) . ", " . intval($countryid) . ")";
		} else {
			$sql = "UPDATE `states` SET `label` = '" . $this->escape($label) . "', `population` = " . ($population == null ? "NULL" : intval($population)) . ", `countryid` = '" . $this->escape($countryid) . "' WHERE `stateid` = " . intval($stateid);
		}
		$this->query($sql);
		if ($stateid == null) {
			$stateid = $this->getInsertID();
		}
		return $stateid;
	}
	public function delState($stateid) {
		$sql = "DELETE FROM `states` WHERE `stateid` = " . intval($stateid);
		return $this->query($sql);
	}

	/**
	 * Country
	 **/
	public function parseCountry($row) {
		return $this->parseRow($row, null, "Country");
	}
	public function getCountryResult() {
		$sql = "SELECT `countryid`, `label` FROM `countries`";
		return $this->getResult($sql, array($this, "parseCountry"));
	}
	public function getCountry($countryid) {
		$sql = "SELECT `countryid`, `label` FROM `countries` WHERE `countryid` = " . intval($countryid);
		return $this->getResult($sql, array($this, "parseCountry"))->fetchRow();
	}
	public function setCountry($countryid, $label) {
		if ($countryid == null) {
			$sql = "INSERT INTO `countries` (`label`) VALUES ('" . $this->escape($label) . "')";
		} else {
			$sql = "UPDATE `countries` SET `label` = '" . $this->escape($label) . "' WHERE `countryid` = " . intval($countryid);
		}
		$this->query($sql);
		if ($countryid == null) {
			$countryid = $this->getInsertID();
		}
		return $countryid;
	}
	public function delCountry($countryid) {
		$sql = "DELETE FROM `countries` WHERE `countryid` = " . intval($countryid);
		return $this->query($sql);
	}

	/**
	 * Mitgliedschaften
	 **/
	public function parseMitgliedschaft($row) {
		return $this->parseRow($row, null, "Mitgliedschaft");
	}
	public function getMitgliedschaftResult() {
		$sql = "SELECT `mitgliedschaftid`, `label`, `description` FROM `mitgliedschaften`";
		return $this->getResult($sql, array($this, "parseMitgliedschaft"));
	}
	public function getMitgliedschaft($mitgliedschaftid) {
		$sql = "SELECT `mitgliedschaftid`, `label`, `description` FROM `mitgliedschaften` WHERE `mitgliedschaftid` = " . intval($mitgliedschaftid);
		return $this->getResult($sql, array($this, "parseMitgliedschaft"))->fetchRow();
	}
	public function setMitgliedschaft($mitgliedschaftid, $globaleid, $label, $description) {
		if ($mitgliedschaftid == null) {
			$sql = "INSERT INTO `mitgliedschaften` (`label`, `description`) VALUES ('" . $this->escape($label) . "', '" . $this->escape($description) . "')";
		} else {
			$sql = "UPDATE `mitgliedschaften` SET `label` = '" . $this->escape($label) . "', `description` = '" . $this->escape($description) . "' WHERE `mitgliedschaftid` = " . intval($mitgliedschaftid);
		}
		$this->query($sql);
		if ($mitgliedschaftid == null) {
			$mitgliedschaftid = $this->getInsertID();
		}
		return $mitgliedschaftid;
	}
	public function delMitgliedschaft($mitgliedschaftid) {
		$sql = "DELETE FROM `mitgliedschaften` WHERE `mitgliedschaftid` = " . intval($mitgliedschaftid);
		return $this->query($sql);
	}

	/**
	 * NatPerson
	 **/
	public function parseNatPerson($row) {
		return $this->parseRow($row, null, "NatPerson");
	}
	public function getNatPerson($natpersonid) {
		$sql = "SELECT `natpersonid`, `anrede`, `name`, `vorname`, `geburtsdatum`, `nationalitaet` FROM `natperson` WHERE `natpersonid` = " . intval($natpersonid);
		return $this->getResult($sql, array($this, "parseNatPerson"))->fetchRow();
	}
	public function setNatPerson($natpersonid, $anrede, $name, $vorname, $geburtsdatum, $nationalitaet) {
		if ($natpersonid == null) {
			$sql = "INSERT INTO `natperson` (`anrede`, `name`, `vorname`, `geburtsdatum`, `nationalitaet`) VALUES ('" . $this->escape($anrede) . "', '" . $this->escape($name) . "', '" . $this->escape($vorname) . "', '" . date("Y-m-d", $geburtsdatum) . "', '" . $this->escape($nationalitaet) . "')";
		} else {
			$sql = "UPDATE `natperson` SET `anrede` = '" . $this->escape($anrede) . "', `name` = '" . $this->escape($name) . "', `vorname` = '" . $this->escape($vorname) . "', `geburtsdatum` = '" . date("Y-m-d", $geburtsdatum) . "', `nationalitaet` = '" . $this->escape($nationalitaet) . "' WHERE `natpersonid` = " . intval($natpersonid);
		}
		$this->query($sql);
		if ($natpersonid == null) {
			$natpersonid = $this->getInsertID();
		}
		return $natpersonid;
	}
	public function delNatPerson($natpersonid) {
		$sql = "DELETE FROM `natperson` WHERE `natpersonid` = " . intval($natpersonid);
		return $this->query($sql);
	}
	public function searchNatPerson($anrede, $name, $vorname, $geburtsdatum, $nationalitaet) {
		$sql = "SELECT `natpersonid`, `anrede`, `name`, `vorname`, `geburtsdatum`, `nationalitaet` FROM `natperson` WHERE `anrede` = '" . $this->escape($anrede) . "' AND `name` = '" . $this->escape($name) . "' AND `vorname` = '" . $this->escape($vorname) . "' AND `geburtsdatum` = '" . date("Y-m-d", $geburtsdatum) . "' AND `nationalitaet` = '" . $this->escape($nationalitaet) . "'";
		$result = $this->getResult($sql, array($this, "parseNatPerson"));
		if ($result->getCount() > 0) {
			return $result->fetchRow();
		}
		$natperson = new NatPerson($this);
		$natperson->setAnrede($anrede);
		$natperson->setName($name);
		$natperson->setVorname($vorname);
		$natperson->setGeburtsdatum($geburtsdatum);
		$natperson->setNationalitaet($nationalitaet);
		$natperson->save();
		return $natperson;
	}

	/**
	 * JurPerson
	 **/
	public function parseJurPerson($row) {
		return $this->parseRow($row, null, "JurPerson");
	}
	public function getJurPerson($jurpersonid) {
		$sql = "SELECT `jurpersonid`, `label` FROM `jurperson` WHERE `jurpersonid` = " . intval($jurpersonid);
		return $this->getResult($sql, array($this, "parseJurPerson"))->fetchRow();
	}
	public function setJurPerson($jurpersonid, $firma) {
		if ($jurpersonid == null) {
			$sql = "INSERT INTO `jurperson` (`label`) VALUES ('" . $this->escape($firma) . "')";
		} else {
			$sql = "UPDATE `jurperson` SET `label` = '" . $this->escape($firma) . "' WHERE `jurpersonid` = " . intval($jurpersonid);
		}
		$this->query($sql);
		if ($jurpersonid == null) {
			$jurpersonid = $this->getInsertID();
		}
		return $jurpersonid;
	}
	public function delJurPerson($jurpersonid) {
		$sql = "DELETE FROM `jurperson` WHERE `jurpersonid` = " . intval($jurpersonid);
		return $this->query($sql);
	}
	public function searchJurPerson($firma) {
		$sql = "SELECT `jurpersonid`, `label` FROM `jurperson` WHERE `label` = '" . $this->escape($firma) . "'";
		$result = $this->getResult($sql, array($this, "parseJurPerson"));
		if ($result->getCount() > 0) {
			return $result->fetchRow();
		}
		$jurperson = new JurPerson($this);
		$jurperson->setLabel($firma);
		$jurperson->save();
		return $jurperson;
	}

	/**
	 * MailTemplates
	 **/
	public function parseMailTemplate($row) {
		return $this->parseRow($row, null, "MailTemplate");
	}
	public function getMailTemplateResult($gliederungid) {
		$sql = "SELECT `templateid`, `gliederungid`, `label`, `body` FROM `mailtemplates`";
		if ($gliederungid != null) {
			$sql .= " WHERE `gliederungid` " . (is_array($gliederungid) ? " IN (" . implode(",", array_map("intval", $gliederungid)) . ")" : " = " . intval($gliederungid));
		}
		return $this->getResult($sql, array($this, "parseMailTemplate"));
	}
	public function getMailTemplate($mailtemplateid) {
		$sql = "SELECT `templateid`, `gliederungid`, `label`, `body` FROM `mailtemplates` WHERE `templateid` = " . intval($mailtemplateid);
		return $this->getResult($sql, array($this, "parseMailTemplate"))->fetchRow();
	}
	public function setMailTemplate($templateid, $gliederungid, $label, $body) {
		if ($templateid == null) {
			$sql = "INSERT INTO `mailtemplates` (`gliederungid`, `label`, `body`) VALUES (" . intval($gliederungid) . ", '" . $this->escape($label) . "', '" . $this->escape($body) . "')";
		} else {
			$sql = "UPDATE `mailtemplates` SET `gliederungid` = " . intval($gliederungid) . ", `body` = '" . $this->escape($body) . "', `label` = '" . $this->escape($label) . "' WHERE `templateid` = " . intval($templateid);
		}
		$this->query($sql);
		if ($templateid == null) {
			$templateid = $this->getInsertID();
		}
		return $templateid;
	}
	public function delMailTemplate($mailtemplateid) {
		$sql = "DELETE FROM `mailtemplateheaders` WHERE `templateid` = " . intval($mailtemplateid);
		$this->query($sql);
		$sql = "DELETE FROM `mailtemplateattachments` WHERE `templateid` = " . intval($mailtemplateid);
		$this->query($sql);
		$sql = "DELETE FROM `mailtemplates` WHERE `templateid` = " . intval($mailtemplateid);
		return $this->query($sql);
	}

	public function parseMailTemplateHeader($row) {
		return $this->parseRow($row, null, "MailTemplateHeader");
	}
	public function getMailTemplateHeaderResult($mailtemplateid) {
		$sql = "SELECT `templateid`, `field`, `value` FROM `mailtemplateheaders` WHERE `templateid` = " . intval($mailtemplateid);
		return $this->getResult($sql, array($this, "parseMailTemplateHeader"));
	}
	public function setMailTemplateHeaderList($templateid, $fields, $values) {
		$sql = "DELETE FROM `mailtemplateheaders` WHERE `templateid` = " . intval($templateid);
		$this->query($sql);
		$sqlinserts = array();
		while (count($fields) > 0) {
			$sqlinserts[] = "(" . intval($templateid) . ", '" . $this->escape(array_shift($fields)) . "', '" . $this->escape(array_shift($values)) . "')";
		}
		if (count($sqlinserts) > 0) {
			$sql = "INSERT INTO `mailtemplateheaders` (`templateid`, `field`, `value`) VALUES " . implode(", ", $sqlinserts);
			$this->query($sql);
		}
	}

	public function getMailTemplateAttachmentResult($mailtemplateid) {
		$sql = "SELECT `files`.`fileid`, `files`.`filename`, `files`.`exportfilename`, `files`.`mimetype` FROM `mailtemplateattachments` LEFT JOIN `files` ON (`mailtemplateattachments`.`fileid` = `files`.`fileid`) WHERE `mailtemplateattachments`.`templateid` = " . intval($mailtemplateid);
		return $this->getResult($sql, array($this, "parseFile"));
	}
	public function setMailTemplateAttachmentList($mailtemplateid, $files) {
		$sql = "DELETE FROM `mailtemplateattachments` WHERE `templateid` = " . intval($mailtemplateid);
		$this->query($sql);
		$sqlinserts = array();
		while (count($files) > 0) {
			$sqlinserts[] = "(" . intval($mailtemplateid) . ", '" . $this->escape(array_shift($files)) . "')";
		}
		if (count($sqlinserts) > 0) {
			$sql = "INSERT INTO `mailtemplateattachments` (`templateid`, `fileid`) VALUES " . implode(", ", $sqlinserts);
			$this->query($sql);
		}
	}

	/**
	 * MailHeader
	 **/
	public function parseMailHeader($row) {
		return $this->parseRow($row, null, "MailHeader");
	}
	public function getMailHeaderResult() {
		$sql = "SELECT `headerid`, `label` FROM `mailheaders`";
		return $this->getResult($sql, array($this, "parseMailHeader"));
	}
	public function getMailHeader($headerid) {
		$sql = "SELECT `headerid`, `label` FROM `mailheaders` WHERE `headerid` = " . intval($headerid);
		return $this->getResult($sql, array($this, "parseMailHeader"))->fetchRow();
	}
	public function setMailHeader($headerid, $label) {
		if ($headerid == null) {
			$sql = "INSERT INTO `mailheaders` (`label`) VALUES ('" . $this->escape($label) . "')";
		} else {
			$sql = "UPDATE `mailheaders` SET `label` = '" . $this->escape($label) . "' WHERE `headerid` = " . intval($headerid);
		}
		$this->query($sql);
		if ($headerid == null) {
			$headerid = $this->getInsertID();
		}
		return $headerid;
	}
	public function delMailHeader($headerid) {
		$sql = "DELETE FROM `mailheaders` WHERE `headerid` = " . intval($headerid);
		return $this->query($sql);
	}

	/**
	 * Prozesse
	 **/
	public function parseProcess($row) {
		return $this->parseRow($row, null, "Process");
	}
	public function getProcessResult() {
		$sql = "SELECT `processid`, `userid`, `type`, `typedata`, `progress`, UNIX_TIMESTAMP(`queued`) as `queued`, UNIX_TIMESTAMP(`started`) as `started`, UNIX_TIMESTAMP(`finished`) as `finished`, `finishedpage` FROM `processes`";
		return $this->getResult($sql, array($this, "parseProcess"));
	}
	public function getProcess($processid) {
		$sql = "SELECT `processid`, `userid`, `type`, `typedata`, `progress`, UNIX_TIMESTAMP(`queued`) as `queued`, UNIX_TIMESTAMP(`started`) as `started`, UNIX_TIMESTAMP(`finished`) as `finished`, `finishedpage` FROM `processes` WHERE `processid` = " . intval($processid);
		return $this->getResult($sql, array($this, "parseProcess"))->fetchRow();
	}
	public function setProcess($processid, $userid, $type, $typedata, $progress, $queued, $started, $finished, $finishedpage) {
		if ($processid == null) {
			$sql = "INSERT INTO `processes`
				(`userid`, `type`, `typedata`, `progress`, `queued`, `started`, `finished`, `finishedpage`) VALUES
				(" . ($userid == null ? "NULL" : intval($userid)) . ",
				 '" . $this->escape($type) . "',
				 '" . $this->escape($typedata) . "',
				 " . doubleval($progress) . ",
				 '" . date("Y-m-d H:i:s", $queued) . "',
				 " . ($started != null ? "'" . date("Y-m-d H:i:s", $started) . "'" : "NULL") . ",
				 " . ($finished != null ? "'" . date("Y-m-d H:i:s", $finished) . "'" : "NULL") . ",
				 '" . $this->escape($finishedpage) . "')";
		} else {
			$sql = "UPDATE	`processes`
				SET	`userid` = " . ($userid == null ? "NULL" : intval($userid)) . ",
					`type` = '" . $this->escape($type) . "',
					`typedata` = '" . $this->escape($typedata) . "',
					`progress` = '" . doubleval($progress) . "',
					`queued` = '" . date("Y-m-d H:i:s", $queued) . "',
					`started` = " . ($started != null ? "'" . date("Y-m-d H:i:s", $started) . "'" : "NULL") . ",
					`finished` = " . ($finished != null ? "'" . date("Y-m-d H:i:s", $finished) . "'" : "NULL") . ",
					`finishedpage` = '" . $this->escape($finishedpage) . "'
				WHERE `processid` = " . intval($processid);
		}
		$this->query($sql);
		if ($processid == null) {
			$processid = $this->getInsertID();
		}
		return $processid;
	}
	public function delProcess($processid) {
		$sql = "DELETE FROM `processes` WHERE `processid` = " . intval($processid);
		return $this->query($sql);
	}

	/**
	 * Dokument
	 **/
	protected function parseDokumentMatcher($matcher) {
		if ($matcher instanceof TrueDokumentMatcher || $matcher == null) {
			return "1";
		}
		if ($matcher instanceof FalseDokumentMatcher) {
			return "0";
		}
		if ($matcher instanceof AndDokumentMatcher) {
			return "(" . implode(" AND ",array_map(array($this,'parseDokumentMatcher'), $matcher->getConditions())) . ")";
		}
		if ($matcher instanceof OrDokumentMatcher) {
			return "(" . implode(" OR ",array_map(array($this,'parseDokumentMatcher'), $matcher->getConditions())) . ")";
		}
		if ($matcher instanceof NotDokumentMatcher) {
			return "NOT (" . $this->parseDokumentMatcher . ")";
		}
		if ($matcher instanceof GliederungDokumentMatcher) {
			return "`d`.`gliederungid` IN (" . implode(",", array_map("intval", $matcher->getGliederungIDs())) . ")";
		}
		if ($matcher instanceof KategorieDokumentMatcher) {
			return "`d`.`dokumentkategorieid` = " . intval($matcher->getKategorieID());
		}
		if ($matcher instanceof StatusDokumentMatcher) {
			return "`d`.`dokumentstatusid` = " . intval($matcher->getStatusID());
		}
		if ($matcher instanceof MitgliedDokumentMatcher) {
			return "`d`.`dokumentid` IN (SELECT `dokumentid` FROM `mitglieddokument` WHERE `mitgliedid` = " . intval($matcher->getMitgliedID()) . ")";
		}
		if ($matcher instanceof SearchDokumentMatcher) {
			$fields = array("`d`.`identifier`", "`d`.`label`", "`d`.`content`");
			$wordclauses = array();
			$escapedwords = array();
			foreach ($matcher->getWords() as $word) {
				$escapedwords[] = $this->escape($word);
				$clauses = array();
				foreach ($fields as $field) {
					$clauses[] = $field . " LIKE '%" . $this->escape($word) . "%'";
				}
				$wordclauses[] = implode(" OR ", $clauses);
			}
			return "( (" . implode(") AND (", $wordclauses) . ") OR `dokumentid` IN (SELECT `dokumentid` FROM `dokumentnotizen` WHERE
					(`nextlabel` LIKE '%" . implode("%' AND `nextlabel` LIKE '%", $escapedwords) . "%') OR
					(`nextidentifier` LIKE '%" . implode("%' AND `nextidentifier` LIKE '%", $escapedwords) . "%') OR
					(`kommentar` LIKE '%" . implode("%' AND `kommentar` LIKE '%", $escapedwords) . "%') ))";
		}
		throw new Exception("Not implemented: ".get_class($matcher));
	}

	public function parseDokument($row) {
		return $this->parseRow($row, null, "Dokument");
	}
	public function getDokumentCount($matcher) {
		$sql = "SELECT	COUNT(`d`.`dokumentid`)
			FROM	`dokument` `d`
			WHERE	" . $this->parseDokumentMatcher($matcher);
		return reset($this->getResult($sql)->fetchRow());
	}
	public function getDokumentResult($matcher, $limit = null, $offset = null) {
		$sql = "SELECT	`d`.`dokumentid`,
				`d`.`gliederungid`,
				`d`.`dokumentkategorieid`,
				`d`.`dokumentstatusid`,
				`d`.`identifier`,
				`d`.`label`,
				`d`.`content`,
				`d`.`data`,
				`d`.`fileid`
			FROM	`dokument` `d`
			WHERE	" . $this->parseDokumentMatcher($matcher);
		if ($limit !== null or $offset !== null) {
			$sql .= " LIMIT ";
			if ($offset !== null) {
				$sql .= $offset . ",";
			}
			if ($limit !== null) {
				$sql .= $limit;
			}
		}
		return $this->getResult($sql, array($this, "parseDokument"));
	}
	public function getDokumentIdentifierMaxNumber($identifierPrefix, $identifierNumberCount) {
		$identifierPrefixEscaped = str_replace(array("/", "%", "?", "_"), array("//", "/%", "/?", "/_"), $identifierPrefix);
		$sql = "SELECT `identifier` FROM `dokument` WHERE `identifier` LIKE '" . $this->escape($identifierPrefixEscaped) . str_repeat("_", $identifierNumberCount) . "' ESCAPE '/' ORDER BY `identifier` DESC LIMIT 1";
		$rslt = $this->getResult($sql);
		if ($rslt->getCount() == 0) {
			return 0;
		}
		$row = $rslt->fetchRow();
		return intval(substr($row["identifier"], strlen($identifierPrefix)));
	}
	public function getDokumentByMitgliedResult($mitgliedid) {
		$sql = "SELECT `dokumentid`, `gliederungid`, `dokumentkategorieid`, `dokumentstatusid`, `identifier`, `label`, `content`, `data`, `fileid` FROM `dokument` LEFT JOIN `mitglieddokument` USING (`dokumentid`) WHERE `mitgliedid` = " . intval($mitgliedid);
		return $this->getResult($sql, array($this, "parseDokument"));
	}
	public function getDokument($dokumentid) {
		$sql = "SELECT `dokumentid`, `gliederungid`, `dokumentkategorieid`, `dokumentstatusid`, `identifier`, `label`, `content`, `data`, `fileid` FROM `dokument` WHERE `dokumentid` = " . intval($dokumentid);
		return $this->getResult($sql, array($this, "parseDokument"))->fetchRow();
	}
	public function setDokument($dokumentid, $gliederungid, $dokumentkategorieid, $dokumentstatusid, $identifier, $label, $content, $data, $fileid) {
		if ($dokumentid == null) {
			$sql = "INSERT INTO `dokument`
				(`gliederungid`, `dokumentkategorieid`, `dokumentstatusid`, `identifier`, `label`, `content`, `data`, `fileid`) VALUES
				(" . intval($gliederungid) . ",
				 " . intval($dokumentkategorieid) . ",
				 " . intval($dokumentstatusid) . ",
				 '" . $this->escape($identifier) . "',
				 '" . $this->escape($label) . "',
				 '" . $this->escape($content) . "',
				 '" . $this->escape($data) . "',
				 " . intval($fileid) . ")";
		} else {
			$sql = "UPDATE `dokument`
				SET	`gliederungid` = " . intval($gliederungid) . ",
					`dokumentkategorieid` = " . intval($dokumentkategorieid) . ",
					`dokumentstatusid` = " . intval($dokumentstatusid) . ",
					`identifier` = '" . $this->escape($identifier) . "',
					`label` = '" . $this->escape($label) . "',
					`content` = '" . $this->escape($content) . "',
					`data` = '" . $this->escape($data) . "',
					`fileid` = " . intval($fileid) . "
				WHERE `dokumentid` = " . intval($dokumentid);
		}
		$this->query($sql);
		if ($dokumentid == null) {
			$dokumentid = $this->getInsertID();
		}
		return $dokumentid;
	}
	public function delDokument($dokumentid) {
		$sql = "DELETE FROM `dokument` WHERE `dokumentid` = " . intval($dokumentid);
		return $this->query($sql);
	}

	/**
	 * DokumentDokumentFlag
	 **/
	public function getDokumentDokumentFlagResult($dokumentid) {
		$sql = "SELECT	`dokumentflags`.`flagid`, `dokumentflags`.`label`
			FROM	`dokumentdokumentflags`
			LEFT JOIN `dokumentflags` USING (`flagid`)
			WHERE	`dokumentdokumentflags`.`dokumentid` = " . intval($dokumentid);
		return $this->getResult($sql, array($this, "parseDokumentFlag"));
	}

	public function setDokumentDokumentFlagList($dokumentid, $flagids) {
		$sql = "DELETE FROM `dokumentdokumentflags` WHERE `dokumentid` = " . intval($dokumentid);
		$this->query($sql);
		$sqlinserts = array();
		foreach ($flagids as $flagid) {
			$sqlinserts[] = "(" . intval($dokumentid) . ", " . intval($flagid) . ")";
		}
		if (count($sqlinserts) > 0) {
			$sql = "INSERT INTO `dokumentdokumentflags` (`dokumentid`, `flagid`) VALUES " . implode(", ", $sqlinserts);
			$this->query($sql);
		}
	}

	/**
	 * DokumentNotify
	 **/
	public function parseDokumentNotify($row) {
		return $this->parseRow($row, null, "DokumentNotify");
	}
	public function getDokumentNotifyResult($gliederungid = null, $dokumentkategorieid = null, $dokumentstatusid = null) {
		$sql_gliederungid = "`gliederungid` = NULL";
		if ($gliederungid != null) {
			$sql_gliederungid .= " OR `gliederungid` = " . intval($gliederungid);
		}
		$sql_dokumentkategorieid = "`dokumentkategorieid` IS NULL";
		if ($dokumentkategorieid != null) {
			$sql_dokumentkategorieid .= " OR `dokumentkategorieid` = " . intval($dokumentkategorieid);
		}
		$sql_dokumentstatusid = "`dokumentstatusid` IS NULL";
		if ($dokumentstatusid != null) {
			$sql_dokumentstatusid .= " OR `dokumentstatusid` = " . intval($dokumentstatusid);
		}
		$sql = "SELECT `dokumentnotifyid`, `gliederungid`, `dokumentkategorieid`, `dokumentstatusid`, `emailid` FROM `dokumentnotifies` WHERE (" . $sql_gliederungid . ") AND (" . $sql_dokumentkategorieid . ") AND (" . $sql_dokumentstatusid . ")";
		return $this->getResult($sql, array($this, "parseDokumentNotify"));
	}
	public function getDokumentNotify($dokumentnotifyid) {
		$sql = "SELECT `dokumentnotifyid`, `gliederungid`, `dokumentkategorieid`, `dokumentstatusid`, `emailid` FROM `dokumentnotifies` WHERE `dokumentnotifyid` = " . intval($dokumentnotifyid);
		return $this->getResult($sql, array($this, "parseDokumentNotify"))->fetchRow();
	}
	public function setDokumentNotify($dokumentnotifyid, $gliederungid, $dokumentkategorieid, $dokumentstatusid, $emailid) {
		if ($dokumentnotifyid == null) {
			$sql = "INSERT INTO `dokumentnotifies` (`gliederungid`, `dokumentkategorieid`, `dokumentstatusid`, `emailid`) VALUES (" . ($gliederungid == null ? "NULL" : intval($gliederungid)) . ", " . ($dokumentkategorieid == null ? "NULL" : intval($dokumentkategorieid)) . ", " . ($dokumentstatusid == null ? "NULL" : intval($dokumentstatusid)) . ", " . ($emailid == null ? "NULL" : intval($emailid)) . ")";
		} else {
			$sql = "UPDATE `dokumentnotifies` SET `gliederungid` = " . ($gliederungid == null ? "NULL" : intval($gliederungid)) . ", `dokumentkategorieid` = " . ($dokumentkategorieid == null ? "NULL" : intval($dokumentkategorieid)) . ", `dokumentstatusid` = " . ($dokumentstatusid == null ? "NULL" : intval($dokumentstatusid)) . ", `emailid` = " . ($emailid == null ? "NULL" : intval($emailid)) . " WHERE `dokumentnotifyid` = " . intval($dokumentnotifyid);
		}
		$this->query($sql);
		if ($dokumentnotifyid == null) {
			$dokumentnotifyid = $this->getInsertID();
		}
		return $dokumentnotifyid;
	}

	/**
	 * DokumentKategorie
	 **/
	public function parseDokumentKategorie($row) {
		return $this->parseRow($row, null, "DokumentKategorie");
	}
	public function getDokumentKategorieResult() {
		$sql = "SELECT `dokumentkategorieid`, `label` FROM `dokumentkategorien`";
		return $this->getResult($sql, array($this, "parseDokumentKategorie"));
	}
	public function getDokumentKategorie($dokumentkategorieid) {
		$sql = "SELECT `dokumentkategorieid`, `label` FROM `dokumentkategorien` WHERE `dokumentkategorieid` = " . intval($dokumentkategorieid);
		return $this->getResult($sql, array($this, "parseDokumentKategorie"))->fetchRow();
	}
	public function setDokumentKategorie($dokumentkategorieid, $label) {
		if ($dokumentkategorieid == null) {
			$sql = "INSERT INTO `dokumentkategorien`
				(`label`) VALUES
				('" . $this->escape($label) . "')";
		} else {
			$sql = "UPDATE	`dokumentkategorien`
				SET	`label` = '" . $this->escape($label) . "'
				WHERE `dokumentkategorieid` = " . intval($dokumentkategorieid);
		}
		$this->query($sql);
		if ($dokumentkategorieid == null) {
			$dokumentkategorieid = $this->getInsertID();
		}
		return $dokumentkategorieid;
	}
	public function delDokumentKategorie($dokumentkategorieid) {
		$sql = "DELETE FROM `dokumentkategorie` WHERE `dokumentkategorieid` = " . intval($dokumentkategorieid);
		return $this->query($sql);
	}

	/**
	 * DokumentStatus
	 **/
	public function parseDokumentStatus($row) {
		return $this->parseRow($row, null, "DokumentStatus");
	}
	public function getDokumentStatusResult() {
		$sql = "SELECT `dokumentstatusid`, `label` FROM `dokumentstatus`";
		return $this->getResult($sql, array($this, "parseDokumentStatus"));
	}
	public function getDokumentStatus($dokumentstatusid) {
		$sql = "SELECT `dokumentstatusid`, `label` FROM `dokumentstatus` WHERE `dokumentstatusid` = " . intval($dokumentstatusid);
		return $this->getResult($sql, array($this, "parseDokumentStatus"))->fetchRow();
	}
	public function setDokumentStatus($dokumentstatusid, $label) {
		if ($dokumentstatusid == null) {
			$sql = "INSERT INTO `dokumentstatus`
				(`label`) VALUES
				('" . $this->escape($label) . "')";
		} else {
			$sql = "UPDATE	`dokumentstatus`
				SET	`label` = '" . $this->escape($label) . "'
				WHERE `dokumentstatusid` = " . intval($dokumentstatusid);
		}
		$this->query($sql);
		if ($dokumentstatusid == null) {
			$dokumentstatusid = $this->getInsertID();
		}
		return $dokumentstatusid;
	}
	public function delDokumentStatus($dokumentstatusid) {
		$sql = "DELETE FROM `dokumentstatus` WHERE `dokumentstatusid` = " . intval($dokumentstatusid);
		return $this->query($sql);
	}

	/**
	 * DokumentFlag
	 **/
	public function parseDokumentFlag($row) {
		return $this->parseRow($row, null, "DokumentFlag");
	}
	public function getDokumentFlagResult() {
		$sql = "SELECT `flagid`, `label` FROM `dokumentflags`";
		return $this->getResult($sql, array($this, "parseDokumentFlag"));
	}
	public function getDokumentFlag($flagid) {
		$sql = "SELECT `flagid`, `label` FROM `dokumentflags` WHERE `flagid` = " . intval($flagid);
		return $this->getResult($sql, array($this, "parseDokumentFlag"))->fetchRow();
	}
	public function setDokumentFlag($flagid, $label) {
		if ($flagid == null) {
			$sql = "INSERT INTO `dokumentflags` (`label`) VALUES ('" . $this->escape($label) . "')";
		} else {
			$sql = "UPDATE `dokumentflags` SET `label` = '" . $this->escape($label) . "' WHERE `flagid` = " . intval($flagid);
		}
		$this->query($sql);
		if ($flagid == null) {
			$flagid = $this->getInsertID();
		}
		return $flagid;
	}
	public function delDokumentFlag($flagid) {
		$sql = "DELETE FROM `dokumentflags` WHERE `flagid` = " . intval($flagid);
		return $this->query($sql);
	}

	/**
	 * DokumentNotiz
	 **/
	public function parseDokumentNotiz($row) {
		return $this->parseRow($row, null, "DokumentNotiz");
	}
	public function getDokumentNotizResultTimeline($gliederungids, $start, $count) {
		$sql = "SELECT `dokumentnotizid`, `dokumentid`, `author`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `nextState`, `nextKategorie`, `nextLabel`, `nextIdentifier`, `kommentar` FROM `dokumentnotizen` ORDER BY `timestamp` DESC LIMIT " . intval($start) . "," . intval($count);
		return $this->getResult($sql, array($this, "parseDokumentNotiz"));
	}
	public function getDokumentNotizResult($dokumentid = null) {
		$sql = "SELECT `dokumentnotizid`, `dokumentid`, `author`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `nextState`, `nextKategorie`, `nextLabel`, `nextIdentifier`, `kommentar` FROM `dokumentnotizen` WHERE 1=1";
		if ($dokumentid != null) {
			$sql .= " AND `dokumentid` = " . intval($dokumentid);
		}
		$sql .= " ORDER BY `timestamp` ASC";
		return $this->getResult($sql, array($this, "parseDokumentNotiz"));
	}
	public function getDokumentNotiz($dokumentnotizid) {
		$sql = "SELECT `dokumentnotizid`, `dokumentid`, `author`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `nextState`, `nextKategorie`, `nextLabel`, `nextIdentifier`, `kommentar` FROM `dokumentnotizen` WHERE `dokumentnotizid` = " . intval($dokumentnotizid);
		return $this->getResult($sql, array($this, "parseDokumentNotiz"))->fetchRow();
	}
	public function setDokumentNotiz($dokumentnotizid, $dokumentid, $author, $timestamp, $nextKategorie, $nextState, $nextLabel, $nextIdentifier, $kommentar) {
		if ($dokumentnotizid == null) {
			$sql = "INSERT INTO `dokumentnotizen`
				(`dokumentid`, `author`, `timestamp`, `nextState`, `nextKategorie`, `nextLabel`, `nextIdentifier`, `kommentar`) VALUES
				(" . intval($dokumentid) . ",
				 " . intval($author) . ",
				 '" . date("Y-m-d H:i:s", $timestamp) . "',
				 " . ($nextState == null ? "NULL" : intval($nextState)) . ",
				 " . ($nextKategorie == null ? "NULL" : intval($nextKategorie)) . ",
				 " . ($nextLabel == null ? "NULL" : "'".$this->escape($nextLabel)."'") . ",
				 " . ($nextIdentifier == null ? "NULL" : "'".$this->escape($nextIdentifier)."'") . ",
				 '" . $this->escape($kommentar) . "')";
		} else {
			$sql = "UPDATE	`dokumentnotizen`
				SET	`dokumentid` = " . intval($dokumentid) . ",
					`author` = " . intval($author) . ",
					`timestamp` = '" . date("Y-m-d H:i:s", $timestamp) . "',
					`nextState` = " . ($nextState == null ? "NULL" : intval($nextState)) . ",
					`nextKategorie` = " . ($nextKategorie == null ? "NULL" : intval($nextKategorie)) . ",
					`nextLabel` = " . ($nextLabel == null ? "NULL" : "'".$this->escape($nextLabel)."'") . ",
					`nextIdentifier` = " . ($nextIdentifier == null ? "NULL" : "'".$this->escape($nextIdentifier)."'") . ",
					`kommentar` = '" . $this->escape($kommentar) . "'
				WHERE `dokumentnotizid` = " . intval($dokumentnotizid);
		}
		$this->query($sql);
		if ($dokumentnotizid == null) {
			$dokumentnotizid = $this->getInsertID();
		}
		return $dokumentnotizid;
	}
	public function delDokumentNotiz($dokumentnotizid) {
		$sql = "DELETE FROM `dokumentnotizen` WHERE `dokumentnotizid` = " . intval($dokumentnotizid);
		return $this->query($sql);
	}

	/**
	 * DokumentNotizFlags
	 **/
	public function getDokumentNotizFlagResultAdd($notizid) {
		$sql = "SELECT	`dokumentflags`.`flagid`, `dokumentflags`.`label`
			FROM	`dokumentnotizflags`
			LEFT JOIN	`dokumentflags` ON (`dokumentnotizflags`.`flagid` = `dokumentflags`.`flagid`)
			WHERE	`dokumentnotizflags`.`notizid` = " . intval($notizid) . " AND `dokumentnotizflags`.`change` = 'add'";
		return $this->getResult($sql, array($this, "parseDokumentFlag"));
	}
	public function setDokumentNotizFlagListAdd($notizid, $flags) {
		$sql = "DELETE FROM `dokumentnotizflags` WHERE `notizid` = " . intval($notizid) . " AND `change` = 'add'";
		$this->query($sql);
		$sqlinserts = array();
		while (count($flags) > 0) {
			$sqlinserts[] = "(" . intval($notizid) . ", " . intval(array_shift($flags)) . ", 'add')";
		}
		if (count($sqlinserts) > 0) {
			$sql = "INSERT INTO `dokumentnotizflags` (`notizid`, `flagid`, `change`) VALUES " . implode(", ", $sqlinserts);
			$this->query($sql);
		}
	}
	public function getDokumentNotizFlagResultDel($notizid) {
		$sql = "SELECT	`dokumentflags`.`flagid`, `dokumentflags`.`label`
			FROM	`dokumentnotizflags`
			LEFT JOIN	`dokumentflags` ON (`dokumentnotizflags`.`flagid` = `dokumentflags`.`flagid`)
			WHERE	`dokumentnotizflags`.`notizid` = " . intval($notizid) . " AND `dokumentnotizflags`.`change` = 'del'";
		return $this->getResult($sql, array($this, "parseDokumentFlag"));
	}
	public function setDokumentNotizFlagListDel($notizid, $flags) {
		$sql = "DELETE FROM `dokumentnotizflags` WHERE `notizid` = " . intval($notizid) . " AND `change` = 'del'";
		$this->query($sql);
		$sqlinserts = array();
		while (count($flags) > 0) {
			$sqlinserts[] = "(" . intval($notizid) . ", " . intval(array_shift($flags)) . ", 'del')";
		}
		if (count($sqlinserts) > 0) {
			$sql = "INSERT INTO `dokumentnotizflags` (`notizid`, `flagid`, `change`) VALUES " . implode(", ", $sqlinserts);
			$this->query($sql);
		}
	}

	/**
	 * File
	 **/
	public function parseFile($row) {
		return $this->parseRow($row, null, "File");
	}
	public function getFileResult() {
		$sql = "SELECT `fileid`, `filename`, `exportfilename`, `mimetype` FROM `files`";
		return $this->getResult($sql, array($this, "parseFile"));
	}
	public function getFile($fileid) {
		$sql = "SELECT `fileid`, `filename`, `exportfilename`, `mimetype` FROM `files` WHERE `fileid` = " . intval($fileid);
		return $this->getResult($sql, array($this, "parseFile"))->fetchRow();
	}
	public function setFile($fileid, $filename, $exportfilename, $mimetype) {
		if ($fileid == null) {
			$sql = "INSERT INTO `files`
				(`filename`, `exportfilename`, `mimetype`) VALUES
				('" . $this->escape($filename) . "',
				 '" . $this->escape($exportfilename) . "',
				 '" . $this->escape($mimetype) . "')";
		} else {
			$sql = "UPDATE	`files`
				SET	`filename` = '" . $this->escape($filename) . "',
					`exportfilename` = '" . $this->escape($exportfilename) . "',
					`mimetype` = '" . $this->escape($mimetype) . "'
				WHERE `fileid` = " . intval($fileid);
		}
		$this->query($sql);
		if ($fileid == null) {
			$fileid = $this->getInsertID();
		}
		return $fileid;
	}
	public function delFile($fileid) {
		$sql = "DELETE FROM `files` WHERE `fileid` = " . intval($fileid);
		return $this->query($sql);
	}

	/**
	 * TempFile
	 **/
	public function parseTempFile($row) {
		return $this->parseRow($row, null, "TempFile");
	}
	public function getTempFileResult() {
		$sql = "SELECT `tempfileid`, `userid`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `fileid` FROM `tempfiles`";
		return $this->getResult($sql, array($this, "parseTempFile"));
	}
	public function getTempFile($tempfileid) {
		$sql = "SELECT `tempfileid`, `userid`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `fileid` FROM `tempfiles` WHERE `tempfileid` = " . intval($tempfileid);
		return $this->getResult($sql, array($this, "parseTempFile"))->fetchRow();
	}
	public function setTempFile($tempfileid, $userid, $timestamp, $fileid) {
		if ($tempfileid == null) {
			$sql = "INSERT INTO `tempfiles`
				(`userid`, `timestamp`, `fileid`) VALUES
				(" . intval($userid) . ",
				 '" . date("Y-m-d H:i:s", $timestamp) . "',
				 " . intval($fileid) . ")";
		} else {
			$sql = "UPDATE	`tempfiles`
				SET	`userid` = " . intval($userid) . ",
					`timestamp` = '" . date("Y-m-d H:i:s", $timestamp) . "',
					`fileid` = " . intval($fileid) . "
				WHERE `tempfileid` = " . intval($tempfileid);
		}
		$this->query($sql);
		if ($tempfileid == null) {
			$tempfileid = $this->getInsertID();
		}
		return $tempfileid;
	}
	public function delTempFile($tempfileid) {
		$sql = "DELETE FROM `tempfiles` WHERE `tempfileid` = " . intval($tempfileid);
		return $this->query($sql);
	}
}

class SQLStorageResult extends AbstractStorageResult {
	private $stor;
	private $rslt;
	private $callback;

	public function __construct(Storage $stor, $rslt, $callback = null) {
		$this->stor = $stor;
		$this->rslt = $rslt;
		$this->callback = $callback;
	}

	public function parseRow($row) {
		if ($row == null) {
			return null;
		}
		if ($this->callback != null) {
			$row = call_user_func($this->callback, $row);
		}
		return $row;
	}

	public function fetchRaw() {
		return $this->stor->fetchRow($this->rslt);
	}

	public function fetchRow() {
		return $this->parseRow($this->fetchRaw());
	}

	public function fetchAll($keyfield = null) {
		$rows = array();
		while ($row = $this->fetchRaw()) {
			if ($keyfield != null) {
				$rows[$row[$keyfield]] = $this->parseRow($row);
			} else {
				$rows[] = $this->parseRow($row);
			}
		}
		return $rows;
	}

	public function getCount() {
		return $this->stor->numRows($this->rslt);
	}
}

?>
