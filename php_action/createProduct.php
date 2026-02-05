<?php

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if ($_POST) {

	$productName = $_POST['productName'];
	// $productImage 	= $_POST['productImage'];
	$quantity = $_POST['quantity'];
	$rate = $_POST['rate'];
	$brandName = $_POST['brandName'];
	$categoryName = $_POST['categoryName'];
	$productStatus = $_POST['productStatus'];

	$type = explode('.', $_FILES['productImage']['name']);
	$type = $type[count($type) - 1];
	$url = '../assests/images/stock/' . uniqid(rand()) . '.' . $type;
	if (in_array($type, array('gif', 'jpg', 'jpeg', 'png', 'JPG', 'GIF', 'JPEG', 'PNG'))) {
		if (is_uploaded_file($_FILES['productImage']['tmp_name'])) {
			if (move_uploaded_file($_FILES['productImage']['tmp_name'], $url)) {

				$barcode = $_POST['barcode'];
				// Default values if empty
				if (empty($brandName))
					$brandName = 1;
				if (empty($categoryName))
					$categoryName = 1;
				if (empty($productStatus))
					$productStatus = 1;

				$sql = "INSERT INTO product (product_name, product_image, brand_id, categories_id, quantity, rate, active, status, barcode) 
				VALUES ('$productName', '$url', '$brandName', '$categoryName', '$quantity', '$rate', '$productStatus', 1, '$barcode')";

				if ($connect->query($sql) === TRUE) {
					$productId = $connect->insert_id;

					// Register initial stock movement
					$userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 1;
					$movementSql = "INSERT INTO stock_movements (product_id, barcode, movement_type, quantity, stock_before, stock_after, notes, user_id) 
					                VALUES ($productId, '$barcode', 'entrada', $quantity, 0, $quantity, 'Producto nuevo', $userId)";
					$connect->query($movementSql);

					$valid['success'] = true;
					$valid['messages'] = "Successfully Added";
				} else {
					$valid['success'] = false;
					$valid['messages'] = "Error while adding the members";
				}

			} else {
				return false;
			}	// /else	
		} // if
	} // if in_array 		

	$connect->close();

	echo json_encode($valid);

} // /if $_POST