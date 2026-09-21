# ARTURO MOTORS — Cronograma de Desarrollo
## Módulo: Cambio de Piezas / Almacén GNV
### Plazo: hasta el Viernes 12 de Septiembre 2026

---

## FLUJO CORREGIDO ✅

```
┌─────────────────────────────────────────────────────────────────┐
│                 FLUJO CORRECTO (3 pasos)                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  PASO 1: VER EQUIPOS (sin iniciar)                              │
│  ─────────────────────────────────────────────────────────────  │
│  Técnico ve la lista de piezas asignadas                        │
│  NO puede reportar nada todavía                                 │
│  Solo ve: "Iniciar conversión"                                  │
│                                                                  │
│       │                                                          │
│       ▼ Presiona "Iniciar conversión"                           │
│                                                                  │
│  PASO 2: CONVERSIÓN EN PROCESO                                 │
│  ─────────────────────────────────────────────────────────────  │
│  Ahora SÍ aparece el botón al lado de cada pieza:               │
│  ┌─────────────────────────────────────┐                        │
│  │ Sensor O2    (Serie: ABC-123)       │                        │
│  │ [Asignado]  [Solicitar pieza faltante] ← NUEVO             │
│  └─────────────────────────────────────┘                        │
│                                                                  │
│       │                                                          │
│       ▼ Presiona "Solicitar pieza faltante"                    │
│                                                                  │
│  PASO 3: MODAL DE SOLICITUD                                    │
│  ─────────────────────────────────────────────────────────────  │
│  3a. Escribe POR QUÉ no calza                                  │
│  3b. Sistema busca piezas sueltas automáticamente              │
│  3c. Si encuentra → reemplaza directo                          │
│  3d. Si NO encuentra → "Solicitar al almacén"                  │
│  3e. Almacén abre kit → asigna pieza nueva                     │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## ESTADO ACTUAL

```
LUN 08      MAR 09      MIÉ 10      JUE 11      VIE 12
──────────────────────────────────────────────────────────

✅ COMPLETADO HOY:
[████████] Migración DB: reporte_piezas_no_encajadas
[████████] Modelo ReportePiezaNoEncajada + relaciones
[████████] Servicio CambioPiezaService (lógica de negocio)
[████████] Componente Realizar.php (modal 2 pasos)
[████████] Componente ReportesPendientes.php (almacén)
[████████] Vistas Blade actualizadas
[████████] Ruta /almacen/reportes-piezas
[████████] Sidebar: "Pendientes" → "Solicitudes de Reemplazo"
[████████] Flujo corregido: botón solo DESPUÉS de iniciar

🔲 PENDIENTE:
                      [░░░░░░░░] Notificaciones al almacén
                      [░░░░░░░░] Historial de cambios
                      [░░░░░░░░] Pruebas end-to-end
                      [░░░░░░░░] Ajustes UI/UX

PROGRESO: ████████████████████░░░░ 80%
```

---

## ARCHIVOS MODIFICADOS

| Archivo | Qué cambió |
|---------|------------|
| `Realizar.php` | Lógica separada: items se ven siempre, botón solo después de iniciar |
| `realizar.blade.php` | Botón "Solicitar pieza faltante" solo visible si `fecha_inicio_conversion` existe |
| `ReportesPendientes.php` | Panel de almacén para abrir kits |
| `reportes-pendientes.blade.php` | Vista completa de almacén |
| `ReportePiezaNoEncajada.php` | Modelo con estados y acciones |
| `CambioPiezaService.php` | Servicio con toda la lógica |
| `custom-nav-menu.blade.php` | Sidebar actualizado |

---

## CÓMO PROBAR

```
1. Ir a: /conversiones/{id}/realizar
2. Ver piezas asignadas (botón NO visible aún)
3. Presionar "Iniciar conversión"
4. AHORA aparece "Solicitar pieza faltante" al lado de cada pieza
5. Presionar ese botón → se abre el modal
6. Escribir motivo → sistema busca piezas
7. Si no hay → "Solicitar al almacén"
8. Ir a: /almacen/reportes-piezas
9. Ver el reporte pendiente → abrir kit → asignar pieza
```

---

*Generado: Mar 09 de Septiembre 2026*
*Versión: 1.1 — Flujo corregido*
