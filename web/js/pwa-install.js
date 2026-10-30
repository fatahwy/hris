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

    // 1. Register Service Worker (requires HTTPS or localhost in modern browsers)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            let swPath = '/sw.js';
            const manifestLink = document.querySelector('link[rel="manifest"]');
            if (manifestLink) {
                const href = manifestLink.getAttribute('href');
                swPath = href.replace(/manifest\.json(\?.*)?$/, 'sw.js');
            }

            navigator.serviceWorker.register(swPath)
                .then((registration) => {
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
                    console.warn('PWA: ServiceWorker note (Requires HTTPS or localhost):', err);
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
        const androidModal = document.getElementById('pwaAndroidModal');
        const sidebarBtn = document.querySelector('.pwa-sidebar-btn');

        // Check if banner was recently dismissed
        const dismissedUntil = localStorage.getItem(DISMISS_KEY);
        const isDismissed = dismissedUntil && (Date.now() < parseInt(dismissedUntil, 10));

        // Always show install triggers on mobile
        if (isMobile) {
            document.querySelectorAll('.pwa-install-trigger').forEach(el => {
                el.style.display = '';
            });
            if (sidebarBtn) {
                sidebarBtn.style.display = 'flex';
            }

            // Show floating bottom banner after 1.2s on mobile if not dismissed
            if (banner && !isDismissed) {
                setTimeout(() => {
                    banner.style.display = 'flex';
                }, 1200);
            }
        }

        // Global click listener for PWA buttons
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.pwa-install-trigger');
            if (btn) {
                e.preventDefault();
                triggerInstall();
                return;
            }

            const closeBtn = e.target.closest('.pwa-dismiss-btn');
            if (closeBtn) {
                e.preventDefault();
                dismissBanner();
                return;
            }

            const closeIosBtn = e.target.closest('.pwa-close-ios');
            if (closeIosBtn) {
                e.preventDefault();
                closeModal(iosModal);
                return;
            }

            const closeAndroidBtn = e.target.closest('.pwa-close-android');
            if (closeAndroidBtn) {
                e.preventDefault();
                closeModal(androidModal);
                return;
            }
        });

        // Close modals on backdrop click
        if (iosModal) {
            iosModal.addEventListener('click', (e) => {
                if (e.target === iosModal) {
                    closeModal(iosModal);
                }
            });
        }
        if (androidModal) {
            androidModal.addEventListener('click', (e) => {
                if (e.target === androidModal) {
                    closeModal(androidModal);
                }
            });
        }
    }

    // 3. Listen to beforeinstallprompt (Fired on Android / Chrome HTTPS / localhost)
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome mini-infobar
        e.preventDefault();
        deferredPrompt = e;
        window.deferredPWAInstallPrompt = e;

        // Show install button & banner
        document.querySelectorAll('.pwa-install-trigger').forEach(el => {
            el.style.display = '';
        });
        const sidebarBtn = document.querySelector('.pwa-sidebar-btn');
        if (sidebarBtn) {
            sidebarBtn.style.display = 'flex';
        }

        const banner = document.getElementById('pwaInstallBanner');
        const dismissedUntil = localStorage.getItem(DISMISS_KEY);
        const isDismissed = dismissedUntil && (Date.now() < parseInt(dismissedUntil, 10));

        if (banner && !isDismissed) {
            banner.style.display = 'flex';
        }
    });

    // 4. Trigger Install Action
    window.triggerPWAInstall = triggerInstall;
    function triggerInstall() {
        if (deferredPrompt) {
            // Native Android / Chromium prompt
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted PWA install');
                    hideBanner();
                } else {
                    console.log('User dismissed PWA install');
                }
                deferredPrompt = null;
                window.deferredPWAInstallPrompt = null;
            });
        } else if (isIOS) {
            // Show iOS guide modal
            openModal(document.getElementById('pwaIosModal'));
        } else {
            // Chrome on HTTP / fallback guide modal
            openModal(document.getElementById('pwaAndroidModal'));
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

    function openModal(modal) {
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeModal(modal) {
        if (modal) {
            modal.style.display = 'none';
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
