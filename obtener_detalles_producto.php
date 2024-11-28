<?php
// obtener_detalles_producto.php

include('config.php');

if (isset($_GET['id_producto'])) {
    $id_producto = $_GET['id_producto'];

    // Obtener precio e impuesto del producto seleccionado
    $stmt = $pdo->prepare("SELECT precio, impuesto_unitario FROM productos WHERE id_producto = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        echo json_encode($producto);
    } else {
        echo json_encode(['precio' => 0, 'impuesto_unitario' => 0]);
    }
} else {
    echo json_encode(['precio' => 0, 'impuesto_unitario' => 0]);
}
