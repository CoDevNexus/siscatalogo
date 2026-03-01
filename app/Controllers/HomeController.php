<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $productoModel = $this->model('ProductoModel');
        $portfolioModel = $this->model('PortfolioModel');

        // Cargar 6 productos destacados (los más recientes activos)
        $productos = $productoModel->getFeatured(6);

        // Cargar 3 casos de éxito más recientes
        $casos = $portfolioModel->getAll(3, 0);

        $data = [
            'title' => 'Inicio',
            'productos' => $productos,
            'casos' => $casos,
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
