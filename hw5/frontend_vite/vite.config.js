import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  base: '/hw5/',
  plugins: [vue()],
  resolve: {
    alias:{
      "@" : path.resolve(__dirname,"./src"),
      "@components" : path.resolve(__dirname,"./src/components"),
      "@functions" : path.resolve(__dirname,"./src/functions"),
    },
  }
})

