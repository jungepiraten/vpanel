<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("stats_show")) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/mitgliedermatcher/logic.class.php");
require_once(VPANEL_CORE . "/mitgliedermatcher/state.class.php");
require_once(VPANEL_CORE . "/mitgliedermatcher/mitgliedschaft.class.php");
require_once(VPANEL_CORE . "/mitgliedermatcher/ausgetreten.class.php");

$storage = $session->getStorage();

$matcher = new AndMitgliederMatcher(new GliederungMitgliederMatcher($session->getAllowedGliederungIDs("mitglieder_show")),
                                    new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()) );

$mitgliedercount = $storage->getMitgliederCount($matcher);

$mitgliedschaften = $storage->getMitgliedschaftList();
$countPerMitgliedschaft = array();
foreach ($storage->getMitgliedschaftList() as $mitgliedschaft) {
	$count = $storage->getMitgliederCount(new AndMitgliederMatcher($matcher, new MitgliedschaftMitgliederMatcher($mitgliedschaft->getMitgliedschaftID())));
	$countPerMitgliedschaft[$mitgliedschaft->getMitgliedschaftID()] = $count;
}
		
$states = $storage->getStateList();
$countPerState = array();
foreach ($states as $state) {
	$count = $storage->getMitgliederCount(new AndMitgliederMatcher($matcher, new StateMitgliederMatcher($state->getStateID())));
	$countPerState[$state->getStateID()] = $count;
}

$ui->viewStats($mitgliedercount, $mitgliedschaften, $countPerMitgliedschaft, $states, $countPerState);

?>
