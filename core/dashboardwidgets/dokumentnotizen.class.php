<?php

require_once(VPANEL_CORE . "/dashboardwidget.class.php");

class DokumentNotizenTimelineDashboardWidget extends DashboardWidget {
	public static function factory(Storage $storage, $row) {
		$static = new DokumentNotizenTimelineDashboardWidget($storage);
		return $static;
	}
}

?>
