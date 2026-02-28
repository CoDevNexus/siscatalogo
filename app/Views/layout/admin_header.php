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
    $_headerCompany = $db->fetch("SELECT name, logo_url FROM company_profile WHERE id = 1");
    $logoUrl = !empty($_headerCompany['logo_url']) ? APP_URL . htmlspecialchars($_headerCompany['logo_url']) : null;
    $currentUri = $_SERVER['REQUEST_URI'];
    ?>

    <?php if ($logoUrl): ?>
        <link rel="icon" type="image/png" href="<?= $logoUrl ?>?t=<?= time() ?>">
    <?php endif; ?>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            color: #fff;
        }

        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #343a40;
            color: #fff;
        }

        .content-area {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 250px;">
            <h4 class="text-white text-center mb-4 fw-bold">
                <i class="bi bi-gear-fill text-warning"></i> Admin
            </h4>
            <hr class="text-secondary">
            <nav class="nav flex-column mt-3">
                <a href="<?= APP_URL ?>admin"
                    class="nav-link <?= (strpos($currentUri, '/admin') !== false && strpos($currentUri, '/admin/') === false) ? 'active' : '' ?>"><i
                        class="bi bi-speedometer2 me-2"></i>
                    Dashboard</a>
                <a href="<?= APP_URL ?>admin/productos"
                    class="nav-link <?= strpos($currentUri, '/admin/productos') !== false ? 'active' : '' ?>"><i
                        class="bi bi-box-seam me-2"></i>
                    Inventario</a>
                <a href="<?= APP_URL ?>admin/ventas"
                    class="nav-link <?= strpos($currentUri, '/admin/ventas') !== false ? 'active' : '' ?>"><i
                        class="bi bi-cart-check me-2"></i>
                    Proformas</a>
                <a href="<?= APP_URL ?>admin/digitales"
                    class="nav-link <?= strpos($currentUri, '/admin/digitales') !== false ? 'active' : '' ?>"><i
                        class="bi bi-cloud-arrow-down me-2"></i>
                    Entregas Digitales</a>
                <a href="<?= APP_URL ?>admin/portfolio"
                    class="nav-link <?= strpos($currentUri, '/admin/portfolio') !== false ? 'active' : '' ?>"><i
                        class="bi bi-image me-2"></i> Casos de
                    Éxito</a>
                <a href="<?= APP_URL ?>admin/perfil"
                    class="nav-link <?= strpos($currentUri, '/admin/perfil') !== false ? 'active' : '' ?>"><i
                        class="bi bi-shop me-2"></i> Perfil Empresa</a>
            </nav>
            <div class="mt-auto pt-5">
                <hr class="text-secondary">
                <a href="<?= APP_URL ?>syslogin/logout" class="nav-link text-danger mt-3">
                    <i class="bi bi-box-arrow-left me-2"></i> Cerrar Sesión
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-area flex-grow-1 p-4">
            <!-- Header superior -->
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
                <h5 class="mb-0 fw-bold text-dark">
                    <?= $title ?? 'Dashboard' ?>
                </h5>
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