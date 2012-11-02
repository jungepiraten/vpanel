<?php

require_once(VPANEL_CORE . "/dashboardwidget.class.php");

class StaticDashboardWidget extends DashboardWidget {
	private $text;

	public static function factory(Storage $storage, $row) {
		$static = new StaticDashboardWidget($storage);
		$static->setText($row["text"]);
		return $static;
	}

	public function getText() {
		return $this->text;
	}

	public function setText($text) {
		$this->text = $text;
	}

	public function getData() {
		$data = parent::getData();
		$data["text"] = $this->getText();
		return $data;
	}
}

?>
