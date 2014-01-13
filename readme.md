# Yii2 Yandex Maps Components #

* * *

## Components ##

- [`yii\yandexmaps\Api`](https://github.com/mirocow/yii2-yandex-maps#yandexmapsapi)
- [`yii\yandexmaps\Map`](https://github.com/mirocow/yii2-yandex-maps#yandexmapsmap)
- [`yii\yandexmaps\Canvas`](https://github.com/mirocow/yii2-yandex-maps#yandexmapscanvas)
- `yii\yandexmaps\JavaScript`
- `yii\yandexmaps\Placemark`
- `yii\yandexmaps\Polyline`
- TODO: [Geo XML](http://api.yandex.ru/maps/doc/jsapi/2.x/dg/concepts/geoxml.xml)
- TODO: [GeoObject](http://api.yandex.ru/maps/doc/jsapi/2.x-stable/ref/reference/GeoObject.xml)
- TODO: [Balloon](http://api.yandex.ru/maps/doc/jsapi/2.x-stable/ref/reference/Balloon.xml)
- TODO: [Hint](http://api.yandex.ru/maps/doc/jsapi/2.x-stable/ref/reference/Hint.xml)

### yii\yandexmaps\Api ###

Application components which register scripts.

__Usage__

Attach component to application (e.g. edit config/main.php):
```php
'components' => array(
	'yandexMapsApi' => array(
		'class' => 'yii\yandexmaps\Api';
	)
 ),
```

Important! You need render script in controller after render view,
proposed in View::afterRender():
```php
protected function afterRender($view, &$output)
{
	Yii::$app->getComponent('yandexMapsApi')->render();
	parent::afterRender($view, $output);
}
```

### yii\yandexmaps\Map ###

Map instance.

__Usage__

```php
$map = new Map('demo', [
		'center' => ['js:ymaps.geolocation.latitude', 'js:ymaps.geolocation.longitude'],
		'zoom' => 10,
		// Enable zoom with mouse scroll
		'behaviors' => array('default', 'scrollZoom'),
		'type' => "yandex#map",
	], 
	[
		// Permit zoom only fro 9 to 11
		'minZoom' => 9,
		'maxZoom' => 11,
	]);
```

### yii\yandexmaps\Canvas ###

This is widget which render html tag for your map.

__Usage__

Simple add widget to view:
```php

use yii\yandexmaps as YandexMaps;

YandexMaps::widget([
		'htmlOptions' => ]
			'style' => 'height: 400px;',
		],
		'map' => $map,
	]);
```

*more example you can found in [Test](https://github.com/slavcodev/yii-yandex-maps/blob/master/Test) folder*