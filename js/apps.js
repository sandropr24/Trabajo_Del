/*
--------------------------------------------------
VALIDACIÓN DE PRECIO
--------------------------------------------------
Evita que el usuario ingrese valores negativos
*/

var precioInput = document.querySelector('[name="precio"]');

if(precioInput){
    precioInput.addEventListener("input", function(){
        if(this.value < 0) this.value = 0;
    });
}


/*
--------------------------------------------------
MENSAJES DEL SISTEMA (ALERTAS)
--------------------------------------------------
Muestra mensajes según la acción realizada
(vendido, registrado, error, etc.)
*/

var params = new URLSearchParams(window.location.search);
var msg = params.get('msg');

var mensajes = {
    registrado: ['success', 'Producto registrado '],
    actualizado: ['success', 'Producto actualizado '],
    desactivado: ['warning', 'Producto desactivado '],
    reactivado: ['success', 'Producto reactivado '],
    entrada: ['success', 'Entrada registrada '],
    salida: ['success', 'Salida registrada '],
    sin_stock: ['danger', 'Stock insuficiente en el almacén'],
    error: ['danger', 'Error: ' + params.get('detalle')],
    vendido: ['success', 'Venta registrada '],
};

if(msg && mensajes[msg]){
    var alerta = document.getElementById('alerta');

    if(alerta) {
        alerta.className = 'alert alert-' + mensajes[msg][0] + ' mt-3';
        alerta.innerText = mensajes[msg][1];

        // Oculta la alerta después de 3 segundos
        setTimeout(() => alerta.className = 'alert d-none', 3000);
    }
}


/*
--------------------------------------------------
BUSCADOR DE PRODUCTOS
--------------------------------------------------
Filtra productos en tiempo real según el modelo
*/

document.getElementById('buscador')?.addEventListener('input', (e) => {

    var busqueda = e.target.value.toLowerCase();
    var productos = document.querySelectorAll('.tarjeta-producto');

    productos.forEach(prod => {
        var modelo = prod.getAttribute('data-modelo');

        if(modelo.includes(busqueda)){
            prod.classList.remove('d-none');
        }else{
            prod.classList.add('d-none');
        }
    });
});


/*
--------------------------------------------------
CARRITO DE COMPRAS
--------------------------------------------------
Almacena los productos seleccionados para venta
*/

var carrito = [];


/*
--------------------------------------------------
AGREGAR AL CARRITO
--------------------------------------------------
Agrega productos o incrementa cantidad si ya existe
*/

function agregarAlCarrito(id, modelo, precio, stock){

    var existe = carrito.find(item => item.id === id);

    if(existe){

        if(existe.cantidad >= stock){
            alert('No hay suficiente stock');
            return;
        }

        existe.cantidad++;

    }else{
        carrito.push({
            id: id,
            modelo: modelo,
            precio: precio,
            cantidad: 1,
            stock: stock
        });
    }

    actualizarInterfazCarrito();
}


/*
--------------------------------------------------
CAMBIAR CANTIDAD EN CARRITO
--------------------------------------------------
Aumenta o disminuye la cantidad de un producto
*/

function cambiarCantidad(id, cambio){

    var item = carrito.find(p => p.id === id);

    if(item){

        var nuevaCantidad = item.cantidad + cambio;

        if(nuevaCantidad > item.stock){
            alert('No hay más stock disponible');
            return;
        }

        item.cantidad = nuevaCantidad;

        if(item.cantidad <= 0){
            carrito = carrito.filter(p => p.id !== id);
        }
    }

    actualizarInterfazCarrito();
}


/*
--------------------------------------------------
ACTUALIZAR INTERFAZ DEL CARRITO
--------------------------------------------------
Renderiza los productos en pantalla y calcula total
*/

function actualizarInterfazCarrito(){

    var contenedor = document.getElementById('items-carrito');
    var txtTotal = document.getElementById('total-carrito');
    var btnVenta = document.getElementById('btn-procesar-venta');

    if(!contenedor || !txtTotal || !btnVenta) return;

    contenedor.innerHTML = '';
    var total = 0;

    if(carrito.length === 0){

        contenedor.innerHTML = '<p class="text-muted text-center my-4">El carrito está vacío.</p>';
        txtTotal.innerText = '0.00';
        btnVenta.disabled = true;
        return;
    }

    carrito.forEach(item => {

        var subtotal = item.precio * item.cantidad;
        total += subtotal;

        contenedor.innerHTML += `
            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                <div style="max-width: 55%;">
                    <h6 class="mb-0 text-truncate">${item.modelo}</h6>
                    <small class="text-muted">S/. ${item.precio.toFixed(2)}</small>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-light border py-0 px-2 fw-bold" onclick="cambiarCantidad(${item.id}, -1)">-</button>
                    <span class="fw-bold px-1">${item.cantidad}</span>
                    <button class="btn btn-sm btn-light border py-0 px-2 fw-bold" onclick="cambiarCantidad(${item.id}, 1)">+</button>
                    <span class="ms-1 fw-bold text-primary">S/. ${subtotal.toFixed(2)}</span>
                </div>
            </div>`;
    });

    txtTotal.innerText = total.toFixed(2);
    btnVenta.disabled = false;
}


/*
--------------------------------------------------
PROCESAR VENTA (FETCH)
--------------------------------------------------
Envía el carrito al backend para registrar la venta
*/

async function procesarVenta(){

    if(carrito.length === 0) return;

    var btnVenta = document.getElementById('btn-procesar-venta');
    btnVenta.disabled = true;

    try {

        var res = await fetch('php/ventas.php?action=vender_carrito', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(carrito)
        });

        var data = await res.json();

        var alerta = document.getElementById('alerta');
        if(!alerta) return;

        alerta.classList.remove('d-none');

        if(data.status === 'success'){

            alerta.className = "alert alert-success mt-3";
            alerta.innerText = "¡Venta registrada con éxito!";

            carrito = [];
            actualizarInterfazCarrito();

            setTimeout(() => {
                window.location.href = "index.php?vista=ventas&msg=vendido";
            }, 1000);

        }else{

            alerta.className = "alert alert-danger mt-3";
            alerta.innerText = "Error: " + data.message;
            btnVenta.disabled = false;
        }

    }catch(err){
        console.error(err);
        btnVenta.disabled = false;
    }
}