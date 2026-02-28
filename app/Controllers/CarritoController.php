<?php
namespace App\Controllers;

use App\Core\Controller;

class CarritoController extends Controller
{

    public function __construct()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Ruta por defecto al acceder a /carrito directamente
    public function index()
    {
        // Por ahora redirigimos al inicio de forma segura, o podríamos cargar una vista tipo "Tu Carrito"
        // Como el modal de SweetAlert hace el trabajo visual, redirigimos por si entran por la barra de direcciones
        $this->redirect('');
    }

    // Método AJAX para añadir al carrito
    public function add()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
            return;
        }

        // Leer JSON entrante
        $data = json_decode(file_get_contents('php://input'), true);

        $productId = $data['id'] ?? null;
        $name = $data['name'] ?? 'Producto';
        $price = $data['price'] ?? 0;
        $qty = $data['quantity'] ?? 1;
        $isDigital = $data['is_digital'] ?? false;
        $image = $data['image'] ?? '';

        // Extras de personalización (físicos)
        $customNote = $data['custom_note'] ?? '';
        $customLogo = $data['custom_logo'] ?? '';

        if (!$productId) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos del producto.']);
            return;
        }

        // Crear item único basado en id + nota + logo (para permitir mismo producto con distintas notas)
        $itemHash = md5($productId . $customNote . $customLogo);

        if (isset($_SESSION['cart'][$itemHash])) {
            // Si ya existe (mismas características), sumar cantidad
            // Si es digital, solo se vende 1 unidad.
            if ($isDigital) {
                echo json_encode(['status' => 'info', 'message' => 'El producto digital ya está en el carrito.']);
                return;
            }
            $_SESSION['cart'][$itemHash]['quantity'] += $qty;
        } else {
            // Añadir nuevo
            $_SESSION['cart'][$itemHash] = [
                'id' => $productId,
                'name' => $name,
                'price' => $price, // Se re-calculará en checkout si aplican docenas
                'quantity' => $qty,
                'is_digital' => $isDigital,
                'image' => $image,
                'custom_note' => $customNote,
                'custom_logo' => $customLogo
            ];
        }

        $totalItems = array_sum(array_column($_SESSION['cart'], 'quantity'));

        echo json_encode([
            'status' => 'success',
            'message' => 'Agregado al carrito.',
            'total_items' => $totalItems
        ]);
    }

    // Método para obtener el total de items en el carrito (Para actualizar el badge global)
    public function count()
    {
        header('Content-Type: application/json');
        $totalItems = array_sum(array_column($_SESSION['cart'] ?? [], 'quantity'));
        echo json_encode(['count' => $totalItems]);
    }

    // Mostrar el resumen temporal con SweetAlert Modal vía AJAX (o retornar HTML/JSON)
    public function get_summary()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['cart'])) {
            echo json_encode(['empty' => true]);
            return;
        }

        $items = [];
        $total = 0;

        foreach ($_SESSION['cart'] as $hash => $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
            $items[] = [
                'name' => $item['name'],
                'qty' => $item['quantity'],
                'price' => number_format($item['price'], 2),
                'subtotal' => number_format($subtotal, 2)
            ];
        }

        echo json_encode([
            'empty' => false,
            'items' => $items,
            'total' => number_format($total, 2)
        ]);
    }
}
