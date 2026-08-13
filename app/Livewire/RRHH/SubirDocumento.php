<?php

namespace App\Livewire\RRHH;

use App\Http\Controllers\PdfController;
use App\Models\DocumentoUsuario;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use setasign\Fpdi\Fpdi;

class SubirDocumento extends Component
{
    use WithFileUploads;

    public $mostrarModal = false;

    public $userId;

    public $tipoId;

    public $nombreTipo;

    // Campos del formulario
    public $archivo;

    public $fecha_emision;

    public $fecha_expiracion;

    // Añadir esta propiedad
    public $autorizaFirma = false;

    #[On('abrir-modal-subir')]
    public function abrirModal($tipoId = null, $nombreTipo = null, $userId = null)
    {
        // pero recibirán los valores del dispatch.
        $this->reset(['archivo', 'fecha_emision', 'fecha_expiracion']);

        $this->tipoId = $tipoId;
        $this->nombreTipo = $nombreTipo;
        $this->userId = $userId;
        $this->mostrarModal = true;
    }

    public function guardar()
    {
        // LÓGICA A: CONTRATO FIRMADO DIGITALMENTE
        if ($this->nombreTipo === 'Contrato Firmado') {
            $this->validate(['autorizaFirma' => 'accepted'], ['autorizaFirma.accepted' => 'Debes autorizar el estampado de tu firma.']);

            return $this->procesarFirmaDigital();
        }

        // LÓGICA B: CARGA DE ARCHIVO TRADICIONAL
        $this->validate([
            'archivo' => 'required|mimes:pdf,jpg,png|max:2048', // Max 2MB
            'fecha_emision' => 'nullable|date',
            'fecha_expiracion' => 'nullable|date',
        ]);

        // Guardar físicamente
        $ruta = $this->archivo->store('legajosUsuarios/'.$this->userId, 'public');

        // Guardar en BD
        DocumentoUsuario::updateOrCreate(
            ['user_id' => $this->userId, 'tipo_documento_id' => $this->tipoId],
            [
                'nombre' => $this->archivo->getClientOriginalName(),
                'ruta' => $ruta,
                'extension' => $this->archivo->getClientOriginalExtension(),
                'fecha_emision' => $this->fecha_emision,
                'fecha_expiracion' => $this->fecha_expiracion,
                'estado' => 'Pendiente',
            ]
        );

        $this->finalizarProceso('Documento cargado correctamente.');
    }

    private function procesarFirmaDigital()
    {
        $usuario = User::find($this->userId);

        if (! $usuario || ! $usuario->ruta_firma) {
            $this->dispatch('minAlert', titulo: 'SIN FIRMA', mensaje: 'No tienes una firma registrada en tu perfil.', icono: 'error');

            return;
        }

        $pdfController = app(PdfController::class);
        $pdfBaseContenido = $pdfController->generarContrato($this->userId, true);

        // Instanciamos FPDI (Intelephense puede marcar rojo, pero funcionará)
        /** @var mixed $pdf */ // Al declararlo como mixed, Intelephense dejará de validar si los métodos existen
        $pdf = new Fpdi;

        $tmpFile = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tmpFile, $pdfBaseContenido);

        try {
            $pageCount = $pdf->setSourceFile($tmpFile);

            for ($i = 1; $i <= $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i);
                $pdf->addPage();
                $pdf->useTemplate($tplIdx, 0, 0, null, null, true);

                if ($i === $pageCount) {
                    $rutaFirmaFisica = storage_path('app/public/'.$usuario->ruta_firma);

                    if (file_exists($rutaFirmaFisica)) {
                        // 1. Estampamos la imagen de la firma
                        $posX = 130;
                        $posY = 205;
                        $anchoFirma = 45;
                        $pdf->Image($rutaFirmaFisica, $posX, $posY, $anchoFirma, 0);

                        // 2. Agregamos el texto de firma digital debajo
                        // Definimos una fuente pequeña y un color gris para que sea discreto
                        $pdf->SetFont('Arial', 'I', 7); // 'I' para itálica, tamaño 7
                        $pdf->SetTextColor(100, 100, 100); // Color gris

                        // Posicionamos el texto unos milímetros debajo de la firma
                        // Si la imagen mide aprox 15-20mm de alto, sumamos eso a posY
                        $pdf->SetXY($posX - 5, $posY + 22);

                        $textoFirma = 'Firmado digitalmente por: '.mb_strtoupper($usuario->name);
                        $pdf->Cell($anchoFirma + 10, 5, utf8_decode($textoFirma), 0, 0, 'C');
                    }
                }
            }

            $pdfFinalContenido = $pdf->Output('S');
        } catch (\Exception $e) {
            $this->dispatch('minAlert', titulo: 'ERROR', mensaje: 'Error al procesar el PDF: '.$e->getMessage(), icono: 'error');

            return;
        } finally {
            if (file_exists($tmpFile)) {
                unlink($tmpFile);
            }
        }

        $nombreArchivo = 'contrato_firmado_'.time().'.pdf';
        $rutaDestino = 'legajosUsuarios/'.$this->userId.'/'.$nombreArchivo;

        Storage::disk('public')->put($rutaDestino, $pdfFinalContenido);

        DocumentoUsuario::updateOrCreate(
            ['user_id' => $this->userId, 'tipo_documento_id' => $this->tipoId],
            [
                'nombre' => $nombreArchivo,
                'ruta' => $rutaDestino,
                'extension' => 'pdf',
                'estado' => 'Aprobado',
                'fecha_emision' => now(),
            ]
        );

        $this->finalizarProceso('Contrato firmado y guardado correctamente.');
    }

    private function finalizarProceso($mensaje)
    {
        $this->mostrarModal = false;
        $this->dispatch('refresh-legajo');
        $this->dispatch('minAlert', titulo: 'EXITO', mensaje: $mensaje, icono: 'success');
    }

    public function render()
    {
        return view('livewire.r-r-h-h.subir-documento');
    }
}
