/**
 * PRANATA HR - Progressive Web App (PWA) Controller
 * Handles Service Worker registration, install prompts,
 * and restricts install buttons/banners exclusively to Mobile and Tablet view.
 */

(function () {
    'use strict';

    // State
    let deferredPrompt = null;
    const DISMISS_KEY = 'pranata_pwa_dismissed_until';
    const DISMISS_DURATION = 24 * 60 * 60 * 1000; // 24 hours

    // Standalone Detection Helper
    function checkIsStandalone() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('source') === 'pwa' || urlParams.get('mode') === 'standalone') {
            try { sessionStorage.setItem('is_pwa_standalone', '1'); } catch (e) {}
        }

        let isStored = false;
        try { isStored = (sessionStorage.getItem('is_pwa_standalone') === '1'); } catch (e) {}

        return (
            window.matchMedia('(display-mode: standalone)').matches ||
            window.matchMedia('(display-mode: fullscreen)').matches ||
            window.matchMedia('(display-mode: minimal-ui)').matches ||
            window.navigator.standalone === true ||
            document.referrer.includes('android-app://') ||
            isStored
        );
    }

    // Mobile & Tablet Detection Helper (<= 1024px or Mobile/Tablet UA / iPad Touch)
    function checkIsMobileOrTablet() {
        const ua = navigator.userAgent || '';
        const isMobileOrTabletUA = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Tablet|PlayBook|Silk|(Kindle)|(Tablet;)|(Nexus 7)|(Nexus 10)|(Touch)/i.test(ua) ||
            (Boolean(navigator.maxTouchPoints) && navigator.maxTouchPoints > 2);
        const isMobileOrTabletWidth = window.innerWidth <= 1024;
        return isMobileOrTabletUA || isMobileOrTabletWidth;
    }

    const isStandalone = checkIsStandalone();

    // If running inside installed PWA, strictly hide/remove all install UI and exit
    if (isStandalone) {
        document.documentElement.classList.add('is-pwa-standalone');
        if (document.body) document.body.classList.add('is-pwa-standalone');

        function purgeInstallElements() {
            const selectors = [
                '.pwa-install-trigger',
                '.pwa-install-banner',
                '.pwa-sidebar-btn',
                '.pwa-sidebar-wrapper',
                '.pwa-header-install-btn',
                '#pwaInstallBanner',
                '#pwaIosModal',
                '#pwaAndroidModal'
            ];
            document.querySelectorAll(selectors.join(',')).forEach(el => el.remove());
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', purgeInstallElements);
        } else {
            purgeInstallElements();
        }
        return;
    }

    const isIOS = (
        /iPad|iPhone|iPod/.test(navigator.userAgent) &&
        !window.MSStream
    );

    // 1. Register Service Worker
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
                    console.warn('PWA: ServiceWorker note:', err);
                });
        });
    }

    // 2. Initialize UI on DOM Ready (Only when NOT standalone)
    document.addEventListener('DOMContentLoaded', () => {
        setupInstallUI();
    });

    function setupInstallUI() {
        if (checkIsStandalone()) {
            return;
        }

        updateDeviceVisibility();

        const banner = document.getElementById('pwaInstallBanner');
        let isDismissed = false;
        try {
            const dismissedUntil = localStorage.getItem(DISMISS_KEY);
            isDismissed = dismissedUntil && (Date.now() < parseInt(dismissedUntil, 10));
        } catch (e) {}

        // Show floating bottom banner after 1.2s on mobile & tablet if not dismissed
        if (checkIsMobileOrTablet() && banner && !isDismissed) {
            setTimeout(() => {
                if (!checkIsStandalone() && checkIsMobileOrTablet() && banner) {
                    banner.style.display = 'flex';
                }
            }, 1200);
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
                closeModal(document.getElementById('pwaIosModal'));
                return;
            }

            const closeAndroidBtn = e.target.closest('.pwa-close-android');
            if (closeAndroidBtn) {
                e.preventDefault();
                closeModal(document.getElementById('pwaAndroidModal'));
                return;
            }
        });

        // Close modals on backdrop click
        const iosModal = document.getElementById('pwaIosModal');
        const androidModal = document.getElementById('pwaAndroidModal');
        if (iosModal) {
            iosModal.addEventListener('click', (e) => {
                if (e.target === iosModal) closeModal(iosModal);
            });
        }
        if (androidModal) {
            androidModal.addEventListener('click', (e) => {
                if (e.target === androidModal) closeModal(androidModal);
            });
        }
    }

    function updateDeviceVisibility() {
        const isMobileOrTablet = checkIsMobileOrTablet();
        const headerBtn = document.querySelector('.pwa-header-install-btn');
        const sidebarWrapper = document.querySelector('.pwa-sidebar-wrapper');
        const sidebarBtn = document.querySelector('.pwa-sidebar-btn');
        const banner = document.getElementById('pwaInstallBanner');

        if (isMobileOrTablet) {
            if (headerBtn) headerBtn.classList.add('is-active');
            if (sidebarWrapper) {
                sidebarWrapper.classList.add('is-active');
                sidebarWrapper.style.display = 'block';
            }
            if (sidebarBtn) sidebarBtn.style.display = 'flex';
        } else {
            if (headerBtn) headerBtn.classList.remove('is-active');
            if (sidebarWrapper) {
                sidebarWrapper.classList.remove('is-active');
                sidebarWrapper.style.display = 'none';
            }
            if (sidebarBtn) sidebarBtn.style.display = 'none';
            if (banner) banner.style.display = 'none';
        }
    }

    // Window resize handler to adapt dynamically between desktop and mobile/tablet
    window.addEventListener('resize', () => {
        if (!checkIsStandalone()) {
            updateDeviceVisibility();
        }
    });

    // 3. Listen to beforeinstallprompt (Fired on Android / Chrome HTTPS / localhost)
    window.addEventListener('beforeinstallprompt', (e) => {
        if (checkIsStandalone()) {
            return;
        }

        e.preventDefault();
        deferredPrompt = e;
        window.deferredPWAInstallPrompt = e;

        if (!checkIsMobileOrTablet()) {
            return;
        }

        const headerBtn = document.querySelector('.pwa-header-install-btn');
        if (headerBtn) headerBtn.classList.add('is-active');

        const sidebarWrapper = document.querySelector('.pwa-sidebar-wrapper');
        if (sidebarWrapper) {
            sidebarWrapper.classList.add('is-active');
            sidebarWrapper.style.display = 'block';
        }
        const sidebarBtn = document.querySelector('.pwa-sidebar-btn');
        if (sidebarBtn) sidebarBtn.style.display = 'flex';

        const banner = document.getElementById('pwaInstallBanner');
        let isDismissed = false;
        try {
            const dismissedUntil = localStorage.getItem(DISMISS_KEY);
            isDismissed = dismissedUntil && (Date.now() < parseInt(dismissedUntil, 10));
        } catch (err) {}

        if (banner && !isDismissed) {
            banner.style.display = 'flex';
        }
    });

    // 4. Trigger Install Action
    window.triggerPWAInstall = triggerInstall;
    function triggerInstall() {
        if (deferredPrompt) {
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
            openModal(document.getElementById('pwaIosModal'));
        } else {
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
        try {
            localStorage.setItem(DISMISS_KEY, Date.now() + DISMISS_DURATION);
        } catch (e) {}
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

    // 5. Listen to dynamic display-mode changes
    try {
        const standaloneMedia = window.matchMedia('(display-mode: standalone)');
        standaloneMedia.addEventListener('change', (evt) => {
            if (evt.matches) {
                try { sessionStorage.setItem('is_pwa_standalone', '1'); } catch (e) {}
                document.documentElement.classList.add('is-pwa-standalone');
                if (document.body) document.body.classList.add('is-pwa-standalone');
                document.querySelectorAll('.pwa-install-trigger, .pwa-install-banner, .pwa-sidebar-wrapper, .pwa-header-install-btn, #pwaInstallBanner, #pwaIosModal, #pwaAndroidModal').forEach(el => el.remove());
            }
        });
    } catch (e) {}

    // 6. Listen to appinstalled event
    window.addEventListener('appinstalled', () => {
        console.log('PWA: Aplikasi berhasil diinstal.');
        deferredPrompt = null;
        window.deferredPWAInstallPrompt = null;
        try { sessionStorage.setItem('is_pwa_standalone', '1'); } catch (e) {}
        document.documentElement.classList.add('is-pwa-standalone');
        if (document.body) document.body.classList.add('is-pwa-standalone');

        document.querySelectorAll('.pwa-install-trigger, .pwa-install-banner, .pwa-sidebar-wrapper, .pwa-header-install-btn, #pwaInstallBanner, #pwaIosModal, #pwaAndroidModal').forEach(el => el.remove());

        showInstalledToast();
    });

    function showInstalledToast() {
        const toast = document.createElement('div');
        toast.className = 'pwa-installed-toast';
        toast.innerHTML = '<i class="bi bi-check-circle-fill"></i> Aplikasi PRANATA HR berhasil dipasang!';
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

})();
