document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('menu-button');
    const closeButton = document.getElementById('close-btn');
    const sidebar = document.getElementById('sidebar-menu');
    const mainContent = document.getElementById('main-content-wrapper');

    // Pastikan semua elemen ditemukan sebelum menambahkan event listener
    if (menuButton && closeButton && sidebar && mainContent) {
        
        // Fungsi untuk membuka sidebar
        function openSidebar() {
            sidebar.style.left = '0';
            mainContent.style.marginLeft = '280px';
        }

        // Fungsi untuk menutup sidebar
        function closeSidebar() {
            sidebar.style.left = '-280px';
            mainContent.style.marginLeft = '0';
        }

        // Event listener untuk tombol buka
        menuButton.addEventListener('click', openSidebar);

        // Event listener untuk tombol tutup
        closeButton.addEventListener('click', closeSidebar);
    }

    // --- Logika Modal Pengaturan User ---
    const openModalButton = document.getElementById('open-settings-modal');
    const closeModalButton = document.getElementById('modal-close-btn');
    const modalOverlay = document.getElementById('settings-modal');
    const modalContent = document.querySelector('.modal-content');

    if (openModalButton && closeModalButton && modalOverlay) {
        
        // --- Fungsi Buka/Tutup Modal ---
        function openModal() {
            modalOverlay.style.display = 'flex';
            setTimeout(() => { // Memberi sedikit jeda agar transisi terlihat
                modalOverlay.style.opacity = '1';
                modalContent.style.transform = 'scale(1)';
            }, 10);
        }

        function closeModal() {
            modalOverlay.style.opacity = '0';
            modalContent.style.transform = 'scale(0.95)';
            setTimeout(() => { // Tunggu transisi selesai sebelum menyembunyikan
                modalOverlay.style.display = 'none';
            }, 300);
        }

        // Cek jika tombolnya ada (hanya ada jika user login)
        if(openModalButton) {
            openModalButton.addEventListener('click', function(e) {
                e.preventDefault(); // Mencegah link '#' menggulir ke atas
                openModal();
            });
        }

        closeModalButton.addEventListener('click', closeModal);
        
        // Tutup modal jika mengklik di luar area kontennya
        modalOverlay.addEventListener('click', function(event) {
            if (event.target === modalOverlay) {
                closeModal();
            }
        });

        // --- Logika untuk Tab di dalam Modal ---
        const tabLinks = document.querySelectorAll('.modal-tab-link');
        const tabContents = document.querySelectorAll('.modal-tab-content');

        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tabId = this.getAttribute('data-tab');

                // Hapus kelas 'active' dari semua link dan konten
                tabLinks.forEach(item => item.classList.remove('active'));
                tabContents.forEach(item => item.classList.remove('active'));

                // Tambahkan kelas 'active' ke link dan konten yang diklik
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    }
});

