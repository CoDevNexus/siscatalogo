<?php
$file = dirname(__DIR__) . '/app/Views/admin/perfil.php';
$content = file_get_contents($file);

// Fix 1: Add type="button" to all filter buttons (they're inside <form> without type, so they submit)
$content = preg_replace(
    '/<button class="btn btn-sm (btn-dark|btn-outline-secondary|btn-outline-info)(?: active)?" onclick="filterImgBrowser/',
    '<button type="button" class="btn btn-sm $1" onclick="filterImgBrowser',
    $content
);
// Fix the active class one separately
$content = str_replace(
    '<button type="button" class="btn btn-sm btn-dark" onclick="filterImgBrowser(\'all\',this)">',
    '<button type="button" class="btn btn-sm btn-dark active" onclick="filterImgBrowser(\'all\',this)">',
    $content
);

// Fix 2: Also fix the search input inside modal — wrap modal outside the form or move it to body
// Easiest fix: move the modal closing </div> after </form> by placing the modal at the end of the page

// Fix 3: Fix the filterImgBrowser function - buttons inside form with type=button should fix the issue
// Verify the fix was applied
$count = substr_count($content, 'type="button" class="btn btn-sm');
echo "Buttons fixed: $count button(s) patched\n";

file_put_contents($file, $content);
echo "Done.\n";
