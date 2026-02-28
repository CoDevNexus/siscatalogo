<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$rows = $db->getPdo()->query('DESCRIBE company_profile')->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r)
    echo $r['Field'] . ' – ' . $r['Type'] . PHP_EOL;
