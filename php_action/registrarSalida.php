<?php
require_once 'db_connect.php';
session_start();
header('Content-Type: application/json');

$productId = isset($_POST['productId']) ? intval($_POST['productId']) : 0;
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
$destinatario = isset($_POST['destinatario']) ? $connect->real_escape_string($_POST['destinatario']) : '';
$notas = isset($_POST['notas']) ? $connect->real_escape_string($_POST['notas']) : '';
$userId = isset($_SESSION['userId']) ? intval($_SESSION['userId']) : 1;

// Validations
if ($productId < 1 || $cantidad < 1 || empty($destinatario)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Get current stock
$checkSql = "SELECT product_id, barcode, quantity FROM product WHERE product_id = $productId";
$result = $connect->query($checkSql);

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    exit;
}

$product = $result->fetch_assoc();
$stockBefore = intval($product['quantity']);
$barcode = $product['barcode'];

if ($cantidad > $stockBefore) {
    echo json_encode(['success' => false, 'message' => 'Stock insuficiente. Disponible: ' . $stockBefore]);
    exit;
}

$stockAfter = $stockBefore - $cantidad;

// Update stock
$updateSql = "UPDATE product SET quantity = $stockAfter WHERE product_id = $productId";

if ($connect->query($updateSql) === TRUE) {
    // Register movement with destinatario in notes
    $notaCompleta = "Entregado a: $destinatario";

    if (!empty($_POST['areas'])) {
        $areas = $connect->real_escape_string($_POST['areas']);
        $notaCompleta .= " | Área(s): $areas";
    }

    if (!empty($notas)) {
        $notaCompleta .= " | $notas";
    }

    $movementSql = "INSERT INTO stock_movements (product_id, barcode, movement_type, quantity, stock_before, stock_after, notes, user_id) 
                    VALUES ($productId, '$barcode', 'salida', $cantidad, $stockBefore, $stockAfter, '$notaCompleta', $userId)";
    $connect->query($movementSql);

    echo json_encode(['success' => true, 'message' => 'Salida registrada', 'new_stock' => $stockAfter]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $connect->error]);
}

$connect->close();
?>