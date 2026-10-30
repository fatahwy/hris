/**
 * PRANATA HR - Progressive Web App (PWA) Controller
 * Handles Service Worker registration, install prompts (Android/Chromium & iOS),
 * and dynamic install banner UI.
 */

(function () {
    'use strict';

    // State
    let deferredPrompt = null;
    const DISMISS_KEY = 'pranata_pwa_dismissed_until';
    const DISMISS_DURATION = 24 * 60 * 60 * 1000; // 24 hours

    // Checks
    const isStandalone = (
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true ||
        document.referrer.includes('android-app://')
    );

    const isIOS = (
        /iPad|iPhone|iPod/.test(navigator.userAgent) &&
        !window.MSStream
    );

    const isMobile = (
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
        window.innerWidth <= 768
    );

    // If already running as installed app, no need for install prompt
    if (isStandalone) {
        document.documentElement.classList.add('is-pwa-standalone');
        return;
    }

    // 1. Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            let swPath = 'sw.js';
            const manifestLink = document.querySelector('link[rel="manifest"]');
            if (manifestLink) {
                const href = manifestLink.getAttribute('href');
                swPath = href.replace(/manifest\.json(\?.*)?$/, 'sw.js');
            }

            navigator.serviceWorker.register(swPath)
                .then((registration) => {
                    // Check for updates
                    registration.onupdatefound = () => {
                        const installingWorker = registration.installing;
                        if (installingWorker) {
                            installingWorker.onstatechange = () => {
                                if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    console.log('PWA: Versi baru tersedia.');
                                }
                            };
                        }
                    };
                })
                .catch((err) => {
                    console.warn('PWA: Registrasi Service Worker gagal:', err);
                });
        });
    }

    // 2. Initialize UI on DOM Ready
    document.addEventListener('DOMContentLoaded', () => {
        setupInstallUI();
    });

    function setupInstallUI() {
        const banner = document.getElementById('pwaInstallBanner');
        const iosModal = document.getElementById('pwaIosModal');
        const installBtns = document.querySelectorAll('.pwa-install-trigger');
        const sidebarBtn = document.querySelector('.pwa-sidebar-btn');

        // Check if banner was recently dismissed
        const dismissedUntil = localStorage.getItem(DISMISS_KEY);
        const isDismissed = dismissedUntil && (Date.now() < parseInt(dismissedUntil, 10));

        // Setup install buttons (in banner and custom triggers)
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.pwa-install-trigger');
            if (btn) {
                e.preventDefault();
                triggerInstall();
            }

            const closeBtn = e.target.closest('.pwa-dismiss-btn');
            if (closeBtn) {
                e.preventDefault();
                dismissBanner();
            }

            const closeIosBtn = e.target.closest('.pwa-close-ios');
            if (closeIosBtn) {
                e.preventDefault();
                closeIosInstructions();
            }
        });

        // Close iOS modal if clicking backdrop
        if (iosModal) {
            iosModal.addEventListener('click', (e) => {
                if (e.target === iosModal) {
                    closeIosInstructions();
                }
            });
        }

        // Show sidebar button always if mobile or installable
        if (sidebarBtn) {
            sidebarBtn.style.display = 'flex';
        }

        // iOS Safari Handling
        if (isIOS && !isStandalone) {
            if (sidebarBtn) {
                sidebarBtn.style.display = 'flex';
            }
            if (banner && !isDismissed && isMobile) {
                setTimeout(() => {
                    banner.style.display = 'flex';
                }, 1500);
            }
        }
    }

    // 3. Listen to beforeinstallprompt (Android Chrome / Edge / etc.)
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent default mini-infobar
        e.preventDefault();
        deferredPrompt = e;
        window.deferredPWAInstallPrompt = e;

        // Make install triggers visible
        document.querySelectorAll('.pwa-install-trigger').forEach(el => {
            el.style.display = '';
        });
        const sidebarBtn = document.querySelector('.pwa-sidebar-btn');
        if (sidebarBtn) {
            sidebarBtn.style.display = 'flex';
        }

        // Show the banner on mobile if not recently dismissed
        const banner = document.getElementById('pwaInstallBanner');
        const dismissedUntil = localStorage.getItem(DISMISS_KEY);
        const isDismissed = dismissedUntil && (Date.now() < parseInt(dismissedUntil, 10));

        if (banner && !isDismissed) {
            setTimeout(() => {
                banner.style.display = 'flex';
            }, 1000);
        }
    });

    // 4. Trigger Install Action
    window.triggerPWAInstall = triggerInstall;
    function triggerInstall() {
        if (deferredPrompt) {
            // Android / Chromium native install dialog
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted PWA installation');
                    hideBanner();
                } else {
                    console.log('User dismissed PWA installation');
                }
                deferredPrompt = null;
                window.deferredPWAInstallPrompt = null;
            });
        } else if (isIOS) {
            // Show iOS guide
            showIosInstructions();
        } else {
            // Other browsers instructions or already installed
            showGenericInstructions();
        }
    }

    function dismissBanner() {
        const banner = document.getElementById('pwaInstallBanner');
        if (banner) {
            banner.classList.add('pwa-closing');
            setTimeout(() => {
                banner.style.display = 'none';
                banner.classList.remove('pwa-closing');
            }, 250);
        }
        localStorage.setItem(DISMISS_KEY, Date.now() + DISMISS_DURATION);
    }

    function hideBanner() {
        const banner = document.getElementById('pwaInstallBanner');
        if (banner) {
            banner.style.display = 'none';
        }
    }

    function showIosInstructions() {
        const iosModal = document.getElementById('pwaIosModal');
        if (iosModal) {
            iosModal.style.display = 'flex';
        }
    }

    function closeIosInstructions() {
        const iosModal = document.getElementById('pwaIosModal');
        if (iosModal) {
            iosModal.style.display = 'none';
        }
    }

    function showGenericInstructions() {
        // Fallback for browsers when beforeinstallprompt hasn't fired yet
        if (window.Swal) {
            Swal.fire({
                title: 'Install PRANATA HR',
                html: `
                    <div class="text-start fs-6" style="line-height: 1.6;">
                        <p class="mb-2">Untuk menginstal aplikasi di browser Anda:</p>
                        <ol class="ps-3 mb-0">
                            <li>Buka menu opsi browser Anda (ikon <b>titik tiga ⋮</b> di kanan atas).</li>
                            <li>Pilih <b>"Install Aplikasi"</b> atau <b>"Tambahkan ke Layar Utama"</b>.</li>
                        </ol>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#2b5fe1'
            });
        } else {
            alert('Untuk menginstal aplikasi: Buka menu titik tiga di browser Anda, lalu pilih "Install Aplikasi" atau "Tambahkan ke Layar Utama".');
        }
    }

    // 5. Listen to appinstalled event
    window.addEventListener('appinstalled', () => {
        console.log('PWA: Aplikasi berhasil diinstal.');
        deferredPrompt = null;
        window.deferredPWAInstallPrompt = null;
        hideBanner();

        const sidebarBtn = document.querySelector('.pwa-sidebar-btn');
        if (sidebarBtn) {
            sidebarBtn.style.display = 'none';
        }

        // Show brief success toast
        showInstalledToast();
    });

    function showInstalledToast() {
        const toast = document.createElement('div');
        toast.className = 'pwa-installed-toast';
        toast.innerHTML = '<i class="bi bi-check-circle-fill"></i> Aplikasi PRANATA HR berhasil di-install!';
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

})();
