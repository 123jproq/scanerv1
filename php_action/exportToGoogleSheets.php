<?php
require_once 'db_connect.php';
require_once '../config/GoogleSheetsExporter.php';

header('Content-Type: application/json');

$startDate = isset($_GET['startDate']) ? $connect->real_escape_string($_GET['startDate']) : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['endDate']) ? $connect->real_escape_string($_GET['endDate']) : date('Y-m-d');
$movementType = isset($_GET['movementType']) ? $connect->real_escape_string($_GET['movementType']) : '';

try {
    // Get data from database
    $sql = "SELECT 
                DATE_FORMAT(sm.created_at, '%Y-%m-%d %H:%i') as fecha_hora,
                IFNULL(p.product_name, 'Producto eliminado') as producto,
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
        $sql .= " AND sm.movement_type = '$movementType'";
    }

    $sql .= " ORDER BY sm.created_at DESC";

    $result = $connect->query($sql);

    // Prepare data array with headers
    $data = [];
    $data[] = ['Fecha/Hora', 'Producto', 'Código', 'Tipo', 'Cantidad', 'Stock Anterior', 'Stock Final', 'Usuario', 'Notas'];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    // Export to existing spreadsheet
    $exporter = new GoogleSheetsExporter();
    $response = $exporter->exportToSpreadsheet($data);

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$connect->close();
?>