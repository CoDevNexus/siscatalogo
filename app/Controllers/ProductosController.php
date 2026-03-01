<?php
namespace App\Controllers;

use App\Core\Controller;

class ProductosController extends Controller
{
    public function index()
    {
        $productoModel = $this->model('ProductoModel');
        $categoryModel = $this->model('CategoryModel');

        $filterCat = isset($_GET['cat']) ? (int) $_GET['cat'] : null;
        $filterType = isset($_GET['tipo']) ? $_GET['tipo'] : null;
        $search = isset($_GET['q']) ? trim($_GET['q']) : null;

        // Podríamos implementar paginación aquí en el futuro si el catálogo crece mucho
        $productos = $productoModel->getActive($filterCat, $filterType, $search);
        $categorias = $categoryModel->getAll();

        $data = [
            'title' => 'Catálogo de Productos',
            'productos' => $productos,
            'categorias' => $categorias,
            'filterCat' => $filterCat,
            'filterType' => $filterType,
            'search' => $search
        ];

        $this->view('productos/index', $data);
    }
}
