<?php
// Instancia Router
use Router\RouteList;

// Instancia Controllers
use Controller\MainCtrl;

// Instancia Globales
$default_styles = ['start' => ['fonts/css/all.min', 'html'], 'end' => ['media-query']];
$default_GET = [];
$default_js = ['start' => ['jquery-3.6.1.min', 'dir'], 'end' => ['final']];

$routeList = new RouteList($default_styles, $default_js, $default_GET);

/*
  estructura: add( HTTP method, URI(path), callback, parameters: [layout, [window-title , [css]], GET [[módulos], [local]], POST], [javascript] )

  HTTP method,
  URI(path),
  callback,
  [layout, 
         [window-title , [css]], 
         [[módulos], [local]], 
         POST],
  [javascript]
*/

$routeList->add(
        'get',
        '/error/',
        'error',
        ['error',
                ['Error', []],
                [['head' => []],
                        []],
                []],
        []
);

$routeList->add(
        'get',
        '/',
        'home',
        ['main',
                ['Inicio', ['interstyle']],
                [['head' => []],
                        []],
                []],
        ['interjs']
);

// pseudorutas y/o llamadas AJAX

// $routeList->add(
//         'get',
//         '/captcha',
//         function () {
//                 $captcha = new Captcha;
//                 $captcha->GET_captcha();
//         },
//         ['', '', [], []],
//         []
// );

// $routeList->add(
//         'get',
//         '/logout',
//         function () {
//                 MainCtrl::logout();
//                 header('Location: ' . URL_BASE);
//         },
//         ['', ['', []], [[], []], []],
//         []
// );
