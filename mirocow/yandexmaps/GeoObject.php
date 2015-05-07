<?php
/**
 * mirocow\yandexmaps\GeoObject class file.
 */

namespace mirocow\yandexmaps;

use mirocow\yandexmaps\Interfaces;

use yii\base\Exception;

/**
 * @property array $feature
 * @property array $options
 * @property array $properties
 * @property array $geometry
 */
class GeoObject extends JavaScript implements Interfaces\GeoObject, Interfaces\EventAggregate {
	/** @var array */
	private $_feature;
	/** @var array */
	private $_options = array();

	/** @var array */
	private $_events = array();

	/**
	 * @param array $feature
	 * @param array $options
	 */
	public function __construct(array $feature, array $options = array()) {
		if (isset($options['events'])) {
			$this->setEvents($options['events']);
			unset($options['events']);
		}

		$this->setFeature($feature);
		$this->setOptions($options);
	}

	/**
	 * @param string $code
	 * @throws Exception
	 */
	final public function setCode($code) {
		throw new Exception('Cannot change code directly.');
	}

	/**
	 * @param array $events
	 */
	public function setEvents(array $events) {
		$this->_events = $events;
	}

	/**
	 * @return array
	 */
	public function getEvents() {
		return $this->_events;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function getFeature() {
		if (empty($this->_feature)) {
			throw new Exception('Empty placemark geometry.');
		}

		return $this->_feature;
	}

	/**
	 * @param array $feature
	 */
	public function setFeature(array $feature) {
		$this->_feature = $feature;
	}

	/**
	 * @return array
	 */
	public function getOptions() {
		return $this->_options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions(array $options) {
		$this->_options = $options;
	}

	/**
	 * @return array
	 */
	public function getGeometry() {
		return isset($this->_feature['geometry']) ? $this->_feature['geometry'] : array();
	}

	/**
	 * @param array $geometry
	 */
	public function setGeometry(array $geometry) {
		$this->_feature['coordinates'] = $geometry;
	}

	/**
	 * @return array
	 */
	public function getProperties() {
		return isset($this->_feature['properties']) ? $this->_feature['properties'] : array();
	}

	/**
	 * @param array $properties
	 */
	public function setProperties(array $properties) {
		$this->_feature['properties'] = $properties;
	}
}