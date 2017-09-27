<?php
/**
 * mirocow\yandexmaps\Interfaces\GeoObjectCollection class file.
 */

namespace mirocow\yandexmaps\Interfaces;

/**
 * GeoObject interface.
 */
interface GeoObjectCollection {
	/**
	 * @return array
	 */
	public function getObjects();

	/**
	 * @param array $objects
	 */
	public function setObjects(array $objects = []);

    /**
     * @param $object
     * @param $name
     * @return mixed
     */
	public function addObject($object, $key = null);
}