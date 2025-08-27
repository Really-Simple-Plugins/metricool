import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import { tanstackRouter } from '@tanstack/router-vite-plugin'
import { resolve } from 'node:path';

// https://vite.dev/config/
export default defineConfig({
    plugins: [
        tanstackRouter({
            autoCodeSplitting: true,
        }),
        react(),
        tailwindcss(),
    ],
    build: {
        outDir: './build',
        emptyOutDir: true,
        manifest: true,
        sourcemap: true,
        rollupOptions: {
            input: './src/main.tsx',
        },
        modulePreload: {
            polyfill: false,
        },
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, './src'),
        },
    },
});
