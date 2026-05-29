# 📦 Sistema de Inventario y Ventas (PHP + MySQL)

---

## 📌 Descripción del proyecto

Este sistema es una aplicación web desarrollada en **PHP puro y MySQL**, orientada a la gestión de inventario, productos, kardex (movimientos de stock) y ventas con carrito de compras.

El sistema permite controlar el stock en tiempo real mediante el modelo **Kardex**, registrar entradas/salidas de productos y generar ventas con detalle.

---

## 🚀 Tecnologías utilizadas

- PHP (sin frameworks)
- MySQL
- JavaScript
- HTML5
- Bootstrap 5
- AJAX (Fetch API)

---

## 🧠 Arquitectura del sistema

El proyecto sigue una estructura tipo MVC básico:

- **Models/** → lógica de base de datos (consultas SQL)
- **php/** → controladores (acciones del sistema)
- **views/** → interfaz de usuario
- **js/** → lógica del frontend

---

## 📁 Estructura del proyecto

```
/index.php

/php
├── conexion.php
├── historial.php
├── productos.php
├── kardex.php
└── ventas.php

/models
├── Producto.php
├── Kardex.php
└── Venta.php

/views
├── productos_view.php
├── kardex_view.php
├── ventas_view.php
└── historial_view.php

/js
└── apps.js

/img
```

---

## 📦 Módulos del sistema

---

### 🛒 Productos

Módulo encargado de la gestión de productos.

- ✔ Registrar productos con imagen
- ✔ Actualizar productos
- ✔ Activar / desactivar productos
- ✔ Listado de productos activos e inactivos

---

### 📦 Kardex (Inventario)

Controla el movimiento de stock.

- ✔ Registro de entradas
- ✔ Registro de salidas
- ✔ Control de stock en tiempo real
- ✔ Historial de movimientos
- ✔ Stock por almacén

> 📌 El stock se calcula desde el último registro del kardex.

---

### 💰 Ventas

Módulo de ventas con carrito.

- ✔ Venta de productos individuales
- ✔ Carrito de compras dinámico
- ✔ Validación de stock antes de vender
- ✔ Registro de detalle de venta
- ✔ Descuento automático del stock

---

### 📊 Historial de ventas

- ✔ Listado de ventas realizadas
- ✔ Visualización de detalle por venta (AJAX)
- ✔ Información de productos vendidos

---

## 🔄 Flujo del sistema

1. Se registran productos
2. Se ingresan entradas en kardex (stock inicial)
3. El usuario realiza ventas
4. El sistema valida stock
5. Se descuenta automáticamente del kardex
6. Se registra la venta con detalle

---

## 🗄️ Base de datos

Tablas principales:

| Tabla | Descripción |
|---|---|
| `tb_productos` | Registro de productos |
| `tb_kardex` | Movimientos de stock |
| `tb_ventas` | Cabecera de ventas |
| `tb_detalle_venta` | Detalle de cada venta |
| `tb_almacen` | Almacenes disponibles |

---

## ⚙️ Funcionalidades principales

- ✔ Control de inventario automático
- ✔ Sistema de ventas con carrito
- ✔ Validación de stock
- ✔ Registro de movimientos tipo kardex
- ✔ Arquitectura modular
- ✔ Separación de lógica y vista

---

## 🧩 Flujo técnico

| Capa | Carpeta | Responsabilidad |
|---|---|---|
| Modelo | `models/` | Clases con consultas SQL |
| Controlador | `php/` | Acciones: insertar, vender, listar |
| Vista | `views/` | Muestra datos al usuario |
| Frontend | `js/` | Carrito y peticiones AJAX |

---

## 🛒 Funcionalidad del carrito

El carrito permite:

- Agregar productos
- Aumentar o disminuir cantidad
- Validar stock en tiempo real
- Calcular total automático
- Enviar venta mediante Fetch API

---

## 📡 API interna (ventas)

### POST

```
php/ventas.php?action=vender_carrito
```

**Payload enviado:**

```json
[
  {
    "id": 1,
    "modelo": "Producto",
    "precio": 10,
    "cantidad": 2
  }
]
```

**Respuesta:**

```json
{ "status": "success" }
```

---

## 👨‍💻 Autor

> Proyecto académico de desarrollo de software  
> Sistema creado para práctica de PHP + MVC básico + MySQL

---

## 🔥 Estado del proyecto

- ✔ Funcional
- ✔ Modular
- ✔ En mejora continua