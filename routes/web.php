<?php

use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PdfController;
use App\Livewire\AdminPermisos;
use App\Livewire\AdminRoles;
use App\Livewire\Almacen\Categorias\Listado as CategoriasListado;
use App\Livewire\Almacen\Productos\Listado as ProductosListado;
use App\Livewire\Caja\AbrirCaja;
use App\Livewire\Caja\CerrarCaja;
use App\Livewire\Caja\DetalleSesion;
use App\Livewire\Caja\HistorialSesiones;
use App\Livewire\Caja\RegistrarEgreso;
use App\Livewire\Cms\GestionarApariencia;
use App\Livewire\Cms\GestionarContacto;
use App\Livewire\Cms\GestionarContenido;
use App\Livewire\Cms\GestionarLogo;
use App\Livewire\Cms\GestionarPasos;
use App\Livewire\Cms\GestionarPorQue;
use App\Livewire\Cms\GestionarRedes;
use App\Livewire\Cms\GestionarServicios;
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
use App\Livewire\RRHH\GestionarVacaciones;
use App\Livewire\RRHH\GestionDocumentos;
use App\Livewire\RRHH\ListaPlanilla;
use App\Livewire\RRHH\MisPlanillas;
use App\Livewire\SelectorClienteVehiculo;
use App\Livewire\ServiceOrders\CrearSimple;
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
        Route::get('/crear-cita', CrearCitas::class)->name('CrearCita');

        // Expedientes
        Route::get('/lista-expedientes', ListaExpedientes::class)->name('ListaExpedientes');
        // Route::get('/evaluacion', ExpedienteModal::class)->name('evaluacion');

        // Reportes
        Route::get('/rpta-citas', ReporteCitas::class)->name('Rpta.Citas');

        // Vehículos
        Route::get('/lista-vehiculos', ListaVehiculos::class)->name('ListaVehiculos');

        // Clientes
        Route::get('/lista-clientes', ListaClientes::class)->name('ListaClientes');

        // Servicios
        Route::get('/lista-servicios', ListaServicios::class)->name('ListaServicios');

        // Rutas modulo de caja
        Route::get('/caja/abrir', AbrirCaja::class)->name('caja.abrir');
        Route::get('/caja/egreso', RegistrarEgreso::class)->name('caja.egreso');
        Route::get('/caja/cerrar', CerrarCaja::class)->name('caja.cerrar');
        Route::get('/caja/historial', HistorialSesiones::class)->name('caja.historial');
        Route::get('/caja/sesion/{sesionId}', DetalleSesion::class)->name('caja.sesion');

        // Rutas de servicios
        // Route::get('/ordenes', Listado::class)->name('ordenes.listado'); // TODO: crear App\Livewire\ServiceOrders\Listado antes de reactivar
        Route::get('/ordenes/simple/crear', CrearSimple::class)->name('ordenes.simple.crear');
        Route::get('/conversiones/crear', Crear::class)->name('conversiones.crear');

        // Rutas modulo de conversiones
        Route::get('/conversiones/asignar', AsignarTecnico::class)->name('conversiones.asignar');
        Route::get('/conversiones/mis-asignadas', MisAsignadas::class)->name('conversiones.mis-asignadas');
        Route::get('/conversiones/{ordenId}/evaluar', Evaluar::class)->name('conversiones.evaluar');
        Route::get('/conversiones/almacen/pendientes', AlmacenPendientes::class)->name('conversiones.almacen-pendientes');
        Route::get('/conversiones/{ordenId}/asignar-equipos', AsignarEquipos::class)->name('conversiones.asignar-equipos');
        Route::get('/conversiones/{ordenId}/realizar', Realizar::class)->name('conversiones.realizar');
        Route::get('/conversiones/entregas/pendientes', EntregaPendientes::class)->name('conversiones.entregas-pendientes');
        Route::get('/conversiones/{ordenId}/entregar', EntregarCobrar::class)->name('conversiones.entregar');

        Route::get('/selector', SelectorClienteVehiculo::class)->name('selector');
        Route::get('/procesarcobro', ProcesarCobro::class)->name('procesar');

        // Rutas modulo de almacen
        Route::get('/almacen/categorias', CategoriasListado::class)->name('almacen.categorias.listado');
        Route::get('/almacen/productos', ProductosListado::class)->name('almacen.productos.listado');

        // Rutas modulo de recursos humanos
        Route::get('/rrhh/contratos', Contratos::class)->middleware('can:rrhh.contratos')->name('rrhh.contratos');
        Route::get('/rrhh/vacaciones/contrato/{idContrato}', GestionarVacaciones::class)->name('rrhh.vacaciones.index');
        Route::get('/rrhh/documentos/{id?}', GestionDocumentos::class)->name('rrhh.documentos');
        Route::get('/rrhh/planillas', ListaPlanilla::class)->middleware('can:rrhh.planillas')->name('rrhh.planillas');
        Route::get('/rrhh/mis-planillas', MisPlanillas::class)->name('rrhh.mis-planillas');

        // Rutas modulo de Usuarios y Roles
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
        Route::get('/admin/apariencia', GestionarApariencia::class)->name('cms.apariencia');
        Route::get('/admin/logo', GestionarLogo::class)->name('cms.logo');

        // CMS Upload (bypasses Livewire to avoid iframe re-render)
        Route::post('/admin/cms/upload', function (Request $request) {
            $request->validate([
                'file' => 'required|image|max:5120',
                'section_id' => 'required|integer',
            ]);

            $limits = ['hero' => 5, 'about' => 2];
            $section = PageSection::findOrFail($request->section_id);

            if (! isset($limits[$section->key])) {
                return response()->json(['error' => 'Esta sección no admite imágenes'], 422);
            }

            $currentCount = PageMedia::where('page_section_id', $section->id)->count();
            if ($currentCount >= $limits[$section->key]) {
                return response()->json(['error' => 'Límite de '.$limits[$section->key].' imágenes alcanzado'], 422);
            }

            $path = $request->file->store('cms', 'public');

            // Auto-optimize: convert to WebP + generate responsive sizes
            $meta = [];
            try {
                $optimizationService = app(\App\Services\ImageOptimizationService::class);
                $optimized = $optimizationService->optimizeAndConvert($path);
                $meta = [
                    'webp_path' => $optimized['webp'],
                    'responsive_paths' => $optimized['responsive'],
                ];
            } catch (\Throwable $e) {
                // Upload succeeds even if optimization fails
            }

            $media = Media::create([
                'name' => $request->file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => 'image',
                'mime_type' => $request->file->getMimeType(),
                'file_size' => $request->file->getSize(),
                'meta' => $meta,
            ]);

            PageMedia::create([
                'page_section_id' => $section->id,
                'media_id' => $media->id,
                'usage' => 'gallery',
                'sort_order' => $currentCount,
            ]);

            return response()->json(['success' => true, 'path' => $path]);
        })->name('cms.upload-media');

        // PDF Routes
        Route::get('/garantia/pdf/{id}', [PdfController::class, 'generaPdfCartaGarantia'])->name('vehiculo.pdf');
        Route::get('/manual/pdf/{id}', [PdfController::class, 'generaPdfManual'])->name('manual.pdf');
        Route::get('/ordenRepuestos/pdf/{id}', [PdfController::class, 'generaPdfOrdenRepuestos'])->name('ordenRepuestos.pdf');
        Route::get('/evaluacion/pdf/{id}', [PdfController::class, 'generaPdfEvaluacion'])->name('expedientesEvaluacion.pdf');

        Route::get('/rrhh/contrato/{id}/pdf', [PdfController::class, 'generarContrato'])->name('rrhh.contrato.pdf');
        // Route::get('/rrhh/contrato/{id}/pdf', 'generarContrato')->name('rrhh.contrato.pdf');

        Route::get('/comprobantes/{ordenId}/pdf', [ComprobanteController::class, 'pdf'])->name('comprobantes.pdf');

    });