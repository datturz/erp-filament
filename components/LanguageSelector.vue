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
const { locale, locales, setLocale } = useI18n()

const selectedLocale = ref(locale.value)

const localeOptions = computed(() => {
  return locales.value.map(locale => ({
    code: locale.code,
    name: locale.name,
    flag: locale.code === 'id' ? '🇮🇩' : '🇺🇸'
  }))
})

const changeLanguage = async (newLocale) => {
  if (newLocale && newLocale !== locale.value) {
    await setLocale(newLocale)
    
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

watch(locale, (newLocale) => {
  selectedLocale.value = newLocale
})
</script>