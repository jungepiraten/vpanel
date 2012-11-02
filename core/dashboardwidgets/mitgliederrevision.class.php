<?php

require_once(VPANEL_CORE . "/dashboardwidget.class.php");

class MitgliederRevisionTimelineDashboardWidget extends DashboardWidget {
	public static function factory(Storage $storage, $row) {
		$static = new MitgliederRevisionTimelineDashboardWidget($storage);
		return $static;
	}
}

?>
