<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
require_once(VPANEL_UI . "/template.class.php");
$ui = new Template($session);

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

	$role = reset($session->getStorage()->getRoleList($roleid));
	$roleusers = $session->getStorage()->getUserList(null, $roleid);
	$users = $session->getStorage()->getUserList();

	$ui->viewRoleDetails($role, $roleusers, $users);
	exit;
default:
	$roles = $session->getStorage()->getRoleList();
	$ui->viewRoleList($roles);
	exit;
}

?>
