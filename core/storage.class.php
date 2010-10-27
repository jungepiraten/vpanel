<?php

interface Storage {
	public function validLogin($username, $password);
	public function getUserList($userid = null);
	public function addUser($username, $password);
	public function modUser($userid, $username);
	public function changePassword($userid, $password);
	public function getPermissions($userid);

	public function getRoleList($userid = null);
	public function addRole($label, $description);
	public function modRole($roleid, $label, $description);

	public function addUserRole($userid, $roleid);
	public function delUserRole($userid, $roleid);
}

?>
