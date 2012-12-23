<?php

$config->registerPage("index", "index.php");
$config->registerPage("dashboard_widget_json", "json/dashboard_widget.php?widgetid=%d");
$config->registerPage("dashboard_widget_del", "index.php?delWidget&widgetid=%d");

$config->registerPage("login", "login.php");
$config->registerPage("logout", "login.php?logout=1");
$config->registerPage("einstellungen", "einstellungen.php");

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

$config->registerPage("dokumente", "dokumente.php");
$config->registerPage("dokumente_page", "dokumente.php?gliederungid=%d&kategorieid=%d&statusid=%d&page=%d");
$config->registerPage("dokumente_del", "dokumente.php?mode=delete&dokumentid=%d");
$config->registerPage("dokumente_json", "json/dokumente.php");
$config->registerPage("dokumente_create", "dokumente.php?mode=create&dokumenttemplateid=%d");
$config->registerPage("dokumente_details", "dokumente.php?mode=details&dokumentid=%d");
$config->registerPage("dokumente_mitglied", "mitglieddokument.php?mode=add&dokumentid=%d");
$config->registerPage("dokumente_mitglied_create", "mitglieder.php?mode=create&dokumentid=%d&mitgliedtemplateid=%d");
$config->registerPage("dokumente_get", "file.php?mode=get&dokumentid=%d");
$config->registerPage("dokumente_view", "file.php?mode=view&dokumentid=%d");
$config->registerPage("dokumente_transitionaction", "dokumente.php?mode=transition&transitionid=%s&filterid=%s");
$config->registerPage("dokumente_transitionprocess", "dokumente.php?mode=transitionprocess&transitionid=%s&processid=%d");
$config->registerPage("dokumente_timeline_json", "json/dokumentnotizen_timeline.php");

$config->registerPage("mitglieddokument", "mitglieddokument.php");
$config->registerPage("mitglieddokument_delete", "mitglieddokument.php?mode=delete&mitgliedid=%d&dokumentid=%d");

$config->registerPage("orte_json", "json/orte.php");

$config->registerPage("mitglieder", "mitglieder.php");
$config->registerPage("mitglieder_json", "json/mitglieder.php");
$config->registerPage("mitglieder_page", "mitglieder.php?filterid=%s&page=%d");
$config->registerPage("mitglieder_create", "mitglieder.php?mode=create&mitgliedtemplateid=%d");
$config->registerPage("mitglieder_details", "mitglieder.php?mode=details&mitgliedid=%d");
$config->registerPage("mitglieder_details_revision", "mitglieder.php?mode=details&revisionid=%d");
$config->registerPage("mitglieder_bouncelist", "emails.php?mode=listbounces&mitgliederrevisionid=%d");
$config->registerPage("mitglieder_dokument", "mitglieddokument.php?mode=add&mitgliedid=%d");
$config->registerPage("mitglieder_beitraege", "mitglieder.php?mode=beitraege&mitgliedid=%d");
$config->registerPage("mitglieder_beitraege_del", "mitglieder.php?mode=beitragdelete&mitgliedbeitragid=%d");
$config->registerPage("mitglieder_beitraege_buchungen", "mitglieder.php?mode=beitraege_buchungen&mitgliedbeitragid=%d");
$config->registerPage("mitglieder_beitraege_buchungen_timeline_json", "json/mitgliederbeitragbuchung_timeline.php");
$config->registerPage("mitglieder_beitraege_buchungen_del", "mitglieder.php?mode=beitraege_buchungen_delete&buchungid=%d");
$config->registerPage("mitglieder_filteraction", "mitglieder.php?mode=filteraction&actionid=%s&filterid=%s");
$config->registerPage("mitglieder_filterprocess", "mitglieder.php?mode=filterprocess&actionid=%s&processid=%d");
$config->registerPage("mitglieder_composefilter", "mitglieder.php?mode=composefilter");
$config->registerPage("mitglieder_revision_timeline_json", "json/mitgliederrevisions_timeline.php");

$config->registerPage("emailbounces_delete", "emails.php?mode=delbounce&bounceid=%d");

$config->registerPage("beitraege", "beitraege.php");
$config->registerPage("beitraege_create", "beitraege.php?mode=create");
$config->registerPage("beitraege_details", "beitraege.php?mode=details&beitragid=%d");
$config->registerPage("beitraege_details_page", "beitraege.php?mode=details&beitragid=%d&page=%d");
$config->registerPage("beitraege_del", "beitraege.php?mode=delete&beitragid=%d");

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
$config->registerPage("processes_json", "json/processes.php");

?>
