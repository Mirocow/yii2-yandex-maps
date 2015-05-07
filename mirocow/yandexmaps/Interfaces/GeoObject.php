<?php
/**
 * mirocow\yandexmaps\Interfaces\GeoObject class file.
 */

namespace mirocow\yandexmaps\Interfaces;

/**
 * GeoObject interface.
 */
interface GeoObject {
	/**
	 * @param array $feature
	 * @param array $options
	 */
	public function __construct(array $feature, array $options = array());

	/**
	 * @return array
	 */
	public function getFeature();

	/**
	 * @param array $feature
	 */
	public function setFeature(array $feature);

	/**
	 * @return array
	 */
	public function getOptions();

	/**
	 * @param array $options
	 */
	public function setOptions(array $options);

	/**
	 * @return array
	 */
	public function getGeometry();

	/**
	 * @param array $geometry
	 */
	public function setGeometry(array $geometry);

	/**
	 * @return array
	 */
	public function getProperties();

	/**
	 * @param array $properties
	 */
	public function setProperties(array $properties);
}