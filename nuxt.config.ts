export default defineNuxtConfig({
  devtools: { enabled: true },
  
  modules: [
    '@nuxt/ui',
    '@pinia/nuxt',
    '@vueuse/nuxt',
    '@vite-pwa/nuxt',
    '@nuxt/image'
  ],

  // PWA Configuration
  pwa: {
    registerType: 'autoUpdate',
    workbox: {
      navigateFallback: '/',
      globPatterns: ['**/*.{js,css,html,png,svg,ico}'],
    },
    client: {
      installPrompt: true,
    },
    manifest: {
      name: 'Pants ERP Mobile',
      short_name: 'PantsERP',
      description: 'Mobile interface for Pants Manufacturing ERP System',
      theme_color: '#1f2937',
      background_color: '#ffffff',
      display: 'standalone',
      orientation: 'portrait',
      scope: '/',
      start_url: '/',
      icons: [
        {
          src: '/icon-192x192.png',
          sizes: '192x192',
          type: 'image/png',
        },
        {
          src: '/icon-512x512.png',
          sizes: '512x512',
          type: 'image/png',
        }
      ]
    }
  },

  // Runtime config for API
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api/v1',
      appName: process.env.NUXT_PUBLIC_APP_NAME || 'Pants ERP',
      enableOffline: process.env.NUXT_PUBLIC_ENABLE_OFFLINE === 'true',
      apiTimeout: parseInt(process.env.NUXT_PUBLIC_API_TIMEOUT || '30000')
    }
  },
  
  // Nitro config for Railway deployment
  nitro: {
    preset: 'node-server',
    port: process.env.PORT || 3000,
    host: '0.0.0.0'
  },

  // CSS Framework
  css: ['~/assets/css/main.css'],

  // Auto-import
  components: {
    dirs: ['~/components']
  },

  // TypeScript
  typescript: {
    strict: true
  },

})