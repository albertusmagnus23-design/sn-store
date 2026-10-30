

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;

document.addEventListener('submit', async (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.matches('[data-confirm-delete]') || form.dataset.confirmed === 'true') {
        return;
    }

    event.preventDefault();

    const resource = form.dataset.confirmResource ?? 'kategori';
    const name = form.dataset.confirmName ?? form.dataset.categoryName ?? 'item ini';
    const capitalizedResource = resource.charAt(0).toUpperCase() + resource.slice(1);

    const result = await Swal.fire({
        icon: 'warning',
        title: `Hapus ${resource}?`,
        text: `${capitalizedResource} "${name}" akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.`,
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#be123c',
        cancelButtonColor: '#64748b',
        reverseButtons: true,
        focusCancel: true,
    });

    if (result.isConfirmed) {
        form.dataset.confirmed = 'true';
        form.requestSubmit();
    }
});

Alpine.start();
