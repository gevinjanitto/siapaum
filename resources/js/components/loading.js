const MIN_VISIBLE = 650;
const T0 = performance.now();

const animatePercent = () => {
    const pct = document.querySelector('.sa-preloader__pct');
    if (!pct) return;
    const start = performance.now();
    const tick = (now) => {
        const p = Math.min(1, (now - start) / MIN_VISIBLE);
        pct.textContent = Math.round((1 - Math.pow(1 - p, 3)) * 100) + '%';
        if (p < 1 && document.body.contains(pct)) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
};

const hidePageLoader = () => {
    document.querySelectorAll('.loader-container').forEach((loader) => {
        if (loader.classList.contains('is-leaving')) return;
        loader.classList.add('is-leaving');
        document.documentElement.classList.add('sa-loaded');
        window.setTimeout(() => loader.remove(), 900);
    });
};

// Loader tampil minimal sebentar agar animasinya terlihat, lalu hilang begitu DOM siap.
const scheduleHide = () => {
    const wait = Math.max(0, MIN_VISIBLE - (performance.now() - T0));
    window.setTimeout(hidePageLoader, wait);
};

animatePercent();
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scheduleHide, { once: true });
} else {
    scheduleHide();
}

// Pengaman jika ada script lain yang menghambat inisialisasi halaman.
window.setTimeout(hidePageLoader, 2500);
