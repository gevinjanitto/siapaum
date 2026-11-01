// Efek interaktif landing: kursor kustom, spotlight kartu, tombol magnetik, tilt, counter, teks bertahap.
const fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Kursor kustom hanya aktif di dalam hero.
function cursor() {
    const hero = document.querySelector('.lx-hero');
    if (!fine || reduce || !hero) return;
    const dot = document.createElement('div');
    const ring = document.createElement('div');
    dot.className = 'lx-cursor-dot';
    ring.className = 'lx-cursor-ring';
    document.body.append(dot, ring);
    document.documentElement.classList.add('lx-has-cursor');
    hero.addEventListener('pointerenter', () => document.documentElement.classList.add('lx-cursor-on'));
    hero.addEventListener('pointerleave', () => document.documentElement.classList.remove('lx-cursor-on'));
    let x = innerWidth / 2, y = innerHeight / 2, rx = x, ry = y;
    addEventListener('pointermove', (e) => { x = e.clientX; y = e.clientY; }, { passive: true });
    document.addEventListener('pointerover', (e) => {
        ring.classList.toggle('is-hover', !!e.target.closest('a, button, input, select, summary, [data-tilt]'));
    });
    document.addEventListener('pointerdown', () => ring.classList.add('is-down'));
    document.addEventListener('pointerup', () => ring.classList.remove('is-down'));
    const loop = () => {
        rx += (x - rx) * 0.16;
        ry += (y - ry) * 0.16;
        dot.style.transform = `translate3d(${x}px, ${y}px, 0)`;
        ring.style.transform = `translate3d(${rx}px, ${ry}px, 0)`;
        requestAnimationFrame(loop);
    };
    loop();
}

function heroSpotlight() {
    const hero = document.querySelector('.lx-hero');
    if (!hero) return;
    const bg = hero.querySelector('.lx-hero__bg');
    hero.addEventListener('pointermove', (e) => {
        const r = bg.getBoundingClientRect();
        hero.style.setProperty('--hx', `${e.clientX - r.left}px`);
        hero.style.setProperty('--hy', `${e.clientY - r.top}px`);
    }, { passive: true });
}

function spotlight() {
    document.querySelectorAll('[data-spot]').forEach((el) => {
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            el.style.setProperty('--mx', `${e.clientX - r.left}px`);
            el.style.setProperty('--my', `${e.clientY - r.top}px`);
        }, { passive: true });
    });
}

function magnetic() {
    if (!fine || reduce) return;
    document.querySelectorAll('[data-magnetic]').forEach((el) => {
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            const dx = e.clientX - (r.left + r.width / 2);
            const dy = e.clientY - (r.top + r.height / 2);
            el.style.transform = `translate(${dx * 0.22}px, ${dy * 0.3}px)`;
        });
        el.addEventListener('pointerleave', () => { el.style.transform = ''; });
    });
}

function tilt() {
    if (!fine || reduce) return;
    document.querySelectorAll('[data-tilt]').forEach((el) => {
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            const px = (e.clientX - r.left) / r.width - 0.5;
            const py = (e.clientY - r.top) / r.height - 0.5;
            el.style.transform = `perspective(900px) rotateX(${-py * 7}deg) rotateY(${px * 9}deg) translateY(-4px)`;
        });
        el.addEventListener('pointerleave', () => { el.style.transform = ''; });
    });
}

function counters() {
    const els = document.querySelectorAll('[data-count]');
    if (!('IntersectionObserver' in window)) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const target = Number(el.dataset.count) || 0;
            if (reduce || target === 0) return;
            const t0 = performance.now();
            const step = (now) => {
                const p = Math.min(1, (now - t0) / 1400);
                el.textContent = Math.round(target * (1 - Math.pow(1 - p, 4))).toLocaleString('en-US');
                if (p < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        });
    }, { threshold: 0.4 });
    els.forEach((el) => io.observe(el));
}

function splitWords() {
    document.querySelectorAll('[data-split]').forEach((root) => {
        let i = 0;
        const walk = (node) => {
            [...node.childNodes].forEach((child) => {
                if (child.nodeType === 3) {
                    const frag = document.createDocumentFragment();
                    child.textContent.split(/(\s+)/).forEach((part) => {
                        if (!part) return;
                        if (/^\s+$/.test(part)) { frag.append(part); return; }
                        const w = document.createElement('span');
                        w.className = 'lx-word';
                        w.innerHTML = `<span style="--i:${i++}">${part}</span>`;
                        frag.append(w);
                    });
                    child.replaceWith(frag);
                } else if (child.nodeType === 1) {
                    walk(child);
                }
            });
        };
        walk(root);
        root.classList.add('is-split');
        if (root.dataset.split === 'reveal') root.classList.add('tb-split');
    });
}

export function initLandingFx() {
    splitWords();
    cursor();
    heroSpotlight();
    spotlight();
    magnetic();
    tilt();
    counters();

    const canvas = document.getElementById('gate3d');
    if (canvas) {
        import('./gate3d.js').then(({ mountGate }) => mountGate(canvas, { ornament: canvas.dataset.ornament, reduceMotion: reduce }))
            .catch(() => canvas.parentElement.classList.add('is-fallback'));
    }
}
