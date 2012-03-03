<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class User extends StorageClass {
	private $userid;
	private $username;
	private $password;
	private $apikey;

	private $roles;
	private $permissions;

	public static function factory(Storage $storage, $row) {
		$user = new User($storage);
		$user->setUserID($row["userid"]);
		$user->setUsername($row["username"]);
		$user->setPassword($row["password"]);
		$user->setPasswordSalt($row["passwordsalt"]);
		$user->setAPIKey($row["apikey"]);
		$user->setDefaultDokumentKategorieID($row["defaultdokumentkategorieid"]);
		$user->setDefaultDokumentStatusID($row["defaultdokumentstatusid"]);
		return $user;
	}

	protected function generateSalt($minlen = 3, $maxlen = 7) {
		$str = "";
		$chrs = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!"§$%&/()=?{[]}\\\'*+~#-.,;:<>|';
		for ($i=0;$i<=rand($minlen, $maxlen);$i++) {
			$str .= substr($chrs, rand(0,strlen($chrs)-1), 1);
		}
		return $str;
	}

	protected function hash($str) {
		return hash('sha256', $this->getPasswordSalt() . $str);
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

	public function getPasswordSalt() {
		return $this->passwordsalt;
	}

	public function setPasswordSalt($salt) {
		$this->passwordsalt = $salt;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function changePassword($password) {
		$salt = $this->generateSalt();
		$this->setPasswordSalt($salt);
		$this->setPassword($this->hash($password));
	}

	public function getAPIKey() {
		return $this->apikey;
	}

	public function setAPIKey($apikey = null) {
		if ($apikey == null) {
			$apikey = $this->generateSalt(16,16);
		}
		$this->apikey = $apikey;
	}

	public function getRoles() {
		if ($this->roles === null) {
			$roles = $this->getStorage()->getUserRoleList($this->getUserID());
			$this->roles = array();
			foreach ($roles as $role) {
				$this->roles[$role->getRoleID()] = $role;
			}
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

	public function getDefaultDokumentKategorieID() {
		return $this->defaultdokumentkategorieid;
	}

	public function setDefaultDokumentKategorieID($id) {
		$this->defaultdokumentkategorieid = $id;
	}

	public function getDefaultDokumentStatusID() {
		return $this->defaultdokumentstatusid;
	}

	public function setDefaultDokumentStatusID($id) {
		$this->defaultdokumentstatusid = $id;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setUserID( $storage->setUser(
			$this->getUserID(),
			$this->getUsername(),
			$this->getPassword(),
			$this->getPasswordSalt(),
			$this->getAPIKey(),
			$this->getDefaultDokumentKategorieID(),
			$this->getDefaultDokumentStatusID() ));
		
		$storage->setUserRoleList(
			$this->getUserID(),
			$this->getRoleIDs() );
	}
}

?>
