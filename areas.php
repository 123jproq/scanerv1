<?php require_once 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-10">
                        <h3 class="panel-title" style="padding-top:8px;"><i class="glyphicon glyphicon-home"></i>
                            Gestión de Áreas</h3>
                    </div>
                    <div class="col-md-2" align="right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAreaModal">
                            <i class="glyphicon glyphicon-plus"></i> Nueva Área
                        </button>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <table id="areasTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Área</th>
                            <th>Estado</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="addAreaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addAreaForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="glyphicon glyphicon-plus"></i> Nueva Área</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre del Área</label>
                        <input type="text" class="form-control" id="areaName" name="areaName"
                            placeholder="Ej: Unidad de apoyo laboratorio" required>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="areaStatus" name="areaStatus">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editAreaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editAreaForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="glyphicon glyphicon-edit"></i> Editar Área</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editAreaId" name="areaId">
                    <div class="form-group">
                        <label>Nombre del Área</label>
                        <input type="text" class="form-control" id="editAreaName" name="areaName" required>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="editAreaStatus" name="areaStatus">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var table = $('#areasTable').DataTable({
            "ajax": "php_action/fetchAreas.php",
            "order": [[0, "asc"]],
            "language": {
                "url": "assests/plugins/datatables/Spanish.json"
            }
        });

        // Agregar
        $('#addAreaForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: 'php_action/createArea.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#addAreaModal').modal('hide');
                        $('#addAreaForm')[0].reset();
                        table.ajax.reload();
                        alert('Área creada correctamente');
                    } else {
                        alert('Error: ' + response.messages);
                    }
                }
            });
        });

        // Editar - cargar datos
        $(document).on('click', '.editBtn', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'php_action/fetchSelectedArea.php',
                type: 'POST',
                data: { areaId: id },
                dataType: 'json',
                success: function (data) {
                    $('#editAreaId').val(data.area_id);
                    $('#editAreaName').val(data.area_name);
                    $('#editAreaStatus').val(data.area_active);
                    $('#editAreaModal').modal('show');
                }
            });
        });

        // Editar - guardar
        $('#editAreaForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: 'php_action/editArea.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#editAreaModal').modal('hide');
                        table.ajax.reload();
                        alert('Área actualizada');
                    } else {
                        alert('Error: ' + response.messages);
                    }
                }
            });
        });

        // Eliminar
        $(document).on('click', '.removeBtn', function () {
            if (confirm('¿Eliminar esta área?')) {
                var id = $(this).data('id');
                $.ajax({
                    url: 'php_action/removeArea.php',
                    type: 'POST',
                    data: { areaId: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            table.ajax.reload();
                            alert('Área eliminada');
                        }
                    }
                });
            }
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>