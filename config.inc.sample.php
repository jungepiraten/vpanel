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
require_once(VPANEL_MITGLIEDERFILTERACTIONS . "/delete.class.php");
require_once(VPANEL_MITGLIEDERFILTERACTIONS . "/sendmail.class.php");
require_once(VPANEL_MITGLIEDERFILTERACTIONS . "/export.class.php");
require_once(VPANEL_MITGLIEDERFILTERACTIONS . "/statistik.class.php");
require_once(VPANEL_MITGLIEDERFILTERACTIONS . "/setbeitrag.class.php");

class MyConfig extends DefaultConfig {}

$config = new MyConfig;
$config->setStorage(new MySQLStorage("localhost", "root", "anything92", "vpanel"));
$config->setSendMailBackend(new SleepSendMailBackend());
$config->registerLang("de", new PHPLanguage(VPANEL_LANGUAGE . "/de.lang.php"));

require_once(dirname(__FILE__) . "/config.page.php");

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

$config->getStorage()->registerMitgliederFilterAction(new DeleteMitgliederFilterAction(1));
$config->getStorage()->registerMitgliederFilterAction(new SendMailMitgliederFilterAction(2));
$config->getStorage()->registerMitgliederFilterAction(new ExportMitgliederFilterAction(3, new CSVTempFileExportStreamHandler()));
$config->getStorage()->registerMitgliederFilterAction(new StatistikMitgliederFilterAction(4));
$config->getStorage()->registerMitgliederFilterAction(new SetBeitragMitgliederFilterAction(5));

?>
