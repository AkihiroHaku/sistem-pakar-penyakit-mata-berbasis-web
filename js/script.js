document.addEventListener('DOMContentLoaded', function () {
    // Sidebar
    const menuButton = document.getElementById('menu-button');
    const closeSidebarButton = document.getElementById('close-btn');
    const sidebar = document.getElementById('sidebar-menu');
    const mainContent = document.getElementById('main-content-wrapper');
    const overlay = document.getElementById('sidebar-overlay');

    // Modal Pengaturan
    const openSettingsModalButton = document.getElementById('open-settings-modal');
    const closeSettingsModalButton = document.getElementById('modal-close-btn');
    const settingsModalOverlay = document.getElementById('settings-modal');
    const settingsModalContent = document.querySelector('#settings-modal .modal-content');
    const tabLinks = document.querySelectorAll('.modal-tab-link');
    const tabContents = document.querySelectorAll('.modal-tab-content');

    // Modal Logout
    const openLogoutModalButton = document.getElementById('open-logout-modal');
    const closeLogoutModalButton = document.getElementById('logout-modal-close-btn');
    const cancelLogoutButton = document.getElementById('logout-cancel-btn');
    const logoutModalOverlay = document.getElementById('logout-modal');
    const logoutModalContent = document.querySelector('#logout-modal .modal-content');

    // Dropdown Profil
    const profileOptionsBtn = document.getElementById('profile-options-btn');
    const profileDropdown = document.getElementById('profile-dropdown');

    // Pencarian Gejala
    const searchInput = document.getElementById('gejala-search');
    const symptomItems = document.querySelectorAll('.symptom-item');

    // Validasi Password (Signup)
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const errorMessage = document.getElementById('password-mismatch-error');
    const form = document.getElementById('signup-form');

    // Ikon Mata (Password)
    const togglePasswordIcons = document.querySelectorAll('.toggle-password');

    // ===================================
    // === LOGIKA SIDEBAR RESPONSIVE  ===
    // ===================================
    if (menuButton && closeSidebarButton && sidebar && mainContent && overlay) {

        function openSidebar() {
            if (window.innerWidth <= 768) {
                // Di HP: Tampilkan overlay, JANGAN dorong konten
                sidebar.style.left = '0';
                overlay.classList.add('active');
            } else {
                // Di Desktop: Dorong konten
                sidebar.style.left = '0';
                mainContent.style.marginLeft = '280px';
            }
        }

        function closeSidebar() {
            if (window.innerWidth <= 768) {
                // Di HP: Sembunyikan overlay
                sidebar.style.left = '-280px';
                overlay.classList.remove('active');
            } else {
                // Di Desktop: Kembalikan konten
                sidebar.style.left = '-280px';
                mainContent.style.marginLeft = '0';
            }
        }

        menuButton.addEventListener('click', openSidebar);
        closeSidebarButton.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
    }

    // ===================================
    // === LOGIKA UNTUK MODAL PENGATURAN ===
    // ===================================
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
    // === LOGIKA UNTUK DROPDOWN PROFIL ===
    // ============================================
    if (profileOptionsBtn && profileDropdown) {
        profileOptionsBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        window.addEventListener('click', function (event) {
            if (profileDropdown.classList.contains('show') && !profileOptionsBtn.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    }

    // ===================================
    // === LOGIKA UNTUK PENCARIAN GEJALA ===
    // ===================================
    if (searchInput && symptomItems) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = searchInput.value.toLowerCase();

            symptomItems.forEach(item => {
                const label = item.querySelector('label');
                if (label) {
                    const labelText = label.textContent.toLowerCase();
                    if (labelText.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        });
    }

    // ===========================================
    // === LOGIKA UNTUK VALIDASI PASSWORD SAMA ===
    // ===========================================
    if (passwordInput && confirmPasswordInput && errorMessage && form) {
        function validatePasswords() {
            if (confirmPasswordInput.value.length > 0) {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    errorMessage.textContent = 'Password tidak sama!';
                    confirmPasswordInput.style.borderColor = '#e74c3c';
                    return false;
                } else {
                    errorMessage.textContent = '';
                    confirmPasswordInput.style.borderColor = '#2ecc71';
                }
            } else {
                errorMessage.textContent = '';
                confirmPasswordInput.style.borderColor = '#ccc';
            }
            return true;
        }

        passwordInput.addEventListener('keyup', validatePasswords);
        confirmPasswordInput.addEventListener('keyup', validatePasswords);

        form.addEventListener('submit', function (event) {
            if (passwordInput.value !== confirmPasswordInput.value) {
                event.preventDefault();
                errorMessage.textContent = 'Harap pastikan password konfirmasi sudah sama.';
            }
        });
    }

    // ======================================
    // === LOGIKA UNTUK TOGGLE PASSWORD MATA ===
    // ======================================
    togglePasswordIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const passwordField = icon.closest('.password-wrapper').querySelector('input');

            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });

    // ===================================
    // === LOGIKA FORM UBAH PASSWORD ====
    // ===================================
    (function () {
        // Elemen2 di tab Profile (pastikan ID sama dengan HTML barumu)
        const btnShow = document.getElementById('show-password-form-btn');
        const changePwdForm = document.getElementById('change-password-form');
        const msg = document.getElementById('profile-message');
        const flag = document.getElementById('change_password');
        const btnCancel = document.getElementById('cancel-password-change-btn');

        const oldPwd = document.getElementById('old_password');
        const newPwd = document.getElementById('new_password');
        const confPwd = document.getElementById('confirm_new_password');

        if (!btnShow || !changePwdForm) return; // kalau tab/elemen belum ada

        const fields = [oldPwd, newPwd, confPwd];

        function setEnabled(on) {
            fields.forEach(el => {
                el.disabled = !on;
                if (on) el.setAttribute('required', 'required');
                else { el.removeAttribute('required'); el.value = ''; }
            });
            flag.value = on ? '1' : '0';
            changePwdForm.style.display = on ? 'block' : 'none';
            btnShow.textContent = on ? 'Batalkan Ubah Password' : 'Ubah Password';
            if (!on) msg.innerHTML = '';
        }

        // awal tersembunyi
        setEnabled(false);

        btnShow.addEventListener('click', () => setEnabled(flag.value !== '1'));
        if (btnCancel) btnCancel.addEventListener('click', () => setEnabled(false));

        // validasi ringan
        changePwdForm.addEventListener('submit', (e) => {
            if (flag.value !== '1') return; // tidak sedang ubah
            if (newPwd.value.trim().length < 8) {
                e.preventDefault();
                msg.innerHTML = '<p style="color:#e74c3c;">Password baru minimal 8 karakter.</p>';
                return;
            }
            if (newPwd.value !== confPwd.value) {
                e.preventDefault();
                msg.innerHTML = '<p style="color:#e74c3c;">Konfirmasi password tidak cocok.</p>';
            }
            changePwdForm.hidden = ion;
        });
    })();

    // ===================================
    // === TOGGLE TEMA: TERANG / GELAP ===
    // ===================================
    (function () {
        const btnLight = document.getElementById('theme-light');
        const btnDark = document.getElementById('theme-dark');
        const btnSystem = document.getElementById('theme-system');
        if (!btnLight || !btnDark || !btnSystem) return;

        const mediaDark = window.matchMedia('(prefers-color-scheme: dark)');

        function setActive(mode) {
            // reset
            [btnLight, btnDark, btnSystem].forEach(b => b.classList.remove('active'));
            // set
            ({ light: btnLight, dark: btnDark, system: btnSystem }[mode]).classList.add('active');
        }

        function applyTheme(mode, { save = true } = {}) {
            // mode: 'light' | 'dark' | 'system'
            const effectiveDark = (mode === 'dark') || (mode === 'system' && mediaDark.matches);
            document.body.classList.toggle('dark-mode', effectiveDark);
            setActive(mode);
            if (save) localStorage.setItem('theme', mode);
        }

        // Dengarkan perubahan tema OS saat mode 'system'
        mediaDark.addEventListener('change', () => {
            const current = localStorage.getItem('theme') || 'system';
            if (current === 'system') applyTheme('system', { save: false });
        });

        // Klik tombol
        btnLight.addEventListener('click', () => applyTheme('light'));
        btnDark.addEventListener('click', () => applyTheme('dark'));
        btnSystem.addEventListener('click', () => applyTheme('system'));

        // Inisialisasi dari localStorage (default: system)
        const saved = localStorage.getItem('theme') || 'system';
        applyTheme(saved, { save: false });
    })();

}); // <-- AKHIR DARI DOMContentLoaded