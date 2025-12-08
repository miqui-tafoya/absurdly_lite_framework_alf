<?php
/**
 * Configuración de Rutas
 *
 * Aquí se definen todas las rutas existentes de tu proyecto.
 *
 * TIPOS DE RUTAS PERMITIDAS POR PHP-ALFv1:
 * - Ruta dinámica para errores
 * - Ruta estática GET/POST
 * - Ruta dinámica GET/POST
 * - Pseudoruta GET/POST con función que llama método AJAX de Controlador
 * - Pseudoruta GET/POST con función declarada por el usuario
 * - Endpoint GET/POST
 *
 * ESTRUCTURA DEL MÉTODO add():
 *
 * Sintaxis: add(HTTP method, URI(path), callback, parameters, javascript)
 *
 * Parámetros:
 * - HTTP method: 'get' o 'post'
 * - URI(path): '/ruta' para rutas estáticas, o '/ruta/' para rutas dinámicas
 * - callback: string (nombre de MVC a invocar) o callable (función anónima).
 *   Aquí se registra el nombre que el framework buscará en Controller, Model y View
 * - parameters: [layout, [Título de ventana, [css]], [[propiedades de módulos], [propiedades locales]]]
 *   Los layouts se registran en Render y se crean sus archivos correspondientes en Core/View/Layouts
 * - javascript: array de scripts específicos de la ruta
 *
 * Formato legible:
 * $routeList->add(
 *     'método',                                 // HTTP method (get o post)
 *     '/ruta',                                  // URI
 *     'MVC',                                    // callback
 *     ['layout',                                // layout a usar
 *         ['Título de ventana', ['css1', ...]], // título y estilos específicos (nombres de archivos sin la extensión .css)
 *         [['módulo' => ['propiedad', ...]],    // módulos GET
 *          ['propiedad', ...]]                  // propiedades locales
 *     ],
 *     ['script1', ...]                    // scripts específicos (nombres de archivos sin la extensión .js)
 * );
 * 
 * NOTA IMPORTANTE:
 * Las rutas con formularios, por ejemplo, deben tener sus dos rutas: la ruta get y la ruta post.
 *
 * EJEMPLOS DE USO:
 *
 * Ruta estática GET:
 * $routeList->add(
 *      'get',
 *      '/contacto',
 *      'contacto',
 *      ['layout_simple',
 *           ['Contáctanos', ['estilos-contacto', 'estilos-formularios']],
 *           [['head' => ['usuario', 'fecha'], ['footer' => ['usuario', 'fecha']],
 *            ['directorio', 'nombres', 'apellidos']]
 *      ],
 *      ['script-enviar']
 * );
 *
 * Ruta estática POST:
 * $routeList->add(
 *      'post',
 *      '/contacto',
 *      'contacto',
 *      ['layout_simple',
 *           ['Contáctanos', ['estilos-contacto', 'estilos-formularios']],
 *           [['head' => ['usuario', 'fecha'], ['footer' => ['usuario', 'fecha']],
 *            ['directorio', 'nombres', 'apellidos']]
 *      ],
 *      ['script-enviar']
 * );
 *
 * Ruta dinámica GET (con parámetro):
 * $routeList->add('get', '/usuario/{id}', 'usuario', ['main', ['Usuario', []], []], []);
 *
 * Pseudoruta con función (AJAX):
 * $routeList->add('post', '/api/guardar', function() {
 *     MainCtrl::guardarDatos();
 * }, ['', ['', []], []], []);
 *
 * - Instancia Controllers
 *   Aquí se hace referencia a aquellos controladores que sean requerimiento para
 *   métodos utilizados en las pseudorutas, por ejemplo, para llamadas AJAX.
 *
 * - Instancia Globales
 *
 *   default_styles: Define hojas de estilo globales con dos posiciones ('start' y 'end').
 *   Orden de carga: 'start' → estilos específicos de ruta → 'end'.
 *   Útil para librerías como FontAwesome (inicio) o Media Queries (final).
 *   Ruta base: Public/styles
 *   Ejemplo: 'fonts/css/all.min' (sin extensión .css)
 *   $default_styles = ['start' => ['fonts/css/all.min', 'html'], 'end' => ['media-query']];
 *
 *   default_GET: Propiedades globales requeridas en todo el sistema.
 *   Deben estar registradas en Render y en sus respectivas clases.
 *   Ejemplo: ['head' => ['foo'], 'footer' => ['bar']] invoca las propiedades
 *   'foo' y 'bar' de los Controladores-Modelo 'Head' y 'Footer'.
 *
 *   default_js: Define scripts globales con dos posiciones ('start' y 'end').
 *   Orden de carga: 'start' → scripts específicos de ruta → 'end'.
 *   Útil para librerías como jQuery que deben cargarse primero.
 *   Ruta base: Public/js
 *   Ejemplo: 'dir/script' (sin extensión .js)
 *   $default_js = ['start' => ['jquery-3.6.1.min'], 'end' => ['script1-final', 'script2-final']];
 */

// Instancia Router
use Router\RouteList;

// Instancia Controllers
use Controller\MainCtrl;

// Instancia Globales
$default_styles = ['start' => [], 'end' => []];
$default_GET = [];
$default_js = ['start' => [], 'end' => []];

$routeList = new RouteList($default_styles, $default_js, $default_GET);

// Rutas Fijas Recomendadas
$routeList->add(
        'get',
        '/error/',
        'error',
        ['error',
                ['Error', []],
                [[],
                        []]],
        []
);

$routeList->add(
        'get',
        '/',
        'home',
        ['main',
                ['Inicio', []],
                [[],
                        []]],
        []
);

// Rutas

// $routeList->add(
//         'get',
//         '/ejemplo',
//         'ejemplo',
//         ['', ['', []], []],
//         []
// );

// $routeList->add(
//         'post',
//         '/ejemplo',
//         'ejemplo',
//         ['', ['', []], []],
//         []
// );

// $routeList->add(
//         'get',
//         '/ejemplo/',
//         'ejemplo',
//         ['main',
//                 ['Inicio', ['interstyle']],
//                 [['head' => []],
//                         []]],
//         []
// );

// $routeList->add(
//         'post',
//         '/ejemplo/',
//         'ejemplo',
//         ['main',
//                 ['Inicio', ['interstyle']],
//                 [['head' => []],
//                         []]],
//         []
// );

// pseudorutas y/o llamadas AJAX

// $routeList->add(
//         'get',
//         '/logout',
//         function () {
//                 MainCtrl::logout();
//                 header('Location: ' . URL_BASE);
//         },
//         ['',['', []],[[],[]]],
//         []
// );

// $routeList->add(
//         'get',
//         '/ejemplo',
//         function () {
//                 $post = new Ejemplo;
//                 $post->GET_handler_AJAX();
//         },
//         ['',['', []],[[],[]]],
//         []
// );

// $routeList->add(
//         'post',
//         '/ejemplo',
//         function () {
//                 $post = new Ejemplo;
//                 $post->POST_handler_AJAX();
//         },
//         ['',['', []],[[],[]]],
//         []
// );

// API endpoints

// $routeList->add(
//         'get',
//         '/apitest',
//         'apitest',
//         ['',['', []],[[],[]]],
//         []
// );

// $routeList->add(
//         'post',
//         '/apilogin',
//         'apilogin',
//         ['',['', []],[[],[]]],
//         []
// );