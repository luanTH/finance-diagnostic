import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // Importante para o Vue funcionar
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({ // Configuração para o Vue processar os Single File Components (.vue)
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,         // <--- FORÇA A PORTA 5173
        strictPort: true,   // <--- IMPEDE PULAR PARA 5174
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true, // Adicione isso para garantir que o Docker detecte mudanças no Windows/Mac
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
