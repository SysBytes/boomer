import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            publicDirectory: '../',
            input: [
                'resources/sass/main/frontend.scss',
                'resources/sass/app.scss',
                'resources/sass/main.scss',
                'resources/js/codebase/app.js',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: '../build',
        emptyOutDir: true,
    },
});
