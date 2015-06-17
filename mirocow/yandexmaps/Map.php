<?php
/**
 * mirocow\yandexmaps\Map class file.
 */

namespace mirocow\yandexmaps;

use yii\base\Exception;

//use StdLib\VarDumper;
use mirocow\yandexmaps\Interfaces;

/**
 * @property string $id
 * @property array $objects
 * @property array $controls
 */
class Map extends JavaScript implements Interfaces\GeoObjectCollection, Interfaces\EventAggregate {
	const CONTROL_MAP_TOOLS = 'mapTools';
	const CONTROL_MINI_MAP = 'miniMap';
	const CONTROL_SCALE_LINE = 'scaleLine';
	const CONTROL_SEARCH = 'searchControl';
	const CONTROL_TRAFFIC = 'trafficControl';
	const CONTROL_TYPE_SELECTOR = 'typeSelector';
	const CONTROL_ZOOM = 'zoomControl';
	const CONTROL_SMALL_ZOOM = 'smallZoomControl';

	const BEHAVIOR_DEFAULT = 'default';
	const BEHAVIOR_DRAG = 'drag';
	const BEHAVIOR_SCROLL_ZOOM = 'scrollZoom';
	const BEHAVIOR_CLICK_ZOOM = 'dblClickZoom';
	const BEHAVIOR_MULTI_TOUCH = 'multiTouch';
	const BEHAVIOR_RIGHT_MAGNIFIER = 'rightMouseButtonMagnifier';
	const BEHAVIOR_LEFT_MAGNIFIER = 'leftMouseButtonMagnifier';
	const BEHAVIOR_RULER = 'ruler';
	const BEHAVIOR_ROUTE_EDITOR = 'routeEditor';

	/** @var array */
	public $state = array();
	/** @var array */
	public $options = array();

	public $use_clusterer = false;

	/** @var string */
	private $_id;
	/** @var array */
	private $_objects = array();
	/** @var array */
	private $_controls = array();
	/** @var array */
	private $_events = array();
	/** @var array */
	private $_behaviors = array();

	/**
	 * @param string $id
	 * @param array $state
	 * @param array $options
	 */
	public function __construct($id = 'myMap', array $state = array(),
	  array $options = array()) {

		$this->setId($id);
		$this->state = $state;

		if (isset($options['controls'])) {
			$this->setControls($options['controls']);
			unset($options['controls']);
		}

		if (isset($options['events'])) {
			$this->setEvents($options['events']);
			unset($options['events']);
		}

		if (isset($options['objects'])) {
			$this->setObjects($options['objects']);
			unset($options['objects']);
		}

		if (isset($options['behaviors'])) {
			$this->setBehaviors($options['behaviors']);
			unset($options['behaviors']);
		}

		$this->options = $options;

	}

	/**
	 * Clone object.
	 */
	function __clone() {
		$this->id = null;
	}

	/**
	 * @param string $code
	 * @throws Exception
	 */
	final public function setCode($code) {
		throw new Exception('Cannot change code directly.');
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function getId() {
		if (empty($this->_id)) {
			throw new Exception('Empty map ID.');
		}

		return $this->_id;
	}

	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->_id = (string) $id;
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
	 * @param array $behaviors
	 */
	public function setBehaviors(array $behaviors) {
		$this->_behaviors = $behaviors;
	}

	/**
	 * @return array
	 */
	public function getBehaviors() {
		return $this->_behaviors;
	}

	/**
	 * @return array
	 */
	public function getObjects() {
		return $this->_objects;
	}

	/**
	 * @param array $objects
	 */
	public function setObjects(array $objects = array()) {
		$this->_objects = array();
		foreach ($objects as $object) {
			$this->addObject($object);
		}
	}

	/**
	 * @param mixed $object
	 * @return Map
	 */
	public function addObject($object) {
		$this->_objects[] = $object;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getControls() {
		return $this->_controls;
	}

	/**
	 * @param array $controls
	 */
	public function setControls(array $controls) {
		$this->_controls = array();
		foreach ($controls as $control) {
			$this->addControl($control);
		}
	}

	/**
	 * The control.
	 * ```php
	 * $map->addControl(array('smallZoomControl', array(
	 *    'left' => '5px',
	 *    'top' => '5px',
	 * )));
	 * ```
	 * @param mixed $control
	 * @return $this
	 * @throws Exception
	 * @todo Add control interface.
	 */
	public function addControl($control) {
		if (is_string($control)) {
			$control = array($control);
		} elseif (is_array($control) && (!isset($control[0]) || !is_string($control[0]))) {
			throw new Exception('Invalid control.');
		}
		$this->_controls[$control[0]] = $control;

		return $this;
	}
}