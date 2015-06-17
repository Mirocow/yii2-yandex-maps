<?php
/**
 * mirocow\yandexmaps\Polyline class file.
 */

namespace mirocow\yandexmaps\objects;

use mirocow\yandexmaps\GeoObject;

/**
 * Polyline
 */
class Polyline extends GeoObject {
	/**
	 * @param array $geometry
	 * @param array $properties
	 * @param array $options
	 */
	public function __construct(array $geometry, array $properties = array(),
	  array $options = array()) {
		$feature = array(
		  'geometry' => array(
			'type' => "LineString",
			'coordinates' => $geometry,
		  ),
		  'properties' => $properties,
		);
		parent::__construct($feature, $options);
	}

	/**
	 * @return array
	 */
	public function getGeometry() {
		$geometry = parent::getGeometry();
		if (isset($geometry['coordinates'])) {
			$geometry = $geometry['coordinates'];
		}

		return $geometry;
	}
}