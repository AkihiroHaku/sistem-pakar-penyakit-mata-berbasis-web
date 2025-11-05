document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('signup-form');

    // --- Logika untuk validasi form signup ---
    if (form) {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const errorMessage = document.getElementById('password-mismatch-error');

        function validatePasswords() {
            if (passwordInput.value !== confirmPasswordInput.value) {
                errorMessage.textContent = 'Password tidak sama!';
                confirmPasswordInput.style.borderColor = '#e74c3c'; // Merah
                return false;
            } else {
                errorMessage.textContent = ''; // Hapus pesan jika sama
                confirmPasswordInput.style.borderColor = ''; // Kembali ke default
                return true;
            }
        }

        // Cek setiap kali pengguna mengetik di salah satu kolom password
        if (passwordInput && confirmPasswordInput) {
            passwordInput.addEventListener('keyup', validatePasswords);
            confirmPasswordInput.addEventListener('keyup', validatePasswords);
        }

        // Mencegah form dikirim jika password tidak sama
        form.addEventListener('submit', function (event) {
            if (!validatePasswords()) {
                event.preventDefault(); // Hentikan pengiriman form
                alert('Harap pastikan password konfirmasi sudah sama.');
            }
        });
    }

    // --- Logika untuk toggle show/hide password (berjalan di semua halaman) ---
    const togglePasswordIcons = document.querySelectorAll('.toggle-password');

    togglePasswordIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const passwordField = icon.closest('.password-wrapper').querySelector('input');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
});
