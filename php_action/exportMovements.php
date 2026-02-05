<?php
require_once 'db_connect.php';

$startDate = $_GET['startDate'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['endDate'] ?? date('Y-m-d');
$movementType = $_GET['movementType'] ?? '';

// Build query
$sql = "SELECT 
            DATE_FORMAT(sm.created_at, '%Y-%m-%d %H:%i') as fecha_hora,
            p.product_name as producto,
            sm.barcode as codigo,
            CASE sm.movement_type WHEN 'entrada' THEN 'Entrada' ELSE 'Salida' END as tipo,
            sm.quantity as cantidad,
            sm.stock_before as stock_anterior,
            sm.stock_after as stock_final,
            IFNULL(u.username, 'Sistema') as usuario,
            IFNULL(sm.notes, '') as notas
        FROM stock_movements sm
        LEFT JOIN product p ON sm.product_id = p.product_id
        LEFT JOIN users u ON sm.user_id = u.user_id
        WHERE DATE(sm.created_at) BETWEEN '$startDate' AND '$endDate'";

if (!empty($movementType)) {
    $movementType = $connect->real_escape_string($movementType);
    $sql .= " AND sm.movement_type = '$movementType'";
}

$sql .= " ORDER BY sm.created_at DESC";

$result = $connect->query($sql);

// Generate CSV/Excel
$filename = "movimientos_" . $startDate . "_a_" . $endDate . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// BOM for Excel UTF-8 compatibility
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Headers
fputcsv($output, ['Fecha/Hora', 'Producto', 'Código', 'Tipo', 'Cantidad', 'Stock Anterior', 'Stock Final', 'Usuario', 'Notas'], ';');

// Data
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row, ';');
    }
}

fclose($output);
$connect->close();
exit;
?>