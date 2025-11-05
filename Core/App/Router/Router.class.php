<?php

namespace Router;

use Controller\MainCtrl;

class Router {

    public Request $request;
    public array $serverRequest;
    public array $routeList;
    public MainCtrl $maincontroller;

    public function __construct(Request $request, array $serverRequest, array $routeList) {
        $this->request = $request;
        $this->serverRequest = $serverRequest;
        $this->routeList = $routeList;
        $this->maincontroller = new MainCtrl;
    }

    public function resolve() {
        if (file_exists(APP_ROOT . DIRECTORY_SEPARATOR . 'man.on')) { // está en modo mantenimiento?
            header('Location:' . URL_PUBLIC . 'mantenimiento/');
            exit;
        } else {
            $method = \strtolower($this->serverRequest['REQUEST_METHOD']); // obtiene método solicitado: GET o POST
            $query_string = [];
            $raw_path = $this->serverRequest['REQUEST_URI'];
            $get_position = \strpos($raw_path, '?');
            switch ($method) {
                case 'get':
                case 'post':
                    if ($get_position !== false) {
                        $query_string = $this->parseQuery($raw_path); // obtiene los queries del query string
                        $raw_path = \substr($raw_path, 0, $get_position);
                    }
                    $clean_path = APP_SUBFOLDER ? str_replace(APP_SUBFOLDER . '/', '', $raw_path) : $raw_path;
                    $path = $this->request->getPath($clean_path); // obtiene URI(path)

                    if (is_array($path)) { // la ruta obtenida es un array (ruta dinámica)
                        $routeParameters = isset($this->routeList[$method][$path[0]]) ? $this->routeList[$method][$path[0]] : false; // existe la ruta?
                        if ($routeParameters !== false) {
                            $isvalid = $this->maincontroller->validateModel($routeParameters[0], $path[1]); // valida existencia del 'query' contenido en el array de la ruta dinámica
                            if ($isvalid !== true) { // si ruta es inválida remite a error 404
                                $routeParameters = false;
                            } else {
                                $routeParameters[1][2][1]['query'] = $path[1]; // agrega segundo valor de array de ruta dinámica al elemento 'query' dentro del array GET de los Parámetros de la Ruta
                            }
                        } else {
                            $routeParameters = false; // ruta inválida
                        }
                    } else { // la ruta obtenida es una función (pseudoruta función)
                        $routeParameters = isset($this->routeList[$method][$path]) ? $this->routeList[$method][$path] : false;
                        if ($routeParameters != false) { // revisa si es nulo
                            if (\is_callable($routeParameters[0])) { // revisa si ruta es función
                                \call_user_func($routeParameters[0]);
                                exit;
                            }
                        }
                    }
                    if ($routeParameters === false) { // la ruta obtenida no existe
                        $status = 404;
                        header('Location:' . URL_BASE . 'error/' . $status);
                        exit;
                    } elseif (\is_string($routeParameters[0])) { // la ruta obtenida es un string (ruta estática)
                        return $this->maincontroller->fetchController($routeParameters, $method, $query_string);
                    }
                    break;
                default:
                    $status = 405;
                    header('Location:' . URL_BASE . 'error/' . $status);
                    exit;
            }
        }
    }

    private function parseQuery($querystring) {
        $segmentos = explode('?', $querystring, 2);
        if (count($segmentos) >= 2) {
            $queryString = html_entity_decode($segmentos[1]);
            parse_str($queryString, $result);
        } else {
            $result = [];
        }
        return $result;
    }
}
