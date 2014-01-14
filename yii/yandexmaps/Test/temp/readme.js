var myButton = new ymaps.control.Button('Добавить ...');

myButton.events
    .add('click', function () { alert('Щёлк'); })
    .add('select', function () { alert('Нажата'); })
    .add('deselect', function () { alert('Отжата'); });

myMap.controls
    // .add("zoomControl", {left: '5px', top: '5px'})
    .add("smallZoomControl", {left: '5px', top: '5px'})
    // .add("mapTools")
    .add(new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid"]), { left: '35px', top: '5px' })
    // .add(new ymaps.control.SearchControl({ provider: 'yandex#map' }), {left: '5px', top: '5px'})
    .add(myButton, { right: '5px', top: '5px' });


// Прокладывание маршрута от станции метро "Смоленская"
// до станции Третьяковская (маршрут должен проходить через метро "Арбатская").
// Точки маршрута можно задавать 3 способами:  как строка, как объект или как массив геокоординат.
ymaps.route([
	me,
	[46.99178678083139,28.792177428222654]
], {
	// Опции маршрутизатора
	mapStateAutoApply: true // автоматически позиционировать карту
}).then(function (route) {
		window.yaMaps.myMap.geoObjects.add(route);
	}, function (error) {
		alert("Возникла ошибка: " + error.message);
	});

marks.each(function (item) {
    var name = item.properties.get('hintContent');
    item.properties.set('iconContent', name);
    if (item.properties.get('hintContent') == 'Дьячий') {
        item.properties.set('iconContent', 'Я');
    }
});

//window.yaMaps.myMap.behaviors
//	.disable(['drag', 'rightMouseButtonMagnifier']);

// Открываем балун на карте (без привязки к геообъекту).
myMap.balloon.open([51.85, 38.37], "Содержимое балуна", {
    // Опция: не показываем кнопку закрытия.
    closeButton: false
});

// Показываем хинт на карте (без привязки к геообъекту).
myMap.hint.show(myMap.getCenter(), "Содержимое хинта", {
    // Опция: задержка перед открытием.
    showTimeout: 1500
});

// Шаблон URL для данных активных областей.
// Источник данных будет запрашивать данные через URL вида:
// '.../hotspot_layer/hotspot_data/9/tile_x=1&y=2', где
// x, y - это номер тайла, для которого запрашиваются данные,
// 9 - значение коэффициента масштабирования карты.
var tileUrlTemplate = 'examples/maps/ru/hotspot_layer/hotspot_data/%z/tile_x=%x&y=%y',

// Шаблон callback-функции, в которую сервер будет оборачивать данные тайла.
// Пример callback-функции после подстановки - 'testCallback_tile_x_1_y_2_z_9'.
    keyTemplate = 'testCallback_tile_%c',

// URL тайлов картиночного слоя.
// Пример URL после подстановки -
// '.../hotspot_layer/images/9/tile_x=1&y=2.png'.
    imgUrlTemplate = 'examples/maps/ru/hotspot_layer/images/%z/tile_x=%x&y=%y.png',

// Создадим источник данных слоя активных областей.
    objSource = new ymaps.hotspot.ObjectSource(tileUrlTemplate, keyTemplate),

// Создаем картиночный слой и слой активных областей.
    imgLayer = new ymaps.Layer(imgUrlTemplate, {tileTransparent: true}),
    hotspotLayer = new ymaps.hotspot.Layer(objSource, {cursor: 'help'});

// Добавляем слои на карту.
myMap.layers.add(hotspotLayer);
myMap.layers.add(imgLayer);

// Создаем геообъект с типом геометрии "Точка".
myGeoObject = new ymaps.GeoObject({
    // Описание геометрии.
    geometry: {
        type: "Point",
        coordinates: [55.8, 37.8]
    },
    // Свойства.
    properties: {
        // Контент метки.
        iconContent: 'Метка',
        balloonContent: 'Меня можно перемещать'
    }
}, {
    // Опции.
    // Иконка метки будет растягиваться под размер ее содержимого.
    preset: 'twirl#redStretchyIcon',
    // Метку можно перемещать.
    draggable: true
});

// Создаем геообъект с типом геометрии "Точка".
myGeoObject = new ymaps.GeoObject({
    // Описание геометрии.
    geometry: {
        type: "Point",
        coordinates: [55.8, 37.8]
    },
    // Свойства.
    properties: {
        // Контент метки.
        iconContent: 'Метка',
        balloonContent: 'Меня можно перемещать'
    }
}, {
    // Опции.
    // Иконка метки будет растягиваться под размер ее содержимого.
    preset: 'twirl#redStretchyIcon',
    // Метку можно перемещать.
    draggable: true
});

// Создаем ломаную.
var myPolyline = new ymaps.Polyline([
    // Указываем координаты вершин.
    [55.80, 37.50],
    [55.80, 37.40],
    [55.70, 37.50],
    [55.70, 37.40]
], {}, {
    // Задаем опции геообъекта.
    // Цвет с прозрачностью.
    strokeColor: "#00000088",
    // Ширину линии.
    strokeWidth: 4,
    // Максимально допустимое количество вершин в ломаной.
    editorMaxPoints: 6,
    // Добавляем в контекстное меню новый пункт, позволяющий удалить ломаную.
    editorMenuManager: function (items) {
        items.push({
            title: "Удалить линию",
            onClick: function () {
                myMap.geoObjects.remove(myPolyline);
            }
        });
        return items;
    }
});

// Включаем режим редактирования.
myPolyline.editor.startEditing();

// Обработка события, возникающего при щелчке
// левой кнопкой мыши в любой точке карты.
// При возникновении такого события откроем балун.
myMap.events.add('click', function (e) {
    if (!myMap.balloon.isOpen()) {
        var coords = e.get('coordPosition');
        myMap.balloon.open(coords, {
            contentHeader:'Событие!',
            contentBody:'<p>Кто-то щелкнул по карте.</p>' +
                '<p>Координаты щелчка: ' + [
                coords[0].toPrecision(6),
                coords[1].toPrecision(6)
            ].join(', ') + '</p>',
            contentFooter:'<sup>Щелкните еще раз</sup>'
        });
    }
    else {
        myMap.balloon.close();
    }
});

// Обработка события, возникающего при щелчке
// правой кнопки мыши в любой точке карты.
// При возникновении такого события покажем всплывающую подсказку
// в точке щелчка.
myMap.events.add('contextmenu', function (e) {
    myMap.hint.show(e.get('coordPosition'), 'Кто-то щелкнул правой кнопкой');
});


ym.ready(function() {
    var layerOSM = function() {
        return new ym.Layer('http://otile%d.mqcdn.com/tiles/1.0.0/osm/%z/%x/%y.png', {
            projection: ym.projection.sphericalMercator
        });
    };
    var osMap = new ym.MapType('OSMap', [layerOSM]);
    ym.mapType.storage.add('openstreet#map', osMap);

    var layerGoogle = function() {
        return new ym.Layer('http://mt0.google.com/vt/lyrs=m@176000000&hl=ru&%c', {
            projection: ym.projection.sphericalMercator
        });
    };
    var googleMap = new ym.MapType('GoogleMap', [layerGoogle]);
    ym.mapType.storage.add('google#map', googleMap);

    var layerMail = function() {
        return new ym.Layer('http://t0maps.mail.ru/tiles/scheme/%z/%y/%x.png', {
            projection: ym.projection.sphericalMercator
        });
    };
    var mailMap = new ym.MapType('MailMap', [layerMail]);
    ym.mapType.storage.add('mail#map', mailMap);

    var map = new ym.Map('map', {
        center: [55.74, 37.62],
        zoom: 9
    }, {});

    var yandexSelectorItem = new ym.control.ListBoxItem({
        data: {
            content: "Яндекс.Схема"
        }
    });
    yandexSelectorItem.events.add("click", function(e) {
        this.setType("yandex#map");
    }, map);

    var osmSelectorItem = new ym.control.ListBoxItem({
        data: {
            content: "Open Street Map"
        }
    });
    osmSelectorItem.events.add("click", function(e) {
        this.setType("openstreet#map");
    }, map);

    var googleSelectorItem = new ym.control.ListBoxItem({
        data: {
            content: "Google.Схема"
        }
    });
    googleSelectorItem.events.add("click", function(e) {
        this.setType("google#map");
    }, map);

    var mailSelectorItem = new ym.control.ListBoxItem({
        data: {
            content: "Mail.Схема"
        }
    });
    mailSelectorItem.events.add("click", function(e) {
        this.setType("mail#map");
    }, map);

    var typeSelector = new ym.control.ListBox({
        data: {
            title: "Показать"
        },
        items: [yandexSelectorItem, osmSelectorItem, googleSelectorItem, mailSelectorItem]
    });

    map.controls.add(typeSelector, {
        right: 5,
        top: 5
    });
    map.controls.add("zoomControl", {
        left: 5,
        top: 5
    });
})