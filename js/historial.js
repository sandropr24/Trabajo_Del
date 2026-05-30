async function verDetalle(id_venta){
    var res = await fetch('php/historial.php?action=detalle&id_venta=' + id_venta);
    var data = await res.json();

    var html = '<table class="table table-bordered"><thead class="table-primary"><tr><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th></tr></thead><tbody>';
    data.forEach(item => {
        html += '<tr><td>' + item.modelo + '</td><td>' + item.cantidad + '</td><td>S/. ' + parseFloat(item.precio_unitario).toFixed(2) + '</td><td>S/. ' + parseFloat(item.subtotal).toFixed(2) + '</td></tr>';
    });
    html += '</tbody></table>';

    document.getElementById('contenido-detalle').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalDetalle')).show();
}