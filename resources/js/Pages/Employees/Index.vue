<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/api'

const rows = ref([]), deps = ref([]), loading = ref(true), err = ref('')

async function load() {
  try {
    const [emp, dep] = await Promise.all([api.get('/employees'), api.get('/departments')])
    rows.value = emp.data.data ?? emp.data
    deps.value  = dep.data.data ?? dep.data
  } catch (e) { err.value = e?.response?.data?.message || e.message }
  finally { loading.value = false }
}
function depName(id){ return deps.value.find(d=>d.id===id)?.departement_name || '-' }
async function remove(id){ if(confirm('Delete employee?')){ await api.delete(`/employees/${id}`); load() } }

onMounted(load)
</script>

<template>
  <div style="padding:20px">
    <div style="margin-bottom:12px">
      <a href="/" style="padding:6px 10px;border:1px solid #000;border-radius:6px;text-decoration:none">
        ← Back to Dashboard
      </a>
    </div>
    <h1 style="font-weight:700;font-size:22px">Employees</h1>
    <div style="margin:12px 0">
      <a href="/employees/create" style="padding:8px 12px;border:1px solid #000;border-radius:6px">+ Create</a>
    </div>

    <p v-if="loading">Loading...</p>
    <p v-else-if="err" style="color:red">Error: {{ err }}</p>

    <table v-else border="1" cellpadding="8" cellspacing="0">
      <thead><tr><th>ID</th><th>Emp ID</th><th>Name</th><th>Email</th><th>Departement</th><th>Action</th></tr></thead>
      <tbody>
        <tr v-for="e in rows" :key="e.id">
          <td>{{ e.id }}</td>
          <td>{{ e.employee_id }}</td>
          <td>{{ e.name }}</td>
          <td>{{ e.email }}</td>
          <td>{{ e.departement?.departement_name || depName(e.departement_id) }}</td>
          <td><button @click="remove(e.id)" style="padding:4px 8px;border:1px solid #b00;color:#b00;border-radius:6px">Delete</button></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
