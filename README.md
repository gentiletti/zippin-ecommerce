## Ejercicio E-commerce Zippin

### Objetivo
Armar la arquitectura básica de un sistema de gestión de órdenes de venta de un ecommerce.

### Tarea
Armar una aplicación muy simple que incluya: 
- La estructura de modelos y tablas, con sus migraciones. De las órdenes se debe almacenar sus datos básicos, como comprador, domicilio de entrega, datos de facturación, así como también de los productos vendidos en la orden.
- Rutas y controladores para poder crear una orden por API y luego ver su detalle

### Requisitos
- Se debe utilizar Laravel como framework
- Se debe utilizar MySQL como base de datos

### Endpoints
#### 1. Crear una nueva orden
- **Endpoint:** `POST /api/orders`
- **Descripción:** Crea una nueva orden.
- **Cuerpo de la Solicitud (JSON):**
  ```json
  {
    "user_id": 2,
    "billing_address": {
        "street": "Av. Colón 1750",
        "city": "Córdoba",
        "state": "Córdoba",
        "zip_code": "5000"
    },
    "shipping_address": {
        "street": "Av. Vélez Sarsfield 1800",
        "city": "Córdoba",
        "state": "Córdoba",
        "zip_code": "5010"
    },
    "products": [
        {
            "id": 1,
            "quantity": 4
        },
        {
            "id": 2,
            "quantity": 1
        }
    ]
  }

### 2. Obtener detalles de una orden
- **Endpoint:** `GET /api/orders/{id}`
- **Descripción:** Obtiene los detalles de una orden por ID.

### Comandos Útiles
Para ejecutar las migraciones en tu base de datos, utiliza el siguiente comando:

```bash
php artisan migrate:fresh --seed