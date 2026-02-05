<?php
require_once 'php_action/db_connect.php';

echo "<h2>Reparando Base de Datos...</h2>";

// 1. Asegurar que exista al menos una categoría por defecto
$checkCat = "SELECT categories_id FROM categories LIMIT 1";
$resCat = $connect->query($checkCat);

$defaultCatId = 0;

if ($resCat->num_rows == 0) {
    echo "No hay categorías. Creando categoría 'General'...<br>";
    $insertCat = "INSERT INTO categories (categories_name, categories_active, categories_status) VALUES ('General', 1, 1)";
    if ($connect->query($insertCat) === TRUE) {
        $defaultCatId = $connect->insert_id;
        echo "Categoría 'General' creada con ID: $defaultCatId<br>";
    } else {
        die("Error creando categoría: " . $connect->error);
    }
} else {
    $row = $resCat->fetch_assoc();
    $defaultCatId = $row['categories_id'];
    echo "Usando categoría existente ID: $defaultCatId como predeterminada.<br>";
}

// 2. Actualizar productos con categoría inválida
// (Asumimos que cualquier producto con ID < 1 o NULL está mal)
// También podríamos verificar si el ID existe en la tabla categories, pero por ahora vamos a lo simple.

$sql = "UPDATE product SET categories_id = $defaultCatId WHERE categories_id IS NULL OR categories_id < 1";
if ($connect->query($sql) === TRUE) {
    echo "Productos con categoría nula/inválida actualizados: " . $connect->affected_rows . "<br>";
}

// También verificamos IDs que no existan en la tabla categories (huérfanos)
$sqlOrphan = "UPDATE product p 
              SET p.categories_id = $defaultCatId 
              WHERE NOT EXISTS (SELECT 1 FROM categories c WHERE c.categories_id = p.categories_id)";

if ($connect->query($sqlOrphan) === TRUE) {
    echo "Productos con categoría inexistente (huérfana) actualizados: " . $connect->affected_rows . "<br>";
}

echo "<h3>Reparación Completada.</h3>";
echo "<a href='index.php'>Volver al Inicio</a>";

$connect->close();
?>