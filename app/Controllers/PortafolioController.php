<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\PortfolioModel;
use App\Models\CompanyModel;

class PortafolioController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new PortfolioModel();
    }

    public function index($page = 1)
    {
        $limit = 6; // Cantidad por "piso" o carga
        $offset = ($page - 1) * $limit;

        $items = $this->model->getAll($limit, $offset);
        $total = $this->model->getCount();
        $totalPages = ceil($total / $limit);

        // Si es una petición AJAX, devolver solo los items procesados
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'items' => $items,
                'hasMore' => $page < $totalPages,
                'nextPage' => $page + 1
            ]);
            return;
        }

        $data = [
            'title' => 'Portafolio de Trabajos',
            'items' => $items,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'company' => (new CompanyModel())->getProfile()
        ];

        $this->view('portfolio/index', $data);
    }

    public function ver($slug = null)
    {
        if (!$slug) {
            $this->redirect('portafolio');
            return;
        }

        $item = $this->model->getBySlug($slug);
        if (!$item) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'El artículo solicitado no existe.']);
                return;
            }
            $this->redirect('portafolio');
            return;
        }

        // Si es una petición AJAX, devolver solo los datos del item para el modal
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            if (ob_get_length())
                ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'item' => $item]);
            return;
        }

        $data = [
            'item' => $item,
            'title' => $item['titulo'],
            'company' => (new CompanyModel())->getProfile()
        ];

        $this->view('portfolio/view', $data);
    }
}
