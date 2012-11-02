<?php

require_once(VPANEL_CORE . "/storageobject.class.php");
foreach (glob(VPANEL_CORE . "/dashboardwidgets/*.class.php") as $widgetfile) {
	require_once($widgetfile);
}

abstract class DashboardWidget extends StorageClass {
	private $widgetid;
	private $userid;
	private $column;

	private $user;

	public static function factory(Storage $storage, $row) {
		$widget = $row["type"]::factory($storage, unserialize($row["typedata"]));
		$widget->setWidgetID($row["widgetid"]);
		$widget->setUserID($row["userid"]);
		$widget->setColumn($row["column"]);
		return $widget;
	}

	public function getWidgetID() {
		return $this->widgetid;
	}

	public function setWidgetID($widgetid) {
		$this->widgetid = $widgetid;
	}

	public function getUserID() {
		return $this->userid;
	}

	public function setUserID($userid) {
		if ($this->userid == $userid) {
			$this->user = null;
		}
		$this->userid = $userid;
	}

	public function getUser() {
		if ($this->user == null) {
			$this->user = $this->getStorage()->getUser($this->getUserID());
		}
		return $this->user;
	}

	public function setUser($user) {
		$this->setUserID($user->getUserID());
		$this->user = $user;
	}

	public function getColumn() {
		return $this->column;
	}

	public function setColumn($column) {
		$this->column = $column;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setWidgetID( $storage->setDashboardWidget(
			$this->getWidgetID(),
			$this->getUserID(),
			$this->getColumn(),
			get_class($this),
			serialize($this->getData()) ));
	}

	public function delete(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$storage->delDashboardWidget($this->getWidgetID());
	}

	protected function getData() {
		return array();
	}
}

?>
