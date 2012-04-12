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
	$aktiv = $session->getBoolVariable("aktiv");
	$defaultgliederungid = $session->getVariable("defaultgliederungid");
	$defaultdokumentkategorieid = $session->getVariable("defaultdokumentkategorieid");
	$defaultdokumentstatusid = $session->getVariable("defaultdokumentstatusid");
	$generateapikey = $session->hasVariable("apikey") && $session->getVariable("apikey") == "generate";
	$removeapikey = $session->hasVariable("apikey") && $session->getVariable("apikey") == "remove";

	if ($user == null) {
		$user = new User($session->getStorage());
	}

	$user->setUsername($username);
	if (!empty($password)) {
		$user->changePassword($password);
	}
	if ($generateapikey) {
		$user->generateAPIKey();
	}
	if ($removeapikey) {
		$user->unsetAPIKey();
	}
	$user->setAktiv($aktiv);
	$user->setDefaultGliederungID($defaultgliederungid);
	$user->setDefaultDokumentKategorieID($defaultdokumentkategorieid);
	$user->setDefaultDokumentStatusID($defaultdokumentstatusid);
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
	$gliederungen = $session->getStorage()->getGliederungList();
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();

	$ui->viewUserDetails($user, $roles, $gliederungen, $dokumentkategorien, $dokumentstatuslist);
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
	$gliederungen = $session->getStorage()->getGliederungList();
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();

	$ui->viewUserCreate($gliederungen, $dokumentkategorien, $dokumentstatuslist);
	exit;
default:
	$users = $session->getStorage()->getUserList();
	$ui->viewUserList($users);
	exit;
}

?>
