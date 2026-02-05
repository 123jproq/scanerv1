<?php require_once 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-10">
                        <h3 class="panel-title" style="padding-top:8px;"><i class="glyphicon glyphicon-th-list"></i>
                            Gestión de Categorías</h3>
                    </div>
                    <div class="col-md-2" align="right">
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#addCategoriesModal">
                            <i class="glyphicon glyphicon-plus"></i> Nueva Categoría
                        </button>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <table id="categoriesTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
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
<div class="modal fade" id="addCategoriesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addCategoriesForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="glyphicon glyphicon-plus"></i> Nueva Categoría</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="categoriesName" name="categoriesName" required>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="categoriesStatus" name="categoriesStatus">
                            <option value="1">Activo</option>
                            <option value="2">Inactivo</option>
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
<div class="modal fade" id="editCategoriesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editCategoriesForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="glyphicon glyphicon-edit"></i> Editar Categoría</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editCategoriesId" name="categoriesId">
                    <div class="form-group">
                        <label>Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="editCategoriesName" name="categoriesName" required>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="editCategoriesStatus" name="categoriesStatus">
                            <option value="1">Activo</option>
                            <option value="2">Inactivo</option>
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
        var table = $('#categoriesTable').DataTable({
            "ajax": "php_action/fetchCategories.php",
            "order": [[0, "desc"]],
            "language": {
                "url": "assests/plugins/datatables/Spanish.json"
            }
        });

        // Agregar
        $('#addCategoriesForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: 'php_action/createCategories.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#addCategoriesModal').modal('hide');
                        $('#addCategoriesForm')[0].reset();
                        table.ajax.reload();
                        alert('Categoría creada correctamente');
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
                url: 'php_action/fetchSelectedCategories.php',
                type: 'POST',
                data: { categoriesId: id },
                dataType: 'json',
                success: function (data) {
                    $('#editCategoriesId').val(data.categories_id);
                    $('#editCategoriesName').val(data.categories_name);
                    $('#editCategoriesStatus').val(data.categories_active);
                    $('#editCategoriesModal').modal('show');
                }
            });
        });

        // Editar - guardar
        $('#editCategoriesForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: 'php_action/editCategories.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#editCategoriesModal').modal('hide');
                        table.ajax.reload();
                        alert('Categoría actualizada');
                    } else {
                        alert('Error: ' + response.messages);
                    }
                }
            });
        });

        // Eliminar
        $(document).on('click', '.removeBtn', function () {
            if (confirm('¿Eliminar esta categoría?')) {
                var id = $(this).data('id');
                $.ajax({
                    url: 'php_action/removeCategories.php',
                    type: 'POST',
                    data: { categoriesId: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            table.ajax.reload();
                            alert('Categoría eliminada');
                        }
                    }
                });
            }
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>