<?php

class MitgliederBadge {
	private $badgeid;
	private $label;
	private $matcher;
	private $color;

	public function __construct($badgeid, $label, $color, $matcher) {
		$this->badgeid = $badgeid;
		$this->label = $label;
		$this->color = $color;
		$this->matcher = $matcher;
	}

	public function getBadgeID() {
		return $this->badgeid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getColor() {
		return $this->color;
	}

	public function getMatcher() {
		return $this->matcher;
	}

	public function match($mitglied) {
		return $this->matcher->match($mitglied);
	}
}

?>
