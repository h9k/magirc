<?php

$locales = array();
foreach (glob(__DIR__."/../../../locale/*") as $filename) {
	if (is_dir($filename)) {
		$locale = basename($filename);
		//This is dirty but I'm lazy...
		switch ($locale){
			case 'en_US':
				$language = "English";
				break;
			case 'de_DE':
				$language = "Deutsch";
				break;
			case 'es_ES':
				$language = "Español";
				break;
			case 'fr_FR':
				$language = "Français";
				break;
			case 'it_IT':
				$language = "Italiano";
				break;
			case 'nl_NL':
				$language = "Nederlands";
				break;
			case 'ms_MY';
				$language = "Melayu";
				break;
			default:
				$language = $locale;
		}
		$locales[$locale] = $language;
	}
}
$magirc->slim->view()->appendData(array(
	'cfg' => $magirc->cfg,
	'locales' => $locales
));

$custom_routes = __DIR__ . '/customRoutes.inc.php';
if (file_exists($custom_routes)){
	include_once($custom_routes);
}

$magirc->slim->get('/(network)', function() use($magirc) {
	$magirc->slim->render('network_main.tpl', array('section'=> 'network'));
});

$magirc->slim->get('/content/:name', function($name) use($magirc) {
	echo $magirc->getContent($name);
});

$magirc->slim->get('/channel/:target/:action', function($target, $action) use($magirc) {
    $tpl_file = 'channel_' . basename($action) . '.tpl';
    $tpl_path = 'theme/' . $magirc->cfg->theme . '/tpl/' . $tpl_file;
    if (file_exists($tpl_path)) {
        switch ($magirc->service->checkChannel($target)) {
            case 404:
	            $magirc->slim->notFound();
		        exit;
            case 403:
	            $magirc->slim->render('error.tpl', array('err_code' => 403), 403);
		        exit;
        }
		$magirc->slim->render($tpl_file, array(
			'section' => 'channel',
			'target' => $target,
			'mode' => null
		));
    } else {
        $magirc->slim->notFound();
    }
});

$magirc->slim->get('/user/:target/:action', function($target, $action) use($magirc) {
    $tpl_file = 'user_' . basename($action) . '.tpl';
    $tpl_path = 'theme/' . $magirc->cfg->theme . '/tpl/' . $tpl_file;
    if (file_exists($tpl_path)) {
        $mode = null;
        $array = explode(':', $target);
        if (count($array) == 2) {
            $mode = $array[0];
            $target = $array[1];
            if (!$magirc->service->checkUser($target, $mode)) {
                $magirc->slim->notFound();
            }
        } else {
            $magirc->slim->notFound();
        }
		$magirc->slim->render($tpl_file, array(
			'section' => 'user',
			'target' => $target,
			'mode' => $mode
		));
    } else {
        $magirc->slim->notFound();
    }
});

$magirc->slim->get('/:section/:target/:action', function($section, $target, $action) use($magirc) {
	$tpl_file = basename($section) . '_' . basename($action) . '.tpl';
	$tpl_path = 'theme/' . $magirc->cfg->theme . '/tpl/' . $tpl_file;
	if (file_exists($tpl_path)) {
		$magirc->slim->render($tpl_file, array(
			'section' => $section,
			'target' => $target,
			'mode' => null
		));
	} else {
		$magirc->slim->notFound();
	}
});

$magirc->slim->get('/:section(/:action)', function($section, $action = 'main') use($magirc) {
	$tpl_file = basename($section) . '_' . basename($action) . '.tpl';
	$tpl_path = 'theme/' . $magirc->cfg->theme . '/tpl/' . $tpl_file;
	if (file_exists($tpl_path)) {
		$magirc->slim->render($tpl_file, array(
			'section' => $section
		));
	} else {
		$magirc->slim->notFound();
	}
});
