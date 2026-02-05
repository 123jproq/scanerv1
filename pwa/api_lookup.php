<?php
require_once '../php_action/db_connect.php';

header('Content-Type: application/json');

$barcode = $_GET['barcode'] ?? '';

if (!$barcode) {
    echo json_encode(['status' => 'error', 'message' => 'No barcode']);
    exit;
}

// Buscar en la tabla products
$sql = "SELECT * FROM product WHERE barcode = '$barcode' AND status = 1";
$result = $connect->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['status' => 'found', 'product' => $row]);
} else {
    echo json_encode(['status' => 'not_found']);
}
?>