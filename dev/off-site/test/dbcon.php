<?php

// Setup connection to database
$host = '194.233.171.92';
$user = 'root';
$pass = 'LkK21Kaalpdbt11Q';
$charset = 'utf8mb4';

// Connect to database
// https://stackoverflow.com/questions/4380813/how-to-get-rid-of-mysql-error-prepared-statement-needs-to-be-re-prepared
$dsn = "mysql:host=$host;dbname=shapeofus_eu_db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => true                       // Was true, but changed to false to allow for LIMIT in pdo_get_rows
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    trigger_error('Could not connect to database: ' . $e->getMessage(), E_USER_ERROR);
}
