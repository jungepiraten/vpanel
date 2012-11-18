<?php

require_once(dirname(__FILE__) . "/config.default.php");
require_once(VPANEL_UI . "/language.class.php");
require_once(VPANEL_STORAGE . "/mysql.class.php");
require_once(VPANEL_SENDMAILBACKEND . "/sleep.class.php");
require_once(VPANEL_CORE . "/mitgliederfilter.class.php");
foreach (glob(VPANEL_MITGLIEDERMATCHER . "/*.class.php") as $matcherfile) {
	require_once($matcherfile);
}
foreach (glob(VPANEL_DOKUMENTTEMPLATES . "/*.class.php") as $templatefile) {
	require_once($templatefile);
}
foreach (glob(VPANEL_DOKUMENTTRANSITIONEN . "/*.class.php") as $transitionfile) {
	require_once($transitionfile);
}
foreach (glob(VPANEL_MITGLIEDERFILTERACTIONS . "/*.class.php") as $actionfile) {
	require_once($actionfile);
}

class MyConfig extends DefaultConfig {
	public function getWebRoot() {
		return "http://192.168.100.166/~prauscher/vpanel/";
	}
}

$config = new MyConfig;
$config->setStorage(new MySQLStorage("localhost", "root", "anything92", "vpanel"));
$config->setSendMailBackend(new SleepSendMailBackend());
$config->registerLang("de", new PHPLanguage(VPANEL_LANGUAGE . "/de.lang.php"));

require_once(dirname(__FILE__) . "/config.page.php");

$filterid = 0;

$gliederungen = $config->getStorage()->getGliederungList();
$mitgliedschaften = $config->getStorage()->getMitgliedschaftList();
$beitraege = $config->getStorage()->getBeitragList();

$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(++$filterid, "Mitgliedschaft Piratenpartei", null,
	new RevisionFlagMitgliederMatcher(1) ));

foreach ($gliederungen as $gliederung) {
	$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(++$filterid, "Mitglied im " . $gliederung->getLabel(), $gliederung->getGliederungID(), new TrueMitgliederMatcher()));
}

$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(++$filterid, "Momentane Mitglieder", null,
	new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()) ));
$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(++$filterid, "Ausgetretene Mitglieder", null,
	new AusgetretenMitgliederMatcher() ));

foreach ($mitgliedschaften as $mitgliedschaft) {
	$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(++$filterid, $mitgliedschaft->getLabel(), null,
		new MitgliedschaftMitgliederMatcher($mitgliedschaft->getMitgliedschaftID()) ));
}

$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(++$filterid, "Mitglieder mit Schulden", null,
	new BeitragMissingAboveMitgliederMatcher(0) ));
$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(++$filterid, "Mitglieder ohne Schulden", null,
	new BeitragMissingBelowMitgliederMatcher(0) ));

foreach ($beitraege as $beitrag) {
	$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(++$filterid, "Beitrag ".$beitrag->getLabel()." nicht bezahlt", null,
		new BeitragMissingMitgliederMatcher($beitrag->getBeitragID()) ));
}

$states = $config->getStorage()->getStateList();
foreach ($states as $state) {
	$config->getStorage()->registerMitgliederFilter(new MitgliederFilter(++$filterid, "Mitglieder aus " . $state->getLabel(), null,
		new StateMitgliederMatcher($state->getStateID()) ));
}

$dokumenttemplateid = 0;

$config->getStorage()->registerDokumentTemplate(new MitgliedDokumentTemplate(++$dokumenttemplateid, "Neuer Brief eingehend", true,
	1, 1, 2, "BA_", "Brief Eingehend", "Ymd_"));
$config->getStorage()->registerDokumentTemplate(new MitgliedDokumentTemplate(++$dokumenttemplateid, "Neuer Brief ausgehend", true,
	1, 2, 2, "BE_", "Brief Ausgehend", "Ymd_"));

$actionid = 0;

$config->getStorage()->registerMitgliederFilterAction(new DeleteMitgliederFilterAction(++$actionid));
$config->getStorage()->registerMitgliederFilterAction(new SendMailMitgliederFilterAction(++$actionid));
$config->getStorage()->registerMitgliederFilterAction(new ExportMitgliederFilterAction(++$actionid, new CSVTempFileExportStreamHandler()));
$config->getStorage()->registerMitgliederFilterAction(new StatistikMitgliederFilterAction(++$actionid));
$config->getStorage()->registerMitgliederFilterAction(new SetBeitragMitgliederFilterAction(++$actionid));
$config->getStorage()->registerMitgliederFilterAction(new CalculateBeitragMitgliederFilterAction(++$actionid));

$transitionid = 0;

$config->getStorage()->registerDokumentTransition(new DownloadDokumentTransition(++$transitionid, false, null, null, 6, null, 4, "Gesammelt ausgedruckt"));
$config->getStorage()->registerDokumentTransition(new MitgliedLinkDokumentTransition(++$transitionid, false, null, null, null, null, null, "Mitglied verlinkt"));
foreach ($config->getStorage()->getMitgliederTemplateList() as $mitgliedtemplate) {
	$config->getStorage()->registerDokumentTransition(new MitgliedCreateDokumentTransition(++$transitionid, false, null, null, null, null, null, "Mitglied angelegt", $mitgliedtemplate->getMitgliedTemplateID(), $mitgliedtemplate->getLabel() . " anlegen"));
}

?>
