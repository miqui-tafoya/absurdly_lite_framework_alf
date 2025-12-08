<?php
/**
 * Layout: Main
 *
 * Layout principal del sistema con estructura HTML completa.
 * Incluye todos los componentes estándar: meta, head, menu, content, footer y js.
 *
 * PLACEHOLDERS:
 * - {{meta}}: Metadatos (title, charset, viewport, CSS)
 * - {{head}}: Contenido personalizado del <head>
 * - {{menu}}: Menú de navegación
 * - {{content}}: Contenido principal de la vista
 * - {{footer}}: Pie de página
 * - {{js}}: Scripts JavaScript
 *
 * Los placeholders son reemplazados por Render::renderView() con el contenido real.
 *
 * @file main.tpl.php
 */
?>
<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
  {{meta}}
</head>

<body class="pushmenu-push">
  {{head}}
  {{menu}}
  {{content}}
  {{footer}}
</body>
{{js}}

</html>