document.addEventListener('DOMContentLoaded', function() {
    
    // Ambil semua tombol hapus
    const deleteButtons = document.querySelectorAll('.btn-delete-gejala');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // Hentikan link agar tidak langsung berjalan
            event.preventDefault(); 
            
            // Tampilkan kotak konfirmasi
            const isConfirmed = confirm('Apakah Anda yakin ingin menghapus gejala ini? Ini juga akan menghapus semua aturan yang terkait dengan gejala ini.');
            
            // Jika pengguna menekan "OK", lanjutkan ke link hapus
            if (isConfirmed) {
                window.location.href = this.href;
            }
        });
    });

});