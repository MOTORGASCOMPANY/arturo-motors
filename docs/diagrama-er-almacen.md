# Diagrama E/R — Módulo de Almacén e Inventario

## Diagrama Visual (Mermaid)

```mermaid
erDiagram

    %% ============================================
    %% TABLAS NUEVAS
    %% ============================================

    talleres {
        BIGINT id PK
        VARCHAR nombre UK
        VARCHAR direccion
        VARCHAR telefono
        BOOLEAN activo
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    equipo_vehiculos {
        BIGINT id PK
        INT vehiculo_id FK
        ENUM tipo
        VARCHAR marca
        VARCHAR generacion
        VARCHAR capacidad
        VARCHAR serie
        VARCHAR produce
        DATE fecha_llegada
        VARCHAR taller_origen
        DATE fecha_entrega
        DATE fecha_certificacion
        ENUM estado
        TEXT notas
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    repuestos_inventario {
        BIGINT id PK
        VARCHAR nombre
        TEXT descripcion
        INT stock
        INT stock_minimo
        DECIMAL precio_unitario
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    %% ============================================
    %% TABLAS EXISTENTES (sin cambios)
    %% ============================================

    clientes {
        INT id PK
        VARCHAR nombre
        VARCHAR apellido
        VARCHAR documento UK
        VARCHAR telefono
        VARCHAR email
        VARCHAR direccion
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    vehiculos {
        INT id PK
        INT cliente_id FK
        VARCHAR placa UK
        VARCHAR combustible
        VARCHAR marca
        VARCHAR modelo
        YEAR anio
        VARCHAR serie
        VARCHAR color
        BIGINT taller_actual_id FK  /* NUEVO CAMPO */
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    categorias_almacen {
        BIGINT id PK
        VARCHAR nombre
        BOOLEAN es_serializado
        JSON esquema_atributos
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    productos {
        BIGINT id PK
        BIGINT categoria_id FK
        VARCHAR nombre
        VARCHAR marca
        JSON atributos
        DECIMAL precio_referencial
        INT stock
        BOOLEAN activo
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    items_serializados {
        BIGINT id PK
        BIGINT producto_id FK
        VARCHAR serie UK
        JSON atributos
        ENUM estado
        BIGINT service_order_id FK
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    movimientos_stock {
        BIGINT id PK
        BIGINT producto_id FK
        ENUM tipo
        INT cantidad
        BIGINT service_order_id FK
        VARCHAR motivo
        BIGINT usuario_id FK
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    users {
        BIGINT id PK
        VARCHAR name
        VARCHAR email UK
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    services {
        BIGINT id PK
        VARCHAR nombre
        ENUM tipo
        DECIMAL precio_base
        BOOLEAN activo
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    service_orders {
        BIGINT id PK
        INT cliente_id FK
        INT vehiculo_id FK
        BIGINT service_id FK
        BIGINT cita_id FK
        VARCHAR estado
        DECIMAL precio_lista
        DECIMAL precio_final
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    %% ============================================
    %% RELACIONES
    %% ============================================

    clientes ||--o{ vehiculos : "tiene"
    clientes ||--o{ service_orders : "solicita"

    vehiculos ||--o{ equipo_vehiculos : "tiene equipamiento"
    vehiculos ||--o{ service_orders : "recibe servicios"
    vehiculos }o--|| talleres : "asignado a taller"

    talleres ||--o{ equipo_vehiculos : "origen de equipo"

    categorias_almacen ||--o{ productos : "agrupa"
    productos ||--o{ items_serializados : "define"
    productos ||--o{ movimientos_stock : "registra"

    items_serializados }o--|| service_orders : "asignado a orden"
    movimientos_stock }o--|| service_orders : "vinculado a orden"
    movimientos_stock }o--|| users : "registrado por"

    services ||--o{ service_orders : "genera"
```

---

## Descripción de Relaciones

### NUEVAS TABLAS

| Tabla | Relación | Cardinalidad | Descripción |
|-------|----------|--------------|-------------|
| `talleres` | `vehiculos.taller_actual_id` → `talleres.id` | N:1 | Un vehículo está asignado a un taller actual |
| `equipo_vehiculos` | `equipo_vehiculos.vehiculo_id` → `vehiculos.id` | 1:N | Un vehículo tiene múltiples equipos (reductor, tanque, IGT) |
| `repuestos_inventario` | Tabla independiente | - | Stock de repuestos (válvulas, etc.) de la hoja IGT |

### TABLAS EXISTENTES (sin cambios)

| Tabla | Relación | Cardinalidad | Descripción |
|-------|----------|--------------|-------------|
| `clientes` → `vehiculos` | `vehiculos.cliente_id` → `clientes.id` | 1:N | Un cliente tiene múltiples vehículos |
| `categorias_almacen` → `productos` | `productos.categoria_id` → `categorias_almacen.id` | 1:N | Una categoría agrupa múltiples productos |
| `productos` → `items_serializados` | `items_serializados.producto_id` → `productos.id` | 1:N | Un producto tiene múltiples unidades serializadas |
| `productos` → `movimientos_stock` | `movimientos_stock.producto_id` → `productos.id` | 1:N | Un producto tiene múltiples movimientos |
| `users` → `movimientos_stock` | `movimientos_stock.usuario_id` → `users.id` | 1:N | Un usuario registra múltiples movimientos |
| `services` → `service_orders` | `service_orders.service_id` → `services.id` | 1:N | Un servicio genera múltiples órdenes |

### CAMPO NUEVO EN `vehiculos`

```sql
ALTER TABLE vehiculos 
ADD COLUMN taller_actual_id BIGINT UNSIGNED NULL AFTER color,
ADD CONSTRAINT fk_vehiculo_taller 
    FOREIGN KEY (taller_actual_id) REFERENCES talleres(id) ON DELETE SET NULL;
```

---

## Flujo de Datos del Inventario

```
┌─────────────────────────────────────────────────────────────────┐
│                    FLUJO DE ALMACÉN                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────┐    ┌──────────────┐    ┌──────────────────┐      │
│  │ LLEGADA  │───▶│  ASIGNACIÓN  │───▶│  INSTALACIÓN     │      │
│  │ VEHÍCULO │    │  EQUIPO      │    │  EN VEHÍCULO     │      │
│  └──────────┘    └──────────────┘    └──────────────────┘      │
│       │                │                       │                │
│       ▼                ▼                       ▼                │
│  fecha_llegada   marca/serie/          equipo_vehiculos         │
│  taller_origen   generacion/cap        .estado = 'instalado'   │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    ESTADOS DEL EQUIPO                    │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │  en_stock  →  asignado  →  instalado  →  retirado/cambio│  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    CERTIFICACIÓN                         │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │  fecha_certificacion  →  equipo validado oficialmente    │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Categorías del Almacén (Seed Data)

```php
// Categorías para el Excel INVENTARIO 2026
categorias_almacen: [
    {
        nombre: 'Reductores',
        es_serializado: true,
        esquema_atributos: ['marca', 'generacion', 'produce']
    },
    {
        nombre: 'Tanques',
        es_serializado: true,
        esquema_atributos: ['marca', 'capacidad', 'produce']
    },
    {
        nombre: 'Equipos IGT',
        es_serializado: true,
        esquema_atributos: ['marca', 'produce']
    },
    {
        nombre: 'Repuestos',
        es_serializado: false,  // stock por cantidad
        esquema_atributos: []
    }
]
```

---

## Modelos Laravel Necesarios

### 1. `Taller.php` (NUEVO)
```php
class Taller extends Model {
    protected $table = 'talleres';
    protected $fillable = ['nombre', 'direccion', 'telefono', 'activo'];
    
    public function vehiculos() {
        return $this->hasMany(Vehiculo::class, 'taller_actual_id');
    }
}
```

### 2. `EquipoVehiculo.php` (NUEVO)
```php
class EquipoVehiculo extends Model {
    protected $table = 'equipo_vehiculos';
    protected $fillable = [
        'vehiculo_id', 'tipo', 'marca', 'generacion', 
        'capacidad', 'serie', 'produce', 'fecha_llegada',
        'taller_origen', 'fecha_entrega', 'fecha_certificacion',
        'estado', 'notas'
    ];
    
    public function vehiculo() {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }
}
```

### 3. `RepuestoInventario.php` (NUEVO)
```php
class RepuestoInventario extends Model {
    protected $table = 'repuestos_inventario';
    protected $fillable = [
        'nombre', 'descripcion', 'stock', 
        'stock_minimo', 'precio_unitario'
    ];
}
```

### 4. `Vehiculo.php` (MODIFICAR — agregar relación)
```php
// Agregar al modelo existente:
public function taller() {
    return $this->belongsTo(Taller::class, 'taller_actual_id');
}

public function equipoInstalado() {
    return $this->hasMany(EquipoVehiculo::class, 'vehiculo_id');
}
```
