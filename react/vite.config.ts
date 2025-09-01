import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import { tanstackRouter } from '@tanstack/router-vite-plugin';
import { devtools } from '@tanstack/devtools-vite';
// import { resolve } from 'node:path';

// https://vite.dev/config/
export default defineConfig({
    plugins: [
        devtools(),
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
        alias: [
            {find: '@/lib/utils.ts', replacement: './src/components/src/lib/utils.ts'},
            {find: 'tailwind-merge', replacement: './src/components/node_modules/tailwind-merge/src/index.ts'},
            {find: '@', replacement: './src'},
        ],
    dedupe: ["react", "react-dom"]
    },
});
