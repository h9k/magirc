<?php

/*
 * You can add custom Slim routes here with custom PHP code and pass it on to your templates for display.
 * This default template makes use of the REST service via AJAX requests, however you could also build
 * routes which fetch the data via PHP and pass it on to the template directly like in this example.
 * 
 * One day there will be a more detailed documentation about the routing and templating system in MagIRC :)
 * 
 * NOTE: To make this work you should rename this file to customRoutes.inc.php
 */

$magirc->slim->get('/custom/', function() use($magirc) {
	$magirc->slim->render('custom.tpl', array(
		'section' => 'custom',
		'example' => 'Hello World',
		'channels' => $magirc->service->getChannelList()
	));
});

$magirc->slim->get('/custom/:example', function($example) use($magirc) {
	$magirc->slim->render('custom.tpl', array(
		'section' => 'custom',
		'example' => $example,
		'channels' => $magirc->service->getChannelList()
	));
});
