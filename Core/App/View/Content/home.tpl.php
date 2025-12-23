<?php
/**
 * Vista: Home (Página de Inicio)
 *
 * Template de contenido principal para la página de inicio del sistema.
 * Este archivo es renderizado por Render::viewContent() y tiene acceso a las
 * variables pasadas desde el controlador a través de routes.php.
 *
 * EJEMPLOS DE VARIABLES DISPONIBLES:
 *
 * $metaParams - Parámetros de metadatos [título de ventana, [estilos CSS]]
 *                          Ejemplo: ['Inicio', ['style1', 'style2']]
 *
 * $body - Variables locales definidas en routes.php
 *                    Proviene de $bodyParams[1] en la configuración de ruta
 *                    Ejemplo: ['usuario' => 'John', 'edad' => 25]
 *
 * $responseParams - Datos de respuesta POST procesados
 *                              Contiene resultados de operaciones POST
 *                              Ejemplo: ['success' => true, 'mensaje' => 'Guardado']
 *
 * CONFIGURACIÓN EN ROUTES.PHP:
 *
 * $routeList->add(
 *     'get',
 *     '/',
 *     'home',  // <- Este nombre debe coincidir con el archivo View/Content/home.tpl.php así como con Controller/Home.class.php y Model/HomeModel.class.php
 *     ['main',
 *         ['Inicio', ['estilos-home']],
 *         [['head' => ['usuario']],           // Módulos GET foráneos
 *          ['datos-del-controlador-Home']     // Variables locales entregados por el Controlador Home ($body)
 *     ],
 *     ['script-home']
 * );
 *
 * BUENAS PRÁCTICAS:
 * - Mantén la lógica PHP al mínimo, solo para presentación
 * - La lógica de negocio debe estar en Controladores
 * - Las llamadas a Base de Datos deben estar en Modelos
 * - Separa HTML y PHP para mejor legibilidad
 *
 * @file home.tpl.php
 */

// Lógica PHP previa al render de la vista
// Aquí puedes preparar variables, formatear datos, etc.
$helloworld = '¡Hola Mundo!';
// Ejemplo de acceso a variables foráneas desde $head, entregados por el Controlador Head
// $dato = $head['propiedad']

// Ejemplo de acceso a variables locales desde $body
// $usuario = $body['usuario'] ?? 'Invitado';
// $mensaje = $body['mensaje'] ?? '';

// Ejemplo de acceso a respuesta POST
// $success = $responseParams['success'] ?? false;
// $error = $responseParams['error'] ?? '';
?>

<div>Inicio</div>
<div><?php echo $helloworld ?></div>