<?php

require_once(VPANEL_CORE . "/dashboardwidgets/table.class.php");

class MitgliederRevisionTimelineDashboardWidget extends TableDashboardWidget {
	public static function factory(Storage $storage, $row) {
		$widget = new MitgliederRevisionTimelineDashboardWidget($storage);
		$widget->init($row);
		return $widget;
	}
}

?>
