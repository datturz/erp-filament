<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup>
// App setup
useHead({
  title: 'Pants ERP',
  meta: [
    { name: 'description', content: 'Manufacturing ERP Mobile Interface' },
    { name: 'viewport', content: 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' },
    { name: 'theme-color', content: '#1f2937' }
  ],
  link: [
    { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
  ]
})
</script>