<?php

namespace App\Livewire\RRHH;

use Livewire\Component;
use App\Models\PlanillaArchivo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use setasign\Fpdi\Fpdi;

class FirmarBoleta extends Component
{
    public $abierto = false;
    public $archivoId;
    public $confirmacion = false;
    public $archivo;

    #[On('abrir-modal-firma')]
    public function abrirModal($id)
    {
        $this->reset(['confirmacion']);
        $this->archivoId = $id;
        $this->archivo = PlanillaArchivo::with('planilla')->find($id);
        $this->abierto = true;
    }

    public function procesarFirma()
    {
        $this->validate(['confirmacion' => 'accepted']);

        $user = Auth::user();
        if (!$user->ruta_firma || !Storage::disk('public')->exists($user->ruta_firma)) {
            $this->dispatch('minAlert', titulo: 'ERROR', mensaje: 'No tienes una firma registrada o el archivo no existe.', icono: 'error');
            return;
        }

        try {
            // 1. Rutas de archivos
            $rutaOriginal = Storage::disk('public')->path($this->archivo->ruta);
            $extension = $this->archivo->extension;
            $timestamp = now()->format('Ymd_His');

            // Construir nombre similar al original pero con sufijo "firmado"
            $nombreFirmado = "P{$this->archivo->planilla_id}_{$timestamp}_firmado.{$extension}";
            $rutaDestinoRelativa = 'planillas/' . $nombreFirmado;
            $rutaDestinoAbsoluta = Storage::disk('public')->path($rutaDestinoRelativa);

            // 2. Iniciar Proceso de Estampado con FPDI
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($rutaOriginal);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $pdf->AddPage();
                $pdf->useTemplate($templateId);

                if ($pageNo === 1) {
                    $rutaFirmaAbsoluta = Storage::disk('public')->path($user->ruta_firma);

                    // Definimos la posición base para poder reutilizarla
                    $posX = 25;
                    $posY = 230; // Posición Y de la imagen
                    $anchoFirma = 45;
                    $altoFirma = 25;

                    // 1. Estampamos la Imagen
                    $pdf->Image($rutaFirmaAbsoluta, $posX, $posY, $anchoFirma, $altoFirma);

                    // 2. Configuramos el texto justo debajo
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->SetTextColor(100, 100, 100); // Opcional: un gris oscuro para que sea sutil

                    // Seteamos la posición:
                    // X = la misma de la firma
                    // Y = posición de la firma + su altura + 2mm de margen
                    $pdf->SetXY($posX, $posY + $altoFirma + 2);

                    $pdf->Cell($anchoFirma, 0, 'Firmado digitalmente el: ' . now()->format('d/m/Y H:i'), 0, 0, 'C');
                }
            }

            // 3. Guardar el archivo físicamente
            $pdf->Output($rutaDestinoAbsoluta, 'F');

            // 4. Crear nuevo registro en PlanillaArchivo (similar a tu función save)
            PlanillaArchivo::create([
                'planilla_id' => $this->archivo->planilla_id,
                'tipo'        => 'boleta_firmada', // Tipo distinto para diferenciar
                'nombre'      => $this->archivo->nombre . ' (Firmado)',
                'ruta'        => $rutaDestinoRelativa,
                'extension'   => $extension,
            ]);

            $this->abierto = false;
            $this->dispatch('refresh-planilla');
            $this->dispatch('minAlert', titulo: 'ÉXITO', mensaje: 'Boleta firmada y guardada correctamente.', icono: 'success');

        } catch (\Exception $e) {
            $this->dispatch('minAlert', titulo: 'ERROR', mensaje: 'Error al procesar PDF: ' . $e->getMessage(), icono: 'error');
        }
    }

    public function render()
    {
        return view('livewire.r-r-h-h.firmar-boleta');
    }
}
