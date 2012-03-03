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
$config->registerPage("einstellungen", "einstellungen.php");

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
$config->registerPage("dokumente_page", "dokumente.php?kategorieid=%d&statusid=%d&page=%d");
$config->registerPage("dokumente_json", "json/dokumente.php");
$config->registerPage("dokumente_create", "dokumente.php?mode=create");
$config->registerPage("dokumente_details", "dokumente.php?mode=details&dokumentid=%d");
$config->registerPage("dokumente_mitglied", "mitglieddokument.php?mode=add&dokumentid=%d");
$config->registerPage("dokumente_mitglied_create", "mitglieder.php?mode=create&dokumentid=%d");
$config->registerPage("dokumente_get", "file.php?mode=get&dokumentid=%d");
$config->registerPage("dokumente_view", "file.php?mode=view&dokumentid=%d");

$config->registerPage("mitglieddokument", "mitglieddokument.php");
$config->registerPage("mitglieddokument_delete", "mitglieddokument.php?mode=delete&mitgliedid=%d&dokumentid=%d");

$config->registerPage("orte_json", "json/orte.php");

$config->registerPage("mitglieder", "mitglieder.php");
$config->registerPage("mitglieder_json", "json/mitglieder.php");
$config->registerPage("mitglieder_page", "mitglieder.php?filterid=%s&page=%d");
$config->registerPage("mitglieder_create", "mitglieder.php?mode=create");
$config->registerPage("mitglieder_details", "mitglieder.php?mode=details&mitgliedid=%d");
$config->registerPage("mitglieder_details_revision", "mitglieder.php?mode=details&revisionid=%d");
$config->registerPage("mitglieder_bouncelist", "emails.php?mode=listbounces&mitgliederrevisionid=%d");
$config->registerPage("mitglieder_dokument", "mitglieddokument.php?mode=add&mitgliedid=%d");
$config->registerPage("mitglieder_del", "mitglieder.php?mode=delete&filterid=%s");
$config->registerPage("mitglieder_beitraege", "mitglieder.php?mode=beitraege&mitgliedid=%d");
$config->registerPage("mitglieder_beitraege_del", "mitglieder.php?mode=beitragdelete&mitgliedid=%d&beitragid=%d");
$config->registerPage("mitglieder_sendmail.select", "mitglieder.php?mode=sendmail.select&filterid=%s");
$config->registerPage("mitglieder_sendmail.preview", "mitglieder.php?mode=sendmail.preview");
$config->registerPage("mitglieder_sendmail.send", "mitglieder.php?mode=sendmail.send&filterid=%s&templateid=%d");
$config->registerPage("mitglieder_export.options", "mitglieder.php?mode=export.options&filterid=%s");
$config->registerPage("mitglieder_export.export", "mitglieder.php?mode=export.export");
$config->registerPage("mitglieder_statistik.start", "mitglieder.php?mode=statistik.start&filterid=%s");
$config->registerPage("mitglieder_statistik", "mitglieder.php?mode=statistik&statistikid=%d");
$config->registerPage("mitglieder_statistik_get_agegraph", "file.php?mode=get&part=agegraph&statistikid=%d");
$config->registerPage("mitglieder_statistik_get_timegraph", "file.php?mode=get&part=timegraph&statistikid=%d");
$config->registerPage("mitglieder_statistik_get_timebalancegraph", "file.php?mode=get&part=timebalancegraph&statistikid=%d");
$config->registerPage("mitglieder_statistik_get_gliederungchart", "file.php?mode=get&part=gliederungchart&statistikid=%d");
$config->registerPage("mitglieder_statistik_get_statechart", "file.php?mode=get&part=statechart&statistikid=%d");
$config->registerPage("mitglieder_statistik_get_mitgliedschaftchart", "file.php?mode=get&part=mitgliedschaftchart&statistikid=%d");
$config->registerPage("mitglieder_setbeitrag.selectbeitrag", 
"mitglieder.php?mode=setbeitrag.selectbeitrag&filterid=%s");
$config->registerPage("mitglieder_setbeitrag.start", "mitglieder.php?mode=setbeitrag.start");

$config->registerPage("emailbounces_delete", "emails.php?mode=delbounce&bounceid=%d");

$config->registerPage("beitraege", "beitraege.php");
$config->registerPage("beitraege_create", "beitraege.php?mode=create");
$config->registerPage("beitraege_details", "beitraege.php?mode=details&beitragid=%d");
$config->registerPage("beitraege_details_page", "beitraege.php?mode=details&beitragid=%d&page=%d");
$config->registerPage("beitraege_del", "beitraege.php?mode=delete&beitragid=%d");

$config->registerPage("mailtemplates", "mailtemplates.php");
$config->registerPage("mailtemplates_create", "mailtemplates.php?mode=create");
$config->registerPage("mailtemplates_details", "mailtemplates.php?mode=details&templateid=%d");
$config->registerPage("mailtemplates_del", "mailtemplates.php?mode=delete&templateid=%d");

$config->registerPage("mailtemplateattachment_create", "mailtemplates.php?mode=createattachment&templateid=%d");
$config->registerPage("mailtemplateattachment_delete", "mailtemplates.php?mode=deleteattachment&templateid=%d&fileid=%d");
$config->registerPage("mailtemplateattachment_get", "file.php?mode=get&mailtemplateid?%d&fileid=%d");

$config->registerPage("statistik", "statistik.php");

$config->registerPage("file_tokenget", "file.php?mode=get&fileid=%d&token=%s");

$config->registerPage("tempfile_get", "file.php?mode=get&tempfileid=%d");

$config->registerPage("processes_view", "processes.php?mode=view&processid=%d");
$config->registerPage("processes_json", "json/processes.php");
#$config->registerPage("");

$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(1, "Ordentliche Mitglieder", null,
	new AndMitgliederMatcher(
		new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()),
		new MitgliedschaftMitgliederMatcher(1) ) ));
$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(2, "Fördermitglieder", null,
	new AndMitgliederMatcher(
		new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()),
		new MitgliedschaftMitgliederMatcher(2) ) ));
$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(3, "Ehrenmitglieder", null,
	new AndMitgliederMatcher(
		new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()),
		new MitgliedschaftMitgliederMatcher(3) ) ));
$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(4, "Natürliche Personen", null,
	new AndMitgliederMatcher(
		new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()),
		new NatPersonMitgliederMatcher() ) ));
$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(5, "Juristische Personen", null,
	new AndMitgliederMatcher(
		new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()),
		new JurPersonMitgliederMatcher() ) ));
$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(6, "Momentane Mitglieder", null,
	new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()) ));
$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(7, "Ausgetretene Mitglieder", null,
	new AusgetretenMitgliederMatcher() ));

?>
