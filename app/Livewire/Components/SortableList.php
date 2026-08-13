<?php

namespace App\Livewire\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class SortableList extends Component
{
    public $items = [];

    public $modelClass = '';

    public $orderField = 'sort_order';

    public $idField = 'id';

    public $labelField = 'title';

    public $iconField = 'icon';

    public $activeField = 'is_active';

    public $previewFields = [];

    protected $listeners = ['sortableUpdated' => 'handleSortableUpdate'];

    public function mount(array $items = [], string $modelClass = '', array $config = [])
    {
        $this->items = $items;
        $this->modelClass = $modelClass;

        $this->orderField = $config['orderField'] ?? 'sort_order';
        $this->idField = $config['idField'] ?? 'id';
        $this->labelField = $config['labelField'] ?? 'title';
        $this->iconField = $config['iconField'] ?? 'icon';
        $this->activeField = $config['activeField'] ?? 'is_active';
        $this->previewFields = $config['previewFields'] ?? [];
    }

    #[On('sortable-updated')]
    public function handleSortableUpdate(array $newOrder)
    {
        if (! $this->modelClass) {
            return;
        }

        foreach ($newOrder as $index => $itemId) {
            $this->modelClass::where($this->idField, $itemId)
                ->update([$this->orderField => $index + 1]);
        }

        $this->invalidateLandingCache();

        $this->dispatch('sortable-saved', [
            'message' => 'Orden actualizado correctamente',
        ]);
    }

    public function render()
    {
        return view('livewire.components.sortable-list');
    }
}
