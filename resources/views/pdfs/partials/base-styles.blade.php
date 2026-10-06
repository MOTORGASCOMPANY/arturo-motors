{{-- Estilos base compartidos para todos los reportes PDF --}}
<style>
@page { margin: 10px; }
* { margin: 0; padding: 0; box-sizing: border-box; -webkit-font-smoothing: antialiased; }
html, body {
    font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
    font-size: 8.5px; color: #1c2d42; line-height: 1.35;
}
.page-wrap { padding: 26px 34px 40px 34px; }
table { width: 100%; border-collapse: collapse; }
.w-100 { width: 100%; }
.spacer-sm { height: 6px; line-height: 6px; font-size: 1px; }
.spacer-md { height: 10px; line-height: 10px; font-size: 1px; }
.spacer-lg { height: 16px; line-height: 16px; font-size: 1px; }

.header-table td { vertical-align: middle; }
.logo-cell { width: 46%; padding-right: 16px; }
.title-cell { width: 54%; }
.logo { max-height: 36px; width: auto; display: block; }
.company-title { font-family: 'Georgia', serif; font-size: 13px; font-weight: 700; color: #1e3a5f; letter-spacing: 0.4px; margin-top: 5px; }
.company-subtitle { font-size: 7.3px; color: #3b82f6; font-weight: 700; letter-spacing: 0.6px; margin-top: 2px; }
.company-info { font-size: 7px; color: #64748b; line-height: 1.5; margin-top: 4px; }
.report-title-box { background-color: #2563eb; color: #ffffff; padding: 10px 16px; text-align: center; }
.report-title { font-family: 'Georgia', serif; font-size: 11.5px; font-weight: 700; letter-spacing: 1.2px; }
.report-subtitle { font-size: 7px; color: #bfdbfe; margin-top: 3px; }
.header-rule { border-top: 2px solid #2563eb; font-size: 1px; line-height: 1px; }

.meta-table td { font-size: 7.3px; padding: 7px 11px; background-color: #f7f9fc; border: 1px solid #e2e8f0; }
.meta-label { display: block; font-size: 6.5px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 2px; }
.meta-value { font-size: 8px; color: #0d1b30; font-weight: 700; }

.kpi-box { text-align: center; padding: 10px; background-color: #f7f9fc; border: 1px solid #e2e8f0; border-radius: 4px; }
.kpi-box .value { font-size: 16px; font-weight: 700; }
.kpi-box .label { font-size: 6.5px; color: #64748b; text-transform: uppercase; margin-top: 3px; letter-spacing: 0.3px; }
.kpi-green .value { color: #059669; }
.kpi-blue .value { color: #2563eb; }
.kpi-amber .value { color: #d97706; }
.kpi-navy .value { color: #2563eb; }
.kpi-cyan .value { color: #06b6d4; }
.kpi-red .value { color: #dc2626; }

.data-table { border: 1px solid #94a3b8; }
.data-table thead th {
    background-color: #2563eb; color: #ffffff; font-size: 7px; font-weight: 700;
    padding: 7px 8px; text-transform: uppercase; letter-spacing: 0.3px;
    border-right: 1px solid #3b82f6; text-align: left;
}
.data-table thead th:last-child { border-right: none; }
.data-table tbody td {
    padding: 6px 8px; font-size: 7.5px; border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0; vertical-align: middle; color: #1c2d42;
}
.data-table tbody td:last-child { border-right: none; }
.data-table tbody tr:nth-child(even) { background-color: #f7f9fc; }
.cell-strong { font-weight: 700; color: #0d1b30; }
.cell-muted { color: #64748b; }
.cell-right { text-align: right; }
.cell-center { text-align: center; }

.badge { display: inline-block; padding: 2px 8px; border-radius: 8px; font-size: 6.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.badge-completada { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
.badge-enproceso { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.badge-evaluacion { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
.badge-aprobado { background-color: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
.badge-entregado { background-color: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
.badge-aprobado-solicitud { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
.badge-rechazado { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
.badge-pendiente-solicitud { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.badge-pagado { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
.badge-parcial { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.badge-pendiente-pago { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
.badge-red { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
.badge-amber { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.badge-green { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
.badge-blue { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }

.empty-state { text-align: center; padding: 40px 20px; color: #94a3b8; border: 1px dashed #cbd5e1; background-color: #f7f9fc; }
.empty-state p { font-size: 10px; font-weight: 600; }

.bar-track { background: #e2e8f0; height: 8px; border-radius: 4px; width: 100%; }
.bar-fill { background: #2563eb; height: 8px; border-radius: 4px; }
.bar-fill.cuello { background: #dc2626; }

.closing-rule { border-top: 1px solid #cbd5e1; font-size: 1px; line-height: 1px; }
.closing-text { text-align: center; font-size: 6.5px; color: #94a3b8; letter-spacing: 0.4px; text-transform: uppercase; }

.page-footer { position: fixed; bottom: -32px; left: 34px; right: 34px; }
.page-footer table td { border-top: 1px solid #cbd5e1; padding-top: 5px; font-size: 6.5px; color: #94a3b8; }
.fright { text-align: right; }

.section-title { font-size: 8px; font-weight: 700; color: #0d1b30; text-transform: uppercase; letter-spacing: 0.5px; margin: 12px 0 6px 0; padding-bottom: 4px; border-bottom: 2px solid #2563eb; }
</style>