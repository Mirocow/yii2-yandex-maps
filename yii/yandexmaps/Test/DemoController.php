<?php
/**
 * \YandexMaps\Test\DemoController class file.
 */

namespace YandexMaps\Test;

use YandexMaps\GeoObjectCollection;
use Yii,
	Controller;

use YandexMaps\Api,
	YandexMaps\Map,
	YandexMaps\Placemark,
	YandexMaps\Polyline,
	YandexMaps\JavaScript;

/**
 * @property Api $api
 */
class DemoController extends Controller
{
	/**
	 * @param string $view
	 * @param string $output
	 */
	protected function afterRender($view, &$output)
	{
		parent::afterRender($view, $output);
		$this->api->render();
	}

	/**
	 * @return string
	 */
	public function getViewPath()
	{
		return __DIR__ . '/views';
	}

	/**
	 * @return Api
	 */
	public function getApi()
	{
		return Yii::app()->getComponent('yandexMapsApi');
	}

	/**
	 * Default action.
	 */
	public function actionIndex()
	{
		// Create global placemark
		$me = $this->createPlacemarkByLocation();
		$this->api->addObject($me, 'me');

		// Create script to init objects layouts and defaults properties.
		$this->api->addObject($this->createObjectsLayoutsAndDefaults());

		// Create map
		$map = $this->createMap();

		// Add global placemark to map
		$map->addObject('me');

		// Create private placemarks and to map.
		$maroonUrl = Yii::app()->baseUrl . '/images/road-point-maroon.png';
		$redUrl = Yii::app()->baseUrl . '/images/road-point-red.png';
		$blueUrl = Yii::app()->baseUrl . '/images/road-point-blue.png';
		$yellowUrl = Yii::app()->baseUrl . '/images/road-point-yellow.png';
		// $greenUrl = Yii::app()->baseUrl . '/images/road-point-green.png';
		$map->addObject($this->createDefaultPlacemark(array(55.7595, 37.6249), 'Центр Москвы', $redUrl));
		$map->addObject($this->createDefaultPlacemark(array(55.7695, 37.6449), 'Казанский вокзал', $yellowUrl));
		$map->addObject($this->createDefaultPlacemark(array(55.7525, 37.5845), 'Арбат', $blueUrl));

		// Polyline example
		$maroon = '#98202d';
		// $red = '#ff0000';
		// $blue =  '#0000E2';
		$yellow = '#ffff00';
		// $green = '#95d340';
		$map->addObject($this->createPolyline(array(
					array(55.7852, 37.5661),
					array(55.7699, 37.5961),
					array(55.7595, 37.5844),
				), 'Ленинградский проспект', $maroon));
		$map->addObject($this->createPolyline(array(
					array(55.7725, 37.6319),
					array(55.7695, 37.6449),
					array(55.7655, 37.6549),
					array(55.7565, 37.6570),
					array(55.7415, 37.6535),
					array(55.7365, 37.6480),
					array(55.7320, 37.6400),
				), 'Кольцевая', $yellow));

		// Create collection.
		$collection = $this->createPlacemarkCollection(array(
				$this->createPlacemarkByPosition(array(55.7655, 37.6549), 'Test'),
				$this->createPlacemarkByPosition(array(55.7415, 37.6535), 'Test'),
			), $maroonUrl);
		$this->api->addObject($collection, 'points');
		$map->addObject('points');

		// Simple JS code example
		// $this->api->addObject($this->createBoundsScript($map->id));
		// $this->api->addObject($this->createCopyright($map->id));
		// $this->api->addObject($this->createBalloonOpen('me'));

		// Register map in API.
		$this->api->addObject($map, $map->id);

		// Render map canvas
		$this->render('index', array(
				'map' => $map,
			));
	}

	/**
	 * @return \YandexMaps\Map
	 */
	protected function createMap()
	{
		$image = Yii::app()->baseUrl . '/images/road-point-green.png';
		$click = <<<JS
		var coord = e.get("coordPosition");
		var map = e.get("target"); // Получение ссылки на объект, сгенерировавший событие (карта)
		map.geoObjects.add(new ymaps.Placemark(coord, {
				balloonContentHeader: coord,
				hintContent: coord
			}, {
			iconImageHref: '$image',
			preset: 'my#defaultsPit'
		}));
JS;
		return new Map('map', array(
			'center' => array(55.7595, 37.6249),
			'zoom' => 12,
			'behaviors' => array(
				Map::BEHAVIOR_DEFAULT,
				// Map::BEHAVIOR_SCROLL_ZOOM
			),
			'type' => "yandex#map",
		), array(
			'minZoom' => 9,
			'maxZoom' => 12,
			'controls' => array(
				Map::CONTROL_MAP_TOOLS,
				array(Map::CONTROL_SMALL_ZOOM, array(
					'bottom' => '5px',
				)),
			),
			'events' => array(
				'dblclick' => 'js:function (e) {e.preventDefault();}',
				'click' => "js:function(e) {\n" . $click . "}",
			),
		));
	}

	protected function createCopyright($mapId)
	{
		return new JavaScript("var accessor = $mapId.copyrights.add('&copy; My Copyright Text');");
	}

	protected function createBalloonOpen($objectId)
	{
		return new JavaScript($objectId . ".balloon.open();");
	}

	protected function createBoundsScript($mapId)
	{
		return new JavaScript("var bounds = $mapId.getBounds();");
	}

	/**
	 * @return \YandexMaps\JavaScript
	 */
	protected function createObjectsLayoutsAndDefaults()
	{
		return new JavaScript(<<<JS
var fc = ymaps.templateLayoutFactory,
	// Создаем шаблон для отображения контента балуна
	myBalloonLayout = ymaps.templateLayoutFactory.createClass('<a href="#">Просмотр</a> | <a href="#">Голосовать</a>' +
        '<p>Создано: <strong>$[properties.createTime]</strong><br>Голосов: <strong>$[properties.votes]</strong></p>'),
	// Создаем шаблон для отображения контента хинта.
	myHintLayout = ymaps.templateLayoutFactory.createClass('<b>Hint</b>: $[properties.hintContent]'),
	// myIconContentLayout = fc.createClass('<p>Test content layout.</p>'),
	// myIconLayout = fc.createClass('<div style="background-image:url($[properties.iconImageHref]); width:16px; height:16px;"></div>'),
	size = 16, defaultsPit = {
		// iconLayout: myIconLayout, // 'my#iconLayout',
		// iconOffset: [-size/2, -size/2],
		// iconContentLayout => myIconContentLayout,
		// iconContentLayout => 'my#iconContentLayout',
		iconImageSize: [size, size],
		iconImageOffset: [-size/2, -size/2],
		hintContentLayout: 'my#hintLayout',
		// Задаем наш шаблон для балунов геобъекта.
		balloonContentBodyLayout: 'my#balloonLayout',
		// Максимальная ширина балуна в пикселах
		balloonMaxWidth: 300
	}, defaultsRoad = {
		geodesic: true,
		strokeWidth: 8,
		hintContentLayout: 'my#hintLayout',
		// Задаем наш шаблон для балунов геобъекта.
		balloonContentBodyLayout: 'my#balloonLayout',
		// balloonContentLayout: 'my#balloonLayout',
		// Максимальная ширина балуна в пикселах
		balloonMaxWidth: 300
	};

// Помещаем созданный шаблон в хранилище шаблонов. Теперь наш шаблон доступен по ключу 'my#balloonLayout'.
ymaps.layout.storage.add('my#balloonLayout', myBalloonLayout);
// Помещаем созданный шаблон в хранилище шаблонов. Теперь наш шаблон доступен по ключу 'my#hintLayout'.
ymaps.layout.storage.add('my#hintLayout', myHintLayout);

ymaps.option.presetStorage.add('my#defaultsPit', defaultsPit);
ymaps.option.presetStorage.add('my#defaultsRoad', defaultsRoad);
// ymaps.layout.storage.add('my#iconLayout', myIconLayout);
// ymaps.layout.storage.add('my#iconContentLayout', myIconContentLayout);
JS
		);
	}

	protected function createPlacemarkByLocation()
	{
		$yellowUrl = Yii::app()->baseUrl . '/images/road-point-yellow.png';
		return new Placemark(array('js:ymaps.geolocation.latitude', 'js:ymaps.geolocation.longitude'), array(
			'balloonContentHeader' => 'js:ymaps.geolocation.country',
			'balloonContent' => 'js:ymaps.geolocation.city',
			'balloonContentFooter' => 'js:ymaps.geolocation.region',
		), array(
			'iconImageHref' => $yellowUrl,
			'iconImageSize' => array(16, 16),
			'iconImageOffset' => 8,
		));
	}

	protected function createDefaultPlacemark(array $position, $title, $image)
	{
		return new Placemark($position, array(
			'balloonContentHeader' => $title,
			'hintContent' => $title,
			'createTime' => date('Y-m-d'),
			'votes' => rand(0, 20),
		), array(
			'iconImageHref' => $image,
			'preset' => 'my#defaultsPit',
		));
	}

	protected function createPlacemarkByPosition(array $position, $title)
	{
		return new Placemark($position, array(
			'balloonContentHeader' => $title,
			'hintContent' => $title,
			'createTime' => date('Y-m-d'),
			'votes' => rand(0, 20),
		));
	}

	protected function createPolyline(array $position, $title, $color)
	{
		return new Polyline($position, array(
			'balloonContentHeader' => $title,
			'hintContent' => $title,
			'createTime' => date('Y-m-d'),
			'votes' => rand(0, 20),
		), array(
			'strokeColor' => $color,
			'preset' => 'my#defaultsRoad',
		));
	}

	protected function createPlacemarkCollection(array $objects, $image)
	{
		$collection = new GeoObjectCollection(array(), array(
			'iconImageHref' => $image,
			'preset' => 'my#defaultsPit',
		));
		$collection->setObjects($objects);
		return $collection;
	}
}