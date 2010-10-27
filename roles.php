<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->getAuth()->isAllowed("roles_show")) {
	$ui->viewLogin();
	exit;
}

switch ($_REQUEST["mode"]) {
case "details":
	$roleid = intval($_REQUEST["roleid"]);

	if (isset($_REQUEST["save"])) {
		if (!$session->getAuth()->isAllowed("roles_modify")) {
			$ui->viewLogin();
			exit;
		}
		$label = stripslashes($_REQUEST["label"]);
		$description = stripslashes($_REQUEST["description"]);
		$session->getStorage()->modRole($roleid, $label, $description);
	}

	if (isset($_REQUEST["savepermissions"])) {
		if (!$session->getAuth()->isAllowed("roles_modify")) {
			$ui->viewLogin();
			exit;
		}
		$permissions = $_REQUEST["permissions"];
		$rolepermissions = array_keys($session->getStorage()->getRolePermissions($roleid));
		foreach (array_diff($permissions, $rolepermissions) as $perm) {
			$session->getStorage()->addRolePermission($roleid, $perm);
		}
		foreach (array_diff($rolepermissions, $permissions) as $perm) {
			$session->getStorage()->delRolePermission($roleid, $perm);
		}
	}

	$role = reset($session->getStorage()->getRoleList($roleid));
	$roleusers = $session->getStorage()->getUserList(null, $roleid);
	$users = $session->getStorage()->getUserList();
	$rolepermissions = $session->getStorage()->getRolePermissions($roleid);
	$permissions = $session->getStorage()->getPermissions();

	$ui->viewRoleDetails($role, $roleusers, $users, $rolepermissions, $permissions);
	exit;
case "create":
	if (isset($_REQUEST["save"])) {
		if (!$session->getAuth()->isAllowed("roles_create")) {
			$ui->viewLogin();
			exit;
		}
		$label = stripslashes($_REQUEST["label"]);
		$description = stripslashes($_REQUEST["description"]);
		$roleid = $session->getStorage()->addRole($label, $description);
		$ui->redirect($session->getLink("roles_details", $roleid));
	}

	$ui->viewRoleCreate();
	exit;
case "delete":
	if (!$session->getAuth()->isAllowed("roles_delete")) {
		$ui->viewLogin();
		exit;
	}
	$roleid = intval($_REQUEST["roleid"]);
	$session->getStorage()->delRole($roleid);
	$ui->redirect();
	exit;
default:
	$roles = $session->getStorage()->getRoleList();
	$ui->viewRoleList($roles);
	exit;
}

?>
