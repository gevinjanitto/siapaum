import * as THREE from 'three';

// Model 3D prosedural Gerbang Kabupaten Tabanan (candi bentar + atap lumbung).
const WOOD = '#3d1f16';

export function brickTexture() {
    const c = document.createElement('canvas');
    c.width = c.height = 256;
    const g = c.getContext('2d');
    g.fillStyle = '#3a1d15';
    g.fillRect(0, 0, 256, 256);
    const h = 16, w = 42;
    for (let row = 0; row < 256 / h; row++) {
        const off = row % 2 ? w / 2 : 0;
        for (let x = -w; x < 256 + w; x += w) {
            const l = 42 + Math.random() * 12;
            g.fillStyle = `hsl(${12 + Math.random() * 8}, ${40 + Math.random() * 12}%, ${l}%)`;
            g.fillRect(x + off + 1, row * h + 1, w - 2, h - 2);
        }
    }
    const t = new THREE.CanvasTexture(c);
    t.wrapS = t.wrapT = THREE.RepeatWrapping;
    t.colorSpace = THREE.SRGBColorSpace;
    return t;
}

function archShape(a, hgt) {
    const s = new THREE.Shape();
    s.moveTo(-a, 0);
    s.bezierCurveTo(-a * 1.04, hgt * 0.62, -a * 0.5, hgt * 0.94, 0, hgt);
    s.bezierCurveTo(a * 0.5, hgt * 0.94, a * 1.04, hgt * 0.62, a, 0);
    s.lineTo(-a, 0);
    return s;
}

// Panel lengkung: latar kisi merah prosedural + ornamen foto yang dipusatkan dengan tepi lembut.
export function ornamentTexture(url) {
    const W = 1024, H = 480;
    const c = document.createElement('canvas');
    c.width = W; c.height = H;
    const g = c.getContext('2d');
    const paint = () => {
        g.fillStyle = '#7d1524';
        g.fillRect(0, 0, W, H);
        g.fillStyle = 'rgba(40, 4, 10, .55)';
        for (let y = -40; y < H + 40; y += 40) {
            for (let x = (y / 40) % 2 ? 20 : 0; x < W + 40; x += 40) {
                g.beginPath(); g.moveTo(x, y - 14); g.lineTo(x + 12, y); g.lineTo(x, y + 14); g.lineTo(x - 12, y); g.closePath(); g.fill();
            }
        }
        g.fillStyle = 'rgba(255, 210, 200, .18)';
        for (let y = -20; y < H + 40; y += 40) {
            for (let x = (y / 40 + .5) % 2 ? 0 : 20; x < W + 40; x += 40) { g.beginPath(); g.arc(x, y, 3, 0, Math.PI * 2); g.fill(); }
        }
    };
    paint();
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    const img = new Image();
    img.onload = () => {
        const sx = 150, sy = 95, sw = 724, sh = 485;
        const dw = 680, dh = dw * sh / sw, dx = (W - dw) / 2, dy = H - dh - 6;
        const tmp = document.createElement('canvas');
        tmp.width = dw; tmp.height = dh;
        const t = tmp.getContext('2d');
        t.drawImage(img, sx, sy, sw, sh, 0, 0, dw, dh);
        t.globalCompositeOperation = 'destination-in';
        const mask = t.createRadialGradient(dw / 2, dh * .7, dh * .3, dw / 2, dh * .7, dw * .52);
        mask.addColorStop(0, '#000');
        mask.addColorStop(.78, 'rgba(0,0,0,.9)');
        mask.addColorStop(1, 'rgba(0,0,0,0)');
        t.fillStyle = mask;
        t.fillRect(0, 0, dw, dh);
        paint();
        g.drawImage(tmp, dx, dy);
        tex.needsUpdate = true;
    };
    img.src = url;
    return tex;
}

export function buildGate(textures) {
    const gate = new THREE.Group();
    const parts = [];
    const brickMat = new THREE.MeshStandardMaterial({ map: textures.brick, color: '#ffffff', roughness: 0.85 });
    const darkMat = new THREE.MeshStandardMaterial({ color: '#4a2619', roughness: 0.7 });
    const woodMat = new THREE.MeshStandardMaterial({ color: WOOD, roughness: 0.55, metalness: 0.15 });
    const stoneMat = new THREE.MeshStandardMaterial({ color: '#e9dfcf', roughness: 0.5, emissive: '#3a2a1a', emissiveIntensity: 0.2 });

    const add = (parent, geo, mat, x, y, z, delay) => {
        const m = new THREE.Mesh(geo, mat);
        m.position.set(x, y, z);
        m.castShadow = m.receiveShadow = true;
        m.userData.delay = delay;
        m.scale.setScalar(0.001);
        parent.add(m);
        parts.push(m);
        return m;
    };

    const tower = (scale, x, z, baseDelay) => {
        const t = new THREE.Group();
        t.position.set(x, 0, z);
        t.scale.setScalar(scale);
        const tiers = [[2.1, 0.45], [1.65, 2.9], [1.95, 0.22], [1.45, 0.8], [1.65, 0.16], [1.15, 0.7], [1.3, 0.14], [0.85, 0.6], [0.55, 0.5]];
        let y = 0;
        tiers.forEach(([w, h], i) => {
            const geo = new THREE.BoxGeometry(w, h, w);
            add(t, geo, i % 2 === 0 && i > 1 ? darkMat : brickMat, 0, y + h / 2, 0, baseDelay + i * 0.07);
            if (i >= 3 && i % 2 === 1) {
                [[-1, -1], [1, -1], [-1, 1], [1, 1]].forEach(([sx, sz], k) => {
                    add(t, new THREE.BoxGeometry(0.2, 0.34, 0.2), brickMat, sx * w / 2, y + h + 0.12, sz * w / 2, baseDelay + i * 0.07 + k * 0.02);
                });
            }
            y += h;
        });
        add(t, new THREE.ConeGeometry(0.2, 0.7, 8), darkMat, 0, y + 0.35, 0, baseDelay + 0.7);
        add(t, new THREE.BoxGeometry(0.8, 1.6, 0.12), darkMat, 0, 1.35, 0.83, baseDelay + 0.3);
        add(t, new THREE.BoxGeometry(1.0, 0.18, 0.2), stoneMat, 0, 2.25, 0.85, baseDelay + 0.35);
        const disc = new THREE.CylinderGeometry(0.075, 0.075, 0.04, 14);
        disc.rotateX(Math.PI / 2);
        for (let r = 0; r < 5; r++) {
            [-0.62, 0.62].forEach((dx) => add(t, disc, stoneMat, dx, 0.85 + r * 0.5, 0.84, baseDelay + 0.4 + r * 0.04));
        }
        gate.add(t);
        return t;
    };

    tower(1, -3.75, 0, 0);
    tower(1, 3.75, 0, 0.12);
    tower(0.46, 0, 0.25, 0.5);

    add(gate, new THREE.BoxGeometry(7.9, 0.5, 1.3), woodMat, 0, 3.6, 0, 0.9);
    add(gate, new THREE.BoxGeometry(5.6, 0.16, 1.1), darkMat, 0, 3.26, 0, 0.95);

    const arch = new THREE.Group();
    arch.position.set(0, 3.83, -0.1);
    gate.add(arch);
    const outer = archShape(3.7, 3.55);
    const inner = archShape(3.32, 3.12);
    outer.holes.push(new THREE.Path(inner.getPoints(48)));
    const frameGeo = new THREE.ExtrudeGeometry(outer, { depth: 0.7, bevelEnabled: true, bevelThickness: 0.06, bevelSize: 0.05, bevelSegments: 3, curveSegments: 48 });
    add(arch, frameGeo, woodMat, 0, 0, 0, 1.05);

    const panelGeo = new THREE.ShapeGeometry(inner, 48);
    const pos = panelGeo.attributes.position;
    const uv = panelGeo.attributes.uv;
    for (let i = 0; i < pos.count; i++) uv.setXY(i, (pos.getX(i) + 3.32) / 6.64, pos.getY(i) / 3.12);
    const panelMat = new THREE.MeshStandardMaterial({ map: textures.ornament, emissive: '#ffffff', emissiveMap: textures.ornament, emissiveIntensity: 0.28, roughness: 0.6 });
    const panel = add(arch, panelGeo, panelMat, 0, 0, 0.4, 1.15);
    add(arch, new THREE.ConeGeometry(0.09, 0.6, 6), woodMat, 0, 3.78, 0.35, 1.3);

    return { gate, parts, panelMat, panel };
}

export function particles(count) {
    const geo = new THREE.BufferGeometry();
    const p = new Float32Array(count * 3);
    const col = new Float32Array(count * 3);
    const palette = [new THREE.Color('#ff4a36'), new THREE.Color('#ffb547'), new THREE.Color('#e1251b')];
    for (let i = 0; i < count; i++) {
        p[i * 3] = (Math.random() - 0.5) * 22;
        p[i * 3 + 1] = Math.random() * 11;
        p[i * 3 + 2] = (Math.random() - 0.5) * 12;
        const c = palette[i % 3];
        col.set([c.r, c.g, c.b], i * 3);
    }
    geo.setAttribute('position', new THREE.BufferAttribute(p, 3));
    geo.setAttribute('color', new THREE.BufferAttribute(col, 3));
    const mat = new THREE.PointsMaterial({ size: 0.05, vertexColors: true, transparent: true, opacity: 0.55, depthWrite: false });
    return new THREE.Points(geo, mat);
}

export const easeOutBack = (x) => 1 + 2.2 * Math.pow(x - 1, 3) + 1.2 * Math.pow(x - 1, 2);

export function mountGate(canvas, opts) {
    const stage = canvas.parentElement;
    let renderer;
    try {
        renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true, powerPreference: 'high-performance' });
    } catch (e) {
        stage.classList.add('is-fallback');
        return;
    }
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    const scene = new THREE.Scene();
    scene.fog = new THREE.Fog('#f7efed', 26, 52);
    const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 100);

    const ornament = ornamentTexture(opts.ornament);
    const { gate, parts, panelMat } = buildGate({ brick: brickTexture(), ornament });
    scene.add(gate);

    const fadeCanvas = document.createElement('canvas');
    fadeCanvas.width = fadeCanvas.height = 256;
    const fg = fadeCanvas.getContext('2d');
    const grad = fg.createRadialGradient(128, 128, 20, 128, 128, 128);
    grad.addColorStop(0, '#fff');
    grad.addColorStop(1, '#000');
    fg.fillStyle = grad;
    fg.fillRect(0, 0, 256, 256);
    const ground = new THREE.Mesh(new THREE.CircleGeometry(12, 64), new THREE.MeshStandardMaterial({ color: '#f3e6e3', roughness: 0.55, metalness: 0.05, transparent: true, alphaMap: new THREE.CanvasTexture(fadeCanvas) }));
    ground.rotation.x = -Math.PI / 2;
    ground.receiveShadow = true;
    scene.add(ground);
    const ring = new THREE.Mesh(new THREE.RingGeometry(5.6, 5.66, 128), new THREE.MeshBasicMaterial({ color: '#ff3b2b', transparent: true, opacity: 0.55 }));
    ring.rotation.x = -Math.PI / 2;
    ring.position.y = 0.01;
    scene.add(ring);
    const ring2 = ring.clone();
    ring2.material = ring.material.clone();
    ring2.scale.setScalar(1.35);
    ring2.material.opacity = 0.18;
    scene.add(ring2);

    scene.add(new THREE.HemisphereLight('#fff4ec', '#c9b5ae', 1.1));
    const key = new THREE.DirectionalLight('#ffe2c8', 3.2);
    key.position.set(5, 9, 7);
    key.castShadow = true;
    key.shadow.mapSize.set(1024, 1024);
    key.shadow.camera.left = key.shadow.camera.bottom = -10;
    key.shadow.camera.right = key.shadow.camera.top = 10;
    scene.add(key);
    const rim = new THREE.DirectionalLight('#ffd0c0', 1.2);
    rim.position.set(-7, 6, -8);
    scene.add(rim);
    const cursorLight = new THREE.PointLight('#ff4a36', 14, 18, 1.4);
    cursorLight.position.set(0, 4, 5);
    scene.add(cursorLight);

    const dust = particles(window.innerWidth < 768 ? 160 : 380);
    scene.add(dust);

    const mouse = { x: 0, y: 0, tx: 0, ty: 0 };
    const onMove = (e) => {
        mouse.tx = (e.clientX / window.innerWidth) * 2 - 1;
        mouse.ty = (e.clientY / window.innerHeight) * 2 - 1;
    };
    window.addEventListener('pointermove', onMove, { passive: true });

    const resize = () => {
        const w = canvas.clientWidth, h = canvas.clientHeight;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        const vf = THREE.MathUtils.degToRad(camera.fov) / 2;
        const hf = Math.atan(Math.tan(vf) * camera.aspect);
        const far = Math.max(5.6 / Math.tan(hf), 4.35 / Math.tan(vf));
        camera.position.set(0, 4.7, far);
        camera.lookAt(0, 3.45, 0);
        camera.updateProjectionMatrix();
    };
    resize();
    new ResizeObserver(resize).observe(stage);

    let visible = true;
    let start = performance.now();
    new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting && !visible) start = performance.now();
        visible = entry.isIntersecting;
    }, { threshold: 0.05 }).observe(stage);

    const still = opts.reduceMotion;
    const frame = (now) => {
        requestAnimationFrame(frame);
        if (!visible) return;
        const t = (now - start) / 1000;
        mouse.x += (mouse.tx - mouse.x) * 0.06;
        mouse.y += (mouse.ty - mouse.y) * 0.06;

        parts.forEach((m) => {
            const p = still ? 1 : Math.min(1, Math.max(0, (t - 0.25 - m.userData.delay) / 0.7));
            m.scale.setScalar(Math.max(0.001, p < 1 ? easeOutBack(p) : 1));
        });

        gate.rotation.y = mouse.x * 0.4 + (still ? 0 : Math.sin(t * 0.35) * 0.08);
        gate.rotation.x = mouse.y * 0.05;
        gate.position.y = still ? 0 : Math.sin(t * 0.8) * 0.06;
        cursorLight.position.x += (mouse.x * 7 - cursorLight.position.x) * 0.08;
        cursorLight.position.y += (5 - mouse.y * 4 - cursorLight.position.y) * 0.08;
        panelMat.emissiveIntensity = 0.26 + Math.max(0, 0.3 - Math.hypot(mouse.x, mouse.y + 0.3) * 0.3);
        ring.scale.setScalar(1 + Math.sin(t * 1.6) * 0.03);
        ring2.material.opacity = 0.12 + (Math.sin(t * 1.6) + 1) * 0.06;

        const arr = dust.geometry.attributes.position.array;
        for (let i = 1; i < arr.length; i += 3) {
            arr[i] += 0.004;
            if (arr[i] > 11) arr[i] = 0;
        }
        dust.geometry.attributes.position.needsUpdate = true;
        dust.rotation.y = t * 0.02 + mouse.x * 0.1;

        renderer.render(scene, camera);
    };
    requestAnimationFrame(frame);
    stage.classList.add('is-ready');
}
