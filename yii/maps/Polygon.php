<?php
/**
 * yii\yandexmaps\Polygon class file.
 */

namespace yii\yandexmaps;

/**
 * Polyline
 */
class Polygon extends GeoObject
{
	/**
	 * @param array $geometry
	 * @param array $properties
	 * @param array $options
	 */
	public function __construct(array $geometry, array $properties = array(), array $options = array())
	{
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
	public function getGeometry()
	{
		$geometry = parent::getGeometry();
		if (isset($geometry['coordinates'])) {
			$geometry = $geometry['coordinates'];
		}
		return $geometry;
	}
}