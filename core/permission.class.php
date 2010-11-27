<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class Permission extends StorageClass {
	private $permissionid;
	private $label;
	private $description;

	public static function factory(Storage $storage, $row) {
		$permission = new Permission($storage);
		$permission->setPermissionID($row["permissionid"]);
		$permission->setLabel($row["label"]);
		$permission->setDescription($row["description"]);
		return $permission;
	}

	public function getPermissionID() {
		return $this->permissionid;
	}

	public function setPermissionID($permissionid) {
		$this->permissionid = $permissionid;
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

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setPermissionID( $storage->setPermission(
			$this->getPermissionID(),
			$this->getLabel(),
			$this->getDescription() ));
	}
}

?>
