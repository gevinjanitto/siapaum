import {
    defineConfig,
    normalizePath
} from 'vite';
import laravel, {
    refreshPaths
} from 'laravel-vite-plugin';
import path, {
    dirname,
    resolve
} from 'path';

const __dirname = dirname(__filename);

export default defineConfig({
    server: {
        // Vite tetap menerima koneksi dari container Docker, sementara browser
        // lokal mengambil asset melalui alamat yang dapat diaksesnya.
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        origin: 'http://localhost:5173',
        hmr: {
            host: 'localhost',
            port: 5173,
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/landing/landing.js',
                'resources/js/dashboard/dashboard.js',
                'resources/js/pages/register.js',
                'resources/js/landing/login3d.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': normalizePath(__dirname, 'resources/js'),
            '~bootstrap': resolve(__dirname, 'node_modules/bootstrap'),
            '~bootstrap-icons': resolve(__dirname, 'node_modules/bootstrap-icons'),
            '~select2': resolve(__dirname, 'node_modules/select2'),
            '~perfect-scrollbar': resolve(__dirname, 'node_modules/perfect-scrollbar'),
            '~flatpickr': resolve(__dirname, 'node_modules/flatpickr'),
        },
    },
});
