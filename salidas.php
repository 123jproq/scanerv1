<?php require_once 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 style="margin:0;"><i class="glyphicon glyphicon-export"></i> Registrar Salida de Producto</h3>
            </div>
            <div class="panel-body">

                <form id="salidaForm">
                    <!-- Buscar Producto -->
                    <div class="form-group">
                        <label>Buscar Producto (código o nombre)</label>
                        <input type="text" class="form-control" id="searchProduct"
                            placeholder="Escanea el código o escribe el nombre...">
                    </div>

                    <!-- Producto Seleccionado -->
                    <div id="productInfo"
                        style="display:none; background:#f8f9fa; padding:15px; border-radius:8px; margin-bottom:15px;">
                        <input type="hidden" id="productId" name="productId">
                        <h4 id="productName" style="margin:0; color:#008ea9;"></h4>
                        <p style="margin:5px 0;"><code id="productBarcode"></code></p>
                        <p style="margin:0;">Stock disponible: <strong id="productStock"
                                style="color:#a5bf00;"></strong></p>
                    </div>

                    <!-- Cantidad -->
                    <div class="form-group">
                        <label>Cantidad a entregar</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="1"
                            required>
                    </div>

                    <!-- Destinatario -->
                    <div class="form-group">
                        <label>Entregado a (nombre del trabajador)</label>
                        <input type="text" class="form-control" id="destinatario" name="destinatario"
                            placeholder="Nombre del trabajador" required>
                    </div>

                    <!-- Notas -->
                    <div class="form-group">
                        <label>Notas adicionales (opcional)</label>
                        <textarea class="form-control" id="notas" name="notas" rows="2"
                            placeholder="Ej: Para proyecto X..."></textarea>
                    </div>

                    <!-- Áreas Responsables -->
                    <div class="form-group">
                        <label><i class="glyphicon glyphicon-home"></i> Área Responsable (Seleccione una)</label>
                        <div id="areasContainer"
                            style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                            <p class="text-muted">Cargando áreas...</p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" id="btnRegistrar" disabled>
                        <i class="glyphicon glyphicon-ok"></i> Registrar Salida
                    </button>
                </form>

            </div>
        </div>
    </div>

    <!-- Últimas Salidas -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 style="margin:0;"><i class="glyphicon glyphicon-list"></i> Últimas Salidas de Hoy</h3>
            </div>
            <div class="panel-body">
                <table class="table table-striped" id="ultimasSalidas">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Entregado a</th>
                        </tr>
                    </thead>
                    <tbody id="salidasBody">
                        <tr>
                            <td colspan="4" class="text-center">Cargando...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadUltimasSalidas();

        loadAreas();

        // Autocompletar búsqueda de producto
        $('#searchProduct').on('keyup', function () {
            var query = $(this).val();
            if (query.length >= 2) {
                searchProduct(query);
            }
        });

        // Submit form
        $('#salidaForm').on('submit', function (e) {
            e.preventDefault();
            registrarSalida();
        });
    });

    function loadAreas() {
        $.ajax({
            url: 'php_action/fetchAreas.php', // We will reuse this endpoint but need to parse its data format
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                // fetchAreas returns {data: [[id, name, status, btn], ...]}
                // We need to parse that or create a simpler endpoint.
                // Or I can modify fetchAreas to return cleaner JSON if I add a param?
                // Actually, let's just parse the data array.

                var container = $('#areasContainer');
                container.empty();

                if (response.data && response.data.length > 0) {
                    response.data.forEach(function (item) {
                        // item[0] is id, item[1] is name
                        var id = item[0];
                        var name = item[1];

                        var radio = '<div class="radio" style="margin-top:0; margin-bottom:5px;">' +
                            '<label>' +
                            '<input type="radio" name="area" value="' + name + '" required> ' + name +
                            '</label>' +
                            '</div>';
                        container.append(radio);
                    });
                } else {
                    container.html('<p class="text-warning">No hay áreas registradas</p>');
                }
            },
            error: function () {
                $('#areasContainer').html('<p class="text-danger">Error cargando áreas</p>');
            }
        });
    }

    function searchProduct(query) {
        $.ajax({
            url: 'php_action/searchProduct.php',
            type: 'GET',
            data: { q: query },
            dataType: 'json',
            success: function (data) {
                if (data && data.product_id) {
                    showProduct(data);
                }
            }
        });
    }

    function showProduct(product) {
        $('#productId').val(product.product_id);
        $('#productName').text(product.product_name);
        $('#productBarcode').text(product.barcode || 'Sin código');
        $('#productStock').text(product.quantity);
        $('#productInfo').slideDown();
        $('#btnRegistrar').prop('disabled', false);
        $('#cantidad').attr('max', product.quantity);
    }

    function registrarSalida() {
        var productId = $('#productId').val();
        var cantidad = $('#cantidad').val();
        var destinatario = $('#destinatario').val();
        var notas = $('#notas').val();
        var stockActual = parseInt($('#productStock').text());

        // Get selected area
        var area = $('input[name="area"]:checked').val();

        if (!productId || !cantidad || !destinatario || !area) {
            alert('Completa todos los campos requeridos, incluyendo el Área');
            return;
        }

        if (parseInt(cantidad) > stockActual) {
            alert('No hay suficiente stock. Disponible: ' + stockActual);
            return;
        }

        $('#btnRegistrar').prop('disabled', true).html('<i class="glyphicon glyphicon-refresh"></i> Procesando...');

        $.ajax({
            url: 'php_action/registrarSalida.php',
            type: 'POST',
            data: {
                productId: productId,
                cantidad: cantidad,
                destinatario: destinatario,
                notas: notas,
                areas: area // sending as 'areas' to match PHP expectation, or change PHP?
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert('✓ Salida registrada correctamente');
                    // Reset form
                    $('#salidaForm')[0].reset();
                    $('#productInfo').slideUp();
                    $('#btnRegistrar').prop('disabled', true).html('<i class="glyphicon glyphicon-ok"></i> Registrar Salida');
                    loadUltimasSalidas();
                } else {
                    alert('Error: ' + response.message);
                    $('#btnRegistrar').prop('disabled', false).html('<i class="glyphicon glyphicon-ok"></i> Registrar Salida');
                }
            },
            error: function () {
                alert('Error de conexión');
                $('#btnRegistrar').prop('disabled', false).html('<i class="glyphicon glyphicon-ok"></i> Registrar Salida');
            }
        });
    }

    function loadUltimasSalidas() {
        $.ajax({
            url: 'php_action/getUltimasSalidas.php',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                var tbody = $('#salidasBody');
                tbody.empty();

                if (data.length === 0) {
                    tbody.html('<tr><td colspan="4" class="text-center">No hay salidas hoy</td></tr>');
                    return;
                }

                data.forEach(function (row) {
                    var tr = '<tr>' +
                        '<td>' + row.hora + '</td>' +
                        '<td>' + row.product_name + '</td>' +
                        '<td><span class="label label-danger">-' + row.quantity + '</span></td>' +
                        '<td>' + row.destinatario + '</td>' +
                        '</tr>';
                    tbody.append(tr);
                });
            }
        });
    }
</script>

<?php require_once 'includes/footer.php'; ?>