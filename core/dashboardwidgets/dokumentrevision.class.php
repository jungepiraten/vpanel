<?php

require_once(VPANEL_CORE . "/dashboardwidgets/table.class.php");

class DokumentRevisionTimelineDashboardWidget extends TableDashboardWidget {
	public static function factory(Storage $storage, $row) {
		$widget = new DokumentRevisionTimelineDashboardWidget($storage);
		$widget->init($row);
		return $widget;
	}
}

?>
