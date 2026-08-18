<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Conversiones por mes -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Conversiones por Mes</h3>
        <canvas id="chartConversiones"></canvas>
    </div>

    <!-- Vehículos por combustible -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Vehículos por Combustible</h3>
        <canvas id="chartCombustible"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Conversiones por Mes
    new Chart(document.getElementById('chartConversiones'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($conversionesPorMes), 15, 512) ?>,
            datasets: [{
                label: 'Conversiones',
                data: <?php echo json_encode(array_values($conversionesPorMes), 15, 512) ?>,
                backgroundColor: 'rgba(59, 130, 246, 0.7)'
            }]
        }
    });

    // Vehículos por Combustible
    new Chart(document.getElementById('chartCombustible'), {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_keys($vehiculosPorCombustible), 15, 512) ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($vehiculosPorCombustible), 15, 512) ?>,
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444']
            }]
        }
    });
</script>
<?php /**PATH C:\xampp\htdocs\Lifegas\resources\views/livewire/dashboard-charts.blade.php ENDPATH**/ ?>