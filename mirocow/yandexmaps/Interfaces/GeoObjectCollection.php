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
	public function setObjects(array $objects = array());

	/**
	 * @param mixed $object
	 */
	public function addObject($object);
}