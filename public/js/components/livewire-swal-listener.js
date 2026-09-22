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
