<?php
require_once '../php_action/db_connect.php';
session_start();
header('Content-Type: application/json');

// Auth Check
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$barcode = $connect->real_escape_string($_POST['barcode'] ?? '');
$quantity = intval($_POST['quantity'] ?? 0);
$userId = $_SESSION['userId'];

if (empty($barcode) || $quantity < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
    exit;
}

// Get current stock and product_id
$checkSql = "SELECT product_id, quantity FROM product WHERE barcode = '$barcode'";
$result = $connect->query($checkSql);

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
    exit;
}

$product = $result->fetch_assoc();
$productId = $product['product_id'];
$stockBefore = intval($product['quantity']);
$stockAfter = $stockBefore + $quantity;

// Update stock
$updateSql = "UPDATE product SET quantity = $stockAfter WHERE product_id = $productId";

if ($connect->query($updateSql) === TRUE) {
    // Register movement
    $movementSql = "INSERT INTO stock_movements (product_id, barcode, movement_type, quantity, stock_before, stock_after, notes, user_id) 
                    VALUES ($productId, '$barcode', 'entrada', $quantity, $stockBefore, $stockAfter, 'Entrada desde PWA', $userId)";
    $connect->query($movementSql);

    echo json_encode(['status' => 'success', 'message' => 'Stock actualizado correctamente', 'new_stock' => $stockAfter]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar: ' . $connect->error]);
}

$connect->close();
?>