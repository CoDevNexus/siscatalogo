<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($title) ? $title . ' - Admin' : 'Admin Panel' ?>
    </title>

    <?php
    $db = \App\Core\Database::getInstance();
    $_headerCompany = $db->fetch("SELECT * FROM company_profile WHERE id = 1");
    $logoUrl = null;
    if (!empty($_headerCompany['logo_url'])) {
        $logoUrl = str_starts_with($_headerCompany['logo_url'], 'http')
            ? htmlspecialchars($_headerCompany['logo_url'])
            : APP_URL . htmlspecialchars($_headerCompany['logo_url']);
    }
    $currentUri = $_SERVER['REQUEST_URI'];
    ?>

    <?php if ($logoUrl): ?>
        <link rel="icon" type="image/png" href="<?= $logoUrl ?>?t=<?= time() ?>">
    <?php endif; ?>

    <!-- Variables globales para JS -->
    <script>
        const APP_URL = '<?= APP_URL ?>';
        const COMPANY = {
            name: '<?= addslashes($_headerCompany['name'] ?? APP_NAME) ?>',
            logo: '<?= $logoUrl ? addslashes($logoUrl) : '' ?>',
            eslogan: '<?= addslashes($_headerCompany['eslogan'] ?? '') ?>',
            ruc: '<?= addslashes($_headerCompany['ruc_nit'] ?? '') ?>',
            address: '<?= addslashes($_headerCompany['address'] ?? '') ?>',
            city: '<?= addslashes($_headerCompany['ciudad'] ?? '') ?>',
            phone: '<?= addslashes($_headerCompany['phone_whatsapp'] ?? $_headerCompany['whatsapp'] ?? '') ?>',
            email: '<?= addslashes($_headerCompany['email'] ?? '') ?>',
            tax_rate: <?= (float) ($_headerCompany['tax_rate'] ?? 0) ?>,
            terms: `<?= addslashes($_headerCompany['terms_conditions'] ?? '') ?>`,
            thanks: `<?= addslashes($_headerCompany['thank_you_message'] ?? '') ?>`,
            footer_image: '<?= !empty($_headerCompany['footer_image_url']) ? (str_starts_with($_headerCompany['footer_image_url'], 'http') ? addslashes($_headerCompany['footer_image_url']) : APP_URL . addslashes($_headerCompany['footer_image_url'])) : '' ?>'
        };
    </script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            --transition-speed: 0.3s;
        }

        body {
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background-color: #1a1d20;
            color: #fff;
            transition: width var(--transition-speed) ease;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar .brand {
            padding: 20px 15px;
            white-space: nowrap;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .brand img {
            max-height: 35px;
            transition: all var(--transition-speed);
        }

        .sidebar.collapsed .brand span,
        .sidebar.collapsed .nav-text {
            display: none;
        }

        .sidebar.collapsed .brand {
            padding: 20px 0;
            text-align: center;
        }

        .sidebar .nav-link {
            color: #adb5bd;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            white-space: nowrap;
            border-radius: 0;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }

        .sidebar .nav-link i {
            font-size: 1.2rem;
            min-width: 30px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #2b3035;
            color: #fff;
            border-left-color: #ffc107;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 15px 0;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0 !important;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed) ease;
            min-height: 100vh;
            background-color: #f4f7f6;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
            }

            .sidebar.show {
                left: 0;
                width: 260px;
            }

            .sidebar.show .nav-text,
            .sidebar.show .brand span {
                display: inline-block !important;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <div class="d-flex align-items-center">
                <?php if ($logoUrl): ?>
                    <img src="<?= $logoUrl ?>" class="me-2 rounded shadow-sm" alt="Logo">
                <?php else: ?>
                    <i class="bi bi-gear-fill text-warning fs-3 me-2"></i>
                <?php endif; ?>
                <span class="fw-bold fs-5 text-truncate"><?= $_headerCompany['name'] ?? 'Admin' ?></span>
            </div>
        </div>

        <nav class="nav flex-column mt-3">
            <a href="<?= APP_URL ?>admin"
                class="nav-link <?= (strpos($currentUri, '/admin') !== false && strpos($currentUri, '/admin/') === false) ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <?php if (\App\Core\Security::can('configuracion.ver') || \App\Core\Security::can('slider.gestionar')): ?>
                <a href="<?= APP_URL ?>admin/home_slider"
                    class="nav-link <?= strpos($currentUri, '/admin/home_slider') !== false ? 'active' : '' ?>">
                    <i class="bi bi-images me-2"></i>
                    <span class="nav-text">Gestión de Slider</span>
                </a>
                <a href="<?= APP_URL ?>admin/home_settings"
                    class="nav-link <?= strpos($currentUri, '/admin/home_settings') !== false ? 'active' : '' ?>">
                    <i class="bi bi-fonts me-2"></i>
                    <span class="nav-text">Textos del Home</span>
                </a>
            <?php endif; ?>

            <?php if (\App\Core\Security::can('productos.ver') || \App\Core\Security::can('categorias.ver')): ?>
                <a href="<?= APP_URL ?>admin/productos"
                    class="nav-link <?= strpos($currentUri, '/admin/productos') !== false ? 'active' : '' ?>">
                    <i class="bi bi-box-seam me-2"></i>
                    <span class="nav-text">Inventario</span>
                </a>
                <a href="<?= APP_URL ?>admin/categorias"
                    class="nav-link <?= strpos($currentUri, '/admin/categorias') !== false ? 'active' : '' ?>">
                    <i class="bi bi-tags me-2"></i>
                    <span class="nav-text">Categorías</span>
                </a>
            <?php endif; ?>

            <?php if (\App\Core\Security::can('pedidos.ver') || \App\Core\Security::can('digitales.ver')): ?>
                <a href="<?= APP_URL ?>cotizacion/admin_index"
                    class="nav-link <?= strpos($currentUri, 'cotizacion/admin_index') !== false ? 'active' : '' ?>">
                    <i class="bi bi-cart-check me-2"></i>
                    <span class="nav-text">Gestión de Pedidos</span>
                </a>
                <a href="<?= APP_URL ?>admin/digitales"
                    class="nav-link <?= strpos($currentUri, '/admin/digitales') !== false ? 'active' : '' ?>">
                    <i class="bi bi-cloud-arrow-down me-2"></i>
                    <span class="nav-text">Entregas Digitales</span>
                </a>
            <?php endif; ?>

            <?php if (\App\Core\Security::can('portfolio.ver')): ?>
                <a href="<?= APP_URL ?>admin/portfolio"
                    class="nav-link <?= strpos($currentUri, '/admin/portfolio') !== false ? 'active' : '' ?>">
                    <i class="bi bi-image me-2"></i>
                    <span class="nav-text">Casos de Éxito</span>
                </a>
            <?php endif; ?>
            <?php if (\App\Core\Security::can('configuracion.ver')): ?>
                <a href="<?= APP_URL ?>admin/perfil"
                    class="nav-link <?= strpos($currentUri, '/admin/perfil') !== false ? 'active' : '' ?>">
                    <i class="bi bi-shop me-2"></i>
                    <span class="nav-text">Perfil Empresa</span>
                </a>
            <?php endif; ?>

            <hr class="text-secondary mx-3 my-2">

            <?php if (\App\Core\Security::can('usuarios.ver')): ?>
                <a href="<?= APP_URL ?>admin/usuarios"
                    class="nav-link <?= strpos($currentUri, '/admin/usuarios') !== false ? 'active' : '' ?>">
                    <i class="bi bi-people-fill me-2"></i>
                    <span class="nav-text">Usuarios</span>
                </a>
            <?php endif; ?>

            <?php if (\App\Core\Security::can('roles.ver')): ?>
                <a href="<?= APP_URL ?>admin/roles"
                    class="nav-link <?= strpos($currentUri, '/admin/roles') !== false ? 'active' : '' ?>">
                    <i class="bi bi-shield-lock-fill me-2"></i>
                    <span class="nav-text">Roles y Permisos</span>
                </a>
            <?php endif; ?>

            <?php if (\App\Core\Security::can('bitacora.ver')): ?>
                <a href="<?= APP_URL ?>admin/bitacora"
                    class="nav-link <?= strpos($currentUri, '/admin/bitacora') !== false ? 'active' : '' ?>">
                    <i class="bi bi-journal-text me-2"></i>
                    <span class="nav-text">Bitácora</span>
                </a>
            <?php endif; ?>
        </nav>


        <div class="mt-auto pb-4">
            <hr class="text-secondary mx-3">
            <a href="<?= APP_URL ?>syslogin/logout" class="nav-link text-danger">
                <i class="bi bi-box-arrow-left me-2"></i>
                <span class="nav-text">Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <script>
            // Aplicar estado colapsado inmediatamente en desktop para evitar el destello (FOUC)
            if (window.innerWidth > 768) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('mainContent').classList.add('expanded');
            }
        </script>
        <!-- Header superior -->
        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 border-bottom sticky-top">
            <div class="d-flex align-items-center">
                <button class="btn btn-light border me-3" id="toggleSidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="d-flex align-items-center d-sm-none me-2">
                    <?php if ($logoUrl): ?>
                        <img src="<?= $logoUrl ?>" height="30" class="me-2 rounded shadow-sm" alt="Logo">
                    <?php endif; ?>
                </div>
                <h5 class="mb-0 fw-bold text-dark d-none d-sm-block">
                    <?= $title ?? 'Dashboard' ?>
                </h5>
            </div>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle border" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                    <?= $_SESSION['username'] ?? 'Admin' ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= APP_URL ?>" target="_blank"><i class="bi bi-eye"></i> Ver
                            Tienda</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>syslogin/logout">Salir</a></li>
                </ul>
            </div>
        </div>

        <!-- Contenedor dinámico -->
        <div class="container-fluid px-0">