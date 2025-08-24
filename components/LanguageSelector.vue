<template>
  <USelectMenu 
    v-model="selectedLocale" 
    :options="localeOptions"
    value-attribute="code"
    option-attribute="name"
    @change="changeLanguage"
  >
    <template #leading>
      <UIcon name="i-heroicons-language" class="w-4 h-4" />
    </template>
  </USelectMenu>
</template>

<script setup>
const { locale, setLocale } = useI18n()

const selectedLocale = ref(locale.value)

const localeOptions = [
  { code: 'en', name: 'English', flag: '🇺🇸' },
  { code: 'id', name: 'Bahasa Indonesia', flag: '🇮🇩' }
]

const changeLanguage = (newLocale) => {
  if (newLocale && newLocale !== locale.value) {
    setLocale(newLocale)
    selectedLocale.value = newLocale
    
    // Save preference to localStorage
    localStorage.setItem('preferred-locale', newLocale)
    
    // Force page refresh to apply translations
    window.location.reload()
  }
}

// Load saved preference on mount
onMounted(() => {
  const savedLocale = localStorage.getItem('preferred-locale')
  if (savedLocale && savedLocale !== locale.value) {
    selectedLocale.value = savedLocale
    setLocale(savedLocale)
  }
})
</script>