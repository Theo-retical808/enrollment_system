import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/theme.css',
                'resources/css/layout.css',
                'resources/css/auth.css',
                'resources/css/admin.css',
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/theme.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
