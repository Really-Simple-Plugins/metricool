import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import { tanstackRouter } from "@tanstack/router-vite-plugin";
import { devtools } from "@tanstack/devtools-vite";
import path from "path";

const wordpressImportsToGlobals = () => ({
    name: 'wordpress-imports-to-globals',
    transform(code: string, id: string) {
        let copyOfCode = code.slice(0);
        const isOwnPluginFile = id.includes("react/src");
        if (isOwnPluginFile) {
            const arrayOfImportStatements = copyOfCode.match(/import(.*);/g);
            const arrayOfWordpressImports = arrayOfImportStatements?.filter((importStatement) => importStatement.includes("@wordpress"));
            arrayOfWordpressImports?.forEach((wpImport) => {
                const allImportedFunctionsAsString = wpImport.match(/{(.*)}/);
                const fullPackageName = wpImport.match(/@(.*)"/);
                if (allImportedFunctionsAsString && fullPackageName) {
                    const splitPackageDomainFromSubPackage = fullPackageName[1].split("/");
                    const subPackage = splitPackageDomainFromSubPackage[splitPackageDomainFromSubPackage.length - 1];
                    const arrayOfImportedFunctions = allImportedFunctionsAsString[1].split(",");
                    arrayOfImportedFunctions.forEach((importedFunction) => {
                        const globalReplaceString = `wp.${subPackage}.${importedFunction.trim()}`;
                        /* Removing the initial import statement by replacing it with an empty string.
                        * Then replacing each invocation of the imported function with the global.
                        * Including the opening `(` in the lookup so the __ part of comments like @__PURE__ aren't replaced. */
                        copyOfCode = copyOfCode.replace(wpImport, "").replaceAll(`${importedFunction.trim()}(`, `${globalReplaceString}(`);
                    })
                }
            });
        }
        return copyOfCode
    }
});

// https://vite.dev/config/
export default defineConfig({
    plugins: [
        devtools(),
        tanstackRouter(),
        react(),
        tailwindcss(),
        wordpressImportsToGlobals(),
    ],
    build: {
        outDir: "./build",
        emptyOutDir: true,
        manifest: true,
        sourcemap: false,
        rollupOptions: {
            input: "./src/main.tsx",
            output: {
                assetFileNames: "assets/[hash][extname]",
                chunkFileNames: "assets/[hash].js",
                entryFileNames: "assets/[hash].js",
            },
        },
        modulePreload: {
            polyfill: false,
        },
        minify: "terser",
        terserOptions: {
            format: {
                comments: /^translators:/
            },
            compress: {
                passes: 2,
            },
        }
    },
    base: "./",
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./src"),
        },
        dedupe: ["react", "react-dom"]
    },
});
