<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$today = date('Y-m-d');

$sql = "SELECT 
            DATE_FORMAT(sm.created_at, '%H:%i') as hora,
            p.product_name,
            sm.quantity,
            SUBSTRING_INDEX(sm.notes, ' | ', 1) as destinatario
        FROM stock_movements sm
        LEFT JOIN product p ON sm.product_id = p.product_id
        WHERE sm.movement_type = 'salida' 
        AND DATE(sm.created_at) = '$today'
        ORDER BY sm.created_at DESC
        LIMIT 20";

$result = $connect->query($sql);

$salidas = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Clean up destinatario (remove "Entregado a: " prefix)
        $row['destinatario'] = str_replace('Entregado a: ', '', $row['destinatario']);
        $salidas[] = $row;
    }
}

echo json_encode($salidas);

$connect->close();
?>