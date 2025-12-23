<?php

/**
 * RouteList - Gestor de Registro de Rutas
 *
 * Administra el registro y configuración de todas las rutas del sistema.
 * Combina parámetros globales (defaults) con parámetros específicos de cada ruta,
 * y prepara la estructura final para el Router.
 *
 * FUNCIONALIDADES:
 * - Registra rutas GET/POST con sus parámetros
 * - Combina estilos CSS globales con específicos de ruta
 * - Combina scripts JS globales con específicos de ruta
 * - Combina módulos GET globales con específicos de ruta
 * - Agrega automáticamente $_POST a rutas POST
 * - Mantiene orden de carga: start → específicos → end
 *
 * ESTRUCTURA DE DEFAULTS:
 * - default_styles: ['start' => [...], 'end' => [...]]
 * - default_js: ['start' => [...], 'end' => [...]]
 * - default_GET: ['modulo' => ['prop1', 'prop2']]
 *
 * ORDEN DE CARGA:
 * CSS: start → específicos de ruta → end
 * JS:  start → específicos de ruta → end
 * GET: globales + específicos de ruta (merge)
 *
 * @file RouteList.class.php
 * @package Router
 * 
 * @var array Estilos CSS por defecto (start/end)
 * @var array Scripts JavaScript por defecto (start/end)
 * @var array Módulos GET por defecto
 * @var array Listado de rutas registradas
 * @return array Array de rutas indexado por [method][path]
 */

namespace Router;

class RouteList {

    private array $params;
    private array $js;
    private array $get;
    protected array $routes = [];

    public function __construct($default_styles = [], $default_js = [], $default_GET = []) {
        !empty($default_styles) ? $this->params = $default_styles : $this->params = [];
        !empty($default_js) ? $this->js = $default_js : $this->js = [];
        !empty($default_GET) ? $this->get = $default_GET : $this->get = [];
    }

    /**
     * Registra una nueva ruta en el sistema
     *
     * Combina parámetros globales con específicos de la ruta y prepara
     * la estructura final. Agrega automáticamente $_POST para rutas POST.
     *
     * Proceso:
     * 1. Combina JS: start → específicos → end
     * 2. Si callback NO es función:
     *    - Combina CSS: start → específicos → end
     *    - Combina GET: globales + específicos
     *    - Agrega $_POST automáticamente si method es 'post'
     * 3. Almacena ruta en array indexado por [method][path]
     */
    public function add($method, $path, $callback, $params, $js = []) {
        $js = empty($js) ? array_merge($this->js['start'], $this->js['end']) : array_merge($this->js['start'], $js, $this->js['end']);
        if (!is_callable($callback)) {
            $params[1][1] = empty($params[1][1]) ? array_merge($this->params['start'], $this->params['end']) : array_merge($this->params['start'], $params[1][1], $this->params['end']);
            $params[2][0] = empty($params[2][0]) ? $this->get : array_merge($this->get, $params[2][0]);
            $params[3] = strtolower($method) === 'post' ? $_POST : [];
        }
        $this->routes[$method][$path] = [$callback, $params, $js];
    }

    public function fetchRoutes() {
        return $this->routes;
    }
}