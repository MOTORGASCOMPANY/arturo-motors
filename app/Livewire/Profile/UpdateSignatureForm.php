<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UpdateSignatureForm extends Component
{
    use WithFileUploads;

    public $firma; // Para el nuevo archivo
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function saveSignature()
    {
        $this->validate([
            'firma' => 'required|image|max:1024',
        ]);

        if ($this->user->ruta_firma) {
            Storage::disk('public')->delete($this->user->ruta_firma);
        }

        // Al usar 'public' como disco, se guarda en storage/app/public/firmaUsuarios
        $path = $this->firma->store('firmaUsuarios', 'public');

        $this->user->update([
            'ruta_firma' => $path,
        ]);

        $this->user->refresh();

        $this->reset('firma');
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.profile.update-signature-form');
    }
}
