<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$row = $db->fetch("SELECT * FROM company_profile WHERE id = 1");
echo "<pre>";
print_r($row);
echo "</pre>";
