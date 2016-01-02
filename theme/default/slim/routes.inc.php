<?php

$custom_routes = __DIR__ . '/customRoutes.inc.php';
if (file_exists($custom_routes)){
    include_once($custom_routes);
}

$magirc->slim->get('/[network]', function($req, $res, $args) {
    $this->view->render($res, 'network_main.twig', [
        'cfg' => $this->config,
        'locales' => $this->locales,
        'section'=> 'network'
    ]);
})->setName('newtwork');

$magirc->slim->get('/content/{name}', function($req, $res, $args) use($magirc) {
    echo $magirc->getContent($args['name']);
});

$magirc->slim->get('/channel/{target}/{action}', function($req, $res, $args) use($magirc) {
    $tpl_file = 'channel_' . basename($args['action']) . '.twig';
    $tpl_path = 'theme/' . $this->config['theme'] . '/tpl/' . $tpl_file;
    if (file_exists($tpl_path)) {
        switch ($magirc->service->checkChannel($args['target'])) {
            case 404:
                return $this->view->render($res, "error.twig", [
                    'cfg' => $this->config,
                    'locales' => $this->locales,
                    'err_code' => 404
                ])->withStatus(404);
            case 403:
                return $this->view->render($res, "error.twig", [
                    'cfg' => $this->config,
                    'locales' => $this->locales,
                    'err_code' => 405
                ])->withStatus(405);
        }
        $this->view->render($res, $tpl_file, [
            'cfg' => $this->config,
            'locales' => $this->locales,
            'section' => 'channel',
            'target' => $args['target'],
            'mode' => null
        ]);
    } else {
        return $this->view->render($res, "error.twig", [
            'cfg' => $this->config,
            'locales' => $this->locales,
            'err_code' => 404
        ])->withStatus(404);
    }
})->setName('channel');

$magirc->slim->get('/user/{target}/{action}', function($req, $res, $args) use($magirc) {
    $tpl_file = 'user_' . basename($args['action']) . '.twig';
    $tpl_path = 'theme/' . $this->config['theme'] . '/tpl/' . $tpl_file;
    if (file_exists($tpl_path)) {
        $mode = null;
        $array = explode(':', $args['target']);
        if (count($array) == 2 && $magirc->service->checkUser($array[1], $array[0])) {
            return $this->view->render($res, $tpl_file, [
                'cfg' => $this->config,
                'locales' => $this->locales,
                'section' => 'user',
                'target' => $array[1],
                'mode' => $array[0]
            ]);
        }
    }
    return $this->view->render($res, "error.twig", [
        'cfg' => $this->config,
        'locales' => $this->locales,
        'err_code' => 404
    ])->withStatus(404);
})->setName('user');

$magirc->slim->get('/{section}/{target}/{action}', function($req, $res, $args) {
    $tpl_file = basename($args['section']) . '_' . basename($args['action']) . '.twig';
    $tpl_path = 'theme/' . $this->config['theme'] . '/tpl/' . $tpl_file;
    if (file_exists($tpl_path)) {
        $this->view->render($res, $tpl_file, [
            'cfg' => $this->config,
            'locales' => $this->locales,
            'section' => $args['section'],
            'target' => $args['target'],
            'mode' => null
        ]);
    } else {
        return $this->view->render($res, "error.twig", [
            'cfg' => $this->config,
            'locales' => $this->locales,
            'err_code' => 404
        ])->withStatus(404);
    }
})->setName('genericFull');

$magirc->slim->get('/{section}[/{action}]', function($req, $res, $args) {
    $action = isset($args['action']) ? $args['action'] : "main";
    $tpl_file = basename($args['section']) . '_' . basename($action) . '.twig';
    $tpl_path = 'theme/' . $this->config['theme'] . '/tpl/' . $tpl_file;
    if (file_exists($tpl_path)) {
        $this->view->render($res, $tpl_file, [
            'cfg' => $this->config,
            'locales' => $this->locales,
            'section' => $args['section']
        ]);
    } else {
        return $this->view->render($res, "error.twig", [
            'cfg' => $this->config,
            'locales' => $this->locales,
            'err_code' => 404
        ])->withStatus(404);
    }
})->setName('generic');
