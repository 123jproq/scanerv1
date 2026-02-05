<?php
require_once 'core.php';

// JOIN categories to get description
$sql = "SELECT product.product_id, product.product_name, product.product_image, product.brand_id, 
        categories.categories_name, product.quantity, product.rate, product.active, product.status, 
        product.barcode
        FROM product 
        LEFT JOIN categories ON product.categories_id = categories.categories_id
        WHERE product.status = 1";

$result = $connect->query($sql);

$output = array('data' => array());

if ($result->num_rows > 0) {

  while ($row = $result->fetch_array()) {
    $productId = $row[0];
    $barcode = $row[9] ?? '---';
    $categoryName = $row[4] ?? 'Sin Categoría'; // Now row[4] is category name

    $button = '<div class="btn-group">
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 6px; padding: 6px 12px;">
            <span class="glyphicon glyphicon-cog"></span> <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" style="min-width: 160px;">
            <li>
              <a href="#" class="editProductBtn" data-product-id="' . $productId . '" data-toggle="modal" data-target="#editProductModal" 
                 style="color: #007bff; padding: 8px 16px; display: block; transition: all 0.2s;">
                <i class="glyphicon glyphicon-edit" style="margin-right: 8px;"></i> 
                <strong>Editar</strong>
              </a>
            </li>
            <li class="divider"></li>
            <li>
              <a href="#" class="removeProductBtn" data-product-id="' . $productId . '" data-toggle="modal" data-target="#removeProductModal"
                 style="color: #dc3545; padding: 8px 16px; display: block; transition: all 0.2s;">
                <i class="glyphicon glyphicon-trash" style="margin-right: 8px;"></i> 
                <strong>Eliminar</strong>
              </a>
            </li>
          </ul>
        </div>';

    // Check image path
    $imageUrl = $row[2];
    if (strpos($imageUrl, '../') === 0) {
      $imageUrl = substr($imageUrl, 3);
    }
    $productImage = "<img class='img-rounded' src='" . $imageUrl . "' style='height:40px; width:40px; object-fit: cover;' />";

    $output['data'][] = array(
      $productImage,
      $row[1], // name
      $barcode,// barcode
      $categoryName, // category
      $row[5], // quantity
      $button
    );
  } // /while 

} // if num_rows

$connect->close();

echo json_encode($output);
?>