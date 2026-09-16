<?php

namespace App\Livewire\Cms\PorQue;

use App\Models\WhyCard;
use Livewire\Component;

class FormPorQue extends Component
{
    public bool $mostrarModal = false;
    public ?int $editingId = null;
    public string $title = '';
    public string $description = '';
    public string $icon = '';
    public bool $active = true;

    public array $iconOptions = [
        'fa-solid fa-shield-halved' => 'Garantía / Seguridad',
        'fa-solid fa-medal' => 'Calidad Certificada',
        'fa-solid fa-award' => 'Premiado',
        'fa-solid fa-star' => 'Excelencia',
        'fa-solid fa-thumbs-up' => 'Satisfacción Garantizada',
        'fa-solid fa-heart' => 'Compromiso',
        'fa-solid fa-hand-holding-dollar' => 'Mejor Precio',
        'fa-solid fa-dollar-sign' => 'Precios Justos',
        'fa-solid fa-truck' => 'Entrega / Transporte',
        'fa-solid fa-clock' => 'Rapidez',
        'fa-solid fa-headset' => 'Atención al Cliente',
        'fa-solid fa-certificate' => 'Certificación',
        'fa-solid fa-users' => 'Equipo Experto',
        'fa-solid fa-trophy' => 'Trayectoria',
        'fa-solid fa-gem' => 'Calidad Premium',
        'fa-solid fa-lock' => 'Confianza y Seguridad',
        'fa-solid fa-tools' => 'Servicio Técnico',
        'fa-solid fa-car' => 'Especialistas Automotrices',
    ];

    public function abrirCrear(): void
    {
        $this->resetForm();
        $this->mostrarModal = true;
    }

    public function abrirEditar(int $id): void
    {
        $card = WhyCard::findOrFail($id);
        $this->editingId = $id;
        $this->title = $card->title;
        $this->description = $card->description ?? '';
        $this->icon = $card->icon ?? '';
        $this->active = $card->is_active;
        $this->mostrarModal = true;
    }

    public function cerrar(): void
    {
        $this->mostrarModal = false;
        $this->resetForm();
    }

    public function guardar(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
        ], [
            'title.required' => 'El campo Título es obligatorio',
            'title.max' => 'El campo Título no debe exceder 255 caracteres',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'is_active' => $this->active,
        ];

        try {
            if ($this->editingId) {
                WhyCard::findOrFail($this->editingId)->update($data);
                $this->dispatch('minToast', titulo: '¡Actualizado!', mensaje: 'Tarjeta actualizada correctamente.', icono: 'success');
            } else {
                $data['sort_order'] = WhyCard::max('sort_order') + 1;
                WhyCard::create($data);
                $this->dispatch('minToast', titulo: '¡Creada!', mensaje: 'Tarjeta creada correctamente.', icono: 'success');
            }

            $this->cerrar();
            $this->dispatch('por-que-guardado');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo guardar la tarjeta.', icono: 'error');
        }
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'description', 'icon', 'active']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.cms.por-que.form-por-que');
    }
}
