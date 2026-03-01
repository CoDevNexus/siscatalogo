<?php
namespace App\Core;

class Router
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function dispatch($url)
    {
        $url = $this->parseUrl($url);

        // --- Alias de Rutas SEO ---
        if (isset($url[0])) {
            if ($url[0] === 'portafolio' && isset($url[1]) && $url[1] === 'page') {
                // portafolio/page/2 -> PortafolioController/index/2
                $url[0] = 'portafolio';
                $url[1] = 'index';
            } elseif ($url[0] === 'caso-de-exito' && isset($url[1])) {
                // caso-de-exito/slug -> PortafolioController/ver/slug
                $url[0] = 'portafolio';
                $params = array_slice($url, 1);
                $url[1] = 'ver';
                // El resto (slug) se mantiene como parámetros
            }
        }

        // Si la URL está vacía, cargar controlador por defecto
        $controllerName = isset($url[0]) ? ucwords($url[0]) . 'Controller' : $this->controller;

        // Comprobar si el archivo del controlador existe
        if (file_exists(BASE_PATH . 'app/Controllers/' . $controllerName . '.php')) {
            $this->controller = $controllerName;
            if (isset($url[0])) {
                unset($url[0]);
            }
        }

        // Instanciar el controlador
        $controllerClass = '\\App\\Controllers\\' . $this->controller;
        if (!class_exists($controllerClass)) {
            // Manejo simple de error 404
            header("HTTP/1.0 404 Not Found");
            die("Error 404: Controlador no encontrado ($controllerClass)");
        }

        $this->controller = new $controllerClass();

        // Comprobar si existe el método en el controlador
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Antes de invocar el método, validar estrictamente que exista en la instancia actual
        // Para evitar Uncaught TypeError: call_user_func_array() si no tiene \index
        if (!method_exists($this->controller, $this->method)) {
            header("HTTP/1.0 404 Not Found");
            die("Error 404: El método '{$this->method}' no existe en el controlador '" . get_class($this->controller) . "'");
        }

        // Obtener parámetros y reindexar el arreglo
        $this->params = $url ? array_values($url) : [];

        // Ejecutar el método del controlador pasándole los parámetros
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    protected function parseUrl($url)
    {
        if ($url) {
            return explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
