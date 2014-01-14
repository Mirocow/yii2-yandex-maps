<?php
/**
 * yii\yandexmaps\Interfaces\GeoObject class file.
 */

namespace yii\yandexmaps\Interfaces;

/**
 * EventAggregate interface.
 */
interface EventAggregate
{
	/**
	 * @return array
	 */
	public function getEvents();
}