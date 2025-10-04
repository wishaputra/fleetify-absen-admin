<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/api'

// state
const employees = ref([])
const loading = ref(true)
const error = ref('')
const info = ref('')

// form
const form = ref({
  employee_id: '',
  work_date: '',
  in_time: '09:00',
  out_time: '17:00',
})

// utils
function todayStr () {
  const d = new Date()
  return d.toISOString().slice(0,10) // YYYY-MM-DD
}

function toIsoLocal(dateStr, timeStr) {
  // "2025-10-04","08:55" -> "2025-10-04T08:55:00"
  return `${dateStr}T${timeStr}:00`
}

// actions
async function loadEmployees() {
  try {
    const { data } = await api.get('/employees')
    const rows = data.data ?? data
    employees.value = rows
    if (!form.value.employee_id && rows.length) {
      form.value.employee_id = rows[0].employee_id || rows[0].id // prefer string employee_id
    }
  } catch (e) {
    error.value = e?.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
}

async function doCheckIn() {
  error.value = ''; info.value = ''
  if (!form.value.employee_id || !form.value.work_date || !form.value.in_time) {
    error.value = 'Employee, date, and in time are required.'; return
  }
  try {
    const payload = {
      employee_id: form.value.employee_id, // string (ex: EMP001)
      timestamp: toIsoLocal(form.value.work_date, form.value.in_time),
    }
    const { data } = await api.post('/attendance/check-in', payload)
    info.value = `✅ Check-In saved at ${data.clock_in}`
  } catch (e) {
    error.value = e?.response?.data?.message || e.message
  }
}

async function doCheckOut() {
  error.value = ''; info.value = ''
  if (!form.value.employee_id || !form.value.work_date || !form.value.out_time) {
    error.value = 'Employee, date, and out time are required.'; return
  }
  try {
    const payload = {
      employee_id: form.value.employee_id,
      timestamp: toIsoLocal(form.value.work_date, form.value.out_time),
    }
    const { data } = await api.put('/attendance/check-out', payload)
    info.value = `✅ Check-Out saved at ${data.clock_out}`
  } catch (e) {
    error.value = e?.response?.data?.message || e.message
  }
}

onMounted(() => {
  form.value.work_date = todayStr()
  loadEmployees()
})
</script>

<template>
  <div style="padding:20px;max-width:640px">
    <div style="margin-bottom:12px">
      <a href="/" style="padding:6px 10px;border:1px solid #000;border-radius:6px;text-decoration:none">
        ← Back to Dashboard
      </a>
    </div>
    <h1 style="font-weight:700;font-size:22px">Attendance</h1>
    <p style="color:#666;margin-top:4px">
      Pilih karyawan, tentukan tanggal, set jam masuk/keluar, lalu klik tombolnya.
    </p>

    <div v-if="loading" style="margin-top:12px">Loading employees...</div>
    <div v-else>
      <div v-if="error" style="color:#b00;margin-top:12px">{{ error }}</div>
      <div v-if="info" style="color:green;margin-top:12px">{{ info }}</div>

      <div style="display:flex;flex-direction:column;gap:12px;margin-top:16px">
        <label>
          Employee
          <select v-model="form.employee_id" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
            <option v-for="e in employees" :key="e.id" :value="e.employee_id || e.id">
              {{ (e.employee_id || e.id) }} — {{ e.name }}
            </option>
          </select>
        </label>

        <label>
          Work Date
          <input type="date" v-model="form.work_date" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
        </label>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <label>
            In Time
            <input type="time" v-model="form.in_time" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
          </label>
          <label>
            Out Time
            <input type="time" v-model="form.out_time" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
          </label>
        </div>

        <div style="display:flex;gap:10px">
          <button @click="doCheckIn"  style="padding:8px 12px;border:1px solid #000;border-radius:6px">Check-In</button>
          <button @click="doCheckOut" style="padding:8px 12px;border:1px solid #000;border-radius:6px">Check-Out</button>
        </div>
      </div>
    </div>
  </div>
</template>
