<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class RolePermission extends StorageClass {
	private $roleid;
	private $permissionid;
	private $gliederungid;
	private $transitive;

	private $role;
	private $permission;
	private $gliederung;

	public static function factory(Storage $storage, $row) {
		$permission = new RolePermission($storage);
		$permission->setRoleID($row["roleid"]);
		$permission->setPermissionID($row["permissionid"]);
		$permission->setGliederungID($row["gliederungid"]);
		$permission->isTransitive($row["transitive"]);
		return $permission;
	}

	public function getRoleID() {
		return $this->roleid;
	}

	public function setRoleID($roleid) {
		if ($this->roleid != $roleid) {
			$this->role = null;
		}
		$this->roleid = $roleid;
	}

	public function getRole() {
		if ($this->role == null) {
			$this->role = $this->getStorage()->getRole($this->roleid);
		}
		return $this->role;
	}

	public function setRole($role) {
		$this->setRoleID($role->getRoleID());
		$this->role = $role;
	}

	public function getPermissionID() {
		return $this->permissionid;
	}

	public function setPermissionID($permissionid) {
		if ($this->permissionid != $permissionid) {
			$this->permission = null;
		}
		$this->permissionid = $permissionid;
	}

	public function getPermission() {
		if ($this->permission == null) {
			$this->permission = $this->getStorage()->getPermission($this->permissionid);
		}
		return $this->permission;
	}

	public function setPermission($permission) {
		$this->setPermissionID($permission->getPermissionID());
		$this->permission = $permission;
	}

	public function getGliederungID() {
		return $this->gliederungid;
	}

	public function setGliederungID($gliederungid) {
		if ($this->gliederungid != $gliederungid) {
			$this->gliederung = null;
		}
		$this->gliederungid = $gliederungid;
	}

	public function getGliederung() {
		if ($this->gliederung == null) {
			$this->gliederung = $this->getStorage()->getGliederung($this->gliederungid);
		}
		return $this->gliederung;
	}

	public function setGliederung($gliederung) {
		$this->setGliederungID($gliederung->getGliederungID());
		$this->gliederung = $gliederung;
	}

	public function isTransitive($transitive = null) {
		if ($transitive != null) {
			$this->transitive = $transitive;
		}
		return $this->transitive;
	}
}

?>
