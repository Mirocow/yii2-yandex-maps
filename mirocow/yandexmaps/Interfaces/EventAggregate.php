<?php
/**
 * mirocow\yandexmaps\Interfaces\GeoObject class file.
 */

namespace mirocow\yandexmaps\Interfaces;

/**
 * EventAggregate interface.
 */
interface EventAggregate {
	/**
	 * @return array
	 */
	public function getEvents();
}