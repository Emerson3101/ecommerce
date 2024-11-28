<?php
// config.php

$host = 'controlescolar.postgres.database.azure.com';
$dbname = 'ecommerce';
$user = 'memerson';
$password = 'Abysswalker@1';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
