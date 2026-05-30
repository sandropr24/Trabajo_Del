function editar(id, modelo, precio){
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_modelo').value = modelo;
    document.getElementById('edit_precio').value = precio;
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}