import * as THREE from 'three';
import { buildGate, brickTexture, particles, easeOutBack, ornamentTexture } from './gate3d.js';

// Gapura 3D: tampil saat halaman dibuka -> palang terangkat (fokus email) -> kain poleng (fokus password) -> terbuka (captcha) -> masuk (login).

function polengTexture() {
    const c = document.createElement('canvas');
    c.width = c.height = 512;
    const g = c.getContext('2d');
    const n = 8, s = 512 / n;
    for (let y = 0; y < n; y++) {
        for (let x = 0; x < n; x++) {
            g.fillStyle = (x + y) % 2 ? '#141414' : '#f4f1ea';
            g.fillRect(x * s, y * s, s, s);
            if ((x + y) % 2 === 0) {
                g.fillStyle = 'rgba(120,120,120,.35)';
                g.fillRect(x * s, y * s + s / 2 - 6, s, 12);
            }
        }
    }
    const t = new THREE.CanvasTexture(c);
    t.wrapS = t.wrapT = THREE.RepeatWrapping;
    t.repeat.set(2, 2);
    t.colorSpace = THREE.SRGBColorSpace;
    return t;
}

function doorTexture() {
    const c = document.createElement('canvas');
    c.width = 256; c.height = 360;
    const g = c.getContext('2d');
    g.fillStyle = '#4a2015';
    g.fillRect(0, 0, 256, 360);
    g.strokeStyle = '#d9a441';
    g.lineWidth = 6;
    g.strokeRect(18, 18, 220, 324);
    g.lineWidth = 3;
    [[40, 40, 176, 120], [40, 190, 176, 130]].forEach(([x, y, w, h]) => g.strokeRect(x, y, w, h));
    g.fillStyle = '#d9a441';
    for (let i = 0; i < 6; i++) {
        g.beginPath();
        g.arc(128, 70 + i * 44, 6, 0, Math.PI * 2);
        g.fill();
    }
    const t = new THREE.CanvasTexture(c);
    t.colorSpace = THREE.SRGBColorSpace;
    return t;
}

function buildDoors(gate) {
    const mat = new THREE.MeshStandardMaterial({ map: doorTexture(), roughness: 0.6, metalness: 0.2 });
    const leaves = [-1, 1].map((side) => {
        const pivot = new THREE.Group();
        pivot.position.set(side * 2.7, 0, 0.35);
        const leaf = new THREE.Mesh(new THREE.BoxGeometry(2.2, 3.1, 0.12), mat);
        leaf.position.set(-side * 1.1, 1.55, 0);
        leaf.castShadow = true;
        pivot.add(leaf);
        gate.add(pivot);
        return { pivot, side };
    });
    const bar = new THREE.Mesh(
        new THREE.BoxGeometry(5.6, 0.16, 0.1),
        new THREE.MeshStandardMaterial({ color: '#ff3b2b', emissive: '#ff2a1a', emissiveIntensity: 1.6 })
    );
    const barPivot = new THREE.Group();
    barPivot.position.set(-2.8, 1.7, 0.5);
    bar.position.set(2.8, 0, 0);
    barPivot.add(bar);
    gate.add(barPivot);
    return { leaves, bar, barPivot };
}

function buildCloth() {
    const geo = new THREE.PlaneGeometry(9.2, 8.6, 36, 36);
    const base = geo.attributes.position.array.slice();
    const mat = new THREE.MeshStandardMaterial({ map: polengTexture(), side: THREE.DoubleSide, roughness: 0.9, transparent: true });
    const mesh = new THREE.Mesh(geo, mat);
    mesh.castShadow = true;
    const trim = new THREE.Mesh(
        new THREE.PlaneGeometry(9.3, 0.5, 36, 1),
        new THREE.MeshStandardMaterial({ color: '#e2b13c', emissive: '#7a4a00', emissiveIntensity: 0.4, side: THREE.DoubleSide, transparent: true })
    );
    trim.position.y = 4.1;
    trim.position.z = 0.02;
    mesh.add(trim);
    return { mesh, base, mat, trim };
}

export function mountLoginGate() {
    const canvas = document.getElementById('loginGate');
    if (!canvas) return;
    const stage = canvas.parentElement;
    const email = document.getElementById('email') || document.getElementById('username');
    const password = document.getElementById('password');
    const needsCaptcha = !!document.querySelector('.sa-login-captcha');
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let captchaOk = false;

    let renderer;
    try {
        renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
    } catch (e) {
        stage.classList.add('is-fallback');
        return;
    }
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.shadowMap.enabled = true;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 100);
    const ornament = ornamentTexture(canvas.dataset.ornament);
    const { gate, parts } = buildGate({ brick: brickTexture(), ornament });
    scene.add(gate);
    const doors = buildDoors(gate);
    const cloth = buildCloth();
    scene.add(cloth.mesh);

    const glow = new THREE.Mesh(
        new THREE.PlaneGeometry(5.2, 3.4),
        new THREE.MeshBasicMaterial({ color: '#ffd27a', transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false })
    );
    glow.position.set(0, 1.7, -0.6);
    gate.add(glow);

    const ground = new THREE.Mesh(new THREE.CircleGeometry(9, 64), new THREE.ShadowMaterial({ opacity: 0.35 }));
    ground.rotation.x = -Math.PI / 2;
    ground.receiveShadow = true;
    scene.add(ground);
    const ring = new THREE.Mesh(new THREE.RingGeometry(5.4, 5.48, 128), new THREE.MeshBasicMaterial({ color: '#ff5a3c', transparent: true, opacity: 0.6 }));
    ring.rotation.x = -Math.PI / 2;
    ring.position.y = 0.01;
    scene.add(ring);

    scene.add(new THREE.HemisphereLight('#ffe9dc', '#3a140c', 1.2));
    const key = new THREE.DirectionalLight('#ffe2c8', 3);
    key.position.set(5, 9, 7);
    key.castShadow = true;
    key.shadow.camera.left = key.shadow.camera.bottom = -10;
    key.shadow.camera.right = key.shadow.camera.top = 10;
    scene.add(key);
    const accent = new THREE.PointLight('#ff3b2b', 18, 16, 1.4);
    accent.position.set(0, 2.5, 4);
    scene.add(accent);
    const dust = particles(window.innerWidth < 768 ? 140 : 320);
    scene.add(dust);

    const mouse = { x: 0, y: 0, tx: 0, ty: 0 };
    window.addEventListener('pointermove', (e) => {
        mouse.tx = (e.clientX / window.innerWidth) * 2 - 1;
        mouse.ty = (e.clientY / window.innerHeight) * 2 - 1;
    }, { passive: true });

    let baseZ = 20;
    const resize = () => {
        const w = canvas.clientWidth, h = canvas.clientHeight;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        const vf = THREE.MathUtils.degToRad(camera.fov) / 2;
        const hf = Math.atan(Math.tan(vf) * camera.aspect);
        baseZ = Math.max(5.6 / Math.tan(hf), 4.6 / Math.tan(vf));
        camera.updateProjectionMatrix();
    };
    resize();
    new ResizeObserver(resize).observe(stage);

    let current = -1;
    const passwordVerify = document.getElementById('password_verify');
    let entering = false;
    const compute = () => {
        const active = document.activeElement;
        let s = 0;
        if (entering) s = 4;
        else if (active && active === email) s = 1;
        else if (active && [password, passwordVerify].includes(active)) s = 2;
        else if (needsCaptcha && captchaOk) s = 3;
        if (s !== current) {
            current = s;
            stage.dataset.stage = String(s);
        }
    };
    ['focus', 'blur'].forEach((ev) => {
        [email, password, passwordVerify].forEach((el) => el?.addEventListener(ev, () => setTimeout(compute, 0)));
    });
    const form = email?.closest('form');
    if (form && form.matches('[data-testid="login-form"]')) {
        form.addEventListener('submit', (e) => {
            if (entering) return;
            e.preventDefault();
            entering = true;
            document.activeElement?.blur();
            compute();
            setTimeout(() => HTMLFormElement.prototype.submit.call(form), reduce ? 200 : 1700);
        });
    }
    document.addEventListener('sa:captcha', (e) => { captchaOk = !!e.detail; compute(); });
    compute();

    const anim = { build: 0, cloth: 0, open: 0, lift: 0, lock: 0, raise: 0, enter: 0 };
    const maxDelay = Math.max(...parts.map((m) => m.userData.delay)) + 0.8;
    let last = performance.now();
    const frame = (now) => {
        requestAnimationFrame(frame);
        const dt = Math.min(0.1, (now - last) / 1000);
        last = now;
        const t = now / 1000;
        mouse.x += (mouse.tx - mouse.x) * 0.06;
        mouse.y += (mouse.ty - mouse.y) * 0.06;

        const speed = reduce ? 50 : 1;
        anim.build = THREE.MathUtils.clamp(anim.build + dt * speed * (1.4), 0, maxDelay);
        const approach = (k, target, rate) => { anim[k] += (target - anim[k]) * Math.min(1, dt * rate * speed); };
        approach('lock', current < 3 ? 1 : 0, 3);
        approach('raise', current === 1 ? 1 : 0, 3.2);
        approach('enter', current >= 4 ? 1 : 0, 1.6);
        approach('cloth', current === 2 || current === 3 ? 1 : 0, 2.6);
        approach('open', current >= 3 ? 1 : 0, 1.8);
        approach('lift', current >= 3 ? 1 : 0, 1.4);

        parts.forEach((m) => {
            const p = THREE.MathUtils.clamp((anim.build - m.userData.delay) / 0.7, 0, 1);
            m.scale.setScalar(Math.max(0.001, p < 1 ? easeOutBack(p) : 1));
        });
        const built = THREE.MathUtils.clamp(anim.build / maxDelay, 0, 1);
        doors.leaves.forEach(({ pivot, side }) => {
            pivot.scale.setScalar(Math.max(0.001, built));
            pivot.rotation.y = -side * (1.7 * anim.open + 0.25 * anim.enter);
        });
        doors.bar.scale.set(Math.max(0.001, built * anim.lock), Math.max(0.001, built * anim.lock), 1);
        doors.barPivot.rotation.z = anim.raise * 1.45;
        doors.bar.material.emissiveIntensity = 1.2 + Math.sin(t * 4) * 0.5;
        glow.material.opacity = Math.min(1, anim.open * (0.55 + Math.sin(t * 2) * 0.1) + anim.enter * 0.45);
        accent.color.lerpColors(new THREE.Color('#ff3b2b'), new THREE.Color('#ffc65a'), anim.open);

        const drop = (1 - anim.cloth) * 10 + anim.lift * 12;
        cloth.mesh.position.set(0, 4.25 + drop, 1.25 + anim.lift * 1.5);
        cloth.mesh.rotation.x = -anim.lift * 0.5;
        cloth.mat.opacity = Math.max(0, Math.min(1, anim.cloth * 1.4) - anim.lift);
        cloth.trim.material.opacity = cloth.mat.opacity;
        cloth.mesh.visible = cloth.mat.opacity > 0.01;
        if (cloth.mesh.visible) {
            const pos = cloth.mesh.geometry.attributes.position;
            const arr = pos.array;
            for (let i = 0; i < arr.length; i += 3) {
                const x = cloth.base[i], y = cloth.base[i + 1];
                const fall = (4.3 - y) / 8.6;
                arr[i + 2] = Math.sin(x * 1.4 + t * 1.8) * 0.12 * fall + Math.sin(y * 2 + t * 2.4) * 0.05 + Math.cos(x * 0.4) * 0.5;
            }
            pos.needsUpdate = true;
            cloth.mesh.geometry.computeVertexNormals();
        }

        gate.rotation.y = mouse.x * 0.3 + (reduce ? 0 : Math.sin(t * 0.35) * 0.06);
        camera.position.set(mouse.x * 0.6 * (1 - anim.enter), 4.4 - mouse.y * 0.3 - anim.enter * 2.4, baseZ - anim.open * 1.6 - anim.enter * (baseZ - 1.4));
        camera.lookAt(0, 3.7 - anim.enter * 2, 0);
        ring.scale.setScalar(1 + Math.sin(t * 1.6) * 0.03);
        ring.material.color.lerpColors(new THREE.Color('#ff5a3c'), new THREE.Color('#ffc65a'), anim.open);
        const arr = dust.geometry.attributes.position.array;
        for (let i = 1; i < arr.length; i += 3) { arr[i] += 0.004 + anim.open * 0.01; if (arr[i] > 11) arr[i] = 0; }
        dust.geometry.attributes.position.needsUpdate = true;
        renderer.render(scene, camera);
    };
    requestAnimationFrame(frame);
    stage.classList.add('is-ready');
}

document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', mountLoginGate, { once: true }) : mountLoginGate();
