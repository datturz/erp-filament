import { ref, computed, readonly } from 'vue'
import enTranslations from '~/locales/en.json'
import idTranslations from '~/locales/id.json'

const locale = ref('en')

const translations = {
  en: enTranslations,
  id: idTranslations
}

export const useI18n = () => {
  const currentTranslations = computed(() => translations[locale.value])

  const $t = (key: string): string => {
    const keys = key.split('.')
    let value: any = currentTranslations.value

    for (const k of keys) {
      value = value?.[k]
    }

    return value || key
  }

  const setLocale = (newLocale: 'en' | 'id') => {
    locale.value = newLocale
  }

  return {
    locale: readonly(locale),
    $t,
    t: $t,
    setLocale
  }
}