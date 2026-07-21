<?php

namespace App\Livewire\RRHH;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\Planilla;

class ListaPlanilla extends Component
{
    use WithPagination;

    public $search = '';
    public $cant = '20';
    public $periodoSeleccionado;

    public function mount()
    {
        $this->cargarUltimoPeriodo();
    }

    public function cargarUltimoPeriodo()
    {
        // Obtenemos el último periodo registrado en la tabla planillas
        $ultimo = Planilla::orderBy('periodo', 'desc')->first();
        if ($ultimo) {
            $this->periodoSeleccionado = $ultimo->periodo->format('Y-m-d');
        }
    }

    /**
     * Este es el cambio clave:
     * Al recibir el evento, refrescamos el periodo para que apunte al nuevo
     */
    #[On('refresh-planilla')]
    public function handleRefresh()
    {
        $this->cargarUltimoPeriodo();
        $this->resetPage(); // Opcional: vuelve a la página 1 para ver los nuevos datos
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPeriodoSeleccionado() { $this->resetPage(); }

    public function togglePago($id)
    {
        $planilla = Planilla::findOrFail($id);
        $planilla->estado_pago = !$planilla->estado_pago;

        // Si se marca como pagado, podrías setear la fecha_pago automáticamente
        if($planilla->estado_pago) {
            $planilla->fecha_pago = now();
        }

        $planilla->save();

        // Notificación opcional
        $this->dispatch('minAlert',
            titulo: 'Actualizado',
            mensaje: 'Estado de pago modificado',
            icono: 'success'
        );
    }

    public function render()
    {
        // 1. Obtener lista de periodos únicos para el Select (Ordenados del más reciente al más antiguo)
        $listaPeriodos = Planilla::select('periodo')
            ->groupBy('periodo')
            ->orderBy('periodo', 'desc')
            ->get();

        // 2. Consulta de planillas filtradas
        $query = Planilla::query();
        //$totalGeneral = 0; // Inicializamos el acumulado
        // Inicializamos variables de totales
        $totales = [
            'general'  => 0,
            'banco'    => 0,
            'efectivo' => 0
        ];

        if ($this->periodoSeleccionado) {
            $query->whereDate('periodo', $this->periodoSeleccionado)
                ->whereHas('contrato.user', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%');
                });

            // Calculamos el total de TODO el periodo seleccionado (independiente de la paginación)
            //$totalGeneral = (clone $query)->sum('total_pagado');
            // Calculamos los 3 totales del periodo seleccionado de una sola vez
            $totales['general']  = (clone $query)->sum('total_pagado');
            $totales['banco']    = (clone $query)->sum('pago_banco');
            $totales['efectivo'] = (clone $query)->sum('pago_efectivo');

            $planillas = $query->with(['contrato.user'])
                ->orderBy('created_at', 'desc')
                ->paginate($this->cant);

            //$planillas = $query->paginate($this->cant);
        } else {
            $planillas = Planilla::where('id', 0)->paginate($this->cant);
        }

        return view('livewire.r-r-h-h.lista-planilla', [
            'planillas' => $planillas,
            'listaPeriodos' => $listaPeriodos,
            'totales' => $totales // array de totales
        ]);
    }
}
