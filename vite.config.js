import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/theme.css',
                'resources/css/landing/theme.css',
                'resources/js/app.js',
                'resources/css/landing/app.css',
                'resources/css/game/app.css',
                'resources/js/game/app.js'
            ],
            refresh: true,
        }),
    ],
});
