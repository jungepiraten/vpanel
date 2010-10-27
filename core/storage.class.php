<?php

interface Storage {
	public function getPermissions();
	public function getUserPermissions($userid);
	public function getRolePermissions($roleid);
	public function addRolePermission($roleid, $perm);
	public function delRolePermission($roleid, $perm);

	public function validLogin($username, $password);
	public function getUserList($userid = null);
	public function addUser($username, $password);
	public function modUser($userid, $username);
	public function delUser($userid);
	public function changePassword($userid, $password);

	public function getRoleList($userid = null);
	public function addRole($label, $description);
	public function modRole($roleid, $label, $description);
	public function delRole($roleid);

	public function addUserRole($userid, $roleid);
	public function delUserRole($userid, $roleid);
}

?>
