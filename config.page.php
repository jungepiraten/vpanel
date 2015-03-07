<?php

$config->registerPage("index", "index.php");
$config->registerPage("dashboard_widget_json", "json/dashboard_widget.php?widgetid=%d");
$config->registerPage("dashboard_widget_del", "index.php?delWidget&widgetid=%d");

$config->registerPage("login", "login.php");
$config->registerPage("logout", "login.php?logout=1");
$config->registerPage("einstellungen", "settings.php");

$config->registerPage("users", "user.php");
$config->registerPage("users_create", "user.php?mode=create");
$config->registerPage("users_details", "user.php?mode=details&userid=%d");
$config->registerPage("users_addrole", "user.php?mode=addrole&userid=%d&roleid=%d");
$config->registerPage("users_delrole", "user.php?mode=delrole&userid=%d&roleid=%d");

$config->registerPage("roles", "roles.php");
$config->registerPage("roles_create", "roles.php?mode=create");
$config->registerPage("roles_details", "roles.php?mode=details&roleid=%d");
$config->registerPage("roles_del", "roles.php?mode=delete&roleid=%d");
$config->registerPage("roles_adduser", "user.php?mode=addrole&roleid=%d");
$config->registerPage("roles_deluser", "user.php?mode=delrole&roleid=%d&userid=%d");

$config->registerPage("dokumente", "documents.php");
$config->registerPage("dokumente_page", "documents.php?gliederungid=%d&kategorieid=%d&statusid=%d&dokumentsuche=%s&page=%d");
$config->registerPage("dokumente_del", "documents.php?mode=delete&dokumentid=%d");
$config->registerPage("dokumente_json", "json/dokumente.php");
$config->registerPage("dokumente_create", "documents.php?mode=create&dokumenttemplateid=%d");
$config->registerPage("dokumente_details", "documents.php?mode=details&dokumentid=%d");
$config->registerPage("dokumente_mitglied", "member_document.php?mode=add&dokumentid=%d");
$config->registerPage("dokumente_mitglied_create", "members.php?mode=create&dokumentid=%d&mitgliedtemplateid=%d");
$config->registerPage("dokumente_get", "file.php?mode=get&dokumentid=%d");
$config->registerPage("dokumente_view", "file.php?mode=view&dokumentid=%d");
$config->registerPage("dokumente_transitionaction", "documents.php?mode=transition&transitionid=%s&filterid=%s");
$config->registerPage("dokumente_transitionprocess", "documents.php?mode=transitionprocess&transitionid=%s&processid=%d");
$config->registerPage("dokumente_timeline_json", "json/dokumentrevisions_timeline.php");
$config->registerPage("dokumentrevision_get", "file.php?mode=get&dokumentrevisionid=%d");

$config->registerPage("mitglieddokument", "member_document.php");
$config->registerPage("mitglieddokument_delete", "member_document.php?mode=delete&mitgliedid=%d&dokumentid=%d");

$config->registerPage("orte_json", "json/orte.php");

$config->registerPage("mitglieder", "members.php");
$config->registerPage("mitglieder_json", "json/mitglieder.php");
$config->registerPage("mitglieder_page", "members.php?filterid=%s&page=%d");
$config->registerPage("mitglieder_create", "members.php?mode=create&mitgliedtemplateid=%d");
$config->registerPage("mitglieder_details", "members.php?mode=details&mitgliedid=%d");
$config->registerPage("mitglieder_details_revision", "members.php?mode=details&revisionid=%d");
$config->registerPage("mitglieder_bouncelist", "emails.php?mode=listbounces&mitgliederrevisionid=%d");
$config->registerPage("mitglieder_dokument", "member_document.php?mode=add&mitgliedid=%d");
$config->registerPage("mitglieder_beitraege", "members.php?mode=beitraege&mitgliedid=%d");
$config->registerPage("mitglieder_beitraege_del", "members.php?mode=beitragdelete&mitgliedbeitragid=%d");
$config->registerPage("mitglieder_beitraege_buchungen", "members.php?mode=beitraege_buchungen&mitgliedbeitragid=%d");
$config->registerPage("mitglieder_beitraege_buchungen_timeline_json", "json/mitgliederbeitragbuchung_timeline.php");
$config->registerPage("mitglieder_beitraege_buchungen_del", "members.php?mode=beitraege_buchungen_delete&buchungid=%d");
$config->registerPage("mitglieder_filteraction", "members.php?mode=filteraction&actionid=%s&filterid=%s");
$config->registerPage("mitglieder_filterprocess", "members.php?mode=filterprocess&actionid=%s&processid=%d");
$config->registerPage("mitglieder_composefilter", "members.php?mode=composefilter");
$config->registerPage("mitglieder_revision_timeline_json", "json/mitgliederrevisions_timeline.php");

$config->registerPage("emailbounces_delete", "emails.php?mode=delbounce&bounceid=%d");

$config->registerPage("beitraege", "fees.php");
$config->registerPage("beitraege_create", "fees.php?mode=create");
$config->registerPage("beitraege_details", "fees.php?mode=details&beitragid=%d");
$config->registerPage("beitraege_details_page", "fees.php?mode=details&beitragid=%d&page=%d");
$config->registerPage("beitraege_del", "fees.php?mode=delete&beitragid=%d");

$config->registerPage("mailtemplates", "mailtemplates.php");
$config->registerPage("mailtemplates_gliederungid", "mailtemplates.php?gliederungid=%d");
$config->registerPage("mailtemplates_create", "mailtemplates.php?mode=create");
$config->registerPage("mailtemplates_create_gliederungid", "mailtemplates.php?mode=create&gliederungid=%d");
$config->registerPage("mailtemplates_details", "mailtemplates.php?mode=details&templateid=%d");
$config->registerPage("mailtemplates_del", "mailtemplates.php?mode=delete&templateid=%d");

$config->registerPage("mailtemplateattachment_create", "mailtemplates.php?mode=createattachment&templateid=%d");
$config->registerPage("mailtemplateattachment_delete", "mailtemplates.php?mode=deleteattachment&templateid=%d&fileid=%d");
$config->registerPage("mailtemplateattachment_get", "file.php?mode=get&mailtemplateid?%d&fileid=%d");

$config->registerPage("stats", "stats.php");

$config->registerPage("file_tokenget", "file.php?mode=get&fileid=%d&token=%s");

$config->registerPage("tempfile_get", "file.php?mode=get&tempfileid=%d");

$config->registerPage("processes_view", "processes.php?mode=view&processid=%d");
$config->registerPage("processes_timeline_json", "json/processes_timeline.php");
$config->registerPage("processes_json", "json/processes.php");

?>
