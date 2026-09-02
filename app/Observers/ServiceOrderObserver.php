<?php

namespace App\Observers;

use App\Models\ServiceOrder;
use App\Models\ServiceOrderStatusHistory;

class ServiceOrderObserver
{
    /**
     * Handle the ServiceOrder "created" event.
     */
    public function created(ServiceOrder $serviceOrder): void
    {
        ServiceOrderStatusHistory::registrar(
            $serviceOrder,
            null, // no había estado anterior
            $serviceOrder->estado,
            auth()->id() ?? $serviceOrder->creadoPor?->id ?? 1
        );
    }

    /**
     * Handle the ServiceOrder "updated" event.
     */
    public function updated(ServiceOrder $serviceOrder): void
    {
        //
    }

    // app/Observers/ServiceOrderObserver.php
    public function updating(ServiceOrder $orden)
    {
        if ($orden->isDirty('estado')) {
            ServiceOrderStatusHistory::registrar(
                $orden,
                $orden->getOriginal('estado'),
                $orden->estado,
                auth()->id() ?? $orden->creadoPor?->id ?? 1
            );
        }
    }

    /**
     * Handle the ServiceOrder "deleted" event.
     */
    public function deleted(ServiceOrder $serviceOrder): void
    {
        //
    }

    /**
     * Handle the ServiceOrder "restored" event.
     */
    public function restored(ServiceOrder $serviceOrder): void
    {
        //
    }

    /**
     * Handle the ServiceOrder "force deleted" event.
     */
    public function forceDeleted(ServiceOrder $serviceOrder): void
    {
        //
    }
}
