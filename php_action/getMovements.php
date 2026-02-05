<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

// Check if table exists
$tableCheck = $connect->query("SHOW TABLES LIKE 'stock_movements'");
if ($tableCheck->num_rows === 0) {
    // Create table if not exists
    $createSql = "CREATE TABLE IF NOT EXISTS `stock_movements` (
      `movement_id` int(11) NOT NULL AUTO_INCREMENT,
      `product_id` int(11) NOT NULL,
      `barcode` varchar(255) DEFAULT NULL,
      `movement_type` ENUM('entrada', 'salida') NOT NULL,
      `quantity` int(11) NOT NULL,
      `stock_before` int(11) NOT NULL,
      `stock_after` int(11) NOT NULL,
      `notes` varchar(500) DEFAULT NULL,
      `user_id` int(11) DEFAULT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`movement_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $connect->query($createSql);
}

$startDate = isset($_GET['startDate']) ? $connect->real_escape_string($_GET['startDate']) : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['endDate']) ? $connect->real_escape_string($_GET['endDate']) : date('Y-m-d');
$movementType = isset($_GET['movementType']) ? $connect->real_escape_string($_GET['movementType']) : '';

// Build query
$sql = "SELECT 
            sm.movement_id,
            sm.barcode,
            sm.movement_type,
            sm.quantity,
            sm.stock_before,
            sm.stock_after,
            sm.notes,
            DATE_FORMAT(sm.created_at, '%Y-%m-%d %H:%i') as created_at,
            IFNULL(p.product_name, 'Producto eliminado') as product_name,
            IFNULL(u.username, 'Sistema') as username
        FROM stock_movements sm
        LEFT JOIN product p ON sm.product_id = p.product_id
        LEFT JOIN users u ON sm.user_id = u.user_id
        WHERE DATE(sm.created_at) BETWEEN '$startDate' AND '$endDate'";

if (!empty($movementType)) {
    $sql .= " AND sm.movement_type = '$movementType'";
}

$sql .= " ORDER BY sm.created_at DESC LIMIT 500";

$result = $connect->query($sql);

if (!$result) {
    echo json_encode(['error' => $connect->error]);
    exit;
}

$movements = [];
while ($row = $result->fetch_assoc()) {
    $movements[] = $row;
}

echo json_encode($movements);

$connect->close();
?>