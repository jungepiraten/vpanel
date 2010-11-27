<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class User extends StorageClass {
	private $userid;
	private $username;
	private $password;

	private $roles;
	private $permissions;

	public static function factory(Storage $storage, $row) {
		$user = new User($storage);
		$user->setUserID($row["userid"]);
		$user->setUsername($row["username"]);
		$user->setPassword($row["password"]);
		return $user;
	}

	protected function hash($str) {
		return hash('sha256', $str);
	}

	public function getUserID() {
		return $this->userid;
	}

	public function setUserID($userid) {
		$this->userid = $userid;
	}

	public function getUserName() {
		return $this->username;
	}

	public function setUserName($username) {
		$this->username = $username;
	}

	public function isValidPassword($password) {
		return ($this->hash($password) == $this->getPassword());
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function changePassword($password) {
		$this->setPassword($this->hash($password));
	}

	public function getRoles() {
		if ($this->roles === null) {
			$this->roles = $this->getStorage()->getUserRoleList($this->getUserID());
		}
		return $this->roles;
	}

	public function getRoleIDs() {
		return array_keys($this->getRoles());
	}

	public function addRoleID($roleid) {
		$this->getRoles();
		$this->roles[$roleid] = $this->getStorage()->getRole($roleid);
	}

	public function delRoleID($roleid) {
		$this->getRoles();
		if (isset($this->roles[$roleid])) {
			unset($this->roles[$roleid]);
		}
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setUserID( $storage->setUser(
			$this->getUserID(),
			$this->getUsername(),
			$this->getPassword() ));
		
		$storage->setUserRoleList(
			$this->getUserID(),
			$this->getRoleIDs() );
	}
}

?>
