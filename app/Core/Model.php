<?php
namespace App\Core;

class Model
{
    protected $db;

    public function __construct()
    {
        // Obtenemos la conexión Singleton a la DB
        $this->db = \App\Core\Database::getInstance();
    }
}
