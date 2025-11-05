document.addEventListener('DOMContentLoaded', function() {
    
    // --- Logika untuk Konfirmasi Hapus Gejala ---
    const deleteGejalaButtons = document.querySelectorAll('.btn-delete-gejala');
    deleteGejalaButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // Mencegah link berjalan normal
            event.preventDefault(); 
            
            // Tampilkan kotak konfirmasi
            const isConfirmed = confirm('Apakah Anda yakin ingin menghapus gejala ini? Ini juga akan menghapus semua aturan yang terkait dengan gejala ini.');
            
            // Jika pengguna menekan "OK", lanjutkan ke link hapus
            if (isConfirmed) {
                window.location.href = this.href;
            }
        });
    });

    // --- Logika untuk Konfirmasi Hapus Penyakit ---
    const deletePenyakitButtons = document.querySelectorAll('.btn-delete-penyakit');
    deletePenyakitButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); 
            const isConfirmed = confirm('PERINGATAN: Menghapus penyakit ini juga akan menghapus SEMUA ATURAN dan RIWAYAT KONSULTASI yang terkait. Apakah Anda benar-benar yakin?');
            if (isConfirmed) {
                window.location.href = this.href;
            }
        });
    });

    // --- Logika untuk Tombol Edit Penyakit ---
    const editPenyakitButtons = document.querySelectorAll('.btn-edit-penyakit');
    const formTambahPenyakit = document.getElementById('form-tambah-penyakit');
    const formEditPenyakit = document.getElementById('form-edit-penyakit');
    const btnBatalEditPenyakit = document.getElementById('btn-batal-edit');

    // Cek jika semua elemen form ada di halaman
    if (formEditPenyakit && formTambahPenyakit && btnBatalEditPenyakit) {
        editPenyakitButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Ambil data dari atribut data-* tombol
                const id = this.dataset.id;
                const kode = this.dataset.kode;
                const nama = this.dataset.nama;
                const keterangan = this.dataset.keterangan;
                const solusi = this.dataset.solusi;

                // Isi formulir edit dengan data
                document.getElementById('id_penyakit_edit').value = id;
                document.getElementById('kode_penyakit_edit').value = kode;
                document.getElementById('nmpenyakit_edit').value = nama;
                document.getElementById('keterangan_edit').value = keterangan;
                document.getElementById('solusi_edit').value = solusi;

                // Tampilkan form edit dan sembunyikan form tambah
                formTambahPenyakit.style.display = 'none';
                formEditPenyakit.style.display = 'block';

                // Gulir ke form edit
                formEditPenyakit.scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Logika untuk tombol "Batal"
        btnBatalEditPenyakit.addEventListener('click', function() {
            formEditPenyakit.style.display = 'none';
            formTambahPenyakit.style.display = 'block';
        });
    }

    // --- Logika untuk Tombol Edit Gejala ---
    const editGejalaButtons = document.querySelectorAll('.btn-edit-gejala');
    const formTambahGejala = document.getElementById('form-tambah-gejala');
    const formEditGejala = document.getElementById('form-edit-gejala');
    const btnBatalEditGejala = document.getElementById('btn-batal-edit-gejala');

    // Cek jika semua elemen form gejala ada
    if (formEditGejala && formTambahGejala && btnBatalEditGejala) {
        editGejalaButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Ambil data dari tombol edit gejala
                const id = this.dataset.id;
                const nama = this.dataset.nama;

                // Isi formulir edit gejala
                document.getElementById('idgejala_edit').value = id;
                document.getElementById('nmgejala_edit').value = nama;

                // Tampilkan form edit dan sembunyikan form tambah
                formTambahGejala.style.display = 'none';
                formEditGejala.style.display = 'block';
                formEditGejala.scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Logika untuk tombol "Batal"
        btnBatalEditGejala.addEventListener('click', function() {
            formEditGejala.style.display = 'none';
            formTambahGejala.style.display = 'block';
        });
    }

    // --- Logika untuk Konfirmasi Hapus Aturan ---
    const deleteAturanButtons = document.querySelectorAll('.btn-delete-aturan');
    deleteAturanButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); 
            const isConfirmed = confirm('Apakah Anda yakin ingin menghapus aturan ini? Ini akan menghapus aturan induk DAN semua gejala yang terkait dengannya.');
            if (isConfirmed) {
                window.location.href = this.href;
            }
        });
    });

});