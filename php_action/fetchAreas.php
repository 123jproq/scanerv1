<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

// Create table if not exists
$connect->query("CREATE TABLE IF NOT EXISTS `areas` (
  `area_id` int(11) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(100) NOT NULL,
  `area_active` tinyint(1) NOT NULL DEFAULT 1,
  `area_status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$sql = "SELECT area_id, area_name, area_active FROM areas WHERE area_status = 1 ORDER BY area_name ASC";
$result = $connect->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $status = $row['area_active'] == 1 ? '<span class="label label-success">Activo</span>' : '<span class="label label-danger">Inactivo</span>';
    $buttons = '<button class="btn btn-sm btn-info editBtn" data-id="' . $row['area_id'] . '"><i class="glyphicon glyphicon-edit"></i></button> ';
    $buttons .= '<button class="btn btn-sm btn-danger removeBtn" data-id="' . $row['area_id'] . '"><i class="glyphicon glyphicon-trash"></i></button>';

    $data[] = [
        $row['area_id'],
        $row['area_name'],
        $status,
        $buttons
    ];
}

echo json_encode(['data' => $data]);
$connect->close();
?>