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
}