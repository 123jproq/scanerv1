<?php require_once 'includes/header.php'; ?>

<!-- DataTables -->
<link rel="stylesheet" href="assests/plugins/datatables/jquery.dataTables.min.css">
<style>
	.panel-heading {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.product-form {
		display: none;
		margin-bottom: 2rem;
		background: #fff;
		padding: 2rem;
		border-radius: 8px;
		box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
	}

	.product-form.active {
		display: block;
		animation: slideDown 0.3s ease-out;
	}

	@keyframes slideDown {
		from {
			opacity: 0;
			transform: translateY(-10px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	/* Estilos mejorados para DataTables */
	.dataTables_wrapper {
		padding: 15px 0;
	}

	.dataTables_length {
		float: left;
		margin-bottom: 15px;
	}

	.dataTables_length label {
		font-weight: 500;
		color: #333;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.dataTables_length select {
		border: 2px solid #3b82f6;
		border-radius: 6px;
		padding: 6px 12px;
		font-size: 14px;
		outline: none;
		transition: all 0.2s;
		background-color: #fff;
		cursor: pointer;
	}

	.dataTables_length select:hover {
		border-color: #2563eb;
		box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
	}

	.dataTables_length select:focus {
		border-color: #2563eb;
		box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
	}

	.dataTables_filter {
		float: right;
		margin-bottom: 15px;
	}

	.dataTables_filter label {
		font-weight: 500;
		color: #333;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.dataTables_filter input {
		border: 2px solid #3b82f6;
		border-radius: 6px;
		padding: 6px 12px;
		font-size: 14px;
		outline: none;
		transition: all 0.2s;
		width: 250px;
	}

	.dataTables_filter input:hover {
		border-color: #2563eb;
		box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
	}

	.dataTables_filter input:focus {
		border-color: #2563eb;
		box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
	}

	.dataTables_info {
		float: left;
		padding-top: 8px;
		color: #666;
		font-size: 14px;
	}

	.dataTables_paginate {
		float: right;
		margin-top: 0;
	}

	/* Mejorar el aspecto de la tabla */
	#manageProductTable {
		border-collapse: separate;
		border-spacing: 0;
	}

	#manageProductTable thead th {
		background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
		color: white;
		font-weight: 600;
		padding: 12px;
		border: none;
		text-transform: uppercase;
		font-size: 12px;
		letter-spacing: 0.5px;
	}

	#manageProductTable tbody tr {
		transition: all 0.2s;
	}

	#manageProductTable tbody tr:hover {
		background-color: #f0f9ff;
		transform: scale(1.01);
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
	}

	#manageProductTable tbody td {
		padding: 12px;
		vertical-align: middle;
	}

	/* Dropdown menu hover effects */
	.dropdown-menu>li>a:hover {
		background-color: #f8f9fa !important;
	}
</style>

<div class="row">
	<div class="col-md-12">

		<div class="panel panel-default">

			<div class="panel-heading">
				<h3 style="margin:0;"><i class="glyphicon glyphicon-th-list"></i> Inventario de Productos</h3>
				<button class="btn btn-primary" id="toggleFormBtn">
					<i class="glyphicon glyphicon-plus"></i> Agregar Nuevo
				</button>
			</div>

			<div class="panel-body">

				<!-- Add Product Form (Hidden by default) -->
				<div class="product-form" id="addProductForm">
					<h4
						style="margin-top:0; margin-bottom:20px; color:#3b82f6; border-bottom:1px solid #eee; padding-bottom:10px;">
						Nuevo Producto
					</h4>

					<form class="form-horizontal" id="submitProductForm" action="php_action/createProduct.php"
						method="POST" enctype="multipart/form-data">

						<div id="add-product-messages"></div>

						<div class="form-group">
							<label class="col-sm-3 control-label">Código de Barras</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="productBarcode" name="barcode"
									placeholder="Escanea o escribe el código" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label">Imagen</label>
							<div class="col-sm-8">
								<input type="file" class="form-control" id="productImage" name="productImage"
									required />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label">Nombre del Producto</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="productName" name="productName"
									placeholder="Ej. Coca Cola Latah" required autocomplete="off">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label">Stock Inicial</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" id="quantity" name="quantity" placeholder="0"
									required autocomplete="off">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label">Categoría</label>
							<div class="col-sm-8">
								<select class="form-control" id="categoryName" name="categoryName" required>
									<option value="">Selecciona una categoría</option>
									<?php
									$sql = "SELECT categories_id, categories_name FROM categories WHERE categories_status = 1 AND categories_active = 1";
									$result = $connect->query($sql);
									while ($row = $result->fetch_array()) {
										echo "<option value='" . $row[0] . "'>" . $row[1] . "</option>";
									}
									?>
								</select>
							</div>
						</div>

						<!-- Hidden fields -->
						<input type="hidden" name="brandName" value="1"> <!-- Default Brand -->
						<input type="hidden" name="rate" value="0"> <!-- Precio oculto -->
						<input type="hidden" name="productStatus" value="1"> <!-- Available -->

						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-8">
								<button type="submit" class="btn btn-primary" id="createProductBtn"
									data-loading-text="Guardando...">
									<i class="glyphicon glyphicon-save"></i> Guardar Producto
								</button>
								<button type="button" class="btn btn-default" id="cancelFormBtn">Cancelar</button>
							</div>
						</div>
					</form>
				</div>

				<!-- Product List -->
				<table class="table table-hover table-striped" id="manageProductTable">
					<thead>
						<tr>
							<th style="width:10%;">Foto</th>
							<th>Producto</th>
							<th>Código</th>
							<th>Categoría</th>
							<th>Stock</th>
							<th style="width:15%;">Opciones</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Essential Scripts for DataTables & Form -->
<script src="assests/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assests/plugins/fileinput/js/plugins/canvas-to-blob.min.js"></script>
<script src="assests/plugins/fileinput/js/fileinput.min.js"></script>
<script src="custom/js/dashboard_products.js"></script>

<script>
	var manageProductTable;

	$(document).ready(function () {
		// Toggle Form
		$("#toggleFormBtn").click(function () {
			$("#addProductForm").addClass('active');
			$("#productBarcode").focus();
			$(this).hide();
		});

		$("#cancelFormBtn").click(function () {
			$("#addProductForm").removeClass('active');
			$("#toggleFormBtn").show();
		});

		// Initialize Native DataTable with Custom API
		manageProductTable = $('#manageProductTable').DataTable({
			'ajax': 'php_action/fetchProduct.php',
			'order': [],
			'language': {
				'url': 'assests/plugins/datatables/Spanish.json'
			}
		});

		// Add Product Form Submit
		$("#submitProductForm").unbind('submit').bind('submit', function (e) {
			e.preventDefault();
			var form = $(this);
			var formData = new FormData(this);

			$.ajax({
				url: form.attr('action'),
				type: form.attr('method'),
				data: formData,
				dataType: 'json',
				cache: false,
				contentType: false,
				processData: false,
				success: function (response) {
					if (response.success == true) {
						$("#submitProductForm")[0].reset();

						// Show success message
						$("#add-product-messages").html('<div class="alert alert-success">' +
							'<button type="button" class="close" data-dismiss="alert">&times;</button>' +
							'<strong><i class="glyphicon glyphicon-ok-sign"></i></strong> ' + response.messages +
							'</div>');

						$(".alert-success").delay(500).show(10, function () {
							$(this).delay(3000).hide(10, function () {
								$(this).remove();
							});
						});

						// Reload Table
						manageProductTable.ajax.reload(null, false);
					} else {
						$("#add-product-messages").html('<div class="alert alert-warning">' +
							'<button type="button" class="close" data-dismiss="alert">&times;</button>' +
							'<strong><i class="glyphicon glyphicon-exclamation-sign"></i></strong> ' + response.messages +
							'</div>');
					}
				}
			});
			return false;
		});
	});
</script>

<?php require_once 'includes/footer.php'; ?>