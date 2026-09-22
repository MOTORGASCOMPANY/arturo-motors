/**
 * livewire-swal-listener — Shared SweetAlert2 bridge for Livewire events.
 *
 * Listens for 'swal' events dispatched from Livewire and shows a Swal popup.
 * Register this once per page that uses Livewire swal events.
 *
 * Depends on: SweetAlert2 (Swal), Livewire
 */
document.addEventListener('livewire:init', () => {
    Livewire.on('swal', (data) => {
        Swal.fire({
            icon: data.tipo || 'info',
            title: data.titulo || '',
            text: data.mensaje || '',
            confirmButtonColor: '#4F46E5',
        });
    });
});
