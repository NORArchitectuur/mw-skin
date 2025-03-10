import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
  base: '/skins/NORA/dist/',
  css: {
    preprocessorOptions: {
      scss: {
        quietDeps: true,
      }
    }
  },
  resolve: {
    alias: {
      '~@utrecht': path.resolve(__dirname, 'node_modules/@utrecht'),
    }
  },
  build: {
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'index.html'),
        'mw-nora-components': path.resolve(__dirname, 'src/css/mw-nora-components.scss'),
        'mw-custom': path.resolve(__dirname, 'src/css/mw-custom.scss')
      },
      output: {
        assetFileNames: (assetInfo) => {
          return 'assets/[name][extname]';
        }
      }
    }
  }
});