<?php
require_once '../php_action/db_connect.php';
session_start();
header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Auth Check
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado. Inicia sesión primero.']);
    exit;
}

$productName = $_POST['productName'] ?? '';
$barcode = $_POST['barcode'] ?? '';
$quantity = intval($_POST['quantity'] ?? 0);
$rate = floatval($_POST['rate'] ?? 0);
// Defaults
$brandName = 1;
$categoryName = intval($_POST['categoryName'] ?? 1);
$productStatus = 1;

if (empty($productName) || empty($barcode)) {
    echo json_encode(['status' => 'error', 'message' => 'Nombre y Código son obligatorios']);
    exit;
}

if ($categoryName <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Debes seleccionar una categoría válida']);
    exit;
}

// 1. Check if barcode exists
$checkSql = "SELECT * FROM product WHERE barcode = '$barcode'";
if ($connect->query($checkSql)->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Este código ya existe']);
    exit;
}

// 2. Handle Image Upload
$url = '../assests/images/photo_default.png'; // Default
if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
    $type = pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION);
    $targetDir = '../assests/images/stock/';
    if (!file_exists($targetDir))
        mkdir($targetDir, 0777, true);

    $fileName = uniqid(rand()) . '.' . $type;
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFile)) {
        $url = $targetFile;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error subiendo la imagen']);
        exit;
    }
}

// 3. Insert Database
$sql = "INSERT INTO product (product_name, product_image, brand_id, categories_id, quantity, rate, active, status, barcode) 
        VALUES ('$productName', '$url', '$brandName', '$categoryName', '$quantity', '$rate', '$productStatus', 1, '$barcode')";

if ($connect->query($sql) === TRUE) {
    $productId = $connect->insert_id;

    // Register initial stock movement
    $userId = $_SESSION['userId'];
    $movementSql = "INSERT INTO stock_movements (product_id, barcode, movement_type, quantity, stock_before, stock_after, notes, user_id) 
                    VALUES ($productId, '$barcode', 'entrada', $quantity, 0, $quantity, 'Producto nuevo desde PWA', $userId)";

    if ($connect->query($movementSql)) {
        echo json_encode(['status' => 'success', 'message' => 'Producto Agregado Correctamente']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Producto agregado (sin registrar movimiento)']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error SQL: ' . $connect->error]);
}

$connect->close();
?>