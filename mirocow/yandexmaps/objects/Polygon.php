<?php
/**
 * mirocow\yandexmaps\Polygon class file.
 */

namespace mirocow\yandexmaps\objects;

use mirocow\yandexmaps\GeoObject;

/**
 * Polyline
 */
class Polygon extends GeoObject {
	/**
	 * @param array $geometry
	 * @param array $properties
	 * @param array $options
	 */
	public function __construct(array $geometry, array $properties = array(),
	  array $options = array()) {
		$feature = array(
		  'geometry' => array(
			'type' => "Polygon",
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

		$content = '';

		if (is_array($geometry)) {
			foreach ($geometry as $_points) {
				$point = [
				  $_points->latitude,
				  $_points->longitude
				];
				$points[] = $point;
			}
			if (isset($points)) {
				$content = [$points];
			}
		} else {
			$content = $geometry;
		}

		return $content;
	}

}