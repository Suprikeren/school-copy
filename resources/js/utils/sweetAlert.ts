import Swal from 'sweetalert2';

export const notif = {
    success: (message: string) => {
        Swal.fire({
            title: 'Berhasil!',
            text: message,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            customClass: {
                popup: 'rounded-xl dark:bg-gray-900 dark:text-gray-100',
            }
        });
    },
    error: (message: string) => {
        Swal.fire({
            title: 'Gagal!',
            text: message,
            icon: 'error',
            confirmButtonColor: '#000',
            customClass: {
                popup: 'rounded-xl dark:bg-gray-900 dark:text-gray-100',
            }
        });
    },
    // --- TAMBAHKAN FUNGSI INFO INI ---
    info: (message: string) => {
        Swal.fire({
            title: 'Informasi',
            text: message,
            icon: 'info',
            timer: 2000,
            showConfirmButton: false,
            customClass: {
                popup: 'rounded-xl dark:bg-gray-900 dark:text-gray-100',
            }
        });
    }
};

export const confirmDelete = (title: string, callback: () => void) => {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: title,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-xl dark:bg-gray-900 dark:text-gray-100',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
};
