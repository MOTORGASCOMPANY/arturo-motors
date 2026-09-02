<?php

namespace App\Exports;

use App\Models\Cita;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class CitaExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $search;
    protected $estado;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct(?string $search = null, ?string $estado = 'todos', ?string $fechaInicio = null, ?string $fechaFin = null)
    {
        $this->search = $search;
        $this->estado = $estado;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        return Cita::with(['cliente', 'vehiculo', 'asesor'])
            ->buscar($this->search)
            ->estado($this->estado)
            ->when($this->fechaInicio, fn ($q) => $q->whereDate('fecha_cita', '>=', $this->fechaInicio))
            ->when($this->fechaFin, fn ($q) => $q->whereDate('fecha_cita', '<=', $this->fechaFin))
            ->orderBy('fecha_cita', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['#', 'Fecha', 'Cliente', 'Documento', 'Placa', 'Asesor', 'Motivo', 'Estado'];
    }

    public function map($cita): array
    {
        return [
            $cita->id,
            $cita->fecha_cita->format('d/m/Y H:i'),
            trim(($cita->cliente->nombre ?? 'N/A') . ' ' . ($cita->cliente->apellido ?? '')),
            $cita->cliente->documento ?? '—',
            $cita->vehiculo->placa ?? 'N/A',
            $cita->asesor->name ?? 'N/A',
            $cita->motivo ?? '—',
            ucfirst($cita->estado),
        ];
    }

    public function title(): string
    {
        return 'Reporte de Citas';
    }
}
