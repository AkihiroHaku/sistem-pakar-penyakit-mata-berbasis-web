document.addEventListener('DOMContentLoaded', function () {

    // ===========================
    // === LOGIKA UNTUK SIDEBAR ===
    // ===========================
    const menuButton = document.getElementById('menu-button');
    const closeSidebarButton = document.getElementById('close-btn');
    const sidebar = document.getElementById('sidebar-menu');
    const mainContent = document.getElementById('main-content-wrapper');

    if (menuButton && closeSidebarButton && sidebar && mainContent) {
        function openSidebar() {
            sidebar.style.left = '0';
            mainContent.style.marginLeft = '280px';
        }
        function closeSidebar() {
            sidebar.style.left = '-280px';
            mainContent.style.marginLeft = '0';
        }
        menuButton.addEventListener('click', openSidebar);
        closeSidebarButton.addEventListener('click', closeSidebar);
    }

    // ===================================
    // === LOGIKA UNTUK MODAL PENGATURAN ===
    // ===================================
    const openSettingsModalButton = document.getElementById('open-settings-modal');
    const closeSettingsModalButton = document.getElementById('modal-close-btn');
    const settingsModalOverlay = document.getElementById('settings-modal');
    const settingsModalContent = document.querySelector('#settings-modal .modal-content');

    if (openSettingsModalButton && closeSettingsModalButton && settingsModalOverlay) {

        function openSettingsModal() {
            settingsModalOverlay.style.display = 'flex';
            setTimeout(() => {
                settingsModalOverlay.style.opacity = '1';
                settingsModalContent.style.transform = 'scale(1)';
            }, 10);
        }

        function closeSettingsModal() {
            settingsModalOverlay.style.opacity = '0';
            settingsModalContent.style.transform = 'scale(0.95)';
            setTimeout(() => {
                settingsModalOverlay.style.display = 'none';
            }, 300);
        }

        openSettingsModalButton.addEventListener('click', function (e) {
            e.preventDefault();
            openSettingsModal();
        });

        closeSettingsModalButton.addEventListener('click', closeSettingsModal);

        settingsModalOverlay.addEventListener('click', function (event) {
            if (event.target === settingsModalOverlay) {
                closeSettingsModal();
            }
        });

        const tabLinks = document.querySelectorAll('.modal-tab-link');
        const tabContents = document.querySelectorAll('.modal-tab-content');

        tabLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const tabId = this.getAttribute('data-tab');
                tabLinks.forEach(item => item.classList.remove('active'));
                tabContents.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    }

    // =================================
    // === LOGIKA UNTUK MODAL LOGOUT ===
    // =================================
    const openLogoutModalButton = document.getElementById('open-logout-modal');
    const closeLogoutModalButton = document.getElementById('logout-modal-close-btn');
    const cancelLogoutButton = document.getElementById('logout-cancel-btn');
    const logoutModalOverlay = document.getElementById('logout-modal');
    const logoutModalContent = document.querySelector('#logout-modal .modal-content');

    if (openLogoutModalButton && logoutModalOverlay) {
        function openLogoutModal() {
            logoutModalOverlay.style.display = 'flex';
            setTimeout(() => {
                logoutModalOverlay.style.opacity = '1';
                logoutModalContent.style.transform = 'scale(1)';
            }, 10);
        }

        function closeLogoutModal() {
            logoutModalOverlay.style.opacity = '0';
            logoutModalContent.style.transform = 'scale(0.95)';
            setTimeout(() => {
                logoutModalOverlay.style.display = 'none';
            }, 300);
        }

        openLogoutModalButton.addEventListener('click', function (e) {
            e.preventDefault();
            openLogoutModal();
        });

        if (closeLogoutModalButton) {
            closeLogoutModalButton.addEventListener('click', closeLogoutModal);
        }
        if (cancelLogoutButton) {
            cancelLogoutButton.addEventListener('click', closeLogoutModal);
        }

        logoutModalOverlay.addEventListener('click', function (event) {
            if (event.target === logoutModalOverlay) {
                closeLogoutModal();
            }
        });
    }

    // ============================================
    // === LOGIKA BARU UNTUK DROPDOWN PROFIL ===
    // ============================================
    const profileOptionsBtn = document.getElementById('profile-options-btn');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (profileOptionsBtn && profileDropdown) {
        profileOptionsBtn.addEventListener('click', function (event) {
            event.stopPropagation(); // Mencegah window.click dieksekusi
            profileDropdown.classList.toggle('show');
        });

        // Menutup dropdown jika klik di luar
        window.addEventListener('click', function (event) {
            if (!profileOptionsBtn.contains(event.target) && profileDropdown.classList.contains('show')) {
                profileDropdown.classList.remove('show');
            }
        });
    }
});

const searchInput = document.getElementById('gejala-search');
const symptomItems = document.querySelectorAll('.symptom-item');

if (searchInput && symptomItems) {
    searchInput.addEventListener('keyup', function () {
        const searchTerm = searchInput.value.toLowerCase();

        symptomItems.forEach(item => {
            const label = item.querySelector('label');
            if (label) {
                const labelText = label.textContent.toLowerCase();
                // Jika teks label mengandung kata kunci pencarian, tampilkan
                if (labelText.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    // Jika tidak, sembunyikan
                    item.style.display = 'none';
                }
            }
        });
    });
}