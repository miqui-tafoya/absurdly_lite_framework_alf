<?php
/**
 * Sistema de Registro de Errores (Error Logging)
 *
 * Captura y registra todos los errores PHP en archivos de log diarios.
 * Incluye manejo de errores estándar, warnings, notices y errores fatales.
 *
 * CARACTERÍSTICAS:
 * - Logs organizados por fecha (YYYY-MM-DD.log)
 * - Captura errores no fatales (warnings, notices, deprecated)
 * - Captura errores fatales (parse, compile, core errors)
 * - Muestra mensaje amigable al usuario en caso de error fatal
 * - Crea automáticamente el directorio de logs si no existe
 *
 * UBICACIÓN DE LOGS:
 * Los archivos de log se guardan en la ruta definida por LOG_DIR (init.php)
 * Por defecto: Public/logs/
 *
 * FORMATO DE LOG:
 * [YYYY-MM-DD HH:MM:SS] [TIPO_ERROR] mensaje in archivo.php:línea
 * ----------------------------------------
 *
 * @file errlog.php
 * @version 1.0.0
 */

// crear directorio
if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}

/**
 * Registra un error en el archivo de log diario
 *
 * @param string $tipo Tipo de error (ERROR, WARNING, NOTICE, etc.)
 * @param string $msg Mensaje del error
 * @param string $file Archivo donde ocurrió el error
 * @param int $line Línea donde ocurrió el error
 * @return void
 */
function errLog($tipo, $msg, $file, $line) {
    $timestamp = date('Y-m-d H:i:s');
    $archivoLog = LOG_DIR . '/' . date('Y-m-d') . '.log';
    $msg = "[$timestamp] [$tipo] $msg in $file:$line" . PHP_EOL;
    $msg .= str_repeat("-", 40) . PHP_EOL;
    error_log($msg, 3, $archivoLog);
}

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $errType = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_NOTICE => 'NOTICE',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_DEPRECATED => 'DEPRECATED'
    ];

    $tipo = $errType[$errno] ?? 'OTRO';
    errLog($tipo, $errstr, $errfile, $errline);

    return false;
});

// handler de error fatal
register_shutdown_function(function () {
    $error = error_get_last();
    $fatal = [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE];

    if ($error && in_array($error['type'], $fatal, true)) {
        errLog('FATAL_ERROR', $error['message'], $error['file'], $error['line']);
        if (ob_get_level() > 0) {
            @ob_end_clean();
        }
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
        }
        echo "Error interno del servidor: intenta nuevamente la operación; si el error persiste, por favor contacta a Soporte TI.";
    }
});