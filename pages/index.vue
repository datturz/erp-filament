<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="mx-auto h-12 w-12 bg-gray-900 rounded-lg flex items-center justify-center">
        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
      </div>
      <h1 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        {{ $config.public.appName }}
      </h1>
      <p class="mt-2 text-center text-sm text-gray-600">
        Manufacturing ERP Mobile Interface
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <!-- Features Overview -->
        <div class="space-y-4">
          <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-900">Inventory Management</h3>
              <p class="text-sm text-gray-500">Real-time stock tracking</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-900">Mobile POS</h3>
              <p class="text-sm text-gray-500">Quick checkout & sales</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-900">Batch Tracking</h3>
              <p class="text-sm text-gray-500">Production management</p>
            </div>
          </div>
        </div>

        <div class="mt-8">
          <UButton 
            to="/login" 
            block 
            size="lg"
            class="w-full"
          >
            Get Started
          </UButton>
        </div>

        <div class="mt-6 text-center">
          <p class="text-xs text-gray-500">
            Offline capable • Progressive Web App
          </p>
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

const config = useRuntimeConfig()
</script>