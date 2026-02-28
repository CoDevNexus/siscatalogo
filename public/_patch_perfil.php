<?php
$file = dirname(__DIR__) . '/app/Views/admin/perfil.php';
$content = file_get_contents($file);

$js = <<<'JSCODE'

// ── Selector de destino de logo ──
function toggleDestCard() {
    const selLocal  = document.getElementById('dest_local');
    const cardLocal = document.getElementById('card_local');
    const cardImgBB = document.getElementById('card_imgbb');
    if (!selLocal) return;
    if (cardLocal) {
        cardLocal.style.borderColor = selLocal.checked ? '#0d6efd' : '';
        cardLocal.style.background  = selLocal.checked ? 'rgba(13,110,253,.06)' : '';
    }
    if (cardImgBB) {
        cardImgBB.style.borderColor = !selLocal.checked ? '#0dcaf0' : '';
        cardImgBB.style.background  = !selLocal.checked ? 'rgba(13,202,240,.06)' : '';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var destLocal = document.getElementById('dest_local');
    if (destLocal) { destLocal.checked = true; toggleDestCard(); }
});
JSCODE;

// Insert before the closing </script>
$content = preg_replace('/<\/script>\s*$/', $js . "\n</script>", $content);
file_put_contents($file, $content);
echo "JS insertado correctamente.\n";
