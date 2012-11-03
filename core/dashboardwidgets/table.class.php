<?php

require_once(VPANEL_CORE . "/dashboardwidget.class.php");

abstract class TableDashboardWidget extends DashboardWidget {
	private $reload;

	public function init($row) {
		$this->setReload($row["reload"]);
	}

	public function hasReload() {
		return $this->reload !== null;
	}

	public function getReload() {
		return $this->reload;
	}

	public function setReload($reload) {
		$this->reload = $reload;
	}

	public function getData() {
		$data = parent::getData();
		$data["reload"] = $this->getReload();
		return $data;
	}
}

?>
