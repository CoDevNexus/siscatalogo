<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\HomeModel;

class ProductosController extends Controller
{
    public function index()
    {
        $productoModel = $this->model('ProductoModel');
        $categoryModel = $this->model('CategoryModel');

        $filterCat = isset($_GET['cat']) ? (int) $_GET['cat'] : null;
        $filterType = isset($_GET['tipo']) ? $_GET['tipo'] : null;
        $search = isset($_GET['q']) ? trim($_GET['q']) : null;

        $homeModel = new HomeModel();

        // Podríamos implementar paginación aquí en el futuro si el catálogo crece mucho
        $productos = $productoModel->getActive($filterCat, $filterType, $search);
        $categorias = $categoryModel->getAll();
        $settings = $homeModel->getSettings();

        $data = [
            'title' => 'Catálogo de Productos',
            'productos' => $productos,
            'categorias' => $categorias,
            'filterCat' => $filterCat,
            'filterType' => $filterType,
            'search' => $search,
            'settings' => $settings
        ];

        $this->view('productos/index', $data);
    }
}
