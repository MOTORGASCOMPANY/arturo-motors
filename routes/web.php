<?php

use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\DocumentosConversionController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PdfController;
use App\Livewire\AdminPermisos;
use App\Livewire\AdminRoles;
use App\Livewire\Almacen\Categorias\Crear as CategoriasCrear;
use App\Livewire\Almacen\Categorias\Listado as CategoriasListado;
use App\Livewire\Almacen\Productos\Crear as ProductosCrear;
use App\Livewire\Almacen\Productos\Listado as ProductosListado;
use App\Livewire\Almacen\Productos\RegistrarEntrada;
use App\Livewire\Caja\AbrirCaja;
use App\Livewire\Caja\CerrarCaja;
use App\Livewire\Caja\DetalleSesion;
use App\Livewire\Caja\HistorialSesiones;
use App\Livewire\Caja\RegistrarEgreso;
use App\Livewire\Caja\Reporte as ReporteCaja;
use App\Livewire\Servicios\Reporte as ReporteServicios;
use App\Livewire\CrearCitas;
use App\Livewire\Conversiones\AlmacenPendientes;
use App\Livewire\Conversiones\AsignarEquipos;
use App\Livewire\Conversiones\AsignarTecnico;
use App\Livewire\Conversiones\Crear;
use App\Livewire\Conversiones\EntregaPendientes;
use App\Livewire\Conversiones\EntregarCobrar;
use App\Livewire\Conversiones\Evaluar;
use App\Livewire\Conversiones\MisAsignadas;
use App\Livewire\Conversiones\Realizar;
use App\Livewire\ExpedienteModal;
use App\Livewire\GestorRepuestos;
use App\Livewire\Inicio;
use App\Livewire\ListaCitas;
use App\Livewire\ListaClientes;
use App\Livewire\ListaConversiones;
use App\Livewire\ListaExpedientes;
use App\Livewire\ListaServicios;
use App\Livewire\ListaVehiculos;
use App\Livewire\ProcesarCobro;
use App\Livewire\Reportes\ReporteCitas;
use App\Livewire\RRHH\Contratos;
use App\Http\Controllers\CmsController;
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
use App\Livewire\SelectorClienteVehiculo;
use App\Livewire\ServiceOrders\CrearSimple;
use App\Livewire\ServiceOrders\Detalle;
use App\Livewire\ServiceOrders\Listado;
use App\Livewire\SolicitudRepuestos;
use App\Livewire\Usuarios;
use App\Models\Media;
use App\Models\PageMedia;
use App\Models\PageSection;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return view('welcome');
});*/

/*Route::get('/', function () {
    return redirect()->to('/login');
});*/

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/home', function () {
    return redirect()->route('landing');
});

Route::get('index', [LandingController::class, 'index'])->name('landing.index');

Route::get('phpmyinfo', function () {
    phpinfo();
})->name('phpmyinfo');

RateLimiter::for('livewire', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip()); // Máx 10 solicitudes por minuto por IP
});

/*Route::middleware('throttle:livewire')->group(function () {
    Route::post('/livewire/message/{component}', '\Livewire\Controllers\HttpConnectionHandler');
});*/

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

     Route::get('/inicio', Inicio::class)->name('inicio');

     // Citas
     Route::get('/lista-citas', ListaCitas::class)->name('ListaCitas');

    // Expedientes
    //Route::get('/lista-expedientes', ListaExpedientes::class)->name('ListaExpedientes');

    // Reportes
    Route::get('/rpta-citas', ReporteCitas::class)->name('Rpta.Citas');

    // Vehículos
    Route::get('/lista-vehiculos', ListaVehiculos::class)->name('ListaVehiculos');

    // Clientes
    Route::get('/lista-clientes', ListaClientes::class)->name('ListaClientes');

    // Rutas modulo de caja (MEJORAR BLADE)
    Route::get('/caja/abrir', AbrirCaja::class)->name('caja.abrir');
    //Route::get('/caja/abrir', AbrirCaja::class)->middleware('can:caja.abrir')->name('caja.abrir');
    Route::get('/caja/egreso', RegistrarEgreso::class)->name('caja.egreso');
    Route::get('/caja/cerrar', CerrarCaja::class)->name('caja.cerrar');
    Route::get('/caja/historial', HistorialSesiones::class)->name('caja.historial');
    Route::get('/caja/sesion/{sesionId}', DetalleSesion::class)->name('caja.sesion');

    Route::get('/caja/reporte', ReporteCaja::class)->name('caja.reporte');

    // Rutas de servicios
    Route::get('/ordenes', Listado::class)->name('ordenes.listado');
    Route::get('/ordenes/simple/crear', CrearSimple::class)->name('ordenes.simple.crear');   
    Route::get('/conversiones/crear', Crear::class)->name('conversiones.crear'); // P1: Crear orden de conversión (Vendedor)

    Route::get('/ordenes/{ordenId}', Detalle::class)->name('ordenes.detalle');

    Route::get('/servicios/reporte', ReporteServicios::class)->name('servicios.reporte');

    // Rutas modulo de conversiones
    Route::get('/conversiones/asignar', AsignarTecnico::class)->name('conversiones.asignar'); // P2: Asignar técnico (Jefe de taller) — ve todas las órdenes creada
    Route::get('/conversiones/mis-asignadas', MisAsignadas::class)->name('conversiones.mis-asignadas'); // P3: Mis conversiones asignadas (Técnico) — filtra por tecnico_id
    Route::get('/conversiones/{ordenId}/evaluar', Evaluar::class)->name('conversiones.evaluar'); // P4: Evaluación (Técnico) — checklist + apto/no apto    
    Route::get('/conversiones/almacen/pendientes', AlmacenPendientes::class)->name('conversiones.almacen-pendientes'); // P5: Asignar equipos (Almacenero) — vincula items_serializados a la orden
    Route::get('/conversiones/{ordenId}/asignar-equipos', AsignarEquipos::class)->name('conversiones.asignar-equipos'); // P5: Asignar equipos (Almacenero) — vincula items_serializados a la orden  
    Route::get('/conversiones/{ordenId}/realizar', Realizar::class)->name('conversiones.realizar'); // P6: Realizar conversión (Técnico) — inicia, marca instalado, finaliza
    Route::get('/conversiones/entregas/pendientes', EntregaPendientes::class)->name('conversiones.entregas-pendientes'); // P7: Entrega y cobro (Cajero) — reutiliza la lógica de cobro que ya armamos en CrearSimple
    Route::get('/conversiones/{ordenId}/entregar', EntregarCobrar::class)->name('conversiones.entregar'); // P7: Entrega y cobro (Cajero) — reutiliza la lógica de cobro que ya armamos en CrearSimple

    
    Route::get('/selector', SelectorClienteVehiculo::class)->name('selector'); // component hijo reutilizable "buscar/crear cliente y vehículo" respecto a CrearSimple
    Route::get('/procesarcobro', ProcesarCobro::class)->name('procesar'); // component hijo reutilizable "entrega y cobro"
    

    // Rutas modulo de almacen
    Route::get('/almacen/categorias', CategoriasListado::class)->name('almacen.categorias.listado');
    //Route::get('/almacen/categorias/crear', CategoriasCrear::class)->name('almacen.categorias.crear'); componenente hijo anidado
    Route::get('/almacen/productos', ProductosListado::class)->name('almacen.productos.listado');
    //Route::get('/almacen/productos/crear', ProductosCrear::class)->name('almacen.productos.crear'); componenente hijo anidado
    //Route::get('/almacen/productos/{productoId}/entrada', RegistrarEntrada::class)->name('almacen.productos.entrada'); // componenente hijo anidado

    // Rutas modulo de recursos humanos
    Route::get('/rrhh/contratos', Contratos::class)->middleware('can:rrhh.contratos')->name('rrhh.contratos');
    Route::get('/rrhh/vacaciones/contrato/{idContrato}', GestionarVacaciones::class)->name('rrhh.vacaciones.index');
    Route::get('/rrhh/documentos/{id?}', GestionDocumentos::class)->name('rrhh.documentos');
    Route::get('/rrhh/planillas', ListaPlanilla::class)->middleware('can:rrhh.planillas')->name('rrhh.planillas');
    Route::get('/rrhh/mis-planillas', MisPlanillas::class)->name('rrhh.mis-planillas');

    //Rutas modulo de Usuarios y Roles
    Route::get('/Usuarios', Usuarios::class)->name('usuarios');
    Route::get('/Roles', AdminRoles::class)->name('usuarios.roles');
    Route::get('/Permisos', AdminPermisos::class)->name('usuarios.permisos');

    

    // PDF Routes
    Route::get('/garantia/pdf/{id}', [PdfController::class, 'generaPdfCartaGarantia'])->name('vehiculo.pdf');
    Route::get('/manual/pdf/{id}', [PdfController::class, 'generaPdfManual'])->name('manual.pdf');
    Route::get('/ordenRepuestos/pdf/{id}', [PdfController::class, 'generaPdfOrdenRepuestos'])->name('ordenRepuestos.pdf');
    Route::get('/evaluacion/pdf/{id}', [PdfController::class, 'generaPdfEvaluacion'])->name('expedientesEvaluacion.pdf');

    Route::get('/rrhh/contrato/{id}/pdf', [PdfController::class, 'generarContrato'])->name('rrhh.contrato.pdf');
    //Route::get('/rrhh/contrato/{id}/pdf', 'generarContrato')->name('rrhh.contrato.pdf');

    Route::get('/comprobantes/{ordenId}/pdf', [ComprobanteController::class, 'pdf'])->name('comprobantes.pdf');

    
    Route::get('/ordenes/{ordenId}/pdf/evaluacion', [DocumentosConversionController::class, 'evaluacion'])->name('conversiones.pdf.evaluacion');
    Route::get('/ordenes/{ordenId}/pdf/ficha-tecnica', [DocumentosConversionController::class, 'fichaTecnica'])->name('conversiones.pdf.ficha-tecnica');
    Route::get('/ordenes/{ordenId}/pdf/garantia', [DocumentosConversionController::class, 'garantia'])->name('conversiones.pdf.garantia');

    // Rutas modulo CMS
    Route::middleware('can:opciones.cms')->prefix('cms')->name('cms.')->group(function () {
        Route::get('/contenido', GestionarContenido::class)->name('contenido');
        Route::get('/servicios', GestionarServicios::class)->name('servicios');
        Route::get('/pasos', GestionarPasos::class)->name('pasos');
        Route::get('/porque', GestionarPorQue::class)->name('porque');
        Route::get('/contacto', GestionarContacto::class)->name('contacto');
        Route::get('/redes', GestionarRedes::class)->name('redes');
        Route::post('/upload-media', [CmsController::class, 'uploadMedia'])->name('upload-media');
    });


});
