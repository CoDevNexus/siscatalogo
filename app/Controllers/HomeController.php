<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $productoModel = $this->model('ProductoModel');
        $portfolioModel = $this->model('PortfolioModel');
        $homeModel = $this->model('HomeModel');

        // Cargar 6 productos destacados
        $productos = $productoModel->getFeatured(6);

        // Cargar 3 casos de éxito
        $casos = $portfolioModel->getAll(3, 0);

        // Cargar datos del slider y configuraciones
        $slides = $homeModel->getActiveSlides();
        $settings = $homeModel->getSettings();

        $data = [
            'title' => 'Inicio',
            'productos' => $productos,
            'casos' => $casos,
            'slides' => $slides,
            'settings' => $settings,
        ];

        $this->view('home/index', $data);

    }

    public function portfolio()
    {
        // Obtener casos de éxito desde la BD (tabla portfolio_items si existe, si no vacío)
        $db = \App\Core\Database::getInstance();
        $casos = [];
        try {
            $casos = $db->fetchAll(
                "SELECT * FROM portfolio_items WHERE status = 'published' ORDER BY created_at DESC"
            );
        } catch (\Exception $e) {
            // Tabla aún no existe, mostrar vacío
        }

        $this->view('home/portfolio', [
            'title' => 'Portafolio de Trabajos',
            'casos' => $casos,
        ]);
    }

    public function nosotros()
    {
        $this->view('home/nosotros', [
            'title' => 'Sobre Nosotros',
        ]);
    }

    public function descargas()
    {
        $this->view('home/descargas', [
            'title' => 'Acceso a Productos Digitales',
        ]);
    }
}
