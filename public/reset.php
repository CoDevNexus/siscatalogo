<?php
session_start();
session_destroy();
echo "<h1>Sesiones borradas</h1><a href='http://localhost/siscatalogo/syslogin'>Intentar login de nuevo</a>";
