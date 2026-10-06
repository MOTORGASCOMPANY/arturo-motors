<?php
use App\Http\Controllers\Caja\DetalleController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentosConversionController;
use App\Http\Controllers\KitComponentesController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PhpMyInfoController;
use App\Http\Controllers\ReporteCitasPdfController;
use App\Http\Controllers\ReporteCitasExcelController;
use App\Http\Controllers\ReporteCajaPdfController;
use App\Http\Controllers\ReporteCajaExcelController;
use App\Http\Controllers\ReporteServiciosPdfController;
use App\Http\Controllers\ReporteServiciosExcelController;
use App\Http\Controllers\ReporteAlmacenPdfController;
use App\Http\Controllers\ReporteAlmacenExcelController;
use App\Http\Controllers\ReporteConversionesPdfController;
use App\Http\Controllers\ReporteConversionesExcelController;
use App\Http\Controllers\FiseReporteController;
use App\Livewire\AdminPermisos;
use App\Livewire\AdminRoles;
use App\Livewire\Almacen\CategoriaListado as CategoriasListado;
use App\Livewire\Almacen\ProductoListado as ProductosListado;
use App\Livewire\Almacen\TrasladoListado as TrasladosListado;
use App\Livewire\Caja\AbrirCaja;
use App\Livewire\Caja\CerrarCaja;
use App\Livewire\Caja\DetalleSesion;
use App\Livewire\Fise\Auditoria as FiseAuditoria;
use App\Livewire\Fise\Detalle as FiseDetalle;
use App\Livewire\Caja\HistorialSesiones;
use App\Livewire\Caja\RegistrarEgreso;
use App\Livewire\Caja\Reporte as ReporteCaja;
use App\Livewire\Servicios\Reporte as ReporteServicios;
use App\Livewire\Almacen\ReporteDashboardInventario as ReporteAlmacen;
use App\Livewire\Conversiones\Reporte as ReporteConversiones;
use App\Livewire\Conversiones\AlmacenPendientes;
use App\Livewire\Conversiones\AsignarEquipos;
use App\Livewire\Conversiones\AsignarTecnico;
use App\Livewire\Conversiones\Crear;
use App\Livewire\Conversiones\EntregaPendientes;
use App\Livewire\Conversiones\EntregarCobrar;
use App\Livewire\Conversiones\Evaluar;
use App\Livewire\Conversiones\MisAsignadas;
use App\Livewire\Conversiones\Realizar;
use App\Livewire\ListaCitas;
use App\Livewire\ListaClientes;
use App\Livewire\ListaConversiones;
use App\Livewire\ListaVehiculos;
use App\Livewire\Reportes\ReporteCitas;
use App\Livewire\RRHH\Contratos;
use App\Livewire\Cms\GestionarContacto;
use App\Livewire\Cms\GestionarContenido;
use App\Livewire\Cms\GestionarPasos;
use App\Livewire\Cms\GestionarPorQue;
use App\Livewire\Cms\GestionarRedes;
use App\Livewire\Cms\GestionarServicios;
use App\Livewire\RRHH\GestionarVacaciones;
use App\Livewire\RRHH\GestionDocumentos;
use App\Livewire\RRHH\ListaPlanilla;
use App\Livewire\RRHH\MisPlanillas;
use App\Livewire\ServiceOrders\CrearSimple;
use App\Livewire\ServiceOrders\Detalle;
use App\Livewire\ServiceOrders\Listado;
use App\Livewire\SolicitudRepuestos;
use App\Livewire\Usuarios;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;



Route::get('/', [LandingController::class, 'index'])->name('landing');

RateLimiter::for('livewire', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip());
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // phpinfo protegido: solo usuarios autenticados (antes era público)
    Route::get('phpmyinfo', [PhpMyInfoController::class, '__invoke'])->name('phpmyinfo');

    // Citas
    Route::middleware('can:opciones.citas')->group(function () {
        Route::get('/lista-citas', ListaCitas::class)->name('citas.lista');

        Route::get('/rpta-citas/export-pdf', ReporteCitasPdfController::class)->name('citas.reporte.pdf');
        Route::get('/rpta-citas/export-excel', ReporteCitasExcelController::class)->name('citas.reporte.excel');
    });

    // Mantenimiento de tablas
    Route::middleware('can:opciones.mantenimientotables')->group(function () {
        Route::get('/lista-vehiculos', ListaVehiculos::class)->name('vehiculos.lista');
        Route::get('/lista-clientes', ListaClientes::class)->name('clientes.lista');
    });

    // Rutas modulo de caja
    Route::prefix('caja')->name('caja.')->middleware('can:opciones.caja')->group(function () {
        Route::get('/abrir', AbrirCaja::class)->name('abrir');
        Route::get('/egreso', RegistrarEgreso::class)->name('egreso');
        Route::get('/cerrar', CerrarCaja::class)->name('cerrar');
        Route::get('/historial', HistorialSesiones::class)->name('historial');
        Route::get('/sesion/{sesionId}', DetalleSesion::class)->name('sesion');
        Route::get('/sesion/{sesion}/detalle', [DetalleController::class, 'show'])->name('sesion.detalle');
    });

    // Rutas modulo FISE (separado de caja)
    Route::middleware('can:opciones.fise')->group(function () {
        Route::get('/fise/control', FiseAuditoria::class)->name('fise.control');
        Route::get('/fise/sesion/{sesion}', FiseDetalle::class)->name('fise.detalle');
        Route::get('/fise/reporte/pdf', [FiseReporteController::class, 'pdf'])->name('fise.reporte.pdf');
        Route::get('/fise/reporte/excel', [FiseReporteController::class, 'excel'])->name('fise.reporte.excel');
    });
    Route::get('/fise/reporte', \App\Livewire\Fise\Reporte::class)->middleware('can:opciones.reportes')->name('fise.reporte');

    // Rutas de servicios
    Route::get('/ordenes', Listado::class)->middleware('can:ordenes.listado')->name('ordenes.listado');
    Route::get('/ordenes/{ordenId}', Detalle::class)->middleware('can:ordenes.listado')->name('ordenes.detalle');
    Route::get('/ordenes/simple/crear', CrearSimple::class)->middleware('can:opciones.servicios')->name('ordenes.simple.crear');
    Route::get('/conversiones/crear', Crear::class)->middleware('can:opciones.servicios')->name('conversiones.crear'); // P1: Crear orden de conversión (Vendedor)

    // Rutas modulo de conversiones
    Route::get('/lista-conversiones', ListaConversiones::class)->middleware('can:opciones.conversiones')->name('conversiones.lista'); // Listado general de conversiones
    Route::get('/conversiones/asignar', AsignarTecnico::class)->middleware('can:conversiones.asignar')->name('conversiones.asignar'); // P2: Asignar técnico (Jefe de taller) — ve todas las órdenes creadas
    Route::get('/conversiones/mis-asignadas', MisAsignadas::class)->middleware('can:conversiones.mis-asignadas')->name('conversiones.mis-asignadas'); // P3: Mis conversiones asignadas (Técnico) — filtra por tecnico_id
    Route::get('/conversiones/{ordenId}/evaluar', Evaluar::class)->middleware('can:conversiones.mis-asignadas')->name('conversiones.evaluar'); // P4: Evaluación (Técnico) — checklist + apto/no apto
    Route::get('/conversiones/almacen/pendientes', AlmacenPendientes::class)->middleware('can:opciones.almacen')->name('conversiones.almacen-pendientes'); // P5: Asignar equipos (Almacenero) — vincula items_serializados a la orden
    Route::get('/conversiones/{ordenId}/asignar-equipos', AsignarEquipos::class)->middleware('can:opciones.almacen')->name('conversiones.asignar-equipos'); // P5: Asignar equipos (Almacenero) — vincula items_serializados a la orden
    Route::get('/conversiones/{ordenId}/realizar', Realizar::class)->middleware('can:conversiones.mis-asignadas')->name('conversiones.realizar'); // P6: Realizar conversión (Técnico) — inicia, marca instalado, finaliza
    Route::get('/conversiones/entregas/pendientes', EntregaPendientes::class)->middleware('can:conversiones.entregas-pendientes')->name('conversiones.entregas-pendientes'); // P7: Entrega y cobro (Cajero) — reutiliza la lógica de cobro existente en CrearSimple
    Route::get('/conversiones/{ordenId}/entregar', EntregarCobrar::class)->middleware('can:conversiones.entregas-pendientes')->name('conversiones.entregar'); // P7: Entrega y cobro (Cajero) — reutiliza la lógica de cobro existente en CrearSimple
    Route::get('/conversiones/{conversionId}/solicitud-repuestos', SolicitudRepuestos::class)->middleware('can:opciones.conversiones')->name('conversiones.solicitud-repuestos'); // Solicitud de repuestos para conversión

    

    // Rutas modulo de almacen
    Route::prefix('almacen')->name('almacen.')->middleware('can:opciones.almacen')->group(function () {
        Route::get('/categorias', CategoriasListado::class)->name('categorias.listado');
        Route::get('/productos', ProductosListado::class)->name('productos.listado');

        // Recepciones de kits
        Route::get('/recepciones', \App\Livewire\Almacen\RecepcionListado::class)->name('recepciones.listado');
        Route::get('/recepciones/crear', \App\Livewire\Almacen\RecepcionAlta::class)->name('recepciones.crear');

        // Monitoreo de conversiones activas (dashboard en tiempo real)
        Route::get('/reportes-piezas', \App\Livewire\Almacen\ReporteConversionesActivas::class)->name('reportes-piezas');

        // Traslados
        Route::get('/traslados', TrasladosListado::class)->name('traslados.listado');
        Route::get('/traslados/crear', \App\Livewire\Almacen\TrasladoAlta::class)->name('traslados.crear');
    });

    // Pantallas de Reportes (sección Reportes del menú)
    Route::middleware('can:opciones.reportes')->group(function () {
        Route::get('/citas/reporte', ReporteCitas::class)->name('citas.reporte');
        Route::get('/caja/reporte', ReporteCaja::class)->name('caja.reporte');
        Route::get('/servicios/reporte', ReporteServicios::class)->name('servicios.reporte');
        Route::get('/almacen/reporte', ReporteAlmacen::class)->name('almacen.reporte');
        Route::get('/conversiones/reporte', ReporteConversiones::class)->name('conversiones.reporte');
    });

    // Exportaciones de reportes (permiso del módulo que contiene los datos)
    Route::middleware('can:opciones.servicios')->group(function () {
        Route::get('/reporte-servicios/pdf', [ReporteServiciosPdfController::class, '__invoke'])->name('servicios.reporte.pdf');
        Route::get('/reporte-servicios/excel', [ReporteServiciosExcelController::class, '__invoke'])->name('servicios.reporte.excel');
    });
    Route::middleware('can:opciones.almacen')->group(function () {
        Route::get('/reporte-almacen/pdf', [ReporteAlmacenPdfController::class, '__invoke'])->name('almacen.reporte.pdf');
        Route::get('/reporte-almacen/excel', [ReporteAlmacenExcelController::class, '__invoke'])->name('almacen.reporte.excel');
    });
    Route::middleware('can:opciones.conversiones')->group(function () {
        Route::get('/reporte-conversiones/pdf', [ReporteConversionesPdfController::class, '__invoke'])->name('conversiones.reporte.pdf');
        Route::get('/reporte-conversiones/excel', [ReporteConversionesExcelController::class, '__invoke'])->name('conversiones.reporte.excel');
    });
    Route::middleware('can:opciones.caja')->group(function () {
        Route::get('/reporte-caja/pdf', [ReporteCajaPdfController::class, '__invoke'])->name('caja.reporte.pdf');
        Route::get('/reporte-caja/excel', [ReporteCajaExcelController::class, '__invoke'])->name('caja.reporte.excel');
    });

    // Rutas modulo de recursos humanos
    Route::get('/rrhh/contratos', Contratos::class)->middleware('can:rrhh.contratos')->name('rrhh.contratos');
    Route::get('/rrhh/vacaciones/contrato/{idContrato}', GestionarVacaciones::class)->middleware('can:rrhh.contratos')->name('rrhh.vacaciones.index');
    Route::get('/rrhh/documentos/{id?}', GestionDocumentos::class)->middleware('can:rrhh.contratos')->name('rrhh.documentos');
    Route::get('/rrhh/planillas', ListaPlanilla::class)->middleware('can:rrhh.planillas')->name('rrhh.planillas');
    // "Mis planillas" se autLimita a Auth::id() (es el panel del propio empleado), por eso solo requiere sesión.
    Route::get('/rrhh/mis-planillas', MisPlanillas::class)->name('rrhh.mis-planillas');

    // Rutas modulo de Usuarios y Roles (exclusivo del Administrador del sistema)
    Route::get('/usuarios', Usuarios::class)->middleware('can:usuarios')->name('usuarios');
    Route::get('/roles', AdminRoles::class)->middleware('can:usuarios.roles')->name('usuarios.roles');
    Route::get('/permisos', AdminPermisos::class)->middleware('can:usuarios.permisos')->name('usuarios.permisos');

    // Rutas de PDF (documentos operativos: quien puede ver órdenes puede descargar sus documentos)
    Route::middleware('can:ordenes.listado')->group(function () {
        Route::get('/garantia/pdf/{id}', [PdfController::class, 'generaPdfCartaGarantia'])->name('vehiculo.pdf');
        Route::get('/manual/pdf/{id}', [PdfController::class, 'generaPdfManual'])->name('manual.pdf');
        Route::get('/ordenRepuestos/pdf/{id}', [PdfController::class, 'generaPdfOrdenRepuestos'])->name('repuestos.orden.pdf');
        Route::get('/evaluacion/pdf/{id}', [PdfController::class, 'generaPdfEvaluacion'])->name('expedientes.evaluacion.pdf');
        Route::get('/comprobantes/{ordenId}/pdf', [ComprobanteController::class, 'pdf'])->name('comprobantes.pdf');
        Route::get('/ordenes/{ordenId}/pdf/carta-garantia', [DocumentosConversionController::class, 'cartaGarantia'])->name('conversiones.pdf.carta-garantia');
        Route::get('/ordenes/{ordenId}/pdf/hoja-recepcion', [DocumentosConversionController::class, 'hojaRecepcion'])->name('conversiones.pdf.hoja-recepcion');
        Route::get('/ordenes/{ordenId}/pdf/constancia-entrega', [DocumentosConversionController::class, 'constanciaEntrega'])->name('conversiones.pdf.constancia-entrega');
    });

    Route::get('/rrhh/contrato/{id}/pdf', [PdfController::class, 'generarContrato'])->middleware('can:rrhh.contratos')->name('rrhh.contrato.pdf');

    // Rutas modulo CMS
    Route::middleware('can:opciones.cms')->prefix('cms')->name('cms.')->group(function () {
        Route::get('/contenido', GestionarContenido::class)->name('contenido');
        Route::get('/servicios', GestionarServicios::class)->name('servicios');
        Route::get('/pasos', GestionarPasos::class)->name('pasos');
        Route::get('/porque', GestionarPorQue::class)->name('porque');
        Route::get('/contacto', GestionarContacto::class)->name('contacto');
        Route::get('/redes', GestionarRedes::class)->name('redes');
    });

    // API para componentes de kit — agrupado por kit individual (lógica en KitComponentesController@index)
    Route::get('/api/kit-componentes/{productoId}', [KitComponentesController::class, 'index'])
        ->middleware('auth')
        ->name('api.kit-componentes');

});