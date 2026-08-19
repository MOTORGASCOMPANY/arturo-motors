<?php

namespace App\Livewire\ServiceOrders;

use App\Models\Documento;
use App\Models\ServiceOrder;
use App\Support\ChecklistEvaluacion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Detalle extends Component
{
    use WithFileUploads;

    public ServiceOrder $orden;

    public array $checklistGrupos = [];

    public bool $showUploadModal = false;

    public ?int $ordenSeleccionadaId = null;

    public ?ServiceOrder $ordenSeleccionada = null;

    public string $tipoDocumento = '';

    public $archivo;

    public array $tiposDocumento = [
        'SOAT',
        'Tarjeta de propiedad',
        'Carta de garantía',
        'Manual del vehículo',
        'Comprobante de pago',
        'Revisión técnica',
        'Otro',
    ];

    private const CALIDAD_JPEG_PDF = 85;

    private const DPI_PDF = 150;

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with([
            'cliente', 'vehiculo', 'service', 'tecnico', 'evaluadoPor', 'creadoPor',
            'items.producto.categoria',
            'movimientosStock.producto',
            'movimientosStock.usuario',
            'comprobante',
            'historialEstados.usuario',
            'documentos',
        ])->findOrFail($ordenId);

        $this->checklistGrupos = ChecklistEvaluacion::grupos();
    }

    protected function rules(): array
    {
        return [
            'tipoDocumento' => 'required|string|min:2|max:100',
            'archivo' => 'required|file|mimes:jpg,jpeg,png,webp,bmp,gif,pdf|max:10240',
        ];
    }

    protected $messages = [
        'archivo.required' => 'Debes seleccionar una foto o archivo.',
        'archivo.mimes' => 'Solo se permiten imágenes (jpg, png, webp, gif, bmp) o PDF.',
        'archivo.max' => 'El archivo no debe superar 10MB.',
    ];

    public function abrirModalSubida($ordenId)
    {
        $this->ordenSeleccionada = ServiceOrder::with('vehiculo')->findOrFail($ordenId);
        $this->ordenSeleccionadaId = $this->ordenSeleccionada->id;

        $this->reset(['tipoDocumento', 'archivo']);
        $this->resetErrorBag();

        $this->showUploadModal = true;
    }

    public function cerrarModalSubida()
    {
        $this->showUploadModal = false;
        $this->reset(['ordenSeleccionadaId', 'ordenSeleccionada', 'tipoDocumento', 'archivo']);
        $this->resetErrorBag();
    }

    public function guardarDocumento()
    {
        $this->validate();

        $orden = $this->ordenSeleccionada;

        if (! $orden || ! $orden->vehiculo) {
            $this->addError('archivo', 'La orden no tiene un vehículo asociado, no se puede subir el documento.');
            $this->dispatch('documento-error', mensaje: 'La orden no tiene un vehículo asociado.');
            return;
        }

        try {
            $placaOriginal = $orden->vehiculo->placa ?: 'SIN-PLACA';
            $placaSlug = Str::upper(Str::slug($placaOriginal, ''));
            $carpeta = "documentos/{$placaSlug}";

            $rutaTemporal = $this->archivo->getRealPath();
            $mime = (string) $this->archivo->getMimeType();
            $esImagen = str_starts_with($mime, 'image');

            Storage::disk('public')->makeDirectory($carpeta);

            $nombreBase = $this->sanitizarNombreArchivo($this->tipoDocumento);
            $extension = 'pdf';
            $nombreArchivo = $this->generarNombreUnico($carpeta, $nombreBase, $extension);

            if ($esImagen) {
                $rutaAbsoluta = Storage::disk('public')->path("{$carpeta}/{$nombreArchivo}");
                $this->convertirImagenAPdf($rutaTemporal, $rutaAbsoluta, self::CALIDAD_JPEG_PDF);
                $path = "{$carpeta}/{$nombreArchivo}";
            } else {
                $path = $this->archivo->storeAs($carpeta, $nombreArchivo, 'public');
            }

            if ($esImagen && is_file($rutaTemporal)) {
                @unlink($rutaTemporal);
            }

            Documento::create([
                'service_order_id' => $orden->id,
                'tipo' => $this->tipoDocumento,
                'path' => $path,
                'nombre_original' => $this->archivo->getClientOriginalName(),
                'subido_por' => auth()->id(),
            ]);

            $this->cerrarModalSubida();
            $this->orden->load('documentos');

            $this->dispatch('documento-subido', mensaje: 'Documento subido correctamente.');
        } catch (\Throwable $e) {
            report($e);
            $this->addError('archivo', 'No se pudo procesar el archivo. Verifica que sea una imagen o PDF válido e intenta de nuevo.');
            $this->dispatch('documento-error', mensaje: 'No se pudo procesar el archivo. Intenta de nuevo.');
        }
    }

    private function sanitizarNombreArchivo(string $nombre): string
    {
        $nombre = preg_replace('/[\/\\\\:\*\?"<>\|]+/', '', $nombre) ?? $nombre;
        $nombre = trim(preg_replace('/\s+/', ' ', $nombre) ?? $nombre);

        return $nombre !== '' ? $nombre : 'Documento';
    }

    private function generarNombreUnico(string $carpeta, string $nombreBase, string $extension): string
    {
        $intento = 0;

        do {
            $sufijo = $intento === 0 ? '' : " ({$intento})";
            $nombreArchivo = "{$nombreBase}{$sufijo}.{$extension}";
            $existe = Storage::disk('public')->exists("{$carpeta}/{$nombreArchivo}");
            $intento++;
        } while ($existe);

        return $nombreArchivo;
    }

    private function convertirImagenAPdf(string $rutaOrigen, string $rutaDestino, int $calidadJpeg = 85): void
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagejpeg')) {
            throw new \RuntimeException('La extensión GD de PHP no está disponible para procesar imágenes.');
        }

        $datos = @file_get_contents($rutaOrigen);
        if ($datos === false || $datos === '') {
            throw new \RuntimeException('No se pudo leer el archivo de imagen temporal.');
        }

        $imagenOriginal = @imagecreatefromstring($datos);

        if ($imagenOriginal === false) {
            throw new \RuntimeException('El archivo no es una imagen válida o está corrupto.');
        }

        $ancho = imagesx($imagenOriginal);
        $alto = imagesy($imagenOriginal);

        $lienzo = imagecreatetruecolor($ancho, $alto);
        $blanco = imagecolorallocate($lienzo, 255, 255, 255);
        imagefill($lienzo, 0, 0, $blanco);
        imagealphablending($lienzo, true);
        imagecopy($lienzo, $imagenOriginal, 0, 0, 0, 0, $ancho, $alto);
        imagedestroy($imagenOriginal);

        $calidadJpeg = max(0, min(100, $calidadJpeg));
        $rutaJpegTemporal = tempnam(sys_get_temp_dir(), 'doc_').'.jpg';

        $ok = imagejpeg($lienzo, $rutaJpegTemporal, $calidadJpeg);
        imagedestroy($lienzo);

        if (! $ok || ! is_file($rutaJpegTemporal)) {
            @unlink($rutaJpegTemporal);
            throw new \RuntimeException('No se pudo generar la imagen intermedia para el PDF.');
        }

        try {
            $this->crearPdfDesdeJpeg($rutaJpegTemporal, $rutaDestino, $ancho, $alto);
        } finally {
            @unlink($rutaJpegTemporal);
        }
    }

    private function crearPdfDesdeJpeg(string $rutaJpeg, string $rutaPdfDestino, int $anchoPx, int $altoPx): void
    {
        $jpegData = file_get_contents($rutaJpeg);
        if ($jpegData === false) {
            throw new \RuntimeException('No se pudo leer el JPEG intermedio para armar el PDF.');
        }
        $jpegLen = strlen($jpegData);

        $anchoPt = $anchoPx * 72 / self::DPI_PDF;
        $altoPt = $altoPx * 72 / self::DPI_PDF;

        $contenido = sprintf('q %.2F 0 0 %.2F 0 0 cm /Im0 Do Q', $anchoPt, $altoPt);

        $objetos = [];
        $objetos[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objetos[2] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objetos[3] = sprintf(
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /XObject << /Im0 5 0 R >> >> /Contents 4 0 R >>',
            $anchoPt,
            $altoPt
        );
        $objetos[4] = '<< /Length '.strlen($contenido)." >>\nstream\n{$contenido}\nendstream";
        $objetos[5] = "<< /Type /XObject /Subtype /Image /Width {$anchoPx} /Height {$altoPx} "
            ."/ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length {$jpegLen} >>\nstream\n{$jpegData}\nendstream";

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objetos as $numero => $cuerpo) {
            $offsets[$numero] = strlen($pdf);
            $pdf .= "{$numero} 0 obj\n{$cuerpo}\nendobj\n";
        }

        $xrefInicio = strlen($pdf);
        $totalObjs = count($objetos) + 1;

        $pdf .= "xref\n0 {$totalObjs}\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objetos); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size {$totalObjs} /Root 1 0 R >>\nstartxref\n{$xrefInicio}\n%%EOF";

        if (@file_put_contents($rutaPdfDestino, $pdf) === false) {
            throw new \RuntimeException('No se pudo guardar el PDF generado en el servidor.');
        }
    }

    public function eliminarDocumento($documentoId)
    {
        try {
            $documento = Documento::findOrFail($documentoId);

            if (Storage::disk('public')->exists($documento->path)) {
                Storage::disk('public')->delete($documento->path);
            }

            $documento->delete();
            $this->orden->load('documentos');

            $this->dispatch('documento-eliminado', mensaje: 'Documento eliminado permanentemente.');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('documento-error', mensaje: 'No se pudo eliminar el documento. Intenta de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.service-orders.detalle');
    }
}
