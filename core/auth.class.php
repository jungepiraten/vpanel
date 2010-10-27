<?php

class Auth {
	private $userid;
	private $username;
	private $permissions;
	
	public function __construct($userid, $username, $permissions = array()) {
		$this->userid = $userid;
		$this->username = $username;
		$this->permissions = $permissions;
	}
	
	public function isAllowed($permission) {
		return in_array($permission, $this->permissions);
	}
}

?>
