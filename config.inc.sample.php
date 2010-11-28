<?php

require_once(dirname(__FILE__) . "/config.default.php");
require_once(VPANEL_UI . "/language.class.php");

class MyConfig extends DefaultConfig {}

$config = new MyConfig;
$config->setStorage(new MySQLStorage("localhost", "root", "anything92", "vpanel"));
$config->registerLang("de", new PHPLanguage(VPANEL_LANGUAGE . "/de.lang.php"));
$config->registerLang("dummy", new EmptyLanguage);

$config->registerPage("index", "index.php");
$config->registerPage("login", "login.php");
$config->registerPage("logout", "login.php?logout");

$config->registerPage("users", "user.php");
$config->registerPage("users_create", "user.php?mode=create");
$config->registerPage("users_details", "user.php?mode=details&userid=%d");
$config->registerPage("users_del", "user.php?mode=delete&userid=%d");
$config->registerPage("users_addrole", "user.php?mode=addrole&userid=%d");
$config->registerPage("users_delrole", "user.php?mode=delrole&userid=%d&roleid=%d");

$config->registerPage("roles", "roles.php");
$config->registerPage("roles_create", "roles.php?mode=create");
$config->registerPage("roles_details", "roles.php?mode=details&roleid=%d");
$config->registerPage("roles_del", "roles.php?mode=delete&roleid=%d");
$config->registerPage("roles_adduser", "user.php?mode=addrole&roleid=%d");
$config->registerPage("roles_deluser", "user.php?mode=delrole&roleid=%d&userid=%d");

$config->registerPage("mitglieder", "mitglieder.php");
$config->registerPage("mitglieder_page", "mitglieder.php?page=%d");
$config->registerPage("mitglieder_create", "mitglieder.php?mode=create&mitgliedschaftid=%d");
$config->registerPage("mitglieder_details", "mitglieder.php?mode=details&mitgliedid=%d");
$config->registerPage("mitglieder_del", "mitglieder.php?mode=delete&mitgliedid=%d");

$config->registerPage("statistik", "statistik.php");
#$config->registerPage("");

?>
