<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$areaId = intval($_POST['areaId']);

$sql = "SELECT * FROM areas WHERE area_id = $areaId";
$result = $connect->query($sql);

echo json_encode($result->fetch_assoc());

$connect->close();
?>