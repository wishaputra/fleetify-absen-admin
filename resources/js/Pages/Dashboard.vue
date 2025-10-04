<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/api'

const loading = ref(true)
const err = ref('')

const summary = ref({
  departments: 0,
  employees: 0,
  today: { total: 0, on_time_in: 0, late_in: 0, not_checked_out: 0, early_leave: 0, on_time_out: 0 },
  latest: []
})

function todayStr() {
  const d = new Date()
  return d.toISOString().slice(0,10)
}

async function load() {
  loading.value = true; err.value=''
  try {
    const [depRes, empRes, logRes] = await Promise.all([
      api.get('/departments'),
      api.get('/employees'),
      api.get('/attendance/logs', { params: { date_from: todayStr(), date_to: todayStr() } })
    ])
    const deps = depRes.data.data ?? depRes.data
    const emps = empRes.data.data ?? empRes.data
    const logs = logRes.data.data ?? logRes.data

    summary.value.departments = Array.isArray(deps) ? deps.length : (deps?.total ?? 0)
    summary.value.employees   = Array.isArray(emps) ? emps.length : (emps?.total ?? 0)

    summary.value.today.total = logs.length
    summary.value.today.on_time_in = logs.filter(x => x.status_in === 'on_time').length
    summary.value.today.late_in = logs.filter(x => x.status_in === 'late').length
    summary.value.today.not_checked_out = logs.filter(x => x.status_out === 'not_checked_out').length
    summary.value.today.early_leave = logs.filter(x => x.status_out === 'early_leave').length
    summary.value.today.on_time_out = logs.filter(x => x.status_out === 'on_time').length

    summary.value.latest = logs.slice(0, 5)
  } catch (e) {
    err.value = e?.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <div style="padding:20px; display:flex; flex-direction:column; gap:16px">
    <!-- Header dengan tombol pintasan -->
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px">
      <h1 style="font-weight:700;font-size:24px">Dashboard</h1>
      <div style="display:flex;gap:8px">
        <a href="/departments" style="padding:8px 12px;border:1px solid #000;border-radius:8px;text-decoration:none">
          Go to Departments
        </a>
        <a href="/employees" style="padding:8px 12px;border:1px solid #000;border-radius:8px;text-decoration:none">
          View Employees
        </a>
        <a href="/attendance/simulate" style="padding:8px 12px;border:1px solid #000;border-radius:8px;text-decoration:none">
          Attendance
        </a>
      </div>
    </div>

    <p v-if="loading">Loading...</p>
    <p v-else-if="err" style="color:red">Error: {{ err }}</p>

    <!-- Summary cards -->
    <div v-else style="display:grid; grid-template-columns: repeat(3,minmax(0,280px)); gap:12px">
      <div style="border:1px solid #ddd;border-radius:12px;padding:16px">
        <div style="font-weight:600">Departements</div>
        <div style="font-size:28px">{{ summary.departments }}</div>
        <div style="margin-top:8px">
          <a href="/departments" style="font-size:12px;text-decoration:underline">Manage departements →</a>
        </div>
      </div>
      <div style="border:1px solid #ddd;border-radius:12px;padding:16px">
        <div style="font-weight:600">Employees</div>
        <div style="font-size:28px">{{ summary.employees }}</div>
        <div style="margin-top:8px">
          <a href="/employees" style="font-size:12px;text-decoration:underline">See all employees →</a>
        </div>
      </div>
      <div style="border:1px solid #ddd;border-radius:12px;padding:16px">
        <div style="font-weight:600">Attendance Today</div>
        <div>Total: {{ summary.today.total }}</div>
        <div>On-time In: {{ summary.today.on_time_in }} | Late In: {{ summary.today.late_in }}</div>
        <div>On-time Out: {{ summary.today.on_time_out }} | Early Leave: {{ summary.today.early_leave }} | Not Out: {{ summary.today.not_checked_out }}</div>
        <div style="margin-top:8px">
          <a href="/attendance/logs" style="font-size:12px;text-decoration:underline">See logs →</a> |
        <a href="/attendance/simulate" style="font-size:12px;text-decoration:underline">Attendance →</a>
        </div>
      </div>
    </div>

    <!-- Latest attendance -->
    <div v-if="!loading && !err" style="border:1px solid #ddd;border-radius:12px;padding:16px">
      <div style="font-weight:600;margin-bottom:8px">Latest Attendance (Today)</div>
      <table border="1" cellpadding="8" cellspacing="0" style="width:100%">
        <thead>
          <tr>
            <th>Tanggal</th><th>Emp ID</th><th>Nama</th><th>Departement</th>
            <th>In</th><th>Status In</th><th>Out</th><th>Status Out</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="summary.latest.length===0">
            <td colspan="8" style="text-align:center">No data</td>
          </tr>
          <tr v-for="r in summary.latest" :key="r.id">
            <td>{{ r.work_date }}</td>
            <td>{{ r.employee?.id }}</td>
            <td>{{ r.employee?.name }}</td>
            <td>{{ r.employee?.department }}</td>
            <td>{{ r.clock_in ?? '-' }}</td>
            <td>{{ r.status_in }}</td>
            <td>{{ r.clock_out ?? '-' }}</td>
            <td>{{ r.status_out }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
