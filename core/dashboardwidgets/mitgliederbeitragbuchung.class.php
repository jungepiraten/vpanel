<?php

require_once(VPANEL_CORE . "/dashboardwidget.class.php");

class MitgliederBeitragBuchungTimelineDashboardWidget extends DashboardWidget {
	public static function factory(Storage $storage, $row) {
		$static = new MitgliederBeitragBuchungTimelineDashboardWidget($storage);
		return $static;
	}
}

?>
