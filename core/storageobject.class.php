<?php

abstract class StorageClass {
	private $storage;

	public function __construct(Storage $storage) {
		$this->setStorage($storage);
	}

	public function setStorage(Storage $storage) {
		$this->storage = $storage;
	}

	public function getStorage() {
		return $this->storage;
	}
}

?>
