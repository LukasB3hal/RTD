



<?php

//=========================================================================
//=========================================================================
// Script na zajištění připojení k databázi.
//=========================================================================
//=========================================================================

// config.php – sdílej tento soubor (include/require) ve všech skriptech

// DB nastavení
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rtd');

// Bezpečné session cookie (pozor: 'secure' => true vyžaduje HTTPS)
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',    // případně '.example.com'
    'secure' => false, // při nasazení do produkce: true (pouze přes HTTPS)
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Připojení přes MySQLi (object-oriented)
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    error_log("DB connect error: " . $mysqli->connect_error);
    // V produkci: nevracej uživateli surové chyby DB
    die("Databáze momentálně nedostupná.");
}
$mysqli->set_charset('utf8mb4');