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
		$permissionsSaved = array();
		foreach ($role->getPermissions() as $permission) {
			$permissionkey = $permission->getPermissionID() . ($permission->getPermission()->isGlobal() ? "" : "-" . $permission->getGliederungID());
			if (!in_array($permissionkey, $permissions)) {
				$role->delPermission($permission->getPermissionID(), $permission->getGliederungID());
			} else {
				$permissionid = $permission->getPermissionID();
				$gliederungid = $permission->getGliederungID();
				$role->setPermission($permissionid, $gliederungid, true);
				$permissionsSaved[] = $permissionkey;
			}
		}
		// Speichere neue Permissions
		foreach (array_diff($permissions, $permissionsSaved) as $permissionkey) {
			$perm = explode("-", $permissionkey);
			$permissionid = $perm[0];
			$gliederungid = isset($perm[1]) ? $perm[1] : null;
			$role->setPermission($permissionid, $gliederungid, true);
		}
		$role->save();
	}

	$users = $session->getStorage()->getUserList();
	$permissions_global = $session->getStorage()->getPermissionGlobalList();
	$permissions_local = $session->getStorage()->getPermissionLocalList();
	$gliederungen = $session->getStorage()->getGliederungList();

	$ui->viewRoleDetails($role, $users, $permissions_global, $permissions_local, $gliederungen);
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
