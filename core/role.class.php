<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

require_once(VPANEL_CORE . "/rolepermission.class.php");

class Role extends StorageClass {
	private $roleid;
	private $label;
	private $description;

	private $users;
	private $permissions;

	public static function factory(Storage $storage, $row) {
		$role = new Role($storage);
		$role->setRoleID($row["roleid"]);
		$role->setLabel($row["label"]);
		$role->setDescription($row["description"]);
		return $role;
	}

	public function getRoleID() {
		return $this->roleid;
	}

	public function setRoleID($roleid) {
		$this->roleid = $roleid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getUsers() {
		if ($this->users === null) {
			$this->users = $this->getStorage()->getRoleUserList($this->getRoleID());
		}
		return $this->users;
	}

	public function getPermissions() {
		if ($this->permissions === null) {
			$this->permissions = array();
			foreach ($this->getStorage()->getRolePermissionList($this->getRoleID()) as $permission) {
				$this->permissions[$permission->getPermissionID() . "-" . $permission->getGliederungID()] = $permission;
			}
		}
		return $this->permissions;
	}

	public function setPermission($permissionid, $gliederungid, $transitive) {
		$this->permissions[$permissionid . "-" . $gliederungid] = new RolePermission($this->getStorage());
		$this->permissions[$permissionid . "-" . $gliederungid]->setRole($this);
		$this->permissions[$permissionid . "-" . $gliederungid]->setPermissionID($permissionid);
		$this->permissions[$permissionid . "-" . $gliederungid]->setGliederungID($gliederungid);
		$this->permissions[$permissionid . "-" . $gliederungid]->isTransitive($transitive);
	}

	public function delPermission($permissionid, $gliederungid) {
		$this->getPermissions();
		unset($this->permissions[$permissionid . "-" . $gliederungid]);
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setRoleID( $storage->setRole(
			$this->getRoleID(),
			$this->getLabel(),
			$this->getDescription() ));

		$storage->setRolePermissionList($this->getRoleID(), $this->getPermissions());
	}
}

?>
