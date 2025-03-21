import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
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
        'mw-nora-components': path.resolve(__dirname, 'nds-dev/css/mw-nora-components.scss'),
        'mw-templates': path.resolve(__dirname, 'nds-dev/css/mw-templates.scss'),
        'mw-custom': path.resolve(__dirname, 'nds-dev/css/mw-custom.scss')
      },
      output: {
        assetFileNames: (assetInfo) => {
          return 'assets/[name][extname]';
        }
      }
    }
  },
  server: {
    open: '/demo.html',
  }
});