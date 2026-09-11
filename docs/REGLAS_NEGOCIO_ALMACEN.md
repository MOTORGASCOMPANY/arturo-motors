# GUÍA DE REGLAS DEL NEGOCIO — MÓDULO ALMACÉN GNV
## Arturo Motors — Implementación actualizada

---

## 1. FLUJO GENERAL

```
┌─────────────────────────────────────────────────────────────┐
│                    FLUJO COMPLETO                           │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  PROVEEDOR ──→ CALLAO ──→ RECEPCIÓN ──→ KIT SELLADO        │
│                                     (stock reservado)       │
│                                         │                   │
│                                         ▼                   │
│  ORDEN DE SERVICIO ──→ ASIGNACIÓN DE KIT ──→ KIT ABIERTO   │
│                       (abrir caja)    (piezas reservadas)   │
│                                         │                   │
│                                         ▼                   │
│                                   INSTALACIÓN               │
│                                         │                   │
│                              ┌──────────┴──────────┐        │
│                              │                     │        │
│                         SÍ ENCAJA            NO ENCAJA      │
│                              │                     │        │
│                              ▼                     ▼        │
│                         INSTALAR           BUSCAR COMPATIBLE│
│                              │                     │        │
│                              │              ┌──────┴──────┐ │
│                              │              │             │ │
│                              │         ENCONTRÓ     NO ENCONTRÓ│
│                              │              │             │ │
│                              │              ▼             ▼ │
│                              │         REEMPLAZAR  SOLICITAR│
│                              │         (devuelve)  ALMACÉN  │
│                              │              │             │ │
│                              │              │             ▼ │
│                              │              │     ABRIR KIT │
│                              │              │     NUEVO     │
│                              ▼              ▼             ▼ │
│                         FINALIZAR                         │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. REGLAS POR PANTALLA

### 2.1 RECEPCIONES (Crear)

| Regla | Descripción |
|-------|-------------|
| **Quién** | Personal de almacén (Callao) |
| **Qué** | Recibe cajas del proveedor |
| **Kit completo** | Marca todos los componentes como incluidos → caja sellada en stock |
| **Kit incompleto** | Marca componentes faltantes → caja incompleta → necesita completarse |
| **NÚMERO DE SERIE** | REQUERIDO para reductor y tanque (sin serie no se puede recibir) |
| **Proveedor** | MOCAVIN, AUTO TOP, UNIGAS, D'WILLIAMS |
| **Ubicación** | Siempre Callao (sede 4) |

### 2.2 RECEPCIONES (Listado)

| Regla | Descripción |
|-------|-------------|
| **Quién** | Personal de almacén |
| **Qué** | Historial de recepciones |
| **Filtros** | Proveedor, fecha, estado |
| **Solo lectura** | No se puede editar ni eliminar |

### 2.3 STOCK (Ver)

| Regla | Descripción |
|-------|-------------|
| **Quién** | Personal de almacén |
| **Qué** | Inventario actual |
| **Kits sellados** | Cajas completas listas para abrir |
| **Piezas sueltas** | Componentes de kits ya abiertos |
| **Repuestos** | Piezas por cantidad (válvulas, pernos, etc.) |
| **Reservas** | Las piezas RESERVADAS para una conversión NO aparecen como disponibles |

### 2.4 KITS (Completar)

| Regla | Descripción |
|-------|-------------|
| **Quién** | Personal de almacén |
| **Qué** | Armar kits incompletos usando piezas del stock |
| **Condición** | Solo para kits que llegaron incompletos del proveedor |
| **Resultado** | Kit completo y sellado listo para asignar |

### 2.5 ASIGNACIÓN DE EQUIPOS (AsignarEquipos)

| Regla | Descripción |
|-------|-------------|
| **Quién** | Personal de almacén |
| **Qué** | Asigna kit completo a una conversión |
| **Kit sellado** | Se selecciona un kit de stock → se abre → piezas se RESERVAN para esta conversión |
| **Piezas sueltas** | Se pueden agregar piezas adicionales del stock (solo las que NO están reservadas) |
| **Repuestos** | Se agregan por cantidad del stock |
| **RESTRICCIÓN** | Las piezas RESERVADAS no aparecen como disponibles para otras conversiones |
| **Resultado** | Kit asignado → orden pasa a "en_conversion" |

### 2.6 INSTALACIÓN (Realizar)

| Regla | Descripción |
|-------|-------------|
| **Quién** | Técnico asignado |
| **Qué** | Instala las piezas del kit |
| **SÍ encaja** | Marca como instalada → stock cuadra |
| **NO encaja** | Presiona "Pieza no encaja" → sistema busca automáticamente compatible |
| **Búsqueda automática** | Sistema busca pieza del mismo tipo en stock (no reservada) |
| **Encontró compatible** | Técnico confirma → se reemplaza → vieja vuelve al stock |
| **NO encontró compatible** | Técnico solicita al almacén → almacén abre kit nuevo |
| **Registrar series** | Link a pantalla de registro de series instaladas |
| **Finalizar** | Cuando todos los items están instalados → orden pasa a "conversion_completada" |

---

## 3. REGLAS DE STOCK

### 3.1 Estados de un Item Serializado

```
en_stock → asignado → instalado
    ↑           │
    │           ▼
    └──── devuelto (si no encaja)
```

| Estado | Descripción |
|--------|-------------|
| **en_stock** | Disponible en almacén |
| **asignado** | Reservado para una conversión (NO disponible para otros) |
| **instalado** | Ya está en el carro |
| **devuelto** | No encajó → vuelve al stock |

### 3.2 Reglas de Reserva

```
CUANDO se abre un kit:
├── El kit pasa a "asignado"
├── Sus piezas quedan RESERVADAS para esa conversión
├── NO aparecen como stock disponible para otros
└── Solo se pueden usar para esa conversión

CUANDO una pieza no encaja:
├── La pieza vieja vuelve a "en_stock"
├── Se busca pieza compatible automáticamente
├── Si se encuentra → se asigna a la conversión
└── Si no se encuentra → se solicita almacén
```

### 3.3 Reglas de Cantidad

| Tipo | Se maneja por |
|------|---------------|
| **Kits** | 1 kit = 1 unidad (sellado) |
| **Piezas serializadas** | 1 pieza = 1 unidad (con serie) |
| **Repuestos** | Cantidad (válvulas ×10, pernos ×8, etc.) |

---

## 4. REGLAS DE TRASLADO

| Regla | Descripción |
|-------|-------------|
| **Origen** | Callao (sede 4) — única sede que distribuye |
| **Destino** | Santa Anita (5) o Ancon (6) |
| **Kit sellado** | Se envía tal cual (sin abrir) |
| **Kit abierto** | Se especifican pieza por pieza |
| **Costo** | $0 (mismo dueño) |
| **Stock** | Se descuenta del origen, se suma al destino |

---

## 5. REGLAS DE INCOMPATIBILIDAD

### 5.1 Flujo del técnico

```
1. Técnico instala pieza
2. ¿Encaja? → SÍ → confirma → siguiente pieza
3. ¿Encaja? → NO → presiona "Pieza no encaja"
4. Sistema busca automáticamente pieza compatible
5. ¿Encontró? → SÍ → técnico confirma → reemplaza
6. ¿Encontró? → NO → técnico solicita al almacén
7. Almacén abre kit nuevo → asigna pieza
8. Pieza vieja vuelve al stock
```

### 5.2 Restricciones

| Regla | Descripción |
|-------|-------------|
| **Solo el técnico** | Puede marcar "pieza no encaja" |
| **Solo el almacén** | Puede abrir kits nuevos |
| **Automático** | El sistema busca pieza compatible antes de pedir apertura |
| **Stock siempre cuadra** | Cada movimiento se registra en movimientos_stock |

---

## 6. REGLAS DE ESTADO DE ORDEN

```
creada → en_evaluacion → aprobado_conversion → en_conversion → conversion_completada → entregado
                                           ↓
                                    evaluacion_rechazada
```

| Estado | Quién cambia | Condición |
|--------|--------------|-----------|
| **aprobado_conversion** | Almacén | Pago verificado |
| **en_conversion** | Almacén | Equipos asignados |
| **conversion_completada** | Técnico | Todos los items instalados |
| **entregado** | Almacén | Cobro registrado |

---

## 7. PANTALLAS POR ROL

| Rol | Pantallas |
|-----|-----------|
| **Almacén** | Recepciones/Crear, Recepciones/Listado, Stock/Ver, Kits/Completar, AsignarEquipos, Traslados/Crear, Almacén Pendientes |
| **Técnico** | Realizar, RegistrarSeries |
| **Cajero** | Finalizar Cobro |
| **Administrador** | Todas |

---

## 8. RESTRICCIONES IMPORTANTES

1. **NO se pueden mezclar kits** de diferentes proveedores en una conversión
2. **NO se pueden usar piezas reservadas** para otras conversiones
3. **NO se puede finalizar** si hay piezas sin instalar
4. **NO se puede recibir** sin número de serie (reductor/tanque)
5. **NO se puede abrir kit** sin ser personal de almacén
6. **NO se puede marcar "no encaja"** sin ser el técnico asignado
7. **TODO se registra** en movimientos_stock (trazabilidad completa)

---

## 9. CASOS ESPECIALES

### 9.1 Kit incompleto del proveedor
```
1. Recibir kit con componentes faltantes
2. Ir a Kits/Completar
3. Agregar piezas del stock para completar
4. Kit queda listo para asignar
```

### 9.2 Pieza no compatible
```
1. Técnico marca "pieza no encaja"
2. Sistema busca compatible automáticamente
3. Si no hay → solicitar al almacén
4. Almacén abre kit nuevo
5. Asigna pieza nueva
6. Pieza vieja vuelve al stock
```

### 9.3 Traslado de kit sellado
```
1. Seleccionar kit en stock
2. Elegir destino (Santa Anita o Ancon)
3. Confirmar traslado
4. Kit se descuenta de Callao
5. Kit se suma al destino
```

### 9.4 Traslado de piezas sueltas
```
1. Seleccionar pieza por pieza
2. Elegir destino
3. Confirmar traslado
4. Piezas se descuentan de Callao
5. Piezas se suman al destino
```

---

## 10. FLUJO RESUMEN

```
LLEGA CAJA ──→ SE REGISTRA ──→ SE ABRE ──→ SE ASIGNA ──→ SE INSTALA
     │              │             │            │              │
  proveedor      almacén      almacén     almacén        técnico
                              (abre)     (reserva)     (instala)
                                                         │
                                                    ┌────┴────┐
                                                    │         │
                                              SÍ ENCAJA  NO ENCAJA
                                                    │         │
                                                    ▼         ▼
                                               CONFIRMAR  BUSCAR
                                                          COMPATIBLE
                                                               │
                                                         ┌─────┴─────┐
                                                         │           │
                                                    ENCONTRÓ   NO ENCONTRÓ
                                                         │           │
                                                         ▼           ▼
                                                    REEMPLAZAR  SOLICITAR
                                                                 ALMACÉN
```

---

*Última actualización: Septiembre 2026*
*Versión: 2.0*
