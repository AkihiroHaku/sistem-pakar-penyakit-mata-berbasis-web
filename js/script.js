document.addEventListener('DOMContentLoaded', () => {
    // Mencari tombol menu dan menu dropdown di dalam dokumen HTML
    const menuButton = document.getElementById('menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    // Memastikan kedua elemen tersebut ada sebelum menambahkan fungsi
    if (menuButton && mobileMenu) {
        
        // Menambahkan fungsi (event listener) saat tombol menu di-klik
        menuButton.addEventListener('click', (event) => {
            // Mencegah event "klik" menyebar ke elemen lain (seperti window)
            event.stopPropagation(); 
            
            // Logika untuk menampilkan atau menyembunyikan menu
            if (mobileMenu.style.display === 'block') {
                mobileMenu.style.display = 'none';
            } else {
                mobileMenu.style.display = 'block';
            }
        });

        // Menambahkan fungsi untuk menyembunyikan menu jika pengguna mengklik di mana saja di luar area menu
        window.addEventListener('click', () => {
            if (mobileMenu.style.display === 'block') {
                mobileMenu.style.display = 'none';
            }
        });
    }
});

