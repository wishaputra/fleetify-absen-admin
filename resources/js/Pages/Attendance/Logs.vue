<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/api'

const rows = ref([])
const loading = ref(true)
const error = ref('')
const departments = ref([])
const filters = ref({
  department_id: '',
  date_from: '',
  date_to: ''
})

async function loadDepartments() {
  const { data } = await api.get('/departments')
  departments.value = data.data ?? data
}

async function load() {
  loading.value = true; error.value = ''
  try {
    const { data } = await api.get('/attendance/logs', { params: filters.value })
    rows.value = data.data ?? data
  } catch (e) {
    error.value = e?.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await loadDepartments()
  await load()
})
</script>

<template>
  <div style="padding:20px">
    <div style="margin-bottom:12px">
      <a href="/" style="padding:6px 10px;border:1px solid #000;border-radius:6px;text-decoration:none">
        ← Back to Dashboard
      </a>
    </div>
    <h1 style="font-weight:700;font-size:22px">Attendance Logs</h1>

    <div style="display:grid;grid-template-columns:repeat(4, minmax(0, 220px));gap:10px;margin:12px 0">
      <input type="date" v-model="filters.date_from" style="padding:8px;border:1px solid #ccc;border-radius:6px">
      <input type="date" v-model="filters.date_to"   style="padding:8px;border:1px solid #ccc;border-radius:6px">
      <select v-model="filters.department_id" style="padding:8px;border:1px solid #ccc;border-radius:6px">
        <option value="">All Departements</option>
        <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.departement_name }}</option>
      </select>
      <button @click="load" style="padding:8px 12px;border:1px solid #000;border-radius:6px">Filter</button>
    </div>

    <p v-if="loading">Loading...</p>
    <p v-else-if="error" style="color:red">Error: {{ error }}</p>

    <table v-else border="1" cellpadding="8" cellspacing="0">
      <thead>
        <tr>
          <th>Tanggal</th><th>Emp ID</th><th>Nama</th><th>Departement</th>
          <th>In</th><th>Status In</th><th>Late (m)</th>
          <th>Out</th><th>Status Out</th><th>Early (m)</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="r in rows" :key="r.id">
          <td>{{ r.work_date }}</td>
          <td>{{ r.employee?.id }}</td>
          <td>{{ r.employee?.name }}</td>
          <td>{{ r.employee?.department }}</td>
          <td>{{ r.clock_in ?? '-' }}</td>
          <td>{{ r.status_in }}</td>
          <td>{{ r.late_minutes }}</td>
          <td>{{ r.clock_out ?? '-' }}</td>
          <td>{{ r.status_out }}</td>
          <td>{{ r.early_leave_minutes }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
