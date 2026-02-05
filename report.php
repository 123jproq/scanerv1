<?php require_once 'includes/header.php'; ?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 style="margin:0;"><i class="glyphicon glyphicon-transfer"></i> Reporte de Movimientos de Inventario
				</h3>
			</div>
			<div class="panel-body">

				<!-- Filtros -->
				<form class="form-inline" id="filterForm" style="margin-bottom:25px;">
					<div class="form-group" style="margin-right:15px;">
						<label for="startDate">Desde:</label>
						<input type="date" class="form-control" id="startDate" name="startDate">
					</div>
					<div class="form-group" style="margin-right:15px;">
						<label for="endDate">Hasta:</label>
						<input type="date" class="form-control" id="endDate" name="endDate">
					</div>
					<div class="form-group" style="margin-right:15px;">
						<label for="movementType">Tipo:</label>
						<select class="form-control" id="movementType" name="movementType">
							<option value="">Todos</option>
							<option value="entrada">Entradas</option>
							<option value="salida">Salidas</option>
						</select>
					</div>
					<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i>
						Filtrar</button>
					<button type="button" class="btn btn-success" id="googleSheetsBtn" style="background:#0F9D58;">
						<i class="glyphicon glyphicon-export"></i> Exportar a Google Sheets
					</button>
					<button type="button" class="btn btn-default" id="exportBtn">
						<i class="glyphicon glyphicon-download-alt"></i> Descargar CSV
					</button>
				</form>

				<!-- Tabla de Resultados -->
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="movementsTable">
						<thead>
							<tr>
								<th>Fecha/Hora</th>
								<th>Producto</th>
								<th>Código</th>
								<th>Tipo</th>
								<th>Cantidad</th>
								<th>Stock Anterior</th>
								<th>Stock Final</th>
								<th>Usuario</th>
								<th>Notas</th>
							</tr>
						</thead>
						<tbody id="movementsBody">
							<!-- Data loaded via AJAX -->
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		// Set default dates (last 30 days)
		var today = new Date();
		var lastMonth = new Date();
		lastMonth.setDate(today.getDate() - 30);

		$('#endDate').val(today.toISOString().split('T')[0]);
		$('#startDate').val(lastMonth.toISOString().split('T')[0]);

		// Load initial data
		loadMovements();

		// Filter form submit
		$('#filterForm').on('submit', function (e) {
			e.preventDefault();
			loadMovements();
		});

		// Export to Google Sheets
		$('#googleSheetsBtn').on('click', function () {
			exportToGoogleSheets();
		});

		// Export CSV button
		$('#exportBtn').on('click', function () {
			exportToExcel();
		});
	});

	function loadMovements() {
		var startDate = $('#startDate').val();
		var endDate = $('#endDate').val();
		var movementType = $('#movementType').val();

		$.ajax({
			url: 'php_action/getMovements.php',
			type: 'GET',
			data: {
				startDate: startDate,
				endDate: endDate,
				movementType: movementType
			},
			dataType: 'json',
			success: function (data) {
				renderTable(data);
			},
			error: function () {
				$('#movementsBody').html('<tr><td colspan="9" class="text-center">Error al cargar datos</td></tr>');
			}
		});
	}

	function renderTable(data) {
		var tbody = $('#movementsBody');
		tbody.empty();

		if (data.length === 0) {
			tbody.html('<tr><td colspan="9" class="text-center">No hay movimientos en este período</td></tr>');
			return;
		}

		data.forEach(function (row) {
			var typeClass = row.movement_type === 'entrada' ? 'success' : 'danger';
			var typeIcon = row.movement_type === 'entrada' ? '↑' : '↓';
			var typeLabel = row.movement_type === 'entrada' ? 'Entrada' : 'Salida';

			var tr = '<tr>' +
				'<td>' + row.created_at + '</td>' +
				'<td>' + row.product_name + '</td>' +
				'<td><code>' + row.barcode + '</code></td>' +
				'<td><span class="label label-' + typeClass + '">' + typeIcon + ' ' + typeLabel + '</span></td>' +
				'<td><strong>' + row.quantity + '</strong></td>' +
				'<td>' + row.stock_before + '</td>' +
				'<td>' + row.stock_after + '</td>' +
				'<td>' + (row.username || 'Sistema') + '</td>' +
				'<td>' + (row.notes || '-') + '</td>' +
				'</tr>';
			tbody.append(tr);
		});
	}

	function exportToExcel() {
		var startDate = $('#startDate').val();
		var endDate = $('#endDate').val();
		var movementType = $('#movementType').val();

		window.location.href = 'php_action/exportMovements.php?startDate=' + startDate +
			'&endDate=' + endDate + '&movementType=' + movementType;
	}

	function exportToGoogleSheets() {
		var startDate = $('#startDate').val();
		var endDate = $('#endDate').val();
		var movementType = $('#movementType').val();
		
		$('#googleSheetsBtn').prop('disabled', true).html('<i class="glyphicon glyphicon-refresh"></i> Exportando...');
		
		$.ajax({
			url: 'php_action/exportToGoogleSheets.php',
			type: 'GET',
			data: {
				startDate: startDate,
				endDate: endDate,
				movementType: movementType
			},
			dataType: 'json',
			success: function(response) {
				$('#googleSheetsBtn').prop('disabled', false).html('<i class="glyphicon glyphicon-export"></i> Exportar a Google Sheets');
				
				if (response.success) {
					if (confirm('✅ Hoja creada!\n\n¿Quieres abrirla ahora?')) {
						window.open(response.url, '_blank');
					}
				} else {
					alert('Error: ' + response.error);
				}
			},
			error: function(xhr) {
				$('#googleSheetsBtn').prop('disabled', false).html('<i class="glyphicon glyphicon-export"></i> Exportar a Google Sheets');
				alert('Error de conexión. Verifica la configuración de Google API.');
			}
		});
	}
</script>

<?php require_once 'includes/footer.php'; ?>