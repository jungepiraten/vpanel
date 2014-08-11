<?php

require_once(VPANEL_CORE . "/dashboardwidgets/table.class.php");

class ProcessTimelineDashboardWidget extends TableDashboardWidget {
	public static function factory(Storage $storage, $row) {
		$widget = new ProcessTimelineDashboardWidget($storage);
		$widget->init($row);
		return $widget;
	}
}

?>
