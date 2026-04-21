import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // Konfigurasi server sebenarnya tidak wajib ditulis di sini
    // karena Vite secara default akan berjalan di:
    // host: 'localhost'
    // port: 5173
    // 
    // Gunakan konfigurasi ini hanya jika ingin:
    // - akses dari network (host: '0.0.0.0')
    // - mengganti port default
    // - kebutuhan khusus (Docker, VM, remote dev, dll)
    /*
    server: {
        host: '0.0.0.0',
        port: 5173,
    },
    */

    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/scan.js',
            ],
            refresh: true,
        }),
    ],
});
