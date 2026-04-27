import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import { writeFileSync, unlinkSync, existsSync, mkdirSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));

const BUILD_DIR = resolve(__dirname, 'assets/build');
const HOT_FILE  = resolve(BUILD_DIR, 'hot');

/**
 * Маркер dev-режима для PHP: пишем `assets/build/hot` при `vite dev`,
 * удаляем при выходе и при `vite build`.
 */
function hotFilePlugin() {
  return {
    name: 'mrent-hot-file',
    apply: 'serve',
    configureServer(server) {
      if (!existsSync(BUILD_DIR)) mkdirSync(BUILD_DIR, { recursive: true });
      const port = server.config.server.port;
      writeFileSync(HOT_FILE, `http://localhost:${port}`);
      const cleanup = () => {
        try { if (existsSync(HOT_FILE)) unlinkSync(HOT_FILE); } catch (_) {}
      };
      process.on('exit', cleanup);
      process.on('SIGINT',  () => { cleanup(); process.exit(); });
      process.on('SIGTERM', () => { cleanup(); process.exit(); });
      process.on('SIGHUP',  () => { cleanup(); process.exit(); });
    },
    buildStart() {
      if (existsSync(HOT_FILE)) unlinkSync(HOT_FILE);
    },
  };
}

export default defineConfig(({ command }) => ({
  // В dev — пути относительно корня (http://localhost:5173/main.js).
  // В build — публичный URL темы для подстановки в манифест/asset-теги.
  base: command === 'build' ? '/wp-content/themes/m-rent/assets/build/' : '/',

  root: 'src',

  build: {
    outDir: '../assets/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: resolve(__dirname, 'src/main.js'),
    },
  },

  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    cors: true,
    origin: 'http://localhost:5173',
    hmr: {
      host: 'localhost',
    },
  },

  plugins: [
    tailwindcss(),
    hotFilePlugin(),
  ],
}));
