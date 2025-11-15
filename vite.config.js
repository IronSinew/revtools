import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from "@tailwindcss/vite";
import path from "path";
import run from "vite-plugin-run";


export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
        run([
            {
                name: "enum generator",
                run: ["make", "stub_generate"],
                pattern: ["app/Enums/**/*.php"],
                startup: true,
            }
        ]),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./resources/js"),
            "@volt": path.resolve(__dirname, "./src/volt"),
            "@styles": path.resolve(__dirname, "./resources/css"),
        },
    },
});
