<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' – ' . APP_NAME : APP_NAME ?></title>
    <meta name="description"
        content="<?= isset($item['meta_description']) ? htmlspecialchars($item['meta_description']) : 'Catálogo de productos láser personalizados: MDF, acrílico, sublimación y vectores digitales.' ?>">

    <?php
    $db = \App\Core\Database::getInstance();
    $company = $db->fetch("SELECT * FROM company_profile WHERE id = 1");
    $companyName = !empty($company['name']) ? htmlspecialchars($company['name']) : APP_NAME;
    $logoUrl = null;
    if (!empty($company['logo_url'])) {
        $logoUrl = str_starts_with($company['logo_url'], 'http')
            ? htmlspecialchars($company['logo_url'])
            : APP_URL . htmlspecialchars($company['logo_url']);
    }
    // Normalizar: preferir nuevas columnas, hacer fallback a las viejas
    $company['whatsapp'] = !empty($company['whatsapp']) ? $company['whatsapp'] : ($company['phone_whatsapp'] ?? '');
    $company['facebook'] = !empty($company['facebook']) ? $company['facebook'] : ($company['facebook_url'] ?? '');
    $company['instagram'] = $company['instagram'] ?? '';
    ?>


    <?php if ($logoUrl): ?>
        <link rel="icon" type="image/png" href="<?= $logoUrl ?>?t=<?= time() ?>">
    <?php endif; ?>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Estilos Premium -->
    <link rel="stylesheet"
        href="<?= APP_URL ?>assets/css/style.css?v=<?= filemtime(BASE_PATH . 'public/assets/css/style.css') ?>">

    <?php if (!empty($company['theme_primary']) || !empty($company['theme_accent']) || !empty($company['theme_navbar']) || !empty($company['theme_footer'])): ?>
        <style>
            :root {
                <?php if (!empty($company['theme_primary'])): ?>
                    --primary:
                        <?= htmlspecialchars($company['theme_primary']) ?>
                    ;
                <?php endif; ?>
                <?php if (!empty($company['theme_accent'])): ?>
                    --accent:
                        <?= htmlspecialchars($company['theme_accent']) ?>
                    ;
                <?php endif; ?>
            }

            <?php
            $navBg = !empty($company['theme_navbar']) ? $company['theme_navbar'] : (!empty($company['theme_primary']) ? $company['theme_primary'] : '');
            if ($navBg):
                ?>
                .navbar-premium {
                    background-color:
                        <?= htmlspecialchars($navBg) ?>
                        !important;
                }

            <?php endif; ?>

            <?php
            $footerBg = !empty($company['theme_footer']) ? $company['theme_footer'] : (!empty($company['theme_primary']) ? $company['theme_primary'] : '');
            if ($footerBg):
                ?>
                .footer-premium {
                    background-color:
                        <?= htmlspecialchars($footerBg) ?>
                        !important;
                }

            <?php endif; ?>
        </style>
    <?php endif; ?>

    <!-- Variables globales para JS -->
    <script>
        const APP_URL = '<?= APP_URL ?>';
        const COMPANY = {
            name: '<?= addslashes($companyName) ?>',
            logo: '<?= $logoUrl ? addslashes($logoUrl) : '' ?>',
            eslogan: '<?= addslashes($company['eslogan'] ?? '') ?>',
            ruc: '<?= addslashes($company['ruc_nit'] ?? '') ?>',
            address: '<?= addslashes($company['address'] ?? '') ?>',
            city: '<?= addslashes($company['ciudad'] ?? '') ?>',
            phone: '<?= addslashes($company['whatsapp'] ?? '') ?>',
            email: '<?= addslashes($company['email'] ?? '') ?>',
            whatsapp: '<?= addslashes($company['whatsapp'] ?? '') ?>',
            facebook: '<?= addslashes($company['facebook'] ?? '') ?>',
            instagram: '<?= addslashes($company['instagram'] ?? '') ?>',
            shipping_cost: <?= (float) ($company['shipping_cost'] ?? 0) ?>,
            tax_rate: <?= (float) ($company['tax_rate'] ?? 0) ?>,
            terms: `<?= addslashes($company['terms_conditions'] ?? '') ?>`,
            thanks: `<?= addslashes($company['thank_you_message'] ?? '') ?>`,
            footer_image: '<?= !empty($company['footer_image_url']) ? (str_starts_with($company['footer_image_url'], 'http') ? addslashes($company['footer_image_url']) : APP_URL . addslashes($company['footer_image_url'])) : '' ?>'
    };
    </script>
</head>

<body>

    <!-- ─── Navbar ─── -->
    <nav class="navbar navbar-expand-lg navbar-premium sticky-top py-3">
        <div class="container d-flex align-items-center">
            <a class="navbar-brand fw-bold text-white d-flex align-items-center gap-2" href="<?= APP_URL ?>">
                <?php if ($logoUrl): ?>
                    <img src="<?= $logoUrl ?>?t=<?= time() ?>" alt="Logo">
                <?php else: ?>
                    <i class="bi bi-heptagon-half text-danger fs-3"></i>
                <?php endif; ?>
                <span class="fs-5"><?= $companyName ?></span>
            </a>

            <button class="navbar-toggler border-0 shadow-none text-white ms-auto me-3" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list fs-2"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <?php
                // Detectar pestaña activa
                $currentUri = $_SERVER['REQUEST_URI'];
                $isHome = (strpos($currentUri, '/productos') === false && strpos($currentUri, '/portafolio') === false && strpos($currentUri, '/nosotros') === false);
                $isFisico = (isset($_GET['tipo']) && $_GET['tipo'] === 'fisico');
                $isDigital = (isset($_GET['tipo']) && $_GET['tipo'] === 'digital');

                // Si se seleccionó una categoría en /productos, revisar su tipo
                if (isset($_GET['cat']) && strpos($currentUri, '/productos') !== false) {
                    $catId = (int) $_GET['cat'];
                    $catData = $db->fetch("SELECT type FROM categories WHERE id = :id", ['id' => $catId]);
                    if ($catData) {
                        if ($catData['type'] === 'physical')
                            $isFisico = true;
                        if ($catData['type'] === 'digital')
                            $isDigital = true;
                    }
                }

                // Por defecto si entra a /productos sin tipo ni cat, se asume físico
                if (strpos($currentUri, '/productos') !== false && !$isFisico && !$isDigital) {
                    $isFisico = true;
                }
                ?>
                <ul class="navbar-nav mx-auto gap-1">
                    <li class="nav-item">
                        <a class="nav-link premium-nav-link <?= $isHome ? 'active' : '' ?>"
                            href="<?= APP_URL ?>">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link premium-nav-link <?= $isFisico ? 'active' : '' ?>"
                            href="<?= APP_URL ?>productos?tipo=fisico">Físicos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link premium-nav-link <?= $isDigital ? 'active' : '' ?>"
                            href="<?= APP_URL ?>productos?tipo=digital">Digitales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link premium-nav-link <?= strpos($currentUri, '/portafolio') !== false ? 'active' : '' ?>"
                            href="<?= APP_URL ?>portafolio">Portafolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link premium-nav-link <?= strpos($currentUri, '/nosotros') !== false ? 'active' : '' ?>"
                            href="<?= APP_URL ?>nosotros">Empresa</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center mt-3 mt-lg-0 gap-2">
                    <!-- WhatsApp rápido -->
                    <?php if (!empty($company['whatsapp'])): ?>
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $company['whatsapp']) ?>" target="_blank"
                            class="btn btn-success rounded-pill px-3 py-1 fw-semibold" style="font-size:.85rem">
                            <i class="bi bi-whatsapp me-1"></i>WhatsApp
                        </a>
                    <?php endif; ?>

                    <!-- Portal Digital -->
                    <a href="<?= APP_URL ?>digital/login"
                        class="btn btn-outline-info rounded-pill px-3 py-1 fw-semibold border-2"
                        style="font-size:.85rem">
                        <i class="bi bi-cloud-arrow-down-fill me-1"></i>Portal
                    </a>

                    <!-- Carrito -->
                    <a href="#" class="btn btn-outline-light rounded-pill px-4 position-relative" data-open-cart>
                        <i class="bi bi-cart3 me-1"></i>Pedido
                        <span id="cart-badge"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-dark"
                            style="display:none">0</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ─── Contenido Principal ─── -->
    <main class="container py-4">