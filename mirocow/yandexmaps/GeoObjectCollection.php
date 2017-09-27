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
	private $_objects = [];

	/**
	 * @return array
	 */
	public function getObjects() {
		return $this->_objects;
	}

	/**
	 * @param array $objects
	 */
	public function setObjects(array $objects = []) {
		$this->_objects = [];
		foreach ($objects as $key => $object) {
			$this->addObject($object, $key);
		}
	}

    /**
     * @param $object
     * @param $key
     */
	public function addObject($object, $key = null) {
        if (null === $key) {
            $this->_objects[] = $object;
        } else {
            $this->_objects[$key] = $object;
        }

        return $this;
	}
}