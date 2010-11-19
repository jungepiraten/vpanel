<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->getAuth()->isAllowed("users_show")) {
	$ui->viewLogin();
	exit;
}

switch ($_REQUEST["mode"]) {
case "addrole":
	if (!$session->getAuth()->isAllowed("users_modify")) {
		$ui->viewLogin();
		exit;
	}
	$userid = intval($_REQUEST["userid"]);
	$roleid = intval($_REQUEST["roleid"]);
	$session->getStorage()->addUserRole($userid, $roleid);
	$ui->redirect();
	exit;
case "delrole":
	if (!$session->getAuth()->isAllowed("users_modify")) {
		$ui->viewLogin();
		exit;
	}
	$userid = intval($_REQUEST["userid"]);
	$roleid = intval($_REQUEST["roleid"]);
	$session->getStorage()->delUserRole($userid, $roleid);
	$ui->redirect();
	exit;
case "details":
	$userid = intval($_REQUEST["userid"]);

	if (isset($_REQUEST["save"])) {
		if (!$session->getAuth()->isAllowed("users_modify")) {
			$ui->viewLogin();
			exit;
		}
		$username = stripslashes($_REQUEST["username"]);
		$password = stripslashes($_REQUEST["password"]);
		$session->getStorage()->modUser($userid, $username);

		if (!empty($password)) {
			$session->getStorage()->changePassword($userid, $password);
		}
	}

	$user = reset($session->getStorage()->getUserList($userid));
	$userroles = $session->getStorage()->getRoleList(null, $userid);
	$roles = $session->getStorage()->getRoleList();
	$permissions = $session->getStorage()->getPermissions($userid);

	$ui->viewUserDetails($user, $userroles, $roles, $permissions);
	exit;
case "create":
	if (isset($_REQUEST["save"])) {
		if (!$session->getAuth()->isAllowed("users_create")) {
			$ui->viewLogin();
			exit;
		}
		$username = stripslashes($_REQUEST["username"]);
		$password = stripslashes($_REQUEST["password"]);
		$userid = $session->getStorage()->addUser($username, $password);
		$ui->redirect($session->getLink("users_details", $userid));
	}

	$ui->viewUserCreate();
	exit;
case "delete":
	if (!$session->getAuth()->isAllowed("users_delete")) {
		$ui->viewLogin();
		exit;
	}
	$userid = intval($_REQUEST["userid"]);
	$session->getStorage()->delUser($userid);
	$ui->redirect();
	exit;
default:
	$users = $session->getStorage()->getUserList();
	$ui->viewUserList($users);
	exit;
}

?>