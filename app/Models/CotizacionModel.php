<?php
namespace App\Models;

use App\Core\Model;

class CotizacionModel extends Model
{
    public function getAll($filters = [], $limit = 10, $offset = 0, $orderBy = 'created_at', $orderDir = 'DESC')
    {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['name'])) {
            $where[] = "customer_name LIKE :name";
            $params['name'] = "%" . $filters['name'] . "%";
        }
        if (!empty($filters['city'])) {
            $where[] = "customer_city LIKE :city";
            $params['city'] = "%" . $filters['city'] . "%";
        }
        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "created_at >= :date_from";
            $params['date_from'] = $filters['date_from'] . " 00:00:00";
        }
        if (!empty($filters['date_to'])) {
            $where[] = "created_at <= :date_to";
            $params['date_to'] = $filters['date_to'] . " 23:59:59";
        }

        // Validar columnas permitidas para el ORDER BY para evitar SQL Injection
        $allowedCols = ['id', 'customer_name', 'customer_city', 'total_amount', 'status', 'created_at', 'has_digital'];
        if (!in_array($orderBy, $allowedCols)) {
            $orderBy = 'created_at';
        }
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT o.*, 
                (SELECT COUNT(*) FROM order_items oi INNER JOIN products p ON oi.product_id = p.id WHERE oi.order_id = o.id AND p.is_digital = 1) as has_digital 
                FROM orders o 
                WHERE " . str_replace(
            " customer_name",
            " o.customer_name",
            str_replace(
                " customer_city",
                " o.customer_city",
                str_replace(
                    " status",
                    " o.status",
                    str_replace(" created_at", " o.created_at", implode(" AND ", $where))
                )
            )
        ) .
            " ORDER BY o.$orderBy $orderDir LIMIT $limit OFFSET $offset";

        return $this->db->fetchAll($sql, $params);
    }

    public function getCount($filters = [])
    {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['name'])) {
            $where[] = "customer_name LIKE :name";
            $params['name'] = "%" . $filters['name'] . "%";
        }
        if (!empty($filters['city'])) {
            $where[] = "customer_city LIKE :city";
            $params['city'] = "%" . $filters['city'] . "%";
        }
        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "created_at >= :date_from";
            $params['date_from'] = $filters['date_from'] . " 00:00:00";
        }
        if (!empty($filters['date_to'])) {
            $where[] = "created_at <= :date_to";
            $params['date_to'] = $filters['date_to'] . " 23:59:59";
        }

        $sql = "SELECT COUNT(*) as total FROM orders WHERE " . implode(" AND ", $where);
        $res = $this->db->fetch($sql, $params);
        return (int) $res['total'];
    }

    public function getById($id)
    {
        return $this->db->fetch("SELECT * FROM orders WHERE id = :id", ['id' => $id]);
    }

    public function getItems($orderId)
    {
        return $this->db->fetchAll(
            "SELECT oi.*, p.name as product_name, p.is_digital, p.digital_file_path 
             FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = :order_id",
            ['order_id' => $orderId]
        );
    }

    public function create($orderData, $items)
    {
        $sql = "INSERT INTO orders (customer_name, customer_id, customer_email, customer_phone, customer_city, customer_address, total_amount, shipping_amount, tax_amount, needs_shipping, needs_invoice, status) 
                VALUES (:name, :cid, :email, :phone, :city, :address, :total, :shipping, :tax, :needs_shipping, :needs_invoice, 'Pendiente')";

        $this->db->query($sql, [
            'name' => $orderData['customer_name'],
            'cid' => $orderData['customer_id'] ?? null,
            'email' => $orderData['customer_email'],
            'phone' => $orderData['customer_phone'] ?? null,
            'city' => $orderData['customer_city'],
            'address' => $orderData['customer_address'] ?? null,
            'total' => $orderData['total_amount'],
            'shipping' => $orderData['shipping_amount'] ?? 0,
            'tax' => $orderData['tax_amount'] ?? 0,
            'needs_shipping' => isset($orderData['needs_shipping']) ? $orderData['needs_shipping'] : 0,
            'needs_invoice' => isset($orderData['needs_invoice']) ? $orderData['needs_invoice'] : 0
        ]);

        $orderId = $this->db->lastInsertId();

        foreach ($items as $item) {
            $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, price_applied, subtotal, custom_note, custom_logo_link) 
                        VALUES (:order_id, :product_id, :qty, :price, :subtotal, :note, :logo)";
            $this->db->query($sqlItem, [
                'order_id' => $orderId,
                'product_id' => $item['id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['qty'],
                'note' => $item['note'] ?? null,
                'logo' => $item['logo_url'] ?? null
            ]);
        }

        return $orderId;
    }

    public function updateStatus($id, $status)
    {
        return $this->db->query("UPDATE orders SET status = :status WHERE id = :id", [
            'status' => $status,
            'id' => $id
        ]);
    }

    public function approveDigital($id, $user, $pass)
    {
        return $this->db->query(
            "UPDATE orders SET digital_user = :user, digital_pass = :pass, digital_approved = TRUE, status = 'Finalizado' WHERE id = :id",
            ['user' => $user, 'pass' => $pass, 'id' => $id]
        );
    }

    public function updateOrder($id, $data, $items)
    {
        // Actualizar totales y datos de la orden
        $sqlOrder = "UPDATE orders SET 
                     customer_name = :name,
                     customer_email = :email,
                     customer_phone = :phone,
                     customer_city = :city,
                     customer_address = :address,
                     customer_id = :cid,
                     total_amount = :total, 
                     shipping_amount = :shipping, 
                     tax_amount = :tax,
                     needs_shipping = :needs_shipping,
                     needs_invoice = :needs_invoice
                     WHERE id = :id";
        $this->db->query($sqlOrder, [
            'name' => $data['customer_name'],
            'email' => $data['customer_email'],
            'phone' => $data['customer_phone'],
            'city' => $data['customer_city'],
            'address' => $data['customer_address'],
            'cid' => $data['customer_id'] ?? null,
            'total' => $data['total_amount'],
            'shipping' => $data['shipping_amount'],
            'tax' => $data['tax_amount'],
            'needs_shipping' => $data['needs_shipping'] ?? 0,
            'needs_invoice' => $data['needs_invoice'] ?? 0,
            'id' => $id
        ]);

        // Eliminar ítems anteriores y re-insertar
        $this->db->query("DELETE FROM order_items WHERE order_id = :id", ['id' => $id]);

        foreach ($items as $item) {
            $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, price_applied, subtotal, custom_note, custom_logo_link) 
                        VALUES (:order_id, :product_id, :qty, :price, :subtotal, :note, :logo)";
            $this->db->query($sqlItem, [
                'order_id' => $id,
                'product_id' => $item['product_id'],
                'qty' => $item['quantity'],
                'price' => $item['price_applied'],
                'subtotal' => $item['subtotal'],
                'note' => $item['custom_note'] ?? null,
                'logo' => $item['custom_logo_link'] ?? null
            ]);
        }
        return true;
    }
}
