<?php

namespace App\Livewire;

use App\Models\Conversion;
use App\Models\ConversionDetalle;
use App\Models\Repuesto;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class SolicitudRepuestos extends Component
{
    // Propiedad para recibir el ID de la conversión desde el componente padre
    public $conversionId;

    // Propiedades del formulario
    public $repuestos = [];
    public $repuesto_id = '';
    public $cantidad = 1;
    public $conversion;

    // Nueva propiedad para controlar la visibilidad de los botones
    public $showButtons = false;

    // Recibe el $conversionId del componente padre (ListaConversiones) a través de la URL.
    public function mount($conversionId)
    {
        $this->conversionId = $conversionId;
        // Carga la conversión y sus detalles existentes si los hay
        //$this->conversion = Conversion::with('conversionDetalles.repuesto')->find($conversionId);
        $this->conversion = Conversion::with('conversionDetalles.repuesto', 'expediente')->find($conversionId);

        // Si ya hay repuestos en la conversión, los cargamos en el formulario
        if ($this->conversion && $this->conversion->conversionDetalles->count() > 0) {
            foreach ($this->conversion->conversionDetalles as $detalle) {
                $this->repuestos[] = [
                    'repuesto_id' => $detalle->repuesto_id,
                    'nombre' => $detalle->repuesto->nombre,
                    'cantidad' => $detalle->cantidad_utilizada,
                ];
            }
        }
    }

    //Agrega un nuevo repuesto a la lista de la solicitud.
    public function addRepuesto()
    {
        // Validación básica
        $this->validate([
            'repuesto_id' => 'required|integer',
            'cantidad' => 'required|integer|min:1',
        ]);

        // Busca el repuesto en la base de datos para obtener su nombre
        $repuesto = Repuesto::find($this->repuesto_id);

        // Verifica si el repuesto ya está en la lista para actualizar la cantidad
        $existingRepuesto = collect($this->repuestos)->firstWhere('repuesto_id', $this->repuesto_id);

        if ($existingRepuesto) {
            // Si el repuesto existe, actualiza su cantidad
            $this->repuestos = collect($this->repuestos)->map(function ($item) use ($repuesto) {
                if ($item['repuesto_id'] === $this->repuesto_id) {
                    $item['cantidad'] += $this->cantidad;
                }
                return $item;
            })->toArray();
        } else {
            // Si no existe, lo agrega a la lista
            $this->repuestos[] = [
                'repuesto_id' => $this->repuesto_id,
                'nombre' => $repuesto->nombre,
                'cantidad' => $this->cantidad,
            ];
        }

        // Resetea los campos del formulario para el siguiente repuesto
        $this->reset(['repuesto_id', 'cantidad']);
    }
    //Elimina un repuesto de la lista.
    public function removeRepuesto($index)
    {
        unset($this->repuestos[$index]);
        $this->repuestos = array_values($this->repuestos);
    }

    //Guarda la solicitud de repuestos en la base de datos y actualiza estado de Expediente y.
    public function saveSolicitud()
    {
        DB::transaction(function () {
            // Obtener los detalles de conversión existentes para esta conversión
            $existingDetails = $this->conversion->conversionDetalles->keyBy('repuesto_id');

            // Recorrer la lista de repuestos del formulario
            foreach ($this->repuestos as $item) {
                $repuestoId = $item['repuesto_id'];
                $nuevaCantidad = $item['cantidad'];

                // Encontrar el detalle existente para este repuesto, si lo hay
                $existingDetail = $existingDetails->get($repuestoId);

                // Si el detalle ya existe, lo actualizamos
                if ($existingDetail) {
                    $cantidadAnterior = $existingDetail->cantidad_utilizada;
                    $diferencia = $nuevaCantidad - $cantidadAnterior;

                    // Actualizar la cantidad en el detalle de la conversión
                    $existingDetail->update(['cantidad_utilizada' => $nuevaCantidad]);

                    // Ajustar el stock del repuesto por la diferencia
                    $repuesto = Repuesto::find($repuestoId);
                    if ($repuesto) {
                        $repuesto->decrement('stock', $diferencia);
                    }

                    // Eliminar el detalle de la lista de existentes para saber cuáles son los que ya no están
                    $existingDetails->forget($repuestoId);
                } else {
                    // Si el detalle no existe, lo creamos
                    ConversionDetalle::create([
                        'conversion_id' => $this->conversionId,
                        'repuesto_id' => $repuestoId,
                        'cantidad_utilizada' => $nuevaCantidad,
                    ]);

                    // Decrementar el stock del repuesto por la cantidad total
                    $repuesto = Repuesto::find($repuestoId);
                    if ($repuesto) {
                        $repuesto->decrement('stock', $nuevaCantidad);
                    }
                }
            }

            // Después de recorrer los nuevos repuestos, los que quedan en $existingDetails
            // son los que fueron eliminados de la lista del formulario.
            foreach ($existingDetails as $detalleRemovido) {
                // Devolver el stock de los repuestos que fueron eliminados
                $repuesto = Repuesto::find($detalleRemovido->repuesto_id);
                if ($repuesto) {
                    $repuesto->increment('stock', $detalleRemovido->cantidad_utilizada);
                }

                // Eliminar el registro de la base de datos
                $detalleRemovido->delete();
            }

            // Validar que el expediente exista en la conversión
            if ($this->conversion && $this->conversion->expediente) {
                // 2. Actualizar el estado del expediente a 'en_conversion'
                $this->conversion->expediente->update([
                    'estado' => 'en_conversion'
                ]);
            }
        });

        // Habilitar la visibilidad de los botones después de guardar
        $this->showButtons = true;
        // Emitir el evento de alerta después de guardar.
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Repuestos agregados correctamente", icono: "success");
    }

    public function redirectToRegresar()
    {
        return redirect()->route('ListaConversiones');
    }
    // Nuevo método para abrir el PDF en una nueva pestaña
    public function openPdf()
    {
        // Obtenemos la URL del PDF usando el helper route() y el ID de la conversión
        $pdfUrl = route('ordenRepuestos.pdf', ['id' => $this->conversionId]);

        // Emitir un evento para que el frontend abra la URL en una nueva pestaña
        $this->dispatch('open-pdf', url: $pdfUrl);
    }

    // Renderiza la vista del componente.
    public function render()
    {
        $repuestosDisponibles = Repuesto::all();
        return view('livewire.solicitud-repuestos', compact('repuestosDisponibles'));
    }
}
