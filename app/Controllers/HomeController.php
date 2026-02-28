<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $productoModel = $this->model('ProductoModel');
        $categoryModel = $this->model('CategoryModel');

        $filterCat = isset($_GET['cat']) ? (int) $_GET['cat'] : null;
        $filterType = isset($_GET['tipo']) ? $_GET['tipo'] : null;

        $productos = $productoModel->getActive($filterCat, $filterType);
        $categorias = $categoryModel->getAll();

        $data = [
            'title' => 'Catálogo de Productos',
            'productos' => $productos,
            'categorias' => $categorias,
            'filterCat' => $filterCat,
            'filterType' => $filterType,
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
}
