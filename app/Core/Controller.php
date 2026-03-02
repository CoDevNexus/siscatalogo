<?php
namespace App\Core;

class Controller
{
    // Cargar modelo
    public function model($model)
    {
        $modelClass = '\\App\\Models\\' . $model;
        if (class_exists($modelClass)) {
            return new $modelClass();
        }
        die("Modelo no encontrado: $modelClass");
    }

    // Cargar vista junto con los layouts cabecera y pie
    public function view($view, $data = [])
    {
        // Extraer variables para que la vista las use directamente (ej: $titulo)
        if (!empty($data)) {
            extract($data);
        }

        // Comprobar que el archivo de vista maestro/específico exista
        if (file_exists(BASE_PATH . 'app/Views/' . $view . '.php')) {
            // Comprobar ruta donde estamos para decidir layout si es Admin o Front
            $isAdminView = strpos($view, 'admin/') === 0;

            if ($isAdminView) {
                // Vistas de autenticación — standalone sin layout admin
                $standaloneViews = ['admin/login', 'admin/forgot_password', 'admin/reset_password'];
                if (in_array($view, $standaloneViews)) {
                    require BASE_PATH . 'app/Views/' . $view . '.php';
                } else {
                    if (file_exists(BASE_PATH . 'app/Views/layout/admin_header.php')) {
                        require BASE_PATH . 'app/Views/layout/admin_header.php';
                    }
                    require BASE_PATH . 'app/Views/' . $view . '.php';
                    if (file_exists(BASE_PATH . 'app/Views/layout/admin_footer.php')) {
                        require BASE_PATH . 'app/Views/layout/admin_footer.php';
                    }
                }
            } else {
                if (file_exists(BASE_PATH . 'app/Views/layout/header.php')) {
                    require BASE_PATH . 'app/Views/layout/header.php';
                }
                require BASE_PATH . 'app/Views/' . $view . '.php';
                if (file_exists(BASE_PATH . 'app/Views/layout/footer.php')) {
                    require BASE_PATH . 'app/Views/layout/footer.php';
                }
            }
        } else {
            die("La vista no existe: $view");
        }
    }

    // Método para redireccionar usando la ruta relativa a la APP
    public function redirect($url)
    {
        header("Location: " . APP_URL . $url);
        exit;
    }
}
