<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($title) ? htmlspecialchars($title) . ' – ' . APP_NAME : 'Portal Digital – ' . APP_NAME ?>
    </title>

    <?php
    $db = \App\Core\Database::getInstance();
    $company = $db->fetch("SELECT * FROM company_profile WHERE id = 1");
    $logoUrl = null;
    if (!empty($company['logo_url'])) {
        $logoUrl = str_starts_with($company['logo_url'], 'http')
            ? htmlspecialchars($company['logo_url'])
            : APP_URL . htmlspecialchars($company['logo_url']);
    }
    ?>

    <?php if ($logoUrl): ?>
        <link rel="icon" type="image/png" href="<?= $logoUrl ?>?t=<?= time() ?>">
    <?php endif; ?>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <script>const APP_URL = '<?= APP_URL ?>';</script>
</head>

<body class="bg-light">