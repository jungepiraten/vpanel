<?php

require_once(VPANEL_CORE . "/storage.class.php");
require_once(VPANEL_CORE . "/user.class.php");
require_once(VPANEL_CORE . "/role.class.php");
require_once(VPANEL_CORE . "/permission.class.php");
require_once(VPANEL_CORE . "/mitglied.class.php");
require_once(VPANEL_CORE . "/mitgliedrevision.class.php");
require_once(VPANEL_CORE . "/mitgliedschaft.class.php");
require_once(VPANEL_CORE . "/natperson.class.php");
require_once(VPANEL_CORE . "/jurperson.class.php");
require_once(VPANEL_CORE . "/mailtemplate.class.php");
require_once(VPANEL_CORE . "/mailattachment.class.php");
require_once(VPANEL_CORE . "/mailtemplateheader.class.php");
require_once(VPANEL_CORE . "/process.class.php");
require_once(VPANEL_CORE . "/tempfile.class.php");

abstract class SQLStorage extends Storage {
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
	 * Berechtigungen
	 */
	public function parsePermission($row) {
		return $this->parseRow($row, null, "Permission");
	}
	public function getPermissionResult() {
		$sql = "SELECT `permissionid`, `label`, `description` FROM `permissions`";
		return $this->getResult($sql, array($this, "parsePermission"));
	}
	public function getPermission($permissionid) {
		$sql = "SELECT `permissionid`, `label`, `description` FROM `permissions` WHERE `permissionid` = " . intval($permissionid);
		return $this->getResult($sql, array($this, "parsePermission"))->fetchRow();
	}

	/**
	 * Benutzer
	 */
	public function parseUser($row) {
		return $this->parseRow($row, null, "User");
	}
	public function getUserResult() {
		$sql = "SELECT `userid`, `username`, `password`, `passwordsalt` FROM `users`";
		return $this->getResult($sql, array($this, "parseUser"));
	}
	public function getUser($userid) {
		$sql = "SELECT `userid`, `username`, `password`, `passwordsalt` FROM `users` WHERE `userid` = " . intval($userid);
		return $this->getResult($sql, array($this, "parseUser"))->fetchRow();
	}
	public function getUserByUsername($username) {
		$sql = "SELECT `userid`, `username`, `password`, `passwordsalt` FROM `users` WHERE `username` = '" . $this->escape($username) . "'";
		return $this->getResult($sql, array($this, "parseUser"))->fetchRow();
	}
	public function setUser($userid, $username, $password, $passwordsalt) {
		if ($userid == null) {
			$sql = "INSERT INTO `users` (`username`, `password`, `passwordsalt`) VALUES ('" . $this->escape($username) . "', '" . $this->escape($password) . "', '" . $this->escape($passwordsalt) . "')";
		} else {
			$sql = "UPDATE `users` SET `username` = '" . $this->escape($username) . "', `password` = '" . $this->escape($password) . "', `passwordsalt` = '" . $this->escape($passwordsalt) . "' WHERE `userid` = " . intval($userid);
		}
		$this->query($sql);
		if ($userid == null) {
			$userid = $this->getInsertID();
		}
		return $userid;
	}
	public function delUser($userid) {
		$sql = "DELETE FROM `userroles` WHERE `userid` = " . intval($userid);
		$this->query($sql);
		$sql = "DELETE FROM `users` WHERE `userid` = " . intval($userid);
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

	public function getRolePermissionResult($roleid) {
		$sql = "SELECT `permissions`.`permissionid` AS 'permissionid', `permissions`.`label`, `permissions`.`description` AS 'description' FROM `rolepermissions` LEFT JOIN `permissions` USING (`permissionid`) WHERE `rolepermissions`.`roleid` = '" . $this->escape($roleid) . "'";
		return $this->getResult($sql, array($this, "parsePermission"));
	}
	public function setRolePermissionList($roleid, $permissionids) {
		$sql = "DELETE FROM `rolepermissions` WHERE `roleid` = " . intval($roleid);
		$this->query($sql);
		if (count($permissionids) > 0) {
			$permissionssql = array();
			foreach ($permissionids as $permissionid) {
				$permissionssql[] = "(" . intval($roleid) . ", " . intval($permissionid) . ")";
			}
			$sql = "INSERT INTO `rolepermissions` (`roleid`, `permissionid`) VALUES " . implode(",", $permissionssql);
			$this->query($sql);
		}
		return true;
	}

	public function getRoleUserResult($roleid) {
		$sql = "SELECT `userid`, `username`, `password`, `passwordsalt` FROM `users` LEFT JOIN `userroles` USING (`userid`) WHERE `roleid` = " . intval($roleid);
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
	 * Gliederung
	 **/
	public function parseGliederung($row) {
		return $this->parseRow($row, null, "Gliederung");
	}
	public function getGliederungResult() {
		$sql = "SELECT `gliederungsid`, `label` FROM `gliederungen`";
		return $this->getResult($sql, array($this, "parseGliederung"));
	}
	public function getGliederung($gliederungid) {
		$sql = "SELECT `gliederungsid`, `label` FROM `gliederungen` WHERE `gliederungsid` = " . intval($gliederungid);
		return reset($this->fetchAsArray($this->query($sql), "gliederungsid", null, 'Gliederung'));
	}

	/**
	 * Mitglieder
	 **/
	protected function parseMitgliederMatcher($matcher) {
		if ($matcher instanceof TrueMitgliederMatcher) {
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
		if ($matcher instanceof MitgliedschaftMitgliederMatcher) {
			return "`r`.`mitgliedschaftid` = " . intval($matcher->getMitgliedschaftID());
		}
		if ($matcher instanceof StateMitgliederMatcher) {
			return "`o`.`stateid` = " . intval($matcher->getStateID());
		}
		if ($matcher instanceof NatPersonMitgliederMatcher) {
			return "`r`.`natpersonid` IS NOT NULL";
		}
		if ($matcher instanceof JurPersonMitgliederMatcher) {
			return "`r`.`jurpersonid` IS NOT NULL";
		}
		if ($matcher instanceof AusgetretenMitgliederMatcher) {
			return "`m`.`austritt` IS NOT NULL";
		}
		throw new Exception("Not implemented: ".get_class($matcher));
	}
	public function getMitgliederCount($matcher = null) {
		if ($matcher instanceof MitgliederFilter) {
			$matcher = $matcher->getMatcher();
		}
		$sql = "SELECT	COUNT(`r`.`revisionid`) as `count`
			FROM	`mitgliederrevisions` `r`
			LEFT JOIN `mitglieder` `m` ON (`m`.`mitgliedid` = `r`.`mitgliedid`)
			LEFT JOIN `natperson` `n` ON (`n`.`natpersonid` = `r`.`natpersonid`)
			LEFT JOIN `jurperson` `j` ON (`j`.`jurpersonid` = `r`.`jurpersonid`)
			LEFT JOIN `kontakte` `k` ON (`k`.`kontaktid` = `r`.`kontaktid`)
			LEFT JOIN `orte` `o` ON (`o`.`ortid` = `k`.`ortid`)
			WHERE	`r`.`timestamp` = (
				SELECT	MAX(`rmax`.`timestamp`)
				FROM	`mitgliederrevisions` `rmax`
				WHERE	`r`.`mitgliedid` = `rmax`.`mitgliedid`)
				".($matcher != null ? "AND ".$this->parseMitgliederMatcher($matcher) : "");
		return reset($this->getResult($sql)->fetchRow());
	}
	public function parseMitglied($row) {
		$o = $this->parseRow($row, null, array("r" => 'MitgliedRevision', "n" => 'NatPerson', "j" => 'JurPerson', "k" => 'Kontakt', "o" => 'Ort', "m" => 'Mitglied'));
		$o["k"]->setOrt($o["o"]);
		if ($o["r"]->getNatPersonID() !== null) {
			$o["r"]->setNatPerson($o["n"]);
		}
		if ($o["r"]->getJurPersonID() !== null) {
			$o["r"]->setJurPerson($o["j"]);
		}
		$o["r"]->setKontakt($o["k"]);
		$o["m"]->addRevision($o["r"]);
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
				`r`.`mitglied_piraten` AS `r_mitglied_piraten`,
				`r`.`verteiler_eingetragen` AS `r_verteiler_eingetragen`,
				`r`.`beitrag` AS `r_beitrag`,
				`r`.`natpersonid` AS `r_natpersonid`,
				`r`.`jurpersonid` AS `r_jurpersonid`,
				`r`.`kontaktid` AS `r_kontaktid`,
				`n`.`natpersonid` AS `n_natpersonid`,
				`n`.`name` AS `n_name`,
				`n`.`vorname` AS `n_vorname`,
				UNIX_TIMESTAMP(`n`.`geburtsdatum`) AS `n_geburtsdatum`,
				`n`.`nationalitaet` AS `n_nationalitaet`,
				`j`.`jurpersonid` AS `j_jurpersonid`,
				`j`.`label` AS `j_label`,
				`k`.`kontaktid` AS `k_kontaktid`,
				`k`.`strasse` AS `k_strasse`,
				`k`.`hausnummer` AS `k_hausnummer`,
				`k`.`ortid` AS `k_ortid`,
				`k`.`telefonnummer` AS `k_telefonnummer`,
				`k`.`handynummer` AS `k_handynummer`,
				`k`.`email` AS `k_email`,
				`o`.`ortid` AS `o_ortid`,
				`o`.`plz` AS `o_plz`,
				`o`.`label` AS `o_label`,
				`o`.`stateid` AS `o_stateid`
			FROM	`mitglieder` `m`
			LEFT JOIN `mitgliederrevisions` `r` USING (`mitgliedid`)
			LEFT JOIN `mitgliederrevisions` `rmax` USING (`mitgliedid`)
			LEFT JOIN `natperson` `n` ON (`n`.`natpersonid` = `r`.`natpersonid`)
			LEFT JOIN `jurperson` `j` ON (`j`.`jurpersonid` = `r`.`jurpersonid`)
			LEFT JOIN `kontakte` `k` ON (`k`.`kontaktid` = `r`.`kontaktid`)
			LEFT JOIN `orte` `o` ON (`o`.`ortid` = `k`.`ortid`)
			".($matcher != null ? "WHERE ".$this->parseMitgliederMatcher($matcher) : "")."
			GROUP BY `m`.`mitgliedid`, `r`.`timestamp`
			HAVING	`r`.`timestamp` = MAX(`rmax`.`timestamp`)
			ORDER BY `r`.`timestamp`";
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
	public function getMitglied($mitgliedid) {
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
				`r`.`mitglied_piraten` AS `r_mitglied_piraten`,
				`r`.`verteiler_eingetragen` AS `r_verteiler_eingetragen`,
				`r`.`beitrag` AS `r_beitrag`,
				`r`.`natpersonid` AS `r_natpersonid`,
				`r`.`jurpersonid` AS `r_jurpersonid`,
				`r`.`kontaktid` AS `r_kontaktid`,
				`n`.`natpersonid` AS `n_natpersonid`,
				`n`.`name` AS `n_name`,
				`n`.`vorname` AS `n_vorname`,
				UNIX_TIMESTAMP(`n`.`geburtsdatum`) AS `n_geburtsdatum`,
				`n`.`nationalitaet` AS `n_nationalitaet`,
				`j`.`jurpersonid` AS `j_jurpersonid`,
				`j`.`label` AS `j_label`,
				`k`.`kontaktid` AS `k_kontaktid`,
				`k`.`strasse` AS `k_strasse`,
				`k`.`hausnummer` AS `k_hausnummer`,
				`k`.`ortid` AS `k_ortid`,
				`k`.`telefonnummer` AS `k_telefonnummer`,
				`k`.`handynummer` AS `k_handynummer`,
				`k`.`email` AS `k_email`,
				`o`.`ortid` AS `o_ortid`,
				`o`.`plz` AS `o_plz`,
				`o`.`label` AS `o_label`,
				`o`.`stateid` AS `o_stateid`
			FROM	`mitglieder` `m`
			LEFT JOIN `mitgliederrevisions` `r` USING (`mitgliedid`)
			LEFT JOIN `mitgliederrevisions` `rmax` USING (`mitgliedid`)
			LEFT JOIN `natperson` `n` ON (`n`.`natpersonid` = `r`.`natpersonid`)
			LEFT JOIN `jurperson` `j` ON (`j`.`jurpersonid` = `r`.`jurpersonid`)
			LEFT JOIN `kontakte` `k` ON (`k`.`kontaktid` = `r`.`kontaktid`)
			LEFT JOIN `orte` `o` ON (`o`.`ortid` = `k`.`ortid`)
			WHERE	`r`.`mitgliedid` = " . intval($mitgliedid) . "
			GROUP BY `m`.`mitgliedid`, `r`.`timestamp`
			HAVING	`r`.`timestamp` = MAX(`rmax`.`timestamp`)";
		return $this->getResult($sql, array($this, "parseMitglied"))->fetchRow();
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
	 * MitgliederRevisions
	 **/
	public function parseMitgliederRevision($row) {
		$os = $this->parseRow($row, array("r" => 'MitgliederRevision', "n" => 'NatPerson', "j" => 'JurPerson', "k" => 'Kontakt', "u" => 'User'));
		$objs = array();
		foreach ($os as $k => &$o) {
			$o["k"]->setOrt($o["o"]);
			$o["r"]->setNatPerson($o["n"]);
			$o["r"]->setJurPerson($o["j"]);
			$o["r"]->setKontakt($o["k"]);
			$objs[$k] = $o["r"];
		}
		return $objs;
	}
	public function getMitgliederRevisionResult($mitgliedid = null) {
		$sql = "SELECT	`r`.`revisionid` AS `r_revisionid`,
				`r`.`globaleid` AS `r_globaleid`,
				UNIX_TIMESTAMP(`r`.`timestamp`) AS `r_timestamp`,
				`r`.`userid` AS `r_userid`,
				`r`.`mitgliedid` AS `r_mitgliedid`,
				`r`.`mitgliedschaftid` AS `r_mitgliedschaftid`,
				`r`.`gliederungsid` AS `r_gliederungsid`,
				`r`.`geloescht` AS `r_geloescht`,
				`r`.`mitglied_piraten` AS `r_mitglied_piraten`,
				`r`.`verteiler_eingetragen` AS `r_verteiler_eingetragen`,
				`r`.`beitrag` AS `r_beitrag`,
				`r`.`natpersonid` AS `r_natpersonid`,
				`r`.`jurpersonid` AS `r_jurpersonid`,
				`r`.`kontaktid` AS `r_kontaktid`,
				`n`.`natpersonid` AS `n_natpersonid`,
				`n`.`name` AS `n_name`,
				`n`.`vorname` AS `n_vorname`,
				UNIX_TIMESTAMP(`n`.`geburtsdatum`) AS `n_geburtsdatum`,
				`n`.`nationalitaet` AS `n_nationalitaet`,
				`j`.`jurpersonid` AS `j_jurpersonid`,
				`j`.`firma` AS `j_firma`,
				`k`.`kontaktid` AS `k_kontaktid`,
				`k`.`strasse` AS `k_strasse`,
				`k`.`hausnummer` AS `k_hausnummer`,
				`k`.`ortid` AS `k_ortid`,
				`k`.`telefon` AS `k_telefon`,
				`k`.`handy` AS `k_handy`,
				`k`.`email` AS `k_email`,
				`o`.`ortid` AS `o_ortid`,
				`o`.`plz` AS `o_plz`,
				`o`.`label` AS `o_label`,
				`o`.`stateid` AS `o_stateid`
			FROM	`mitgliederrevisions` `r`
			LEFT JOIN `natperson` `n` USING (`natpersonid`)
			LEFT JOIN `jurperson` `j` USING (`jurpersonid`)
			LEFT JOIN `kontakte` `k` USING (`kontaktid`)
			LEFT JOIN `kontakte` `o` USING (`ortid`)";
		if ($mitgliedid != null) {
			$sql .= " WHERE `m`.`mitgliedid` = " . intval($mitgliedid);
		}
		$sql .= " ORDER BY `r`.`timestamp`";
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
				`r`.`mitglied_piraten` AS `r_mitglied_piraten`,
				`r`.`verteiler_eingetragen` AS `r_verteiler_eingetragen`,
				`r`.`beitrag` AS `r_beitrag`,
				`r`.`natpersonid` AS `r_natpersonid`,
				`r`.`jurpersonid` AS `r_jurpersonid`,
				`r`.`kontaktid` AS `r_kontaktid`,
				`n`.`natpersonid` AS `n_natpersonid`,
				`n`.`name` AS `n_name`,
				`n`.`vorname` AS `n_vorname`,
				UNIX_TIMESTAMP(`n`.`geburtsdatum`) AS `n_geburtsdatum`,
				`n`.`nationalitaet` AS `n_nationalitaet`,
				`j`.`jurpersonid` AS `j_jurpersonid`,
				`j`.`firma` AS `j_firma`,
				`k`.`kontaktid` AS `k_kontaktid`,
				`k`.`strasse` AS `k_strasse`,
				`k`.`hausnummer` AS `k_hausnummer`,
				`k`.`ortid` AS `k_ortid`,
				`k`.`telefon` AS `k_telefon`,
				`k`.`handy` AS `k_handy`,
				`k`.`email` AS `k_email`,
				`o`.`ortid` AS `o_ortid`,
				`o`.`plz` AS `o_plz`,
				`o`.`label` AS `o_label`,
				`o`.`stateid` AS `o_stateid`
			FROM	`mitgliederrevisions` `r`
			LEFT JOIN `natperson` `n` USING (`natpersonid`)
			LEFT JOIN `jurperson` `j` USING (`jurpersonid`)
			LEFT JOIN `kontakte` `k` USING (`kontaktid`)
			LEFT JOIN `kontakte` `o` USING (`ortid`)
			WHERE	`r`.`revisionid` = " . intval($revisionid);
		return $this->getResult($sql, array($this, "parseMitgliederRevision"));
	}
	public function setMitgliederRevision($revisionid, $globalid, $timestamp, $userid, $mitgliedid, $mitgliedschaftid, $gliederungid, $geloescht, $mitgliedpiraten, $verteilereingetragen, $beitrag, $natpersonid, $jurpersonid, $kontaktid) {
		if ($revisionid == null) {
			$sql = "INSERT INTO `mitgliederrevisions` (`globaleid`, `timestamp`, `userid`, `mitgliedid`, `mitgliedschaftid`, `gliederungsid`, `geloescht`, `mitglied_piraten`, `verteiler_eingetragen`, `beitrag`, `natpersonid`, `jurpersonid`, `kontaktid`) VALUES ('" . $this->escape($globalid) . "', '" . date("Y-m-d H:i:s", $timestamp) . "', " . intval($userid) . ", " . intval($mitgliedid) . ", " . intval($mitgliedschaftid) . ", " . intval($gliederungid) . ", " . ($geloescht ? 1 : 0) . ", " . ($mitgliedpiraten ? 1 : 0) . ", " . ($verteilereingetragen ? 1 : 0) . ", " . doubleval($beitrag) . ", " . ($natpersonid == null ? "NULL" : intval($natpersonid)) . ", " . ($jurpersonid == null ? "NULL" : intval($jurpersonid)) . ", " . intval($kontaktid) . ")";
		} else {
			$sql = "UPDATE `mitgliederrevisions` SET `globaleid` = '" . $this->escape($globalid) . "', `timestamp` = '" . date("Y-m-d H:i:s", $timestamp) . "', `userid` = " . intval($userid) . ", `mitgliedid` = " . intval($mitgliedid) . ", `mitgliedschaftid` = " . intval($mitgliedschaftid) . ", `gliederungsid` = " . intval($gliederungid) . ", `geloescht` = " . ($geloescht ? 1 : 0) . ", `mitglied_piraten` = " . ($mitgliedpiraten ? 1 : 0) . ", `verteiler_eingetragen` = " . ($verteilereingetragen ? 1 : 0) . ", " . doubleval($beitrag) . ", `natpersonid` = " . ($natpersonid == null ? "NULL" : intval($natpersonid)) . ",`jurpersonid` = " . ($jurpersonid == null ? "NULL" : intval($jurpersonid)) . ", `kontaktid` = " . intval($kontaktid) . " WHERE `revisionid` = " . intval($revisionid);
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
	public function getKontakt($kontaktid) {
		$sql = "SELECT `kontaktid`, `strasse`, `hausnummer`, `ortid`, `telefonnummer`, `handynummer`, `email` FROM `kontakt` WHERE `kontaktid` = " . intval($kontaktid);
		return reset($this->fetchAsArray($this->query($sql), "kontaktid", null, 'Kontakt'));
	}
	public function setKontakt($kontaktid, $strasse, $hausnummer, $ortid, $telefon, $handy, $email) {
		if ($kontaktid == null) {
			$sql = "INSERT INTO `kontakte` (`strasse`, `hausnummer`, `ortid`, `telefonnummer`, `handynummer`, `email`) VALUES ('" . $this->escape($strasse) . "', '" . $this->escape($hausnummer) . "', " . intval($ortid) . ", '" . $this->escape($telefon) . "', '" . $this->escape($handy) . "', '" . $this->escape($email) . "')";
		} else {
			$sql = "UPDATE `kontakte` SET `strasse` = '" . $this->escape($strasse) . "', `hausnummer` = '" . $this->escape($hausnummer) . "', `ortid` = " . intval($ortid) . ", `telefonnummer` = '" . $this->escape($telefon) . "', `handynummer` = '" . $this->escape($handy) . "', `email` = '" . $this->escape($email) . "' WHERE `kontaktid` = " . intval($kontaktid);
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
	public function searchKontakt($strasse, $hausnummer, $ortid, $telefon, $handy, $email) {
		$sql = "SELECT `kontaktid`, `strasse`, `hausnummer`, `ortid`, `telefonnummer`, `handynummer`, `email` FROM `kontakte` WHERE `strasse` = '" . $this->escape($strasse) . "' AND `hausnummer` = '" . $this->escape($hausnummer) . "' AND `ortid` = " . intval($ortid) . " AND `telefonnummer` = '" . $this->escape($telefon) . "' AND `handynummer` = '" . $this->escape($handy) . "' AND `email` = '" . $this->escape($email) . "'";
		$array = $this->fetchAsArray($this->query($sql), "kontaktid", null, 'Kontakt');
		if (count($array) > 0) {
			return reset($array);
		}
		$kontakt = new Kontakt($this);
		$kontakt->setStrasse($strasse);
		$kontakt->setHausnummer($hausnummer);
		$kontakt->setOrtID($ortid);
		$kontakt->setTelefonnummer($telefon);
		$kontakt->setHandynummer($handy);
		$kontakt->setEMail($email);
		$kontakt->save();
		return $kontakt;
	}

	/**
	 * Ort
	 **/
	public function parseOrt($row) {
		return $this->parseRow($row, null, "Ort");
	}
	public function getOrtResult() {
		$sql = "SELECT `ortid`, `plz`, `label`, `stateid` FROM `orte`";
		return $this->getResult($sql, array($this, "parseOrt"));
	}
	public function getOrtResultLimit($plz = null, $label = null, $stateid = null, $count = null) {
		$sql = "SELECT `ortid`, `plz`, `label`, `stateid` FROM `orte` WHERE 1";
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
		$sql = "SELECT `ortid`, `plz`, `label`, `stateid` FROM `orte` WHERE `ortid` = " . intval($ortid);
		return $this->getResult($sql, array($this, "parseOrt"))->fetchRow();
	}
	public function setOrt($ortid, $plz, $label, $stateid) {
		if ($ortid == null) {
			$sql = "INSERT INTO `orte` (`plz`, `label`, `stateid`) VALUES ('" . $this->escape($plz) . "', '" . $this->escape($label) . "', " . intval($stateid) . ")";
		} else {
			$sql = "UPDATE `orte` SET `plz` = '" . $this->escape($plz) . "', `label` = '" . $this->escape($label) . "', `stateid` = '" . $this->escape($stateid) . "' WHERE `ortid` = " . intval($ortid);
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
		$sql = "SELECT `ortid`, `plz`, `label`, `stateid` FROM `orte` WHERE `plz` = '" . $this->escape($plz) . "' AND `label` = '" . $this->escape($label) . "' AND `stateid` = " . intval($stateid);
		$array = $this->getResult($sql, array($this, "parseOrt"))->fetchRow();
		if (count($array) > 0) {
			return reset($array);
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
		$sql = "SELECT `stateid`, `label`, `countryid` FROM `states`";
		return $this->getResult($sql, array($this, "parseState"));
	}
	public function getState($stateid) {
		$sql = "SELECT `stateid`, `label`, `countryid` FROM `states` WHERE `stateid` = " . intval($stateid);
		return $this->getResult($sql, array($this, "parseState"))->fetchRow();
	}
	public function setState($stateid, $label, $countryid) {
		if ($stateid == null) {
			$sql = "INSERT INTO `states` (`label`, `countryid`) VALUES ('" . $this->escape($label) . "', " . intval($countryid) . ")";
		} else {
			$sql = "UPDATE `states` SET `label` = '" . $this->escape($label) . "', `countryid` = '" . $this->escape($countryid) . "' WHERE `stateid` = " . intval($stateid);
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
		$sql = "SELECT `mitgliedschaftid`, `label`, `description`, `defaultbeitrag`, `defaultcreatemail` FROM `mitgliedschaften`";
		return $this->getResult($sql, array($this, "parseMitgliedschaft"));
	}
	public function getMitgliedschaft($mitgliedschaftid) {
		$sql = "SELECT `mitgliedschaftid`, `label`, `description`, `defaultbeitrag`, `defaultcreatemail` FROM `mitgliedschaften` WHERE `mitgliedschaftid` = " . intval($mitgliedschaftid);
		return $this->getResult($sql, array($this, "parseMitgliedschaft"))->fetchRow();
	}
	public function setMitgliedschaft($mitgliedschaftid, $globaleid, $label, $description, $defaultbeitrag, $defaultcreatemail) {
		if ($mitgliedschaftid == null) {
			$sql = "INSERT INTO `mitgliedschaften` (`label`, `description`, `defaultbeitrag`, `defaultcreatemail`) VALUES ('" . $this->escape($label) . "', '" . $this->escape($description) . "', " . doubleval($defaultbeitrag) . ", " . intval($defaultcreatemail) . ")";
		} else {
			$sql = "UPDATE `mitgliedschaften` SET `label` = 
'" . $this->escape($label) . "', `description` = '" . $this->escape($description) . "', `defaultbeitrag` = " . doubleval($defaultbeitrag) . ", `defaultcreatemail` = " . intval($defaultcreatemail) . " WHERE `mitgliedschaftid` = " . intval($mitgliedschaftid);
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
		$sql = "SELECT `natpersonid`, `name`, `vorname`, `geburtsdatum`, `nationalitaet` FROM `natperson` WHERE `natpersonid` = " . intval($natpersonid);
		return $this->getResult($sql, array($this, "parseNatPerson"))->fetchRow();
	}
	public function setNatPerson($natpersonid, $name, $vorname, $geburtsdatum, $nationalitaet) {
		if ($natpersonid == null) {
			$sql = "INSERT INTO `natperson` (`name`, `vorname`, `geburtsdatum`, `nationalitaet`) VALUES ('" . $this->escape($name) . "', '" . $this->escape($vorname) . "', '" . date("Y-m-d", $geburtsdatum) . "', '" . $this->escape($nationalitaet) . "')";
		} else {
			$sql = "UPDATE `natperson` SET `name` = '" . $this->escape($name) . "', `vorname` = '" . $this->escape($vorname) . "', `geburtsdatum` = '" . date("Y-m-d", $geburtsdatum) . "', `nationalitaet` = '" . $this->escape($nationalitaet) . "' WHERE `natpersonid` = " . intval($natpersonid);
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
	public function searchNatPerson($name, $vorname, $geburtsdatum, $nationalitaet) {
		$sql = "SELECT `natpersonid`, `name`, `vorname`, `geburtsdatum`, `nationalitaet` FROM `natperson` WHERE `name` = '" . $this->escape($name) . "' AND `vorname` = '" . $this->escape($vorname) . "' AND `geburtsdatum` = '" . date("Y-m-d", $geburtsdatum) . "' AND `nationalitaet` = '" . $this->escape($nationalitaet) . "'";
		$array = $this->getResult($sql, array($this, "parseNatPerson"))->fetchRow();
		if (count($array) > 0) {
			return reset($array);
		}
		$natperson = new NatPerson($this);
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
		$array = $this->getResult($sql, array($this, "parseJurPerson"))->fetchRow();
		if (count($array) > 0) {
			return reset($array);
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
	public function getMailTemplateResult() {
		$sql = "SELECT `templateid`, `label`, `body` FROM `mailtemplates`";
		return $this->getResult($sql, array($this, "parseMailTemplate"));
	}
	public function getMailTemplate($mailtemplateid) {
		$sql = "SELECT `templateid`, `label`, `body` FROM `mailtemplates` WHERE `templateid` = " . intval($mailtemplateid);
		return $this->getResult($sql, array($this, "parseMailTemplate"))->fetchRow();
	}
	public function setMailTemplate($templateid, $label, $body) {
		if ($templateid == null) {
			$sql = "INSERT INTO `mailtemplates` (`label`, `body`) VALUES ('" . $this->escape($label) . "', '" . $this->escape($body) . "')";
		} else {
			$sql = "UPDATE `mailtemplates` SET `body` = '" . $this->escape($body) . "', `label` = '" . $this->escape($label) . "' WHERE `templateid` = " . intval($templateid);
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

	public function parseMailTemplateAttachment($row) {
		return $this->parseRow($row, null, "MailAttachment");
	}
	public function getMailTemplateAttachmentResult($mailtemplateid) {
		$sql = "SELECT `mailattachments`.`attachmentid`, `mailattachments`.`filename`, `mailattachments`.`mimetype`, `mailattachments`.`content` FROM `mailtemplateattachments` LEFT JOIN `mailattachments` ON (`mailtemplateattachments`.`attachmentid` = `mailattachments`.`attachmentid`) WHERE `mailtemplateattachments`.`templateid` = " . intval($mailtemplateid);
		return $this->getResult($sql, array($this, "parseMailTemplateAttachment"));
	}
	public function setMailTemplateAttachmentList($mailtemplateid, $attachments) {
		$sql = "DELETE FROM `mailtemplateattachments` WHERE `templateid` = " . intval($templateid);
		$this->query($sql);
		$sqlinserts = array();
		while (count($attachments) > 0) {
			$sqlinserts[] = "(" . intval($templateid) . ", '" . $this->escape(array_shift($attachments)) . "')";
		}
		if (count($sqlinserts) > 0) {
			$sql = "INSERT INTO `mailtemplateattachments` (`templateid`, `attachmentid`) VALUES " . implode(", ", $sqlinserts);
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
	 * MailAttachments
	 **/
	public function parseMailAttachment($row) {
		return $this->parseRow($row, null, "MailAttachment");
	}
	public function getMailAttachmentResult() {
		$sql = "SELECT `attachmentid`, `filename`, `mimetype`, `content` FROM `mailattachments`";
		return $this->getResult($sql, array($this, "parseMailAttachment"));
	}
	public function getMailAttachment($attachmentid) {
		$sql = "SELECT `attachmentid`, `filename`, `mimetype`, `content` FROM `mailattachments` WHERE `attachmentid` = " . intval($attachmentid);
		return $this->getResult($sql, array($this, "parseMailAttachment"))->fetchRow();
	}
	public function setMailAttachment($attachmentid, $filename, $mimetype, $content) {
		if ($attachmentid == null) {
			$sql = "INSERT INTO `mailattachments` (`filename`, `mimetype`, `content`) VALUES ('" . $this->escape($filename) . "', '" . $this->escape($mimetype) . "', '" . $this->escape($content) . "')";
		} else {
			$sql = "UPDATE `mailattachments` SET `filename` = '" . $this->escape($filename) . "', `mimetype` = '" . $this->escape($mimetype) . "', `content` = '" . $this->escape($content) . "' WHERE `attachmentid` = " . intval($attachmentid);
		}
		$this->query($sql);
		if ($attachmentid == null) {
			$attachmentid = $this->getInsertID();
		}
		return $attachmentid;
	}
	public function delMailAttachment($attachmentid) {
		$sql = "DELETE FROM `mailattachments` WHERE `attachmentid` = " . intval($attachmentid);
		return $this->query($sql);
	}

	/**
	 * Prozesse
	 **/
	public function parseProcess($row) {
		return $this->parseRow($row, null, "Process");
	}
	public function getProcessResult() {
		$sql = "SELECT `processid`, `type`, `typedata`, `progress`, UNIX_TIMESTAMP(`queued`) as `queued`, UNIX_TIMESTAMP(`started`) as `started`, UNIX_TIMESTAMP(`finished`) as `finished`, `finishedpage` FROM `processes`";
		return $this->getResult($sql, array($this, "parseProcess"));
	}
	public function getProcess($processid) {
		$sql = "SELECT `processid`, `type`, `typedata`, `progress`, UNIX_TIMESTAMP(`queued`) as `queued`, UNIX_TIMESTAMP(`started`) as `started`, UNIX_TIMESTAMP(`finished`) as `finished`, `finishedpage` FROM `processes` WHERE `processid` = " . intval($processid);
		return $this->getResult($sql, array($this, "parseProcess"))->fetchRow();
	}
	public function setProcess($processid, $type, $typedata, $progress, $queued, $started, $finished, $finishedpage) {
		if ($processid == null) {
			$sql = "INSERT INTO `processes`
				(`type`, `typedata`, `progress`, `queued`, `started`, `finished`, `finishedpage`) VALUES
				('" . $this->escape($type) . "',
				 '" . $this->escape($typedata) . "',
				 " . doubleval($progress) . ",
				 '" . date("Y-m-d H:i:s", $queued) . "',
				 " . ($started != null ? "'" . date("Y-m-d H:i:s", $started) . "'" : "NULL") . ",
				 " . ($finished != null ? "'" . date("Y-m-d H:i:s", $finished) . "'" : "NULL") . ",
				 '" . $this->escape($finishedpage) . "')";
		} else {
			$sql = "UPDATE	`processes`
				SET	`type` = '" . $this->escape($type) . "',
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
	 * File
	 **/
	public function parseFile($row) {
		return $this->parseRow($row, null, "TempFile");
	}
	public function getFileResult() {
		$sql = "SELECT `fileid`, `userid`, `filename`, `exportfilename`, `mimetype` FROM `files`";
		return $this->getResult($sql, array($this, "parseFile"));
	}
	public function getFile($fileid) {
		$sql = "SELECT `fileid`, `userid`, `filename`, `exportfilename`, `mimetype` FROM `files` WHERE `fileid` = " . intval($fileid);
		return $this->getResult($sql, array($this, "parseFile"))->fetchRow();
	}
	public function setFile($fileid, $userid, $filename, $exportfilename, $mimetype) {
		if ($fileid == null) {
			$sql = "INSERT INTO `files`
				(`userid`, `filename`, `exportfilename`, `mimetype`) VALUES
				(" . intval($userid) . ",
				 '" . $this->escape($filename) . "',
				 '" . $this->escape($exportfilename) . "',
				 '" . $this->escape($mimetype) . "')";
		} else {
			$sql = "UPDATE	`files`
				SET	`userid` = " . intval($userid) . ",
					`filename` = '" . $this->escape($filename) . "',
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
		$sql = "DELETE `files` WHERE `fileid` = " . intval($fileid);
		return $this->query($sql);
	}
}

class SQLStorageResult extends StorageResult {
	private $stor;
	private $rslt;
	private $callback;

	public function __construct(Storage $stor, $rslt, $callback = null) {
		$this->stor = $stor;
		$this->rslt = $rslt;
		$this->callback = $callback;
	}

	public function fetchRow() {
		$row = $this->stor->fetchRow($this->rslt);
		if ($row == null) {
			return null;
		}
		if ($this->callback != null) {
			$row = call_user_func($this->callback, $row);
		}
		return $row;
	}

	public function fetchAll($keyfield = null) {
		$rows = array();
		while ($row = $this->fetchRow()) {
			if ($keyfield != null) {
				$rows[$row[$keyfield]] = $row;
			} else {
				$rows[] = $row;
			}
		}
		return $rows;
	}

	public function getCount() {
		return $this->stor->numRows($this->rslt);
	}
}

?>
