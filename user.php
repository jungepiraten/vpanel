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

function parseUserFormular($session, &$user = null) {
	$username = $session->getVariable("username");
	$password = $session->getVariable("password");

	if ($user == null) {
		$user = new User($session->getStorage());
	}

	$user->setUsername($username);
	if (!empty($password)) {
		$user->changePassword($password);
	}
	$user->save();
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "addrole":
	if (!$session->isAllowed("users_modify")) {
		$ui->viewLogin();
		exit;
	}

	$user = $session->getStorage()->getUser($session->getIntVariable("userid"));
	$user->addRoleID($session->getIntVariable("roleid"));
	$user->save();
	$ui->redirect();
	exit;
case "delrole":
	if (!$session->isAllowed("users_modify")) {
		$ui->viewLogin();
		exit;
	}

	$user = $session->getStorage()->getUser($session->getIntVariable("userid"));
	$user->delRoleID($session->getIntVariable("roleid"));
	$user->save();
	$ui->redirect();
	exit;
case "details":
	$userid = $session->getIntVariable("userid");
	$user = $session->getStorage()->getUser($userid);

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("users_modify")) {
			$ui->viewLogin();
			exit;
		}

		parseUserFormular($session, $user);
	}
	$roles = $session->getStorage()->getRoleList();

	$ui->viewUserDetails($user, $roles);
	exit;
case "create":
	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("users_create")) {
			$ui->viewLogin();
			exit;
		}

		parseUserFormular($session, $user);

		$ui->redirect($session->getLink("users_details", $user->getUserID()));
	}

	$ui->viewUserCreate();
	exit;
case "delete":
	if (!$session->isAllowed("users_delete")) {
		$ui->viewLogin();
		exit;
	}
	$userid = $session->getIntVariable("userid");
	$session->getStorage()->delUser($userid);
	$ui->redirect();
	exit;
default:
	$users = $session->getStorage()->getUserList();
	$ui->viewUserList($users);
	exit;
}

?>
