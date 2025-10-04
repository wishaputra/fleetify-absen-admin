<script setup>
import { ref } from 'vue'
import api from '@/utils/api'
import { router } from '@inertiajs/vue3'

const form = ref({
  departement_name: '',
  max_clock_in_time: '09:00',
  max_clock_out_time: '17:00',
})
const saving = ref(false)
const error = ref('')
const fieldErrors = ref({})

async function submit() {
  saving.value = true; error.value = ''; fieldErrors.value = {}
  try {
    await api.post('/departments', form.value)
    router.visit('/departments')
  } catch (e) {
    if (e.response?.status === 422) fieldErrors.value = e.response.data.errors || {}
    else error.value = e.response?.data?.message || e.message
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div style="padding:20px;max-width:520px">
    <h1 style="font-weight:700;font-size:22px">Create Departement</h1>
    <p v-if="error" style="color:red">{{ error }}</p>

    <div style="display:flex;flex-direction:column;gap:10px;margin-top:12px">
      <label>Name<br>
        <input v-model="form.departement_name" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
        <small v-if="fieldErrors.departement_name" style="color:red">{{ fieldErrors.departement_name[0] }}</small>
      </label>
      <label>Max Check-In (HH:mm)<br>
        <input type="time" v-model="form.max_clock_in_time" style="padding:8px;border:1px solid #ccc;border-radius:6px">
        <small v-if="fieldErrors.max_clock_in_time" style="color:red">{{ fieldErrors.max_clock_in_time[0] }}</small>
      </label>
      <label>Max Check-Out (HH:mm)<br>
        <input type="time" v-model="form.max_clock_out_time" style="padding:8px;border:1px solid #ccc;border-radius:6px">
        <small v-if="fieldErrors.max_clock_out_time" style="color:red">{{ fieldErrors.max_clock_out_time[0] }}</small>
      </label>

      <div style="display:flex;gap:8px;margin-top:8px">
        <button @click="submit" :disabled="saving" style="padding:8px 12px;border:1px solid #000;border-radius:6px">
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
        <a href="/departments" style="padding:8px 12px;border:1px solid #666;border-radius:6px">Cancel</a>
      </div>
    </div>
  </div>
</template>
