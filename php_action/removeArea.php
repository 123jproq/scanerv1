<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$areaId = intval($_POST['areaId']);

$sql = "UPDATE areas SET area_status = 0 WHERE area_id = $areaId";

if ($connect->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'messages' => 'Área eliminada']);
} else {
    echo json_encode(['success' => false, 'messages' => 'Error: ' . $connect->error]);
}

$connect->close();
?>