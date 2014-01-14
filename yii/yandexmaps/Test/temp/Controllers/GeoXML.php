<?php
/**
 * Controller class file.
 */
class GeoXML extends Controller
{
	public $defaultAction = 'json';

	public function actionXml()
	{
/*
<?xml version="1.0" encoding="utf-8"?>
<ymaps xmlns="http://maps.yandex.ru/ymaps/1.x" xmlns:x="http://www.yandex.ru/xscript">
  <GeoObjectCollection>
    <metaDataProperty xmlns="http://www.opengis.net/gml">
      <GeocoderResponseMetaData xmlns="http://maps.yandex.ru/geocoder/1.x">
        <request>Москва, улица Новый Арбат, дом 24</request>
        <found>1</found>
        <results>10</results>
      </GeocoderResponseMetaData>
    </metaDataProperty>
    <featureMember xmlns="http://www.opengis.net/gml">
      <GeoObject xmlns="http://maps.yandex.ru/ymaps/1.x">
        <metaDataProperty xmlns="http://www.opengis.net/gml">
          <GeocoderMetaData xmlns="http://maps.yandex.ru/geocoder/1.x">
            <kind>house</kind>
            <text>Россия, Москва, улица Новый Арбат, 24</text>
            <precision>exact</precision>
            <AddressDetails xmlns="urn:oasis:names:tc:ciq:xsdschema:xAL:2.0">
              <Country>
                <CountryNameCode>RU</CountryNameCode>
                <CountryName>Россия</CountryName>
                <Locality>
                  <LocalityName>Москва</LocalityName>
                  <Thoroughfare>
                    <ThoroughfareName>улица Новый Арбат</ThoroughfareName>
                    <Premise>
                      <PremiseNumber>24</PremiseNumber>
                    </Premise>
                  </Thoroughfare>
                </Locality>
              </Country>
            </AddressDetails>
          </GeocoderMetaData>
        </metaDataProperty>
        <name xmlns="http://www.opengis.net/gml">новый арбат, 24</name>
        <boundedBy xmlns="http://www.opengis.net/gml">
          <Envelope>
            <lowerCorner>37.583490 55.750778</lowerCorner>
            <upperCorner>37.591701 55.755409</upperCorner>
          </Envelope>
        </boundedBy>
        <Point xmlns="http://www.opengis.net/gml">
          <pos>37.587596 55.753093</pos>
        </Point>
      </GeoObject>
    </featureMember>
  </GeoObjectCollection>
</ymaps>
 */
	}

	public function actionJson()
	{
		/*
		{
  "response": {
    "GeoObjectCollection": {
      "metaDataProperty": {
        "GeocoderResponseMetaData": {
          "request": "Москва, улица Новый Арбат, дом 24",
          "found": "1",
          "results": "10"
        }
      },
      "featureMember": [
        {
          "GeoObject": {
            "metaDataProperty": {
              "GeocoderMetaData": {
                "kind": "house",
                "text": "Россия, Москва, улица Новый Арбат, 24",
                "precision": "exact",
                "AddressDetails": {
                  "Country": {
                    "CountryNameCode": "RU",
                    "CountryName": "Россия",
                    "Locality": {
                      "LocalityName": "Москва",
                      "Thoroughfare": {
                        "ThoroughfareName": "улица Новый Арбат",
                        "Premise": {
                          "PremiseNumber": "24"
                        }
                      }
                    }
                  }
                }
              }
            },
            "name": "новый арбат, 24",
            "boundedBy": {
              "Envelope": {
                "lowerCorner": "37.583490 55.750778",
                "upperCorner": "37.591701 55.755409"
              }
            },
            "Point": {
              "pos": "37.587596 55.753093"
            }
          }
        }
      ]
    }
  }
}

		http://geocode-maps.yandex.ru/1.x/?format=json&callback=my_function
		my_function(json);
		 */
	}
}