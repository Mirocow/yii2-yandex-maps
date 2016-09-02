<?php
/**
 * mirocow\yandexmaps\Api class file.
 */

namespace mirocow\yandexmaps;

use mirocow\yandexmaps\Interfaces;
use mirocow\yandexmaps\objects;

use yii;
use yii\base\Component;
use yii\helpers\Json;
use yii\web\View;

/**
 * Yandex Maps API component.
 */
class Api extends Component {
	const SCRIPT_ID = 'yandex.maps.api';

	/** @var string */
	public $protocol = 'http';

	/** @var string */
	public $uri = 'api-maps.yandex.ru';

	/** @var string */
	/* https://tech.yandex.ru/maps/doc/jsapi/2.1/versions/concepts/index-docpage */
	public $api_version = '2.1-dev';

	/** @var string */
	public $language = 'ru-RU';
	/** @var array */
	public $packages = array('package.full');

	/** @var array */
	private $_objects = array();

	/**
	 * @param mixed $key
	 * @param mixed $object
	 * @return $this
	 */
	public function addObject($object, $key = null) {
		if (null === $key) {
			$this->_objects[] = $object;
		} else {
			$this->_objects[$key] = $object;
		}

		return $this;
	}

	/**
	 * Render client scripts.
	 */
	public function render() {
		$this->registerScriptFile();
		$this->registerScript();
	}

	protected function encodeArray($array) {
		return count($array) > 0 ? Json::encode($array) : '{}';
	}

	/**
	 * @todo Add another API params
	 * @see http://api.yandex.ru/maps/doc/jsapi/2.x/dg/concepts/load.xml
	 */
	protected function registerScriptFile() {
		if ('https' !== $this->protocol) {
			$this->protocol = 'http';
		}

		if (is_array($this->packages)) {
			$this->packages = implode(',', $this->packages);
		}

		$url = '//' . $this->uri . '/' . $this->api_version . '/?lang=' . $this->language . '&load=' . $this->packages;

		Yii::$app->view->registerJsFile($url, ['position' => View::POS_END]);
	}

	/**
	 * Register client script.
	 */
	protected function registerScript() {
		$js = "\$Maps = [];\nymaps.ready(function() {\n";

		foreach ($this->_objects as $var => $object) {
			$js .= $this->generateObject($object, $var) . "\n";
		}

		$js .= "});\n";

		Yii::$app->view->registerJs($js, View::POS_READY, self::SCRIPT_ID);
	}

	public function generateObject($object, $var = null) {
		$class = get_class($object);
		$generator = 'generate' . substr($class, strrpos($class, '\\') + 1);
		if (method_exists($this, $generator)) {
			$var = is_numeric($var) ? null : $var;
			$js = $this->$generator($object, $var);

			if ($object instanceof Interfaces\EventAggregate && count($object->getEvents()) > 0) {
				if (null !== $var) {
					$events = "\n$var.events";
					foreach ($object->getEvents() as $event => $handle) {
						$event = Json::encode($event);
						$handle = Json::encode($handle);
						$events .= "\n.add($event, $handle)";
					}
					$js .= "$events;\n";
				}
			}
		} else {
			$js = Json::encode($object);
		}

		return $js;
	}

	public function generateGeoObjectCollection(GeoObjectCollection $object,
	  $var = null) {
		$properties = $this->encodeArray($object->properties);
		$options = $this->encodeArray($object->options);

		$js = "new ymaps.GeoObjectCollection($properties, $options)";
		if (null !== $var) {
			$js = "var $var = $js;\n";

			if (count($object->objects) > 0) {
				foreach ($object->objects as $object) {
					if (!$object) {
						continue;
					}
					if (is_object($object)) {
						$object = $this->generateObject($object);
					}
					$js .= "$var.add($object);\n";
				}
			}
		}

		return $js;
	}

	public function generateMap(Map $map, $var = null) {
		$id = $map->id;
		$state = $this->encodeArray($map->state);
		$options = $this->encodeArray($map->options);

		$js = "new ymaps.Map('$id', $state, $options)";
		if (null !== $var) {
			$js = "\$Maps['$var'] = $js;\n";

			if (count($map->objects) > 0) {

				$jsObj = array();
				$objBegin = false;
				$objects = '';
				$clusterer = "var points = [];\n";

				foreach ($map->objects as $i => $object) {
					if (!$object) {
						continue;
					}
					if (!is_string($object) && !$object instanceof GeoObject) {
						if ($objBegin) {
							$jsObj[] = $object;
						} elseif (is_callable($object)) {
							try {
								$object = $object->__invoke();
								$js .= "\n$object";
							}
							catch (\Exception $e) {
								//
							}
						} else {
							$js .= "\n$object";
						}
					} else {
						$objBegin = true;
						// Load only GeoObjects instanceof GeoObject
						if ($object instanceof GeoObject) {
							$_object = $this->generateObject($object);

							// use Clusterer
							if ($map->use_clusterer && $object instanceof objects\Placemark) {
								$clusterer .= "points[$i] = $_object;\n";
							} else {
								$objects .= ".add($_object)\n";
							}

						} elseif (is_string($object)) {
							$js .= "$object;\n";
						}
					}
				}

				if ($map->use_clusterer) {
					$js .= "$clusterer\n
						var clusterer = new ymaps.Clusterer();\n
						clusterer.add(points);";

					if ($object->clustererOptions) {
						foreach ($object->clustererOptions as $name => $option) {
							if (is_array($option)) {
								$option = $this->encodeArray($option);
							} else {
								$option = "'{$option}'";
							}
							$js .= "clusterer.options.set('{$name}',{$option});\n";
						}
					}

					$objects .= ".add(clusterer)";
				}

				if (!empty($objects)) {
					$js .= "\n\$Maps['$id'].geoObjects$objects;\n";
				}

				if (count($jsObj) > 0) {
					$objects = '';
					foreach ($jsObj as $object) {
						$object = $this->generateObject($object);
						$objects .= "\n$object";
					}
					$js .= "$objects;\n";
				}

			}

			if (count($map->controls) > 0) {
				$controls = "\n\$Maps['$id'].controls";
				foreach ($map->controls as $control) {
					if (count($control) > 1) {
						$config = $this->encodeArray($control[1]);
						$controls .= "\n\t.add($control[0], $config)";
					} else {
						$controls .= "\n\t.add($control[0])";
					}
				}
				$js .= "$controls;\n";
			}

			if (count($map->behaviors) > 0) {
				$behaviors = "\n\$Maps['$id'].behaviors";
				foreach ($map->behaviors as $config => $behavior) {
					$config = $this->encodeArray($config);
					$behaviors .= "\n\t.$behavior($config)";
				}
				$js .= "$behaviors;\n";
			}

		}

		return $js;
	}

	public function generatePlacemark(objects\Placemark $object, $var = null) {
		$geometry = Json::encode($object->geometry);
		$properties = $this->encodeArray($object->properties);
		$options = $this->encodeArray($object->options);

		$js = "new ymaps.Placemark($geometry, $properties, $options)";
		if (null !== $var) {
			$js = "var $var = $js;\n";
		}

		return $js;
	}

	public function generatePolyline(objects\Polyline $object, $var = null) {
		$geometry = Json::encode($object->geometry);
		$properties = $this->encodeArray($object->properties);
		$options = $this->encodeArray($object->options);

		$js = "new ymaps.Polyline($geometry, $properties, $options)";
		if (null !== $var) {
			$js = "var $var = $js;\n";
		}

		return $js;
	}

	public function generatePolygon(objects\Polygon $object, $var = null) {
		$geometry = Json::encode($object->geometry);
		$properties = $this->encodeArray($object->properties);
		$options = $this->encodeArray($object->options);

		$js = "new ymaps.Polygon($geometry, $properties, $options)";
		if (null !== $var) {
			$js = "var $var = $js;\n";
		}

		return $js;
	}

	public function generateJavaScript(JavaScript $object, $var = null) {
		$js = $object->code;
		if (null !== $var) {
			$js = "var $var = $js;\n";
		}

		return $js;
	}
}