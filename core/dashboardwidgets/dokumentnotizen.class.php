<?php

require_once(VPANEL_CORE . "/dashboardwidgets/table.class.php");

class DokumentNotizenTimelineDashboardWidget extends TableDashboardWidget {
	public static function factory(Storage $storage, $row) {
		$widget = new DokumentNotizenTimelineDashboardWidget($storage);
		$widget->init($row);
		return $widget;
	}
}

?>
