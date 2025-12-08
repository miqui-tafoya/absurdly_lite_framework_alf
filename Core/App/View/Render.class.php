<?php

/**
 * Render - Motor de Renderizado de Vistas
 *
 * Clase abstracta que proporciona la funcionalidad para renderizar vistas HTML
 * utilizando un sistema de plantillas (templates). Combina layouts, componentes
 * y contenido dinámico para generar la respuesta HTML final.
 *
 * ARQUITECTURA:
 * - Layouts: Estructuras base de página (p.ej., main, error, default)
 * - Components: Elementos reutilizables (p.ej., head, menu, footer, modal, etc.)
 * - Content: Contenido específico de cada vista
 * - Placeholders: Marcadores {{placeholder}} reemplazados por contenido real
 *
 * SISTEMA DE PLANTILLAS:
 * Los layouts contienen placeholders que son reemplazados por componentes, por ejemplo:
 * - {{meta}}: Metadatos HTML (title, charset, viewport, etc.)
 * - {{head}}: Contenido del <head> personalizado
 * - {{menu}}: Menú de navegación
 * - {{modal}}: Ventanas modales
 * - {{content}}: Contenido principal de la vista
 * - {{footer}}: Pie de página
 * - {{js}}: Scripts JavaScript
 *
 * @file Render.class.php
 * @package View
 */

namespace View;

abstract class Render {

    /**
     * Renderiza una vista completa combinando layout, componentes y contenido
     *
     * Método principal que orquesta el proceso de renderizado. Carga el layout
     * solicitado, obtiene todos los componentes necesarios y reemplaza los
     * placeholders con el contenido real.
     *
     * NOTAS IMPORTANTES:
     *
     * - Layout Fijo: el layout de error puede ser modificado mas no eliminado.
     * - Nuevo Layout: Para crear un layout nuevo es necesario registrarlo como un caso
     *   dentro del switch, y posteriormente agregar las funciones necesarias para su
     *   funcionamiento, es decir ¿utilizará la función viewMeta() o va a requerir
     *   una función propia, por ejemplo, viewMetaAdmin()? en este caso debe crearse
     *   la función viewMetaAdmin() en la Zona de funciones para la carga de layouts
     *   que se encuentra más abajo. Además de registrar el Layout, también es necesario
     *   crear los archivos *.tpl.php correspondientes dentro de Core/App/View/Layouts
     * - Funciones Fijas: no es recomentable eliminar funciones para carga de layout
     *   como layoutContent(), viewContent(), viewMeta(), viewJs(), y viewMetaError().
     *   Fuera de estas anteriores, todas las demás que están de ejemplo pueden ser
     *   eliminadas, tales como viewHead(), viewMenu(), viewModal().
     * - Hay que tener muy presente que $templates y $markup deben coincidir exactamente
     *   en el orden en el que están
     *   Si $templates contiene [$viewMeta, $viewMenu, $viewHead, $viewContent, $viewFooter, $viewJs],
     *   entonces $markup debe reflejarlo ['{{meta}}', '{{menu}}', '{{head}}', '{{content}}', '{{footer}}', '{{js}}']
     *
     * EJEMPLO DE LAYOUT COMPLEJO CON MÓDULOS GET:
     *
     * El caso 'complex' (comentado) muestra cómo usar propiedades foráneas desde routes.php.
     * Las variables $head y $menu extraen datos del array $bodyParams[0] que puede proviner de:
     *
     * 1. Ya sea desde $default_GET en routes.php (propiedades globales):
     *    $default_GET = ['head' => ['usuario', 'fecha'], 'menu' => ['permisos']];
     *
     * 2. O bien desde $routeList->add() en el array de módulos GET foráneos:
     *    $routeList->add(
     *        'get',
     *        '/dashboard',
     *        'dashboard',
     *        ['complex',
     *            ['Dashboard', ['estilos']],
     *            [['head' => ['usuario', 'notificaciones'], 'menu' => ['opciones', 'permisos']],
     *             ['localVar' => 'valor']]
     *        ],
     *        ['scripts']
     *    );
     *
     * En este ejemplo:
     * - $head contendrá ['usuario', 'notificaciones'] que se pasan a viewHead($head)
     * - $menu contendrá ['opciones', 'permisos'] que se pasan a viewMenu($menu)
     * - Estas propiedades deben estar definidas en sus respectivos Controladores-Modelo, por ejemplo
     *   las propiedades ['usuario', 'notificaciones'] provendrán de un Controlador/Modelo llamados
     *   Head, y ['opciones', 'permisos'] de un Controlador/Modelo llamados Menu
     * - Los métodos viewHead() y viewMenu() deben aceptar estos parámetros y procesarlos
     *
     * Para activar este layout:
     * 1. Descomentar dentro de renderView() las líneas de $head y $menu
     * 2. Descomentar el case 'complex' dentro del switch
     * 3. Modificar viewHead() y viewMenu() para aceptar parámetros
     * 4. Registrar las propiedades en los Controladores y Modelos correspondientes; y en caso de no 
     * existir estos aún, crearlos y configurarlos.
     *
     *
     * @param string $view Nombre de la vista a renderizar (sin extensión .tpl.php)
     * @param string $layout Layout a utilizar (p.ej., 'main', 'error', 'default')
     * @param array $metaParams Parámetros para metadatos [título, [estilos CSS]]
     * @param array $bodyParams Parámetros del body (p.ej., [['head' => []], ['local' => []]])
     * @param array $responseParams Parámetros de respuesta (datos POST procesados)
     * @param array $js Array de scripts JavaScript a incluir
     * @return string HTML completo renderizado
     */
    protected function renderView($view, $layout, $metaParams, $bodyParams = [], $responseParams = [], $js = []) {
        // $head = !empty($bodyParams[0]) ? $bodyParams[0]['head'] : [];
        // $menu = !empty($bodyParams[0]) ? $bodyParams[0]['menu'] : [];
        $body = !empty($bodyParams[1]) ? $bodyParams[1] : [];
        $layoutContent = $this->layoutContent($layout);
        $viewContent = $this->viewContent($view, $metaParams, $body, $responseParams);
        $viewJs = $this->viewJs($js);
        $viewFooter = $this->viewFooter();
        // $viewModal = $this->viewModal();
        switch ($layout) {
            case 'error':
                $viewHead = $this->viewHead();
                $viewMenu = $this->viewMenu();
                $viewMetaError = $this->viewMetaError($metaParams, $body);
                $templates = [$viewMetaError, $viewMenu, $viewHead, $viewContent, $viewFooter, $viewJs];
                $markup = ['{{meta}}', '{{menu}}', '{{head}}', '{{content}}', '{{footer}}', '{{js}}'];
                break;
            case 'main':
                $viewHead = $this->viewHead();
                $viewMenu = $this->viewMenu();
                $viewMeta = $this->viewMeta($metaParams);
                $templates = [$viewMeta, $viewMenu, $viewHead, $viewContent, $viewFooter, $viewJs];
                $markup = ['{{meta}}', '{{menu}}', '{{head}}', '{{content}}', '{{footer}}', '{{js}}'];
                break;
            // case 'complex':
            //     $viewHead = $this->viewHead($head);
            //     $viewMenu = $this->viewMenu($menu);
            //     $viewMeta = $this->viewMeta($metaParams);
            //     $templates = [$viewMeta, $viewModal, $viewMenu, $viewHead, $viewContent, $viewFooter, $viewJs];
            //     $markup = ['{{meta}}', '{{modal}}', '{{menu}}', '{{head}}', '{{content}}', '{{footer}}', '{{js}}'];
            //     break;
            default:
                $viewMeta = $this->viewMeta($metaParams);
                $templates = [$viewMeta, $viewContent, $viewJs];
                $markup = ['{{meta}}', '{{content}}', '{{js}}'];
                break;
        }
        return str_replace($markup, $templates, $layoutContent);
    }

    /* Zona de funciones para la carga de layouts */

    /**
     * Carga el contenido del layout especificado
     *
     * @param string $layout Nombre del layout
     * @return string Contenido HTML del layout con placeholders
     */
    private function layoutContent($layout) {
        ob_start();
        include_once COREAPP . "View/Layouts/$layout.tpl.php";
        return ob_get_clean();
    }

    /**
     * Renderiza los metadatos para páginas de error
     *
     * @param array $metaParams Parámetros de metadatos
     * @param array $body Parámetros del body
     * @return string HTML de metadatos de error
     */
    private function viewMetaError($metaParams, $body) {
        ob_start();
        include_once COREAPP . 'View/Layouts/inc/metaerror.tpl.php';
        return ob_get_clean();
    }

    /**
     * Renderiza los metadatos estándar de la página
     *
     * @param array $metaParams Parámetros [título, [estilos CSS]]
     * @return string HTML de metadatos (title, meta tags, CSS)
     */
    private function viewMeta($metaParams) {
        ob_start();
        include_once COREAPP . 'View/Layouts/inc/meta.tpl.php';
        return ob_get_clean();
    }

    private function viewHead() {
        ob_start();
        include_once COREAPP . 'View/Layouts/inc/head.tpl.php';
        return ob_get_clean();
    }
    // private function viewHead($head) {
    //     ob_start();
    //     include_once COREAPP . 'View/Layouts/inc/head.tpl.php';
    //     return ob_get_clean();
    // }

    private function viewMenu() {
        ob_start();
        include_once COREAPP . 'View/Layouts/inc/menu.tpl.php';
        return ob_get_clean();
    }
    // private function viewMenu($menu) {
    //     ob_start();
    //     include_once COREAPP . 'View/Layouts/inc/menu.tpl.php';
    //     return ob_get_clean();
    // }

    // public function viewModal() {
    //     ob_start();
    //     include_once COREAPP . 'View/Layouts/inc/modal.tpl.php';
    //     return ob_get_clean();
    // }

    /**
     * Renderiza el contenido principal de la vista
     *
     * @param string $view Nombre de la vista
     * @param array $metaParams Parámetros de metadatos
     * @param array $body Variables locales disponibles en la vista
     * @param array $responseParams Parámetros de respuesta
     * @return string HTML del contenido principal
     */
    private function viewContent($view, $metaParams, $body, $responseParams) {
        ob_start();
        include_once COREAPP . "View/Content/$view.tpl.php";
        return ob_get_clean();
    }

    private function viewFooter() {
        ob_start();
        include_once COREAPP . 'View/Layouts/inc/footer.tpl.php';
        return ob_get_clean();
    }

    /**
     * Renderiza los scripts JavaScript
     *
     * @param array $js Array de nombres de scripts (sin extensión .js)
     * @return string HTML con tags <script>
     */
    private function viewJs($js) {
        ob_start();
        include_once COREAPP . 'View/Layouts/inc/js.tpl.php';
        return ob_get_clean();
    }
}