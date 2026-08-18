<div class="bg-gray-200 p-8 rounded-2xl shadow-sm w-full">
    <div class="space-y-4">
         <!-- TITULO DE LA TABLA -->
        <div class="text-xl font-semibold text-gray-700 border-b border-gray-200">
            <?php echo e($titulo); ?>

        </div>
        <div class="w-full flex flex-col md:flex-row md:items-center md:justify-between gap-4">            
            <!-- SELECT CANTIDAD -->
            <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                <span class="text-gray-600 text-sm">Mostrar</span>
                <select wire:model="cant"
                    class="mx-2 border-none bg-transparent text-gray-700 text-sm focus:ring-0 focus:outline-none">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-gray-600 text-sm">registros</span>
            </div>
            <!-- BUSCADOR -->
            <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm w-full md:w-1/2">
                <i class="fas fa-search h-5 w-5 text-emerald-500"></i>
                <input type="text" 
                    wire:model.live="search"
                    placeholder="Buscar..."
                    class="ml-2 w-full border-none bg-transparent focus:ring-0 text-sm text-gray-700 placeholder-gray-400">
            </div>
            
            <!-- BOTÓN PRINCIPAL -->
            <div class="flex">
                <?php echo e($btnAgregar); ?>

            </div>          

        </div>
    </div>
    
    <!-- CONTENIDO TABLA -->
    <div class="mt-4 overflow-x-auto rounded-lg shadow-md">
        <?php echo e($contenido); ?>

    </div>
    
</div><?php /**PATH C:\xampp\htdocs\Lifegas\resources\views/components/custom-table.blade.php ENDPATH**/ ?>