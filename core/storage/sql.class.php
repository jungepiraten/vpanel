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
require_once(VPANEL_CORE . "/mailheader.class.php");

abstract class SQLStorage implements Storage {
	public function __construct() {}
	
	abstract protected function query($sql);
	abstract protected function fetchRow($result);
	abstract protected function getInsertID();

	protected function fetchAsArray($result, $keyfield = null, $field = null, $class = null) {
		$rows = array();
		while ($row = $this->fetchRow($result)) {
			if ($field === null) {
				$item = $row;
			} else {
				$item = $row[$field];
			}
			if ($class !== null) {
				if (is_array($class)) {
					$values = array();
					foreach ($row as $col => $val) {
						list($prefix, $col) = explode("_", $col, 2);
						$values[$prefix][$col] = $val;
					}
					$item = array();
					foreach ($class as $l => $c) {
						$item[$l] = call_user_func(array($c, "factory"), $this, $values[$l]);
					}
				} else {
					$item = call_user_func(array($class, "factory"), $this, $row);
				}
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

	/**
	 * Berechtigungen
	 */
	public function getPermissionList() {
		$sql = "SELECT `permissionid`, `label`, `description` FROM `permissions`";
		return $this->fetchAsArray($this->query($sql), "permissionid", null, Permission);
	}
	public function getPermission($permissionid) {
		$sql = "SELECT `permissionid`, `label`, `description` FROM `permissions` WHERE `permissionid` = " . intval($permissionid);
		return reset($this->fetchAsArray($this->query($sql), "permissionid", null, Permission));
	}

	/**
	 * Benutzer
	 */
	public function getUserList() {
		$sql = "SELECT `userid`, `username` FROM `users`";
		return $this->fetchAsArray($this->query($sql), "userid", null, User);
	}
	public function getUser($userid) {
		$sql = "SELECT `userid`, `username`, `password` FROM `users` WHERE `userid` = " . intval($userid);
		return reset($this->fetchAsArray($this->query($sql), "userid", null, User));
	}
	public function getUserByUsername($username) {
		$sql = "SELECT `userid`, `username`, `password` FROM `users` WHERE `username` = '" . $this->escape($username) . "'";
		return reset($this->fetchAsArray($this->query($sql), "userid", null, User));
	}
	public function setUser($userid, $username, $password) {
		if ($userid == null) {
			$sql = "INSERT INTO `users` (`username`, `password`) VALUES ('" . $this->escape($username) . "', '" . $this->escape($password) . "')";
		} else {
			$sql = "UPDATE `users` SET `username` = '" . $this->escape($username) . "', `password` = '" . $this->escape($password) . "' WHERE `userid` = " . intval($userid);
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
	public function getUserRoleList($userid) {
		$sql = "SELECT `roleid`, `label`, `description` FROM `roles` LEFT JOIN `userroles` USING (`roleid`) WHERE `userid` = " . intval($userid);
		return $this->fetchAsArray($this->query($sql), "roleid", null, Role);
	}
	public function setUserRoleList($userid, $roleids) {
		$sql = "INSERT INTO `userroles` (`userid`, `roleid`) VALUES ";
        foreach ($roleids as $roleid) {
            $sql .= "(`" . $this->escape($userid) . "`, `" . $roleid->getRoleID() . "`)";
        }
        echo $sql;
        return $this->query($sql);
	}

	/**
	 * Rollen
	 */
	public function getRoleList() {
		$sql = "SELECT `roleid`, `label`, `description` FROM `roles`";
		return $this->fetchAsArray($this->query($sql), "roleid", null, Role);
	}
	public function getRole($roleid) {
		$sql = "SELECT `roleid`, `label`, `description` FROM `roles` WHERE `roleid` = " . intval($roleid);
		return reset($this->fetchAsArray($this->query($sql), null, null, Role));
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
	public function getRolePermissionList($roleid) {
		$sql = "SELECT `permissions`.`permissionid` AS 'permissionid', `permissions`.`label`, `permissions`.`description` AS 'permission' FROM `rolepermissions` LEFT JOIN `permissions` USING (`permissionid`) WHERE `rolepermissions`.`roleid` = '" . $this->escape($roleid) . "'";
		return $this->fetchAsArray($this->query($sql), "permissionid", null, Permission);
	}
	public function setRolePermissionList($roleid, $permissionids) {
		// TODO
	}
	public function getRoleUserList($roleid) {
		$sql = "SELECT `userid`, `username` FROM `users` LEFT JOIN `userroles` USING (`userid`) WHERE `roleid` = " . intval($roleid);
		return $this->fetchAsArray($this->query($sql), "userid", null, User);
	}
	public function setRoleUserList($roleid, $userids) {
		// TODO
	}

	/**
	 * Gliederung
	 **/
	public function getGliederungList() {
		$sql = "SELECT `gliederungsid`, `label` FROM `gliederungen`";
		return $this->fetchAsArray($this->query($sql), "gliederungsid", null, Gliederung);
	}
	public function getGliederung($gliederungid) {
		$sql = "SELECT `gliederungsid`, `label` FROM `gliederungen` WHERE `gliederungsid` = " . intval($gliederungid);
		return reset($this->fetchAsArray($this->query($sql), "gliederungsid", null, Gliederung));
	}

	/**
	 * Mitglieder
	 **/
	public function getMitgliederList() {
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
				`o`.`ortid` AS `k_ortid`,
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
			GROUP BY `m`.`mitgliedid`, `r`.`timestamp`
			HAVING	`r`.`timestamp` = MAX(`rmax`.`timestamp`)
			ORDER BY `r`.`timestamp`";
		$os = $this->fetchAsArray($this->query($sql), "m_mitgliedid", null, array("r" => MitgliedRevision, "n" => NatPerson, "j" => JurPerson, "k" => Kontakt, "o" => Ort, "m" => Mitglied));
		$objs = array();
		foreach ($os as $o) {
			$o["k"]->setOrt($o["o"]);
			if ($o["r"]->getNatPersonID() !== null) {
				$o["r"]->setNatPerson($o["n"]);
			}
			if ($o["r"]->getJurPersonID() !== null) {
				$o["r"]->setJurPerson($o["j"]);
			}
			$o["r"]->setKontakt($o["k"]);
			$o["m"]->addRevision($o["r"]);
			$objs[] = $o["m"];
		}
		return $objs;
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
		$o = reset($this->fetchAsArray($this->query($sql), "r_revisionid", null, array("r" => MitgliedRevision, "n" => NatPerson, "j" => JurPerson, "k" => Kontakt, "o" => Ort, "m" => Mitglied)));
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
	public function getMitgliederRevisionList($mitgliedid = null) {
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
				`o`.`ortid` AS `k_ortid`,
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
		$os = $this->fetchAsArray($this->query($sql), "revisionid", null, array("r" => MitgliederRevision, "n" => NatPerson, "j" => JurPerson, "k" => Kontakt, "u" => User));
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
	public function getMitgliederRevision($revisionid) {
		$sql = "SELECT	`mitgliederrevisions`.`revisionid`,
				`mitgliederrevisions`.`globaleid`,
				UNIX_TIMESTAMP(`mitgliederrevisions`.`timestamp`),
				`mitgliederrevisions`.`userid`,
				`mitgliederrevisions`.`mitgliedid`,
				`mitgliederrevisions`.`mitgliedschaftid`,
				`mitgliederrevisions`.`gliederungsid`,
				`mitgliederrevisions`.`geloescht`,
				`mitgliederrevisions`.`mitglied_piraten`,
				`mitgliederrevisions`.`verteiler_eingetragen`,
				`mitgliederrevisions`.`beitrag`,
				`mitgliederrevisions`.`natpersonid`,
				`mitgliederrevisions`.`jurpersonid`,
				`mitgliederrevisions`.`kontaktid`
			FROM	`mitgliederrevisions`
			WHERE	`revisionid` = " . intval($mitgliedid);
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
		return reset($this->fetchAsArray($this->query($sql), "kontaktid", null, Kontakt));
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
		$array = $this->fetchAsArray($this->query($sql), "kontaktid", null, Kontakt);
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
	public function getOrtList() {
		$sql = "SELECT `ortid`, `plz`, `label`, `stateid` FROM `orte`";
		return $this->fetchAsArray($this->query($sql), "ortid", null, Ort);
	}
	public function getOrt($ortid) {
		$sql = "SELECT `ortid`, `plz`, `label`, `stateid` FROM `orte` WHERE `ortid` = " . intval($ortid);
		return reset($this->fetchAsArray($this->query($sql), "ortid", null, Ort));
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
		$array = $this->fetchAsArray($this->query($sql), "ortid", null, Ort);
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
	public function getStateList() {
		$sql = "SELECT `stateid`, `label`, `countryid` FROM `states`";
		return $this->fetchAsArray($this->query($sql), "stateid", null, State);
	}
	public function getState($stateid) {
		$sql = "SELECT `stateid`, `label`, `countryid` FROM `states` WHERE `stateid` = " . intval($stateid);
		return reset($this->fetchAsArray($this->query($sql), "stateid", null, State));
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
	public function getCountryList() {
		$sql = "SELECT `countryid`, `label` FROM `countries`";
		return $this->fetchAsArray($this->query($sql), "countryid", null, Country);
	}
	public function getCountry($countryid) {
		$sql = "SELECT `countryid`, `label` FROM `countries` WHERE `countryid` = " . intval($countryid);
		return reset($this->fetchAsArray($this->query($sql), "countryid", null, Country));
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
	public function getMitgliedschaftList() {
		$sql = "SELECT `mitgliedschaftid`, `label`, `description`, `defaultbeitrag`, `defaultcreatemail` FROM `mitgliedschaften`";
		return $this->fetchAsArray($this->query($sql), "mitgliedschaftid", null, Mitgliedschaft);
	}
	public function getMitgliedschaft($mitgliedschaftid) {
		$sql = "SELECT `mitgliedschaftid`, `label`, `description`, `defaultbeitrag`, `defaultcreatemail` FROM `mitgliedschaften` WHERE `mitgliedschaftid` = " . intval($mitgliedschaftid);
		return reset($this->fetchAsArray($this->query($sql), "mitgliedschaftid", null, Mitgliedschaft));
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
	public function getNatPerson($natpersonid) {
		$sql = "SELECT `natpersonid`, `name`, `vorname`, `geburtsdatum`, `nationalitaet` FROM `natperson` WHERE `natpersonid` = " . intval($natpersonid);
		return reset($this->fetchAsArray($this->query($sql), null, null, NatPerson));
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
		$array = $this->fetchAsArray($this->query($sql), "natpersonid", null, NatPerson);
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
	public function getJurPerson($jurpersonid) {
		$sql = "SELECT `jurpersonid`, `label` FROM `jurperson` WHERE `jurpersonid` = " . intval($jurpersonid);
		return reset($this->fetchAsArray($this->query($sql), null, null, JurPerson));
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
		$array = $this->fetchAsArray($this->query($sql), "jurpersonid", null, JurPerson);
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
	public function getMailTemplateList() {
		$sql = "SELECT `templateid`, `body` FROM `mailtemplates`";
		return $this->fetchAsArray($this->query($sql), "templateid", null, MailTemplate);
	}
	public function getMailTemplate($mailtemplateid) {
		$sql = "SELECT `templateid`, `body` FROM `mailtemplates` WHERE `templateid` = " . intval($mailtemplateid);
		return reset($this->fetchAsArray($this->query($sql), "templateid", null, MailTemplate));
	}
	public function setMailTemplate($mailtemplateid, $body) {
		if ($mailtemplateid == null) {
			$sql = "INSERT INTO `mailtemplates` (`body`) VALUES ('" . $this->escape($body) . "')";
		} else {
			$sql = "UPDATE `mailtemplates` SET `body` = '" . $this->escape($body) . "' WHERE `templateid` = " . intval($mailtemplateid);
		}
		$this->query($sql);
		if ($mailtemplateid == null) {
			$mailtemplateid = $this->getInsertID();
		}
		return $mailtemplateid;
	}
	public function delMailTemplate($mailtemplateid) {
		$sql = "DELETE FROM `mailtemplates` WHERE `mailtemplateid` = " . intval($mailtemplateid);
		return $this->query($sql);
	}
	public function getMailTemplateHeaderList($mailtemplateid) {
		$sql = "SELECT `mailheaders`.`headerid`, `mailheader`.`label`, `mailtemplateheaders`.`value` FROM `mailheaders` LEFT JOIN `mailtemplateheaders` ON (`mailtemplateheaders`.`headerid` = `mailheader`.`headerid`) WHERE `mailtemplateheaders`.`templateid` = " . intval($mailtemplateid);
		return $this->fetchAsArray($this->query($sql), "headerid", null, MailTemplateHeader);
	}
	public function setMailTemplateHeaderList($mailtemplateid, $headerids, $values) {
		// TODO
	}
	public function getMailTemplateAttachmentList($mailtemplateid) {
		$sql = "SELECT `mailattachments.`.`attachmentid`, `mailattachments`.`filename`, `mailattachments`.`mimename`, `mailattachments`.`content` FROM `mailattachments` LEFT JOIN `mailtemplateattachments` ON (`mailtemplateattachments`.`attachmentid` = `mailattachment`.`attachmentid`) WHERE `mailtemplateattachments`.`templateid` = " . intval($mailtemplateid);
		return $this->fetchAsArray($this->query($sql), "headerid", null, MailTemplateAttachments);
	}
	public function setMailTemplateAttachmentList($mailtemplateid, $attachments) {
		// TODO
	}

	/**
	 * MailHeader
	 **/
	public function getMailHeaderList() {
		$sql = "SELECT `headerid`, `label` FROM `mailheaders`";
		return $this->fetchAsArray($this->query($sql), "headerid", null, MailHeader);
	}
	public function getMailHeader($headerid) {
		$sql = "SELECT `headerid`, `label` FROM `mailheaders` WHERE `headerid` = " . intval($headerid);
		return reset($this->fetchAsArray($this->query($sql), "headerid", null, MailHeader));
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
	public function getMailAttachmentList() {
		$sql = "SELECT `attachmentid`, `filename`, `mimetype`, `content` FROM `mailattachments`";
		return $this->fetchAsArray($this->query($sql), "attachmentid", null, MailAttachment);
	}
	public function getMailAttachment($attachmentid) {
		$sql = "SELECT `attachmentid`, `filename`, `mimetype`, `content` FROM `mailattachments` WHERE `attachmentid` = " . intval($attachmentid);
		return reset($this->fetchAsArray($this->query($sql), "attachmentid", null, MailAttachment));
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
}

?>
