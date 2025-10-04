<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/api'

const rows = ref([])
const loading = ref(true)
const error = ref('')

async function load() {
  try {
    const { data } = await api.get('/departments')
    rows.value = data.data ?? data
  } catch (e) {
    error.value = e?.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
}

async function remove(id) {
  if (!confirm('Delete this departement?')) return
  await api.delete(`/departments/${id}`)
  load()
}

onMounted(load)
</script>

<template>
    <div style="padding:20px">
    <div style="margin-bottom:12px">
      <a href="/" style="padding:6px 10px;border:1px solid #000;border-radius:6px;text-decoration:none">
        ← Back to Dashboard
      </a>
    </div>
    <h1 style="font-weight:700;font-size:22px">Departements</h1>

    <div style="margin:12px 0">
      <a href="/departments/create" style="padding:8px 12px;border:1px solid #000;border-radius:6px">+ Create</a>
    </div>

    <p v-if="loading">Loading...</p>
    <p v-else-if="error" style="color:red">Error: {{ error }}</p>

    <table v-else border="1" cellpadding="8" cellspacing="0">
      <thead>
        <tr><th>ID</th><th>Name</th><th>Max In</th><th>Max Out</th><th>Action</th></tr>
      </thead>
      <tbody>
        <tr v-for="d in rows" :key="d.id">
          <td>{{ d.id }}</td>
          <td>{{ d.departement_name }}</td>
          <td>{{ d.max_clock_in_time }}</td>
          <td>{{ d.max_clock_out_time }}</td>
          <td>
            <button @click="remove(d.id)" style="padding:4px 8px;border:1px solid #b00;color:#b00;border-radius:6px">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
