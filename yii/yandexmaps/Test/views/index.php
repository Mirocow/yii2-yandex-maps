<?php
/* @var SiteController $this */
/** @var $map */

echo CHtml::openTag('div', array('style' => 'width:100%; margin: 0 auto; padding:20px;',));
$this->widget('\YandexMaps\Canvas', array(
		'htmlOptions' => array(
			'style' => 'height: 600px; width:100%;',
		),
		'map' => $map,
	));
echo CHtml::closeTag('div');