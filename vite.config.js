import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";
import Components from "unplugin-vue-components/vite";
import { PrimeVueResolver } from "@primevue/auto-import-resolver";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.ts"],
            refresh: true,
        }),
        tailwindcss(),
        Components({
            resolvers: [PrimeVueResolver()],
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        // Generate unique hashes for each build to prevent caching issues
        rollupOptions: {
            output: {
                // Add hash to filenames for cache busting
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('primevue')) return 'vendor-primevue';
                        if (id.includes('apexcharts')) return 'vendor-apexcharts';
                        if (id.includes('pusher-js') || id.includes('laravel-echo')) return 'vendor-realtime';
                        if (id.includes('axios')) return 'vendor-axios';
                        return 'vendor'; // All other node_modules
                    }
                }
            }
        }
    }
});
