<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$areaId = intval($_POST['areaId']);
$areaName = $connect->real_escape_string($_POST['areaName']);
$areaStatus = intval($_POST['areaStatus']);

$sql = "UPDATE areas SET area_name = '$areaName', area_active = $areaStatus WHERE area_id = $areaId";

if ($connect->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'messages' => 'Área actualizada']);
} else {
    echo json_encode(['success' => false, 'messages' => 'Error: ' . $connect->error]);
}

$connect->close();
?>