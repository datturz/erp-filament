<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="mx-auto h-12 w-12 bg-gray-900 rounded-lg flex items-center justify-center">
        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
      </div>
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        {{ $t('auth.login') }}
      </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <div class="mb-4 flex justify-center">
          <LanguageSelector />
        </div>
        
        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <UFormGroup :label="$t('auth.email')" name="email" required>
              <UInput 
                v-model="form.email" 
                type="email" 
                :placeholder="$t('auth.email')"
                :disabled="loading"
                size="lg"
              />
            </UFormGroup>
          </div>

          <div>
            <UFormGroup :label="$t('auth.password')" name="password" required>
              <UInput 
                v-model="form.password" 
                type="password" 
                :placeholder="$t('auth.password')"
                :disabled="loading"
                size="lg"
              />
            </UFormGroup>
          </div>

          <div class="flex items-center justify-between">
            <UCheckbox v-model="rememberMe" :label="$t('auth.remember_me')" />
          </div>

          <div>
            <UButton 
              type="submit" 
              :loading="loading"
              block 
              size="lg"
            >
              {{ $t('auth.login') }}
            </UButton>
          </div>
        </form>

        <div v-if="error" class="mt-4">
          <UAlert 
            :title="error" 
            color="red" 
            variant="soft"
            :close-button="{ icon: 'i-heroicons-x-mark-20-solid', color: 'gray', variant: 'link', padded: false }"
            @close="error = null"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: false,
  auth: false
})

const { login } = useAuth()
const { $t } = useI18n()

const form = reactive({
  email: '',
  password: ''
})

const rememberMe = ref(false)
const loading = ref(false)
const error = ref(null)

const handleLogin = async () => {
  if (!form.email || !form.password) {
    error.value = $t('auth.fill_all_fields')
    return
  }

  loading.value = true
  error.value = null

  try {
    await login(form)
  } catch (err: any) {
    error.value = err.data?.message || $t('auth.login_failed')
  } finally {
    loading.value = false
  }
}
</script>