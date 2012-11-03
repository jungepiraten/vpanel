<?php

require_once(VPANEL_CORE . "/dashboardwidgets/table.class.php");

class MitgliederBeitragBuchungTimelineDashboardWidget extends TableDashboardWidget {
	public static function factory(Storage $storage, $row) {
		$widget = new MitgliederBeitragBuchungTimelineDashboardWidget($storage);
		$widget->init($row);
		return $widget;
	}
}

?>
