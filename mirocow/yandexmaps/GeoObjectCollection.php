<?php
/**
 * mirocow\yandexmaps\GeoObjectCollection class file.
 */

namespace mirocow\yandexmaps;

use mirocow\yandexmaps\Interfaces;

/**
 * Objects collection.
 * @property array $objects
 */
class GeoObjectCollection extends GeoObject implements Interfaces\GeoObjectCollection {
	/** @var array */
	private $_objects = array();

	/**
	 * @return array
	 */
	public function getObjects() {
		return $this->_objects;
	}

	/**
	 * @param array $objects
	 */
	public function setObjects(array $objects = array()) {
		$this->_objects = array();
		foreach ($objects as $object) {
			$this->addObject($object);
		}
	}

	/**
	 * @param Interfaces\GeoObject $object
	 */
	public function addObject($object) {
		$this->_objects[] = $object;
	}
}