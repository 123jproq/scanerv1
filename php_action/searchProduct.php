<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$query = isset($_GET['q']) ? $connect->real_escape_string($_GET['q']) : '';

if (empty($query)) {
    echo json_encode(null);
    exit;
}

// Search by barcode first, then by name
$sql = "SELECT product_id, product_name, barcode, quantity 
        FROM product 
        WHERE (barcode = '$query' OR product_name LIKE '%$query%') 
        AND status = 1 
        ORDER BY barcode = '$query' DESC 
        LIMIT 1";

$result = $connect->query($sql);

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(null);
}

$connect->close();
?>