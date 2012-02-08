<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("roles_show")) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/role.class.php");

function parseRoleFormular($session, &$role = null) {
	$label = $session->getVariable("label");
	$description = $session->getVariable("description");

	if ($role == null) {
		$role = new Role($session->getStorage());
	}
	$role->setLabel($label);
	$role->setDescription($description);
	$role->save();
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "details":
	$roleid = $session->getIntVariable("roleid");
	$role = $session->getStorage()->getRole($roleid);

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("roles_modify")) {
			$ui->viewLogin();
			exit;
		}
		
		parseRoleFormular($session, $role);
	}

	if ($session->getBoolVariable("savepermissions")) {
		if (!$session->isAllowed("roles_modify")) {
			$ui->viewLogin();
			exit;
		}
		$permissions = $session->getListVariable("permissions");
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
	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("roles_create")) {
			$ui->viewLogin();
			exit;
		}
		
		parseRoleFormular($session, $role);
		
		$ui->redirect($session->getLink("roles_details", $role->getRoleID()));
	}

	$ui->viewRoleCreate();
	exit;
case "delete":
	if (!$session->isAllowed("roles_delete")) {
		$ui->viewLogin();
		exit;
	}
	$roleid = $session->getIntVariable("roleid");
	$session->getStorage()->delRole($roleid);
	$ui->redirect($session->getLink("roles"));
	exit;
default:
	$roles = $session->getStorage()->getRoleList();
	$ui->viewRoleList($roles);
	exit;
}

?>
