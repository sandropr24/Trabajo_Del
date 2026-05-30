function autocompletarPrecio(){
    let select = document.getElementById('producto');
    let precio = select.options[select.selectedIndex].getAttribute('data-precio');
    if(precio) document.getElementById('valor').value = precio;
}