<?php
require_once '../php_action/db_connect.php';
session_start();
header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
    exit;
}

// Check auth
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    if (md5($password) === $user['password']) { // Using MD5 as per legacy system
        $_SESSION['userId'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        echo json_encode(['status' => 'success', 'user' => $user['username']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
}
?>