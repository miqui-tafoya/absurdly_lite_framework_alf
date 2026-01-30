<?php
/**
 * Middleware - Capa de Validación y Control de Acceso
 *
 * Proporciona métodos para validar credenciales y controlar el acceso a rutas
 * protegidas del sistema. Actúa como intermediario entre la petición del usuario
 * y el controlador final.
 *
 * IMPORTANTE:
 * - Pueden redirigir, modificar la petición o detener la ejecución
 * - Útiles para autenticación, autorización, validación de datos, logging, etc.
 *
 * Uso típico:
 * ```php
 * $middleware = new Middleware();
 * $middleware->Credencial('login'); // Requiere usuario autenticado
 * ```
 *
 * @file Middleware.class.php
 * @version 1.0.0
 */

class Middleware {

    /**
     * Valida credenciales de usuario y controla acceso a rutas
     *
     * Verifica el estado de autenticación del usuario y redirige según el tipo
     * de validación solicitado. Previene acceso no autorizado a áreas protegidas
     * y evita que usuarios autenticados accedan a páginas de login.
     *
     * Tipos de validación de ejemplo:
     * - 'loggedin': Redirige a inicio si el usuario YA está autenticado
     *               (útil para páginas de login/registro)
     * - 'login': Redirige a login si el usuario NO está autenticado
     *            (útil para páginas protegidas que requieren autenticación)
     *
     * @param string $tipo Tipo de validación ('loggedin' o 'login')
     * @return void
     *
     * @example
     * // Proteger una página que requiere login
     * $middleware->Credencial('login');
     *
     * @example
     * // Evitar que usuarios autenticados vean el formulario de login
     * $middleware->Credencial('loggedin');
     */
    public function Credencial($tipo) {
        switch ($tipo) {
            case 'loggedin':
                if (!empty($_SESSION['iduser'])) {
                    header('Location: ' . URL_BASE);
                    exit();
                }
                break;
            case 'login':
                if (empty($_SESSION['iduser'])) {
                    header("Location: " . URL_BASE . 'login');
                    exit();
                }
                break;
        }
    }
    /**
     * Obtiene el origen CORS apropiado para las respuestas HTTP
     *
     * Valida dinámicamente el origen de la petición y devuelve el valor
     * correcto para el header Access-Control-Allow-Origin, permitiendo
     * que el sitio funcione con o sin 'www' en el dominio.
     *
     * @param string|null $mode Si es '*', devuelve '*' (permite cualquier origen).
     *                          Si es null, valida contra el dominio actual.
     * @return string El origen permitido o '*'
     *
     * @example
     * // Permitir solo el dominio actual (con o sin www)
     * header("Access-Control-Allow-Origin: " . $mid->getCorsOrigin());
     *
     * @example
     * // Permitir cualquier origen (útil para APIs públicas)
     * header("Access-Control-Allow-Origin: " . $mid->getCorsOrigin('*'));
     */
    public function getCorsOrigin($mode = null) {
        if ($mode === '*') {
            return '*';
        }
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $baseDomain = str_replace(['https://', 'http://', 'www.'], '', SITE_URL);
        if (strpos($origin, $baseDomain) !== false) {
            return $origin;
        }
        return SITE_URL;
    }
}