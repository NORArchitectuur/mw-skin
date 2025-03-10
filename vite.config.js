import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
  base: '/skins/NORA/dist/',
  root: 'nds-dev',
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
    outDir: path.resolve(__dirname, 'dist'),
    rollupOptions: {
      input: {
        'mw-nora-components': path.resolve(__dirname, 'nora-design-system/css/mw-nora-components.scss'),
        'mw-custom': path.resolve(__dirname, 'nora-design-system/css/mw-custom.scss')
      },
      output: {
        assetFileNames: (assetInfo) => {
          return 'assets/[name][extname]';
        }
      }
    }
  }
});