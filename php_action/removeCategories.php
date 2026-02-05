<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$categoriesId = intval($_POST['categoriesId']);

$sql = "UPDATE categories SET categories_status = 0 WHERE categories_id = $categoriesId";

if ($connect->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'messages' => 'Categoría eliminada']);
} else {
    echo json_encode(['success' => false, 'messages' => 'Error: ' . $connect->error]);
}

$connect->close();
?>