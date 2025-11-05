<?php

namespace Router;

class Request {
    public function getPath($path) {
        // $raw_path = $uri;
        // $path = \str_replace(APP_SUBFOLDER . '/', '', $raw_path);
        $path = (!isset($path) || $path === '/index') ? DIRECTORY_SEPARATOR : $path; // si la ruta está vacía se le asigna raíz '/' o index
        $is_path_dynamic = \substr($path, 1);
        $position_dir = \strpos($is_path_dynamic, '/'); // busca el caracter '/' al final de la ruta
        if ($position_dir === false) { // caracter '/' al final de la ruta no encontrado, ruta no es dinámica
            return $path; // devuelve ruta no-dinámica
        }
        $path = \explode('/', $is_path_dynamic); // divide ruta en elementros de array 
        $path[0] = '/' . $path[0] . '/';
        return $path; // devuelve ruta dinámico
    }
}
