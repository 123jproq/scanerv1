<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$categoriesName = $connect->real_escape_string($_POST['categoriesName']);
$categoriesStatus = intval($_POST['categoriesStatus']);

$sql = "INSERT INTO categories (categories_name, categories_active, categories_status) VALUES ('$categoriesName', $categoriesStatus, 1)";

if ($connect->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'messages' => 'Categoría creada']);
} else {
    echo json_encode(['success' => false, 'messages' => 'Error: ' . $connect->error]);
}

$connect->close();
?>