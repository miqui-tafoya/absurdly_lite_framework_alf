<?php

/**
 * Request - Procesador de Rutas HTTP
 *
 * Analiza y procesa las rutas URI solicitadas, determinando si son rutas
 * estáticas o dinámicas (con parámetros).
 *
 * TIPOS DE RUTAS:
 *
 * 1. Ruta Estática: '/contacto', '/login', '/dashboard'
 *    Retorna: string con la ruta completa
 *
 * 2. Ruta Dinámica: '/usuario/123', '/articulo/mi-post'
 *    Retorna: array ['/usuario/', '123'] donde:
 *    - Primer elemento: ruta base con '/' al inicio y final
 *    - Segundo elemento: parámetro dinámico (ID, slug, etc.)
 * *
 * @file Request.class.php
 * @package Router
 */

namespace Router;

class Request {

    /**
     * Procesa y analiza la ruta URI solicitada
     *
     * Determina si la ruta es estática (sin parámetros) o dinámica (con parámetros).
     * Las rutas dinámicas contienen un '/' adicional que separa la ruta base del parámetro.
     *
     * Proceso:
     * 1. Normaliza rutas vacías o '/index' a '/'
     * 2. Busca '/' adicional para detectar parámetros dinámicos
     * 3. Si no hay '/', retorna la ruta como string (estática)
     * 4. Si hay '/', divide y retorna array [ruta_base, parámetro]
     *
     * @param string $path Ruta URI a procesar (ej: '/usuario/123')
     * @return string|array String para rutas estáticas, Array para rutas dinámicas
     *
     * @example
     * // Ruta estática
     * $request->getPath('/contacto'); // Retorna: '/contacto'
     *
     * @example
     * // Ruta dinámica
     * $request->getPath('/usuario/25'); // Retorna: ['/usuario/', '25']
     */
    public function getPath($path) {
        $path = (!isset($path) || $path === '/index') ? DIRECTORY_SEPARATOR : $path;
        $is_path_dynamic = \substr($path, 1);
        $position_dir = \strpos($is_path_dynamic, '/');
        if ($position_dir === false) {
            return $path;
        }
        $path = \explode('/', $is_path_dynamic);
        $path[0] = '/' . $path[0] . '/';
        return $path;
    }
}
