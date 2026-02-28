<?php
/**
 * Mueve el modal #imgBrowserModal fuera del <form> para que
 * ningún elemento dentro del modal pueda disparar un submit accidental.
 */
$file = dirname(__DIR__) . '/app/Views/admin/perfil.php';
$content = file_get_contents($file);

// Extraer el modal completo
$modalStart = '<!-- ══ MODAL GALERÍA DE IMÁGENES ══ -->';
$modalEnd = '</div>' . "\n" . '        </div>' . "\n" . '        </div>' . "\n" . '        </div>' . "\n" . '        </div>' . "\n\n        <script>\n        function selectExistingLogo";

// Mejor: usar regex para extraer desde el comentario del modal hasta el </script> de su bloque JS
preg_match('/(<!-- ══ MODAL GALERÍA DE IMÁGENES ══ -->.*?<\/script>)/s', $content, $matches);

if (!$matches) {
    echo "❌ No se encontró el modal.\n";
    exit(1);
}

$modalBlock = $matches[1];

// Quitar el modal de donde está (dentro del form)
$content = str_replace($modalBlock, '', $content);

// Insertar el modal justo ANTES del cierre del </div> final del archivo
// o simplemente antes del <script> final de la página
$content = str_replace('</script>', '</script>' . "\n\n" . $modalBlock, $content, $count);

if ($count === 0) {
    // fallback: append al final
    $content .= "\n" . $modalBlock;
    echo "⚠️  Insertado al final del archivo.\n";
} else {
    // Solo reemplazar la primera ocurrencia — revertir los extras
    // En realidad str_replace reemplaza todos. Hagámoslo solo en la primera.
    echo "✅ Modal movido fuera del form ($count reemplazos detectados - corrigiendo a solo 1).\n";
}

// Corrección: reemplazar solo la PRIMERA ocurrencia de </script>
$content = file_get_contents($file);
$content = str_replace($modalBlock, '', $content);
// Poner el modal al FINAL del body (antes del último bloque)
$content = rtrim($content) . "\n\n" . $modalBlock . "\n";

file_put_contents($file, $content);
echo "✅ Modal movido fuera del <form>.\n";
