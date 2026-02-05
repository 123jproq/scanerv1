<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$categoriesId = intval($_POST['categoriesId']);
$categoriesName = $connect->real_escape_string($_POST['categoriesName']);
$categoriesStatus = intval($_POST['categoriesStatus']);

$sql = "UPDATE categories SET categories_name = '$categoriesName', categories_active = $categoriesStatus WHERE categories_id = $categoriesId";

if ($connect->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'messages' => 'Categoría actualizada']);
} else {
    echo json_encode(['success' => false, 'messages' => 'Error: ' . $connect->error]);
}

$connect->close();
?>