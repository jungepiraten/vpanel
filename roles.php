<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("roles_show")) {
	$ui->viewLogin();
	exit;
}

switch (isset($_REQUEST["mode"]) ? stripslashes($_REQUEST["mode"]) : null) {
case "details":
	$roleid = intval($_REQUEST["roleid"]);
	$role = $session->getStorage()->getRole($roleid);

	if (isset($_REQUEST["save"])) {
		if (!$session->isAllowed("roles_modify")) {
			$ui->viewLogin();
			exit;
		}
		$label = stripslashes($_REQUEST["label"]);
		$description = stripslashes($_REQUEST["description"]);

		$role->setLabel($label);
		$role->setDescription($description);
		$role->save();
	}

	if (isset($_REQUEST["savepermissions"])) {
		if (!$session->isAllowed("roles_modify")) {
			$ui->viewLogin();
			exit;
		}
		$permissions = $_REQUEST["permissions"];
		$rolepermissions = $role->getPermissionIDs();
		foreach (array_diff($permissions, $rolepermissions) as $perm) {
			$role->addPermissionID($perm);
		}
		foreach (array_diff($rolepermissions, $permissions) as $perm) {
			$role->delPermissionID($perm);
		}
		$role->save();
	}

	$users = $session->getStorage()->getUserList();
	$permissions = $session->getStorage()->getPermissionList();

	$ui->viewRoleDetails($role, $users, $permissions);
	exit;
case "create":
	if (isset($_REQUEST["save"])) {
		if (!$session->isAllowed("roles_create")) {
			$ui->viewLogin();
			exit;
		}
		$label = stripslashes($_REQUEST["label"]);
		$description = stripslashes($_REQUEST["description"]);

		$role = new Role($session->getStorage());
		$role->setLabel($label);
		$role->setDescription($description);
		$role->save();

		$ui->redirect($session->getLink("roles_details", $role->getRoleID()));
	}

	$ui->viewRoleCreate();
	exit;
case "delete":
	if (!$session->isAllowed("roles_delete")) {
		$ui->viewLogin();
		exit;
	}
	$roleid = intval($_REQUEST["roleid"]);
	$session->getStorage()->delRole($roleid);
	$ui->redirect($session->getLink("roles"));
	exit;
default:
	$roles = $session->getStorage()->getRoleList();
	$ui->viewRoleList($roles);
	exit;
}

?>
