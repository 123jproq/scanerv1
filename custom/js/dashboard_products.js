// Event delegation for edit and delete product buttons in dashboard
$(document).ready(function () {
    console.log('Dashboard products JS loaded');
    
    // Event delegation for edit product button
    $(document).on('click', '.editProductBtn', function (e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        console.log('Edit clicked for product:', productId);
        alert('Editar producto ID: ' + productId + '\n\nPróximamente se habilitará la edición desde el dashboard.');
    });

    // Event delegation for remove product button  
    $(document).on('click', '.removeProductBtn', function (e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        console.log('Remove clicked for product:', productId);
        
        if (confirm('¿Estás seguro de eliminar este producto?')) {
            console.log('User confirmed deletion');
            
            $.ajax({
                url: 'php_action/removeProduct.php',
                type: 'post',
                data: { productId: productId },
                dataType: 'json',
                beforeSend: function() {
                    console.log('Sending delete request...');
                },
                success: function (response) {
                    console.log('Server response:', response);
                    
                    if (response.success == true) {
                        alert('✅ Producto eliminado correctamente');
                        // Reload the table if manageProductTable is available
                        if (typeof manageProductTable !== 'undefined') {
                            console.log('Reloading table...');
                            manageProductTable.ajax.reload(null, false);
                        } else {
                            console.log('manageProductTable not found, reloading page...');
                            location.reload();
                        }
                    } else {
                        alert('❌ Error: ' + response.messages);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    alert('❌ Error de conexión: ' + error);
                }
            });
        } else {
            console.log('User cancelled deletion');
        }
    });
});
