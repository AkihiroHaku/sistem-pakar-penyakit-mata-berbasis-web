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
});

