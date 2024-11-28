<?php
// index.php

// Incluir la configuración de la base de datos
include('config.php');

// Si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_producto = $_POST['id_producto'];
    $cliente = $_POST['cliente'];
    $apellidos = $_POST['apellidos'];
    $unidades = $_POST['unidades'];

    // Obtener precio e impuesto del producto
    $stmt = $pdo->prepare("SELECT precio, impuesto_unitario FROM productos WHERE id_producto = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        // Calcular el costo total
        $precio = $producto['precio'];
        $impuesto = $producto['impuesto_unitario'];
        $costo_total = ($precio + $impuesto) * $unidades;

        // Insertar la transacción en la tabla facturas
        $stmt = $pdo->prepare("INSERT INTO facturas (cliente, apellidos, id_producto, unidades, costo_total) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$cliente, $apellidos, $id_producto, $unidades, $costo_total]);

        // Obtener los detalles de la transacción desde la vista
        $stmt = $pdo->prepare("SELECT * FROM vista_transacciones WHERE cliente = ? AND apellidos = ? ORDER BY id_factura DESC LIMIT 1");
        $stmt->execute([$cliente, $apellidos]);
        $transaccion = $stmt->fetch(PDO::FETCH_ASSOC);

        // Mostrar el resultado de la transacción
        if ($transaccion) {
            echo "<div class='resultado'><h3>Transacción exitosa</h3>";
            echo "<p>Factura registrada con éxito. Los detalles de la compra son:</p>";
            echo "<ul>
                    <li><strong>Cliente:</strong> " . $transaccion['cliente'] . " " . $transaccion['apellidos'] . "</li>
                    <li><strong>Producto:</strong> " . $transaccion['nom_prod'] . "</li>
                    <li><strong>Unidades:</strong> " . $transaccion['unidades'] . "</li>
                    <li><strong>Precio Unitario:</strong> " . $transaccion['precio'] . "</li>
                    <li><strong>Impuesto Unitario:</strong> " . $transaccion['impuesto_unitario'] . "</li>
                    <li><strong>Costo Total:</strong> " . $transaccion['costo_total'] . "</li>
                </ul>
                </div>";
                $stmt = $pdo->query("SELECT id_producto, nom_prod FROM productos");
                $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            echo "<div class='error'><p>No se pudo recuperar la transacción. Intenta nuevamente.</p></div>";
        }
    } else {
        echo "<div class='error'><p>Producto no encontrado.</p></div>";
    }
} else {
    // Mostrar el formulario para seleccionar un producto
    $stmt = $pdo->query("SELECT id_producto, nom_prod FROM productos");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra de Producto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Formulario de Compra</h2>
    <form method="POST">
        <div class="form-group">
            <label for="cliente">Nombre del Cliente:</label>
            <input type="text" id="cliente" name="cliente" required>
        </div>
        
        <div class="form-group">
            <label for="apellidos">Apellidos del Cliente:</label>
            <input type="text" id="apellidos" name="apellidos" required>
        </div>

        <div class="form-group">
            <label for="id_producto">Producto:</label>
            <select name="id_producto" id="id_producto" required>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= $producto['id_producto'] ?>"><?= $producto['nom_prod'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="unidades">Cantidad de Unidades:</label>
            <input type="number" id="unidades" name="unidades" required>
        </div>

        <button type="submit">Comprar</button>
    </form>
</div>

</body>
</html>
