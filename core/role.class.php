<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

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
		if ($this->users == null) {
			$this->users = $this->getStorage()->getUserList($this->getRoleID());
		}
		return $this->users;
	}

	public function getPermissions() {
		if ($this->permissions == null) {
			$this->permissions = $this->getStorage()->getRolePermissionList($this->getRoleID());
		}
		return $this->permissions;
	}

	public function load() {
		if ($this->roleid != null) {
			$row = $this->getStorage()->getRole($this->getRoleID());
			$this->setRoleID($row["roleid"]);
			$this->setLabel($row["label"]);
			$this->setDescription($row["description"]);
		}
	}
	
	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setRoleID( $storage->setRole(
			$this->getRoleID(),
			$this->getLabel(),
			$this->getDescription() ));
	}
}

?>
