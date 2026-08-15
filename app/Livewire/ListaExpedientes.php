<?php

namespace App\Livewire;

use App\Models\Conversion;
use App\Models\DocumentosExpediente;
use App\Models\Evaluacion;
use App\Models\Expediente;
use App\Models\TiposDocumento;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListaExpedientes extends Component
{
    use WithFileUploads, WithPagination;

    public $sort;

    public $order;

    public $cant;

    public $search;

    public $direction;

    public $es;

    // Para el modal
    public $open = false;

    public $expedienteSeleccionado;

    // Documentos existentes del expediente
    public $files = [];

    // Carga nueva (múltiples archivos)
    public $documentoNuevo = [];

    // Técnico asignado
    public $tecnico_id = '';

    // Carga el usuario autenticado
    public $user;

    // Catálogo de tipos
    public $tiposDocumentos;

    public $tecnicos;

    // Variables para modal evaluacion
    public $openevaluar = false;

    public $instalacion;

    public $cambio_tanque;

    public $revision;

    public $certificacion;

    public $servicio;

    public $inyectado;

    public $carburado;

    public $monopunto;

    public $motor_tipo;

    public $cil3;

    public $kilometraje;

    // Variables para detalles evaluacion
    // Declaramos una propiedad para todos los detalles
    public $detalles = [];

    // Nuevas propiedades para crear resultado de la evaluación
    public $resultado;

    public $observaciones;

    protected $rules = [
        'tecnico_id' => 'nullable|exists:users,id',
        // Valida que cada sub-array de 'documentoNuevo' no esté vacío.
        'documentoNuevo.*' => 'nullable|array',
        // Valida cada archivo individualmente dentro de cada sub-array.
        'documentoNuevo.*.*' => 'file|max:2048|mimes:jpg,jpeg,png,gif,bmp,tif,tiff,pdf',
    ];

    public function mount()
    {
        $this->direction = 'desc';
        $this->sort = 'id';
        $this->cant = 10;
        $this->tiposDocumentos = TiposDocumento::all();
        $this->tecnicos = User::role(['Tecnico'])->orderBy('name')->get();
        $this->user = Auth::user();
        // dd($this->user);
    }

    public function order($sort)
    {
        if ($this->sort === $sort) {
            $this->direction = $this->direction === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
    }

    // Pasa datos al modal
    public function verExpediente($id)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        // $this->reset(['documentoNuevo', 'tipo_documento_id']);
        $this->reset(['documentoNuevo']);

        $this->expedienteSeleccionado = Expediente::with(['cliente', 'vehiculo', 'cita.asesor', 'documentos.tipoDocumento'])
            ->findOrFail($id);

        $this->files = $this->expedienteSeleccionado->documentos; // documentos existentes
        $this->tecnico_id = $this->expedienteSeleccionado->tecnico_id;
        $this->open = true;
    }

    // Asignar tecnico y subir documentos
    public function subirDocumento()
    {
        if (! $this->expedienteSeleccionado) {
            return;
        }

        // 1. Asignar técnico
        $this->expedienteSeleccionado->update([
            'tecnico_id' => $this->tecnico_id,
        ]);

        // 2. Subir documentos solo si hay nuevos archivos
        if (! empty($this->documentoNuevo)) {
            $this->validate();

            // Creamos la nueva ruta de la carpeta con el ID y la placa
            $folderName = $this->expedienteSeleccionado->id.'-'.$this->expedienteSeleccionado->vehiculo->placa;
            $basePath = 'expedientes/'.$folderName;

            foreach ($this->documentoNuevo as $tipoDocumentoId => $archivos) {
                // Iterar sobre cada archivo del tipo de documento actual
                foreach ($archivos as $archivo) {
                    // $path = $archivo->store('expedientes/' . $this->expedienteSeleccionado->id, 'public');
                    // Usamos la nueva ruta de la carpeta para guardar el archivo
                    $path = $archivo->store($basePath, 'public');

                    DocumentosExpediente::create([
                        'expediente_id' => $this->expedienteSeleccionado->id,
                        'tipo_documento_id' => $tipoDocumentoId, // Usamos la clave del array
                        'nombre' => $archivo->getClientOriginalName(),
                        'ruta' => '/storage/'.$path,
                        'extension' => $archivo->getClientOriginalExtension(),
                    ]);
                }
            }
        }

        // 3. Limpiar estado y refrescar vista
        $this->expedienteSeleccionado->load('documentos.tipoDocumento');
        $this->files = $this->expedienteSeleccionado->documentos;
        $this->reset('documentoNuevo'); // Limpiar solo el array de subidas nuevas
        $this->resetErrorBag();
        $this->resetValidation();
        $this->open = false;

        $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'Se guardaron los cambios correctamente', icono: 'success');
    }

    // Eliminar archivo
    public function deleteFileUpload($tipo_id, $key)
    {
        if (isset($this->documentoNuevo[$tipo_id][$key])) {
            unset($this->documentoNuevo[$tipo_id][$key]);
            // Si el sub-array queda vacío, lo eliminamos para evitar problemas
            if (empty($this->documentoNuevo[$tipo_id])) {
                unset($this->documentoNuevo[$tipo_id]);
            }
        }
    }

    public function verEvaluacion($id)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset(['resultado', 'observaciones', 'detalles']);

        $this->expedienteSeleccionado = Expediente::with(['cliente', 'vehiculo', 'cita.asesor', 'evaluaciones.detalles'])->findOrFail($id);

        if ($this->expedienteSeleccionado) {
            /*
                // Cargar datos de cliente y vehiculo
                $this->cliente = $this->expedienteSeleccionado->cliente->nombre . ' ' . $this->expedienteSeleccionado->cliente->apellido;
                $this->dni = $this->expedienteSeleccionado->cliente->documento;
                $this->telefono_fijo = $this->expedienteSeleccionado->cliente->telefono;
                $this->telefono_movil = $this->expedienteSeleccionado->cliente->telefono;
                $this->placa_actual = $this->expedienteSeleccionado->vehiculo->placa;
                $this->placa_anterior = $this->expedienteSeleccionado->vehiculo->placa_anterior;
                $this->marca = $this->expedienteSeleccionado->vehiculo->marca;
                $this->modelo = $this->expedienteSeleccionado->vehiculo->modelo;
                $this->motor = $this->expedienteSeleccionado->vehiculo->serie;
                $this->color = $this->expedienteSeleccionado->vehiculo->color;
                $this->anio = $this->expedienteSeleccionado->vehiculo->anio;
                $this->combustible = $this->expedienteSeleccionado->vehiculo->combustible;
            */
            // Cargar datos de evaluacion, verificando si existe
            if ($this->expedienteSeleccionado->evaluaciones->isNotEmpty()) {
                $evaluacion = $this->expedienteSeleccionado->evaluaciones->first(); // O usar ->last() si hay varias
                if ($evaluacion) {
                    $this->resultado = $evaluacion->resultado;
                    $this->observaciones = $evaluacion->observaciones;
                    // Si la evaluación tiene detalles, los cargamos
                    if ($evaluacion->detalles) {
                        $this->detalles = $evaluacion->detalles->toArray();
                    }
                }
            }
        }

        $this->openevaluar = true;
    }

    public function guardarEvaluacion()
    {
        $this->validate([
            'resultado' => 'required|in:apto,no apto',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Verifica si ya existe una evaluación para este expediente
        /*$evaluacion = Evaluacion::where('expediente_id', $this->expedienteSeleccionado->id)->first();
        if ($evaluacion) {
            // Actualiza la evaluación existente
            $evaluacion->update([
                'resultado' => $this->resultado,
                'observaciones' => $this->observaciones,
            ]);
            $mensaje = "Evaluación actualizada correctamente";
        } else {
            // Crea un nuevo registro de evaluación
            Evaluacion::create([
                'expediente_id' => $this->expedienteSeleccionado->id,
                'tecnico_id' => $this->user->id,
                'fecha_evaluacion' => now(),
                'resultado' => $this->resultado,
                'observaciones' => $this->observaciones,
            ]);
            $mensaje = "Evaluación registrada correctamente";
        }*/
        $evaluacion = Evaluacion::updateOrCreate(
            ['expediente_id' => $this->expedienteSeleccionado->id],
            [
                'tecnico_id' => $this->user->id,
                'fecha_evaluacion' => now(),
                'resultado' => $this->resultado,
                'observaciones' => $this->observaciones,
            ]
        );

        // Actualizamos o creamos los detalles de la evaluación
        $evaluacion->detalles()->updateOrCreate(
            ['evaluacion_id' => $evaluacion->id],
            $this->detalles
        );

        // Se actualiza el estado del expediente basándose en el resultado
        if ($this->resultado === 'apto') {
            $this->expedienteSeleccionado->update(['estado' => 'aprobado_conversion']);
            // Usamos updateOrCreate para asegurar que solo se cree un registro de Conversion por expediente
            Conversion::updateOrCreate(
                ['expediente_id' => $this->expedienteSeleccionado->id],
                [
                    'tecnico_id' => $this->user->id,
                    'fecha_inicio' => now(), // donde agregamos la fecha de incio, en el modal ? o despues se actualiza ?
                    'estado' => 'en_proceso',
                    'observaciones' => $this->observaciones,
                ]
            );
        } else {
            $this->expedienteSeleccionado->update(['estado' => 'evaluacion_rechazada']);
        }

        // Se reinician las propiedades del modal
        $this->reset(['openevaluar', 'resultado', 'observaciones', 'detalles']);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->openevaluar = false;

        // Se cierra el modal y se muestra una notificación de éxito
        $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'Evaluación guardada correctamente', icono: 'success');
    }

    // Muestra los expedientes con filtros y orden
    public function render()
    {
        $expedientes = Expediente::with(['cliente', 'vehiculo', 'cita'])
            ->buscar($this->search)
            ->estado($this->es)
            ->when($this->user->hasRole('Tecnico'), function ($q) {
                $q->where('tecnico_id', $this->user->id);
            })
            ->ordenar($this->sort, $this->direction)
            ->paginate($this->cant);

        return view('livewire.lista-expedientes', compact('expedientes'));
    }
}

/*public function subirDocumento()
    {
        if (!$this->expedienteSeleccionado) {
            return;
        }

        // Asignar técnico aunque no se suban archivos
        $this->expedienteSeleccionado->update([
            'tecnico_id' => $this->tecnico_id,
        ]);

        // Subida de documentos SOLO si hay archivos
        if (!empty($this->documentoNuevo)) {
            $this->validate([
                'tipo_documento_id'     => 'required|exists:tipos_documento,id',
                'documentoNuevo'        => 'required|array|min:1',
                'documentoNuevo.*'      => 'file|max:2048|mimes:jpg,jpeg,png,gif,bmp,tif,tiff',
            ]);

            // Guardar cada archivo
            foreach ($this->documentoNuevo as $archivo) {
                // Sugerencia: guardar en subcarpeta por expediente
                $path = $archivo->store('expedientes/' . $this->expedienteSeleccionado->id, 'public');

                DocumentosExpediente::create([
                    'expediente_id'     => $this->expedienteSeleccionado->id,
                    'tipo_documento_id' => $this->tipo_documento_id,
                    'nombre'            => $archivo->getClientOriginalName(),
                    'ruta'              => '/storage/' . $path,
                    'extension'         => $archivo->getClientOriginalExtension(),
                ]);
            }

            // Refrescar vista
            $this->expedienteSeleccionado->load('documentos.tipoDocumento');
            $this->files = $this->expedienteSeleccionado->documentos;

            // Limpiar inputs
            $this->reset(['documentoNuevo', 'tipo_documento_id']);
        }

        $this->resetErrorBag();
        $this->resetValidation();
        $this->open = false;
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Se guardaron los cambios correctamente", icono: "success");
    }
*/

// Eliminar archivo de la BD y del disco
/*public function deleteFile(int $id)
    {
        if (!$this->expedienteSeleccionado) return;

        $doc = DocumentosExpediente::where('id', $id)
            ->where('expediente_id', $this->expedienteSeleccionado->id)
            ->first();

        if (!$doc) return;

        // Eliminar del disco
        $relative = Str::after($doc->ruta, '/storage/');
        Storage::disk('public')->delete($relative);

        // Eliminar DB
        $doc->delete();

        // Refrescar lista
        $this->expedienteSeleccionado->load('documentos.tipoDocumento');
        $this->files = $this->expedienteSeleccionado->documentos;
    }
*/

/*public function deleteFileUpload($key)
    {
        if (isset($this->documentoNuevo[$key])) {
            unset($this->documentoNuevo[$key]);
            $this->documentoNuevo = array_values($this->documentoNuevo); // reindexar
        }
    }
*/
