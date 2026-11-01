
document.addEventListener('DOMContentLoaded', () => {
    // Ambil form register berdasarkan input nama.
    const form = document.querySelector('#name')?.closest('form');

    if (!form) {
        console.error('Form register tidak ditemukan.');
        return;
    }

    console.log('Register validation berhasil dimuat.');

    const fields = {
        name: document.getElementById('name'),
        email: document.getElementById('email'),
        phone: document.getElementById('no_telepon'),
        password: document.getElementById('password'),
        confirmation: document.getElementById('password_confirmation'),
    };

    // Mengikuti regex pada RegisterRequest.php.
    const phonePattern = /^(\+62|62|0)8[1-9][0-9]{6,11}$/;

    // Membuat tempat pesan error di bawah setiap input.
    Object.values(fields).forEach(field => {
        if (!field) return;

        let message = document.getElementById(`${field.id}-validation`);

        if (!message) {
            message = document.createElement('p');
            message.id = `${field.id}-validation`;
            message.className = 'mt-1.5 text-xs text-red-600';
            message.hidden = true;
            message.setAttribute('role', 'alert');

            field.insertAdjacentElement('afterend', message);
        }
    });

    function getError(field) {
        const value = field.value.trim();

        if (field === fields.name) {
            if (!value) return 'Nama lengkap wajib diisi.';
            if (value.length > 255) return 'Nama maksimal 255 karakter.';
        }

        if (field === fields.email) {
            if (!value) return 'Alamat email wajib diisi.';
            if (value.length > 255) return 'Email maksimal 255 karakter.';
            if (field.validity.typeMismatch) {
                return 'Format email belum valid.';
            }
        }

        if (field === fields.phone) {
            if (!value) return 'Nomor telepon wajib diisi.';
            if (!phonePattern.test(value)) {
                return 'Nomor harus diawali +62, 62, atau 0 dan menggunakan format nomor seluler yang valid.';
            }
        }

        if (field === fields.password) {
            if (!field.value) return 'Kata sandi wajib diisi.';
        }

        if (field === fields.confirmation) {
            if (!field.value) {
                return 'Konfirmasi kata sandi wajib diisi.';
            }

            if (field.value !== fields.password.value) {
                return 'Konfirmasi kata sandi tidak cocok.';
            }
        }

        return '';
    }

    function validateField(field) {
        if (!field) return true;

        const message = document.getElementById(`${field.id}-validation`);
        const error = getError(field);

        message.textContent = error;
        message.hidden = !error;

        field.classList.toggle('!border-red-400', Boolean(error));
        field.setAttribute('aria-invalid', error ? 'true' : 'false');

        return !error;
    }

    Object.values(fields).forEach(field => {
        if (!field) return;

        // Munculkan peringatan ketika pengguna meninggalkan kolom.
        field.addEventListener('blur', () => {
            field.dataset.touched = 'true';
            validateField(field);
        });

        // Perbarui peringatan ketika pengguna mengetik.
        field.addEventListener('input', () => {
            if (field.dataset.touched === 'true') {
                validateField(field);
            }

            // Periksa ulang konfirmasi jika password berubah.
            if (
                field === fields.password &&
                fields.confirmation.dataset.touched === 'true'
            ) {
                validateField(fields.confirmation);
            }
        });
    });

    // Tetap lakukan pemeriksaan sebelum form dikirim.
    form.addEventListener('submit', event => {
        let firstInvalidField = null;

        Object.values(fields).forEach(field => {
            if (!field) return;

            field.dataset.touched = 'true';

            if (!validateField(field) && !firstInvalidField) {
                firstInvalidField = field;
            }
        });

        if (firstInvalidField) {
            event.preventDefault();
            firstInvalidField.focus();
        }
    });
});
