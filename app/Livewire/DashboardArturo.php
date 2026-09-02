<?php

namespace App\Livewire;

use App\Models\ServiceOrder;
use App\Models\SesionCaja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Carbon;
use Livewire\Component;

class DashboardArturo extends Component
{
    protected function serieUltimos30Dias($coleccionPorFecha)
    {
        $labels = [];
        $data = [];

        for ($i = 29; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $clave = $fecha->format('Y-m-d');

            $labels[] = $fecha->format('d/m');
            $data[] = (float) ($coleccionPorFecha[$clave] ?? 0);
        }

        return [$labels, $data];
    }

    public function render()
    {
        $user = auth()->user();
        $data = [];

        // Caja (Solo Admin y Jefe de Taller)
        if ($user->hasAnyRole(['Administrador del sistema', 'Jefe de Taller'])) {
            $sesionCaja = SesionCaja::abierta()->with('abiertaPor')->orderByDesc('abierta_en')->first();
            $data['sesionCaja'] = $sesionCaja;

            if ($sesionCaja) {
                $data['ingresosHoy'] = $sesionCaja->movimientos()->where('tipo', 'ingreso')->sum('monto');
                $data['egresosHoy'] = $sesionCaja->movimientos()->where('tipo', 'egreso')->sum('monto');
            }
        }

        // Gráficos: ingresos de los últimos 30 días (Solo Admin)
        if ($user->hasRole('Administrador del sistema')) {
            $ingresosPorDia = MovimientoCaja::where('tipo', 'ingreso')
                ->where('created_at', '>=', now()->subDays(29)->startOfDay())
                ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
                ->groupBy('fecha')
                ->pluck('total', 'fecha');

            [$data['ingresosLabels'], $data['ingresosData']] = $this->serieUltimos30Dias($ingresosPorDia);

            // Gráfico: conversiones creadas por día, últimos 30 días
            $conversionesPorDia = ServiceOrder::whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
                ->where('created_at', '>=', now()->subDays(29)->startOfDay())
                ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                ->groupBy('fecha')
                ->pluck('total', 'fecha');

            [$data['conversionesLabels'], $data['conversionesData']] = $this->serieUltimos30Dias($conversionesPorDia);
        }

        // Contadores de conversiones (No Clientes)
        if (! $user->hasRole('Cliente')) {
            $data['conversionesHoy'] = ServiceOrder::whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
                ->whereDate('created_at', today())->count();

            $data['conversionesSemana'] = ServiceOrder::whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

            $data['conversionesMes'] = ServiceOrder::whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
                ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        }

        // Roles específicos
        if ($user->hasAnyRole(['Jefe de Taller', 'Administrador del sistema'])) {
            $data['pendientesAsignarTecnico'] = ServiceOrder::whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
                ->where('estado', 'creada')->count();
            $data['pendientesEntrega'] = ServiceOrder::where('estado', 'conversion_completada')->count();
        }

        if ($user->hasAnyRole(['Tecnico', 'Administrador del sistema'])) {
            $data['misPendientes'] = ServiceOrder::where('tecnico_id', $user->id)
                ->whereIn('estado', ['en_evaluacion', 'aprobado_conversion', 'en_conversion'])->count();
        }

        if ($user->hasAnyRole(['Almacen', 'Administrador del sistema'])) {
            $data['pendientesAlmacen'] = ServiceOrder::where('estado', 'aprobado_conversion')->count();
        }

        $data['ordenesHoy'] = ServiceOrder::whereDate('created_at', today())->count();

        return view('livewire.dashboard-arturo', $data);
    }
}
