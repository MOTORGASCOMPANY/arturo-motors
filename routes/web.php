<?php

use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\PdfController;
use App\Livewire\AdminPermisos;
use App\Livewire\AdminRoles;
use App\Livewire\Caja\AbrirCaja;
use App\Livewire\Caja\CerrarCaja;
use App\Livewire\Caja\DetalleSesion;
use App\Livewire\Caja\HistorialSesiones;
use App\Livewire\Caja\RegistrarEgreso;
use App\Livewire\Conversiones\AsignarTecnico;
use App\Livewire\Conversiones\Crear;
use App\Livewire\Conversiones\Evaluar;
use App\Livewire\Conversiones\MisAsignadas;
use App\Livewire\CrearCitas;
use App\Livewire\ExpedienteModal;
use App\Livewire\GestorRepuestos;
use App\Livewire\Inicio;
use App\Livewire\ListaCitas;
use App\Livewire\ListaClientes;
use App\Livewire\ListaConversiones;
use App\Livewire\ListaExpedientes;
use App\Livewire\ListaVehiculos;
use App\Livewire\Reportes\ReporteCitas;
use App\Livewire\RRHH\Contratos;
use App\Livewire\RRHH\GestionarVacaciones;
use App\Livewire\RRHH\GestionDocumentos;
use App\Livewire\RRHH\ListaPlanilla;
use App\Livewire\RRHH\MisPlanillas;
use App\Livewire\ServiceOrders\CrearSimple;
use App\Livewire\ServiceOrders\Listado;
use App\Livewire\SolicitudRepuestos;
use App\Livewire\Usuarios;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return view('welcome');
});*/

/*Route::get('/', function () {
    return redirect()->to('/login');
});*/

Route::get('/', function () {
    return view('index');
});

Route::get('index', function () {
    return view('index');
});

Route::get('phpmyinfo', function () {
    phpinfo();
})->name('phpmyinfo');

RateLimiter::for('livewire', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip()); // Máx 10 solicitudes por minuto por IP
});

/*Route::middleware('throttle:livewire')->group(function () {
    Route::post('/livewire/message/{component}', '\Livewire\Controllers\HttpConnectionHandler');
});*/

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])
    ->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

     Route::get('/inicio', Inicio::class)->name('inicio');

     // Citas
     Route::get('/lista-citas', ListaCitas::class)->name('ListaCitas');
     Route::get('/crear-cita', CrearCitas::class)->name('CrearCita');

    // Expedientes
    Route::get('/lista-expedientes', ListaExpedientes::class)->name('ListaExpedientes');
    //Route::get('/evaluacion', ExpedienteModal::class)->name('evaluacion');

    // Conversiones
    Route::get('/lista-conversiones', ListaConversiones::class)->name('ListaConversiones');

    // Almacen
    Route::get('/gestor-repuestos', GestorRepuestos::class)->name('Repuestos');
    Route::get('/solicitud-repuestos/{conversionId}', SolicitudRepuestos::class)->name('SolicitudRepuestos');

    // Reportes
    Route::get('/rpta-citas', ReporteCitas::class)->name('Rpta.Citas');

    // Vehículos
    Route::get('/lista-vehiculos', ListaVehiculos::class)->name('ListaVehiculos');

    // Clientes
    Route::get('/lista-clientes', ListaClientes::class)->name('ListaClientes');

    // Rutas de servicios
    Route::get('/ordenes/simple/crear', CrearSimple::class)->name('ordenes.simple.crear');
    

    Route::get('/ordenes', Listado::class)->name('ordenes.listado');

    Route::get('/conversiones/crear', Crear::class)->name('conversiones.crear'); // P1: Crear orden de conversión (Vendedor)
    Route::get('/conversiones/asignar', AsignarTecnico::class)->name('conversiones.asignar'); // P2: Asignar técnico (Jefe de taller) — ve todas las órdenes creada
    Route::get('/conversiones/mis-asignadas', MisAsignadas::class)->name('conversiones.mis-asignadas'); // P3: Mis conversiones asignadas (Técnico) — filtra por tecnico_id
    Route::get('/conversiones/{ordenId}/evaluar', Evaluar::class)->name('conversiones.evaluar'); // P4: Evaluación (Técnico) — checklist + apto/no apto
    // P5: Asignar equipos (Almacenero) — vincula items_serializados a la orden
    // P6: Realizar conversión (Técnico) — inicia, marca instalado, finaliza
    // P7: Entrega y cobro (Cajero) — reutiliza la lógica de cobro que ya armamos en CrearSimple
    // PLUS: componente hijo reutilizable <livewire:selector-cliente-vehiculo>  "buscar/crear cliente y vehículo" respecto a CrearSimple

    // Rutas modulo de caja
    Route::get('/caja/abrir', AbrirCaja::class)->name('caja.abrir');
    Route::get('/caja/egreso', RegistrarEgreso::class)->name('caja.egreso');
    Route::get('/caja/cerrar', CerrarCaja::class)->name('caja.cerrar');

    Route::get('/caja/historial', HistorialSesiones::class)->name('caja.historial');
    Route::get('/caja/sesion/{sesionId}', DetalleSesion::class)->name('caja.sesion');

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


});
