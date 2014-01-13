<?php
/**
 * yii\yandexmaps\Api class file.
 */

namespace yii\yandexmaps;

use Yii;
use yii\base\Component;
use yii\helpers\Json;
use yii\web\View;
use StdLib\VarDumper;
use yandexmaps\Interfaces;

/**
 * Yandex Maps API component.
 */
class Api extends Component
{
	const SCRIPT_ID = 'yandex.maps.api';

	/** @var string */
	public $protocol = 'http';
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
	public function addObject($object, $key = null)
	{
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
	public function render()
	{
		$this->registerScriptFile();
		$this->registerScript();
	}

	protected function encodeArray($array)
	{
		return count($array) > 0 ? Json::encode($array) : '{}';
	}

	/**
	 * @todo Add another API params
	 * @see http://api.yandex.ru/maps/doc/jsapi/2.x/dg/concepts/load.xml
	 */
	protected function registerScriptFile()
	{
		if ('https' !== $this->protocol) {
			$this->protocol = 'http';
		}

		if (is_array($this->packages)) {
			$this->packages = implode(',', $this->packages);
		}

		$url = $this->protocol . '://api-maps.yandex.ru/2.0-stable'
			.'/?lang=' . $this->language
			. '&load=' . $this->packages;
		
		Yii::$app->view->registerJsFile($url, View::POS_END);
	}

	/**
	 * Register client script.
	 */
	protected function registerScript()
	{
		$js = "ymaps.ready(function() {\n";

		foreach ($this->_objects as $var => $object) {
			$js .= $this->generateObject($object, $var)."\n";
		}

		$js .= "});\n";
		
		Yii::$app->view->registerJs($js, View::POS_END, self::SCRIPT_ID);
	}

	public function generateObject($object, $var = null)
	{
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
						$events .= "\n\t.add($event, $handle)";
					}
					$js .= "$events;\n";
				}
			}
		} else {
			$js = Json::encode($object);
		}

		return $js;
	}

	public function generateGeoObjectCollection(GeoObjectCollection $object, $var = null)
	{
		$properties = $this->encodeArray($object->properties);
		$options = $this->encodeArray($object->options);

		$js = "new ymaps.GeoObjectCollection($properties, $options)";
		if (null !== $var) {
			$js = "var $var = $js;\n";

			if (count($object->objects) > 0) {
				foreach ($object->objects as $object) {
					if (is_object($object)) {
						$object = $this->generateObject($object);
					}
					$js .= "$var.add($object);\n";
				}
			}
		}

		return $js;
	}

	public function generateMap(Map $map, $var = null)
	{
		$id = $map->id;
		$state = $this->encodeArray($map->state);
		$options = $this->encodeArray($map->options);

		$js = "new ymaps.Map('$id', $state, $options)";
		if (null !== $var) {
			$js = "var $var = $js;\n";

			if (count($map->objects) > 0) {
				$jsObj = array();
				$objBegin = false;
				$objects = '';
				foreach ($map->objects as $object) {
					if (!is_string($object) && !$object instanceof GeoObject) {
						if ($objBegin) {
							$jsObj[] = $object;
						} else {
							$js .= "\n$object";
						}
					} else {
						$objBegin = true;
						if ($object instanceof GeoObject) {
							$object = $this->generateObject($object);
						}
						$objects .= "\n\t.add($object)";
					}
				}
				if (!empty($objects)){
					$js .= "\n$var.geoObjects$objects;\n";
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
				$controls = "\n$id.controls";
				foreach ($map->controls as $control) {
					if (count($control) > 1) {
						$config = $this->encodeArray($control[1]);
						$controls .= "\n\t.add('$control[0]', $config)";
					} else {
						$controls .= "\n\t.add('$control[0]')";
					}
				}
				$js .= "$controls;\n";
			}
		}

		return $js;
	}

	public function generatePlacemark(Placemark $object, $var = null)
	{
		$geometry = Json::encode($object->geometry);
		$properties = $this->encodeArray($object->properties);
		$options = $this->encodeArray($object->options);

		$js = "new ymaps.Placemark($geometry, $properties, $options)";
		if (null !== $var) {
			$js = "var $var = $js;\n";
		}

		return $js;
	}

	public function generatePolyline(Polyline $object, $var = null)
	{
		$geometry = Json::encode($object->geometry);
		$properties = $this->encodeArray($object->properties);
		$options = $this->encodeArray($object->options);

		$js = "new ymaps.Polyline($geometry, $properties, $options)";
		if (null !== $var) {
			$js = "var $var = $js;\n";
		}

		return $js;
	}

	public function generatePolygon(Polygon $object, $var = null)
	{
		$geometry = Json::encode($object->geometry);
		$properties = $this->encodeArray($object->properties);
		$options = $this->encodeArray($object->options);

		$js = "new ymaps.Polygon($geometry, $properties, $options)";
		if (null !== $var) {
			$js = "var $var = $js;\n";
		}

		return $js;
	}

	public function generateJavaScript(JavaScript $object, $var = null)
	{
		$js = $object->code;
		if (null !== $var) {
			$js = "var $var = $js;\n";
		}

		return $js;
	}
}