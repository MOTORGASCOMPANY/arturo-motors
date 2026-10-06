<?php

namespace App\Livewire;

use App\Models\ServiceOrder;
use App\Models\SesionCaja;
use App\Models\MovimientoCaja;
use App\Models\FisePago;
use App\Models\User;
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

    /**
     * Retorna el rol de mayor jerarquía (más permisos) del usuario.
     * Orden de prioridad: Administrador del sistema > Jefe de Taller > Vendedor > Tecnico > Almacen > Cajero > Cliente
     */
    protected function getRolPrincipal(User $user): ?string
    {
        $jerarquia = [
            'Administrador del sistema' => 7,
            'Jefe de Taller' => 6,
            'Vendedor' => 5,
            'Tecnico' => 4,
            'Almacen' => 3,
            'Cajero' => 2,
            'Cliente' => 1,
        ];

        $rolesUsuario = $user->getRoleNames()->toArray();
        $rolPrincipal = null;
        $maxPrioridad = 0;

        foreach ($rolesUsuario as $rol) {
            $prioridad = $jerarquia[$rol] ?? 0;
            if ($prioridad > $maxPrioridad) {
                $maxPrioridad = $prioridad;
                $rolPrincipal = $rol;
            }
        }

        return $rolPrincipal;
    }

    public function render()
    {
        $user = auth()->user();
        $data = [];

        // Caja (quien tiene el módulo de caja: Admin, Jefe de Taller, Cajero)
        if ($user->can('opciones.caja')) {
            $sesionCaja = SesionCaja::abierta()->with('abiertaPor')->orderByDesc('abierta_en')->first();
            $data['sesionCaja'] = $sesionCaja;

            if ($sesionCaja) {
                $data['ingresosHoy'] = $sesionCaja->movimientos()->where('tipo', 'ingreso')->sum('monto');
                $data['egresosHoy'] = $sesionCaja->movimientos()->where('tipo', 'egreso')->sum('monto');
            }
        }

        // Gráficos: ingresos de los últimos 30 días (Admin y Jefe de Taller)
        if ($user->can('dashboard.graficos')) {
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

        // Contadores de conversiones (quien participa del flujo de conversiones)
        if ($user->can('dashboard.metricas')) {
            $data['conversionesHoy'] = ServiceOrder::whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
                ->whereDate('created_at', today())->count();

            $data['conversionesSemana'] = ServiceOrder::whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

            $data['conversionesMes'] = ServiceOrder::whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
                ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        }

        if ($user->can('conversiones.asignar')) {
            $data['pendientesAsignarTecnico'] = ServiceOrder::whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
                ->where('estado', 'creada')->count();
        }

        if ($user->can('conversiones.entregas-pendientes')) {
            $data['pendientesEntrega'] = ServiceOrder::where('estado', 'conversion_completada')->count();
        }

        if ($user->can('conversiones.mis-asignadas')) {
            $data['misPendientes'] = ServiceOrder::where('tecnico_id', $user->id)
                ->whereIn('estado', ['en_evaluacion', 'aprobado_conversion', 'en_conversion'])->count();
        }

        if ($user->can('opciones.almacen')) {
            $data['pendientesAlmacen'] = ServiceOrder::where('estado', 'aprobado_conversion')->count();
        }

        // FISE pendientes
        if ($user->can('opciones.fise')) {
            $data['fisePendientes'] = FisePago::where('estado', 'pendiente')->count();
        }

        if ($user->can('ordenes.listado')) {
            $data['ordenesHoy'] = ServiceOrder::whereDate('created_at', today())->count();
        }

        $data['rolPrincipal'] = $this->getRolPrincipal($user);

        return view('livewire.dashboard-arturo', $data);
    }
}
