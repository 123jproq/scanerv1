<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$categoriesId = intval($_POST['categoriesId']);

$sql = "SELECT * FROM categories WHERE categories_id = $categoriesId";
$result = $connect->query($sql);

echo json_encode($result->fetch_assoc());

$connect->close();
?>