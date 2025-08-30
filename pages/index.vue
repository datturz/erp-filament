<template>
  <div></div>
</template>

<script setup>
// Redirect to dashboard immediately
if (process.client) {
  window.location.href = '/dashboard'
}
</script>