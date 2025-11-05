<?php

namespace Router;

class RouteList {

    private array $params;
    private array $js;
    private array $get;
    protected array $routes = [];

    public function __construct($default_styles = [], $default_js = [], $default_GET = []) { // construír defaults si es que existen
        !empty($default_styles) ? $this->params = $default_styles : $this->params = [];
        !empty($default_js) ? $this->js = $default_js : $this->js = [];
        !empty($default_GET) ? $this->get = $default_GET : $this->get = [];
    }

    public function add($method, $path, $callback, $params, $js = []) { // agrega ruta al listado y acomoda parámetros para lectura de Router
        // agregar js default
        $js = empty($js) ? array_merge($this->js['start'], $this->js['end']) : array_merge($this->js['start'], $js, $this->js['end']);
        if (!is_callable($callback)) { // el callback de la ruta NO es función
            // agregar estilos default
            $params[1][1] = empty($params[1][1]) ? array_merge($this->params['start'], $this->params['end']) : array_merge($this->params['start'], $params[1][1], $this->params['end']);
            // agregar GET default
            $params[2][0] = empty($params[2][0]) ? $this->get : array_merge($this->get, $params[2][0]);
        }
        $this->routes[$method][$path] = [$callback, $params, $js];
    }

    public function fetchRoutes() { // obtiene rutas existentes
        return $this->routes;
    }
}