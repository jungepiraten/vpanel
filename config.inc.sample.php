<?php

require_once(dirname(__FILE__) . "/config.default.php");
require_once(VPANEL_UI . "/language.class.php");
require_once(VPANEL_STORAGE . "/mysql.class.php");
require_once(VPANEL_SENDMAILBACKEND . "/sleep.class.php");
require_once(VPANEL_CORE . "/mitgliederfilter.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/logic.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/mitgliedschaft.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/natperson.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/jurperson.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/ausgetreten.class.php");

class MyConfig extends DefaultConfig {}

$config = new MyConfig;
$config->setStorage(new MySQLStorage("localhost", "root", "anything92", "vpanel"));
$config->setSendMailBackend(new SleepSendMailBackend());
$config->registerLang("de", new PHPLanguage(VPANEL_LANGUAGE . "/de.lang.php"));
$config->registerLang("dummy", new EmptyLanguage);

$config->registerPage("index", "index.php");
$config->registerPage("login", "login.php");
$config->registerPage("logout", "login.php?logout=1");

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

$config->registerPage("dokumente", "dokumente.php");
$config->registerPage("dokumente_json", "json/dokumente.php");
$config->registerPage("dokumente_create", "dokumente.php?mode=create");
$config->registerPage("dokumente_create_kategoriestatus", "dokumente.php?mode=create&kategorieid=%s&statusid=%s");
$config->registerPage("dokumente_details", "dokumente.php?mode=details&dokumentid=%d");
$config->registerPage("dokumente_mitglied", "mitglieddokument.php?mode=add&dokumentid=%d");
$config->registerPage("dokumente_mitglied_create", "mitglieder.php?mode=create&dokumentid=%d");
$config->registerPage("dokumente_get", "file.php?mode=get&dokumentid=%d");

$config->registerPage("mitglieddokument", "mitglieddokument.php");
$config->registerPage("mitglieddokument_delete", "mitglieddokument.php?mode=delete&mitgliedid=%d&dokumentid=%d")

$config->registerPage("orte_json", "json/orte.php");

$config->registerPage("mitglieder", "mitglieder.php");
$config->registerPage("mitglieder_json", "json/mitglieder.php");
$config->registerPage("mitglieder_page", "mitglieder.php?page=%d");
$config->registerPage("mitglieder_create", "mitglieder.php?mode=create&mitgliedschaftid=%d");
$config->registerPage("mitglieder_details", "mitglieder.php?mode=details&mitgliedid=%d");
$config->registerPage("mitglieder_dokument", "mitglieddokument.php?mode=add&mitgliedid=%d");
$config->registerPage("mitglieder_del", "mitglieder.php?mode=delete&mitgliedid=%d");
$config->registerPage("mitglieder_sendmail.select", "mitglieder.php?mode=sendmail.select&filterid=%s");
$config->registerPage("mitglieder_sendmail.preview", "mitglieder.php?mode=sendmail.preview");
$config->registerPage("mitglieder_sendmail.send", "mitglieder.php?mode=sendmail.send");
$config->registerPage("mitglieder_sendmail.done", "mitglieder.php?mode=sendmail.done&processid=%d");
$config->registerPage("mitglieder_export.options", "mitglieder.php?mode=export.options&filterid=%s");
$config->registerPage("mitglieder_export.export", "mitglieder.php?mode=export.export");

$config->registerPage("mailtemplates", "mailtemplates.php");
$config->registerPage("mailtemplates_create", "mailtemplates.php?mode=create");
$config->registerPage("mailtemplates_details", "mailtemplates.php?mode=details&templateid=%d");
$config->registerPage("mailtemplates_del", "mailtemplates.php?mode=delete&templateid=%d");
$config->registerPage("mailattachment", "mailattachment.php?attachmentid=%d");

$config->registerPage("statistik", "statistik.php");

$config->registerPage("tempfile_get", "file.php?mode=get&tempfileid=%d");

$config->registerPage("processes_view", "processes.php?mode=view&processid=%d");
$config->registerPage("processes_json", "json/processes.php");
#$config->registerPage("");

$config->registerMitgliederFilter(new MitgliederFilter(1, "Ordentliche Mitglieder",
	new AndMitgliederMatcher(
		new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()),
		new MitgliedschaftMitgliederMatcher(1) ) ));
$config->registerMitgliederFilter(new MitgliederFilter(2, "Fördermitglieder",
	new AndMitgliederMatcher(
		new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()),
		new MitgliedschaftMitgliederMatcher(2) ) ));
$config->registerMitgliederFilter(new MitgliederFilter(3, "Ehrenmitglieder",
	new AndMitgliederMatcher(
		new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()),
		new MitgliedschaftMitgliederMatcher(3) ) ));
$config->registerMitgliederFilter(new MitgliederFilter(4, "Natürliche Personen",
	new AndMitgliederMatcher(
		new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()),
		new NatPersonMitgliederMatcher() ) ));
$config->registerMitgliederFilter(new MitgliederFilter(5, "Juristische Personen",
	new AndMitgliederMatcher(
		new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()),
		new JurPersonMitgliederMatcher() ) ));
$config->registerMitgliederFilter(new MitgliederFilter(6, "Momentane Mitglieder",
	new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()) ));
$config->registerMitgliederFilter(new MitgliederFilter(7, "Ausgetretene Mitglieder",
	new AusgetretenMitgliederMatcher() ));

?>
