<?php

use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\PdfController;
use App\Livewire\AdminPermisos;
use App\Livewire\AdminRoles;
use App\Livewire\CrearCitas;
use App\Livewire\ExpedienteModal;
use App\Livewire\GestorRepuestos;
use App\Livewire\Inicio;
use App\Livewire\ListaCitas;
use App\Livewire\ListaClientes;
use App\Livewire\ListaConversiones;
use App\Livewire\ListaExpedientes;
use App\Livewire\ListaVehiculos;
use App\Livewire\ListaServicios;
use App\Livewire\Reportes\ReporteCitas;
use App\Livewire\RRHH\Contratos;
use App\Livewire\RRHH\GestionarVacaciones;
use App\Livewire\RRHH\GestionDocumentos;
use App\Livewire\RRHH\ListaPlanilla;
use App\Livewire\RRHH\MisPlanillas;
use App\Livewire\ServiceOrders\CrearSimple;
use App\Livewire\SolicitudRepuestos;
use App\Livewire\Usuarios;
use App\Livewire\Cms\GestionarContenido;
use App\Livewire\Cms\GestionarServicios;
use App\Livewire\Cms\GestionarPasos;
use App\Livewire\Cms\GestionarContacto;
use App\Livewire\Cms\GestionarRedes;
use App\Livewire\Cms\GestionarPorQue;
use App\Http\Controllers\LandingController;
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

    // Servicios
    Route::get('/lista-servicios', ListaServicios::class)->name('ListaServicios');

    // Rutas de servicios
    Route::get('/ordenes/simple/crear', CrearSimple::class)->name('ordenes.simple.crear');

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

    // CMS Landing Page
    Route::get('/admin/contenido', GestionarContenido::class)->name('cms.contenido');
    Route::get('/admin/servicios', GestionarServicios::class)->name('cms.servicios');
    Route::get('/admin/pasos', GestionarPasos::class)->name('cms.pasos');
    Route::get('/admin/contacto', GestionarContacto::class)->name('cms.contacto');
    Route::get('/admin/redes', GestionarRedes::class)->name('cms.redes');
    Route::get('/admin/por-que', GestionarPorQue::class)->name('cms.porque');

    

    // PDF Routes
    Route::get('/garantia/pdf/{id}', [PdfController::class, 'generaPdfCartaGarantia'])->name('vehiculo.pdf');
    Route::get('/manual/pdf/{id}', [PdfController::class, 'generaPdfManual'])->name('manual.pdf');
    Route::get('/ordenRepuestos/pdf/{id}', [PdfController::class, 'generaPdfOrdenRepuestos'])->name('ordenRepuestos.pdf');
    Route::get('/evaluacion/pdf/{id}', [PdfController::class, 'generaPdfEvaluacion'])->name('expedientesEvaluacion.pdf');

    Route::get('/rrhh/contrato/{id}/pdf', [PdfController::class, 'generarContrato'])->name('rrhh.contrato.pdf');
    //Route::get('/rrhh/contrato/{id}/pdf', 'generarContrato')->name('rrhh.contrato.pdf');

    Route::get('/comprobantes/{ordenId}/pdf', [ComprobanteController::class, 'pdf'])->name('comprobantes.pdf');


});
