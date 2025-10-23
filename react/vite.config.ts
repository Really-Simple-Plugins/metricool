import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import { tanstackRouter } from "@tanstack/router-vite-plugin";
import { devtools } from "@tanstack/devtools-vite";

// https://vite.dev/config/
export default defineConfig({
    plugins: [
        devtools(),
        tanstackRouter(),
        react(),
        tailwindcss(),
    ],
    build: {
        outDir: "./build",
        emptyOutDir: true,
        manifest: true,
        sourcemap: false,
        rollupOptions: {
            input: "./src/main.tsx",
        },
        modulePreload: {
            polyfill: false,
        },
    },
    base: "./",
    resolve: {
        alias: [
            { find: "@", replacement: "./src" },
        ],
        dedupe: ["react", "react-dom"]
    },
});
