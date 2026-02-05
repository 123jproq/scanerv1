<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$sql = "SELECT categories_id, categories_name, categories_active FROM categories WHERE categories_status = 1 ORDER BY categories_id DESC";
$result = $connect->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $status = $row['categories_active'] == 1 ? '<span class="label label-success">Activo</span>' : '<span class="label label-danger">Inactivo</span>';
    $buttons = '<button class="btn btn-sm btn-info editBtn" data-id="' . $row['categories_id'] . '"><i class="glyphicon glyphicon-edit"></i></button> ';
    $buttons .= '<button class="btn btn-sm btn-danger removeBtn" data-id="' . $row['categories_id'] . '"><i class="glyphicon glyphicon-trash"></i></button>';

    $data[] = [
        $row['categories_id'],
        $row['categories_name'],
        $status,
        $buttons
    ];
}

echo json_encode(['data' => $data]);
$connect->close();
?>