import Lenis from 'lenis';
import { initLandingFx } from './fx.js';

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (document.querySelector('.siap-aum-landing')) {
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', initLandingFx, { once: true }) : initLandingFx();
}

$('.btn-toggle-password').on('click', function () {
    var $this = $(this);
    var $input = $this.closest('.input-group, .sa-login-field').find('input');
    var type = $input.attr('type') === 'password' ? 'text' : 'password';
    $input.attr('type', type);
    $this.toggleClass('isax-eye-slash isax-eye');
});

function smoothScroll() {
    if (reduceMotion || !document.querySelector('.siap-aum-landing')) return;
    const lenis = new Lenis({ lerp: 0.09, wheelMultiplier: 1, anchors: { offset: -90 } });
    window.saLenis = lenis;
    const raf = (time) => { lenis.raf(time); requestAnimationFrame(raf); };
    requestAnimationFrame(raf);
}

function scrollProgress() {
    if (!document.querySelector('.siap-aum-landing')) return;
    const bar = document.createElement('div');
    bar.className = 'tb-progress';
    bar.setAttribute('aria-hidden', 'true');
    document.body.append(bar);
    const update = () => {
        const max = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.transform = `scaleX(${max > 0 ? Math.min(1, window.scrollY / max) : 0})`;
    };
    window.addEventListener('scroll', update, { passive: true });
    update();
}

function parallax() {
    const items = Array.from(document.querySelectorAll('[data-parallax]'));
    if (!items.length || reduceMotion) return;
    const update = () => {
        items.forEach((el) => {
            const r = el.parentElement.getBoundingClientRect();
            const center = r.top + r.height / 2 - window.innerHeight / 2;
            el.style.setProperty('--py', `${(-center * Number(el.dataset.parallax)).toFixed(1)}px`);
        });
    };
    window.addEventListener('scroll', () => requestAnimationFrame(update), { passive: true });
    update();
}

// Animasi muncul diputar ulang setiap kali elemen masuk/keluar layar.
function reveals() {
    const targets = document.querySelectorAll(
        '.sa-survey, [data-reveal], .fx-heading, .fx-footer__grid > *, .tb-reveal'
    );
    if (reduceMotion || !('IntersectionObserver' in window)) return;
    targets.forEach((element, index) => {
        element.classList.add('sa-reveal');
        element.style.setProperty('--sa-reveal-delay', (index % 4) * 70 + 'ms');
    });
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => entry.target.classList.toggle('is-visible', entry.isIntersecting));
    }, { threshold: 0.1 });
    targets.forEach((element) => observer.observe(element));
}

document.addEventListener('DOMContentLoaded', function () {
    var navbar = document.querySelector('.sa-navbar-wrap');
    var navbarHoverZone = document.querySelector('.sa-navbar-hover-zone');
    var mobileNavbar = document.querySelector('.navbar.fixed-bottom');
    var navLinks = Array.from(document.querySelectorAll('.sa-navbar__links a[href*="#"]'));
    var lastScrollY = window.scrollY;
    var ticking = false;

    smoothScroll();
    scrollProgress();
    parallax();
    reveals();

    if (navbar) {
        var revealNavbar = function () { navbar.classList.remove('is-hidden'); };
        var updateNavbar = function () {
            var currentScrollY = Math.max(window.scrollY, 0);
            var movingDown = currentScrollY > lastScrollY;
            navbar.classList.toggle('is-scrolled', currentScrollY > 90);
            navbar.classList.toggle('is-hidden', movingDown && currentScrollY > 180);
            if (mobileNavbar) mobileNavbar.classList.toggle('is-hidden', movingDown && currentScrollY > 180);
            lastScrollY = currentScrollY;
            ticking = false;
        };
        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(updateNavbar);
                ticking = true;
            }
        }, { passive: true });
        document.addEventListener('mousemove', function (event) {
            if (event.clientY <= 90) revealNavbar();
        }, { passive: true });
        if (navbarHoverZone) navbarHoverZone.addEventListener('mouseenter', revealNavbar);
        navbar.addEventListener('mouseleave', function () {
            if (window.scrollY > 180) navbar.classList.add('is-hidden');
        });
    }

    var sections = Array.from(document.querySelectorAll('.siap-aum-landing section[id], .sa-footer[id]'));
    if ('IntersectionObserver' in window && navLinks.length) {
        var sectionObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                navLinks.forEach(function (link) {
                    link.classList.toggle('is-active', link.hash === '#' + entry.target.id);
                });
            });
        }, { rootMargin: '-35% 0px -55% 0px' });
        sections.forEach(function (section) { sectionObserver.observe(section); });
        window.addEventListener('scroll', function () {
            if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4) {
                navLinks.forEach(function (link) { link.classList.toggle('is-active', link.hash === '#kontak'); });
            }
        }, { passive: true });
    }

    var carousel = document.querySelector('.tb-services');
    if (carousel) {
        var slides = Array.from(carousel.querySelectorAll('.tb-services__slide'));
        var tabs = Array.from(carousel.querySelectorAll('.tb-services__tab'));
        var media = carousel.querySelector('[data-services-media]');
        var counter = carousel.querySelector('[data-services-no]');
        var previousButton = carousel.querySelector('[data-carousel-prev]');
        var nextButton = carousel.querySelector('[data-carousel-next]');
        var activeSlide = 0;
        var autoplayTimer = null;
        var pointerStartX = null;

        var showSlide = function (index) {
            activeSlide = (index + slides.length) % slides.length;
            slides.forEach(function (slide, i) {
                slide.classList.toggle('is-active', i === activeSlide);
                slide.setAttribute('aria-hidden', i === activeSlide ? 'false' : 'true');
            });
            tabs.forEach(function (tab, i) {
                tab.classList.remove('is-active');
                tab.setAttribute('aria-selected', i === activeSlide ? 'true' : 'false');
            });
            void carousel.offsetWidth;
            tabs[activeSlide].classList.add('is-active');
            counter.textContent = String(activeSlide + 1).padStart(2, '0');
        };
        var stopAutoplay = function () {
            if (autoplayTimer) window.clearInterval(autoplayTimer);
            autoplayTimer = null;
            carousel.classList.add('is-paused');
        };
        var startAutoplay = function () {
            stopAutoplay();
            carousel.classList.remove('is-paused');
            if (!reduceMotion && !document.hidden) {
                autoplayTimer = window.setInterval(function () { showSlide(activeSlide + 1); }, 5600);
            }
        };

        tabs.forEach(function (tab, index) {
            tab.addEventListener('click', function () { showSlide(index); startAutoplay(); });
        });
        previousButton.addEventListener('click', function () { showSlide(activeSlide - 1); startAutoplay(); });
        nextButton.addEventListener('click', function () { showSlide(activeSlide + 1); startAutoplay(); });
        media.addEventListener('mouseenter', stopAutoplay);
        media.addEventListener('mouseleave', function () { showSlide(activeSlide); startAutoplay(); });
        media.addEventListener('pointerdown', function (event) { pointerStartX = event.clientX; });
        media.addEventListener('pointerup', function (event) {
            if (pointerStartX === null) return;
            var distance = event.clientX - pointerStartX;
            if (Math.abs(distance) > 55) { showSlide(activeSlide + (distance < 0 ? 1 : -1)); startAutoplay(); }
            pointerStartX = null;
        });
        media.addEventListener('pointercancel', function () { pointerStartX = null; });
        document.addEventListener('visibilitychange', function () { document.hidden ? stopAutoplay() : startAutoplay(); });
        showSlide(0);
        startAutoplay();
    }
});
