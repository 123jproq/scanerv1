<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$areaName = $connect->real_escape_string($_POST['areaName']);
$areaStatus = intval($_POST['areaStatus']);

$sql = "INSERT INTO areas (area_name, area_active, area_status) VALUES ('$areaName', $areaStatus, 1)";

if ($connect->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'messages' => 'Área creada']);
} else {
    echo json_encode(['success' => false, 'messages' => 'Error: ' . $connect->error]);
}

$connect->close();
?>