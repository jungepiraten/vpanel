<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("users_show")) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/user.class.php");
require_once(VPANEL_CORE . "/role.class.php");
echo $_REQUEST["mode"];
switch ($_REQUEST["mode"]) {
case "addrole":
	if (!$session->isAllowed("users_modify")) {
		$ui->viewLogin();
		exit;
	}
	$userid = intval($_REQUEST["userid"]);
	$roleid = intval($_REQUEST["roleid"]);
	$user = $session->getStorage()->getUser($userid);
	$user->addRoleID($roleid);
	$user->save();
	$ui->redirect();
	exit;
case "delrole":
	if (!$session->isAllowed("users_modify")) {
		$ui->viewLogin();
		exit;
	}
	$userid = intval($_REQUEST["userid"]);
	$roleid = intval($_REQUEST["roleid"]);
	$user = $session->getStorage()->getUser($userid);
	$user->delRoleID($roleid);
	$user->save();
	$ui->redirect();
	exit;
case "details":
	$userid = intval($_REQUEST["userid"]);
	$user = $session->getStorage()->getUser($userid);

	if (isset($_REQUEST["save"])) {
		if (!$session->isAllowed("users_modify")) {
			$ui->viewLogin();
			exit;
		}

		$username = stripslashes($_REQUEST["username"]);
		$password = stripslashes($_REQUEST["password"]);

		$user->setUserID($userid);
		$user->setUsername($username);
		if (!empty($password)) {
			$user->changePassword($password);
		}
		$user->save();
    }
	$roles = $session->getStorage()->getRoleList();

	$ui->viewUserDetails($user, $roles);
	exit;
case "create":
	if (isset($_REQUEST["save"])) {
		if (!$session->isAllowed("users_create")) {
			$ui->viewLogin();
			exit;
		}
		$username = stripslashes($_REQUEST["username"]);
		$password = stripslashes($_REQUEST["password"]);

		$user = new User($session->getStorage());
		$user->setUsername($username);
		$user->setPassword($password);
		$user->save();

		$ui->redirect($session->getLink("users_details", $user->getUserID()));
	}

	$ui->viewUserCreate();
	exit;
case "delete":
	if (!$session->isAllowed("users_delete")) {
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
