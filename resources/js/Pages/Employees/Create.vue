<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import api from '@/utils/api'

const deps = ref([])
const form = ref({ employee_id:'', name:'', email:'', address:'', departement_id:'' })
const saving = ref(false), err = ref(''), fieldErrors = ref({})

onMounted(async()=>{ const {data}=await api.get('/departments'); deps.value=data.data??data })

async function submit(){
  saving.value=true; err.value=''; fieldErrors.value={}
  try{ await api.post('/employees', form.value); router.visit('/employees') }
  catch(e){ if(e.response?.status===422) fieldErrors.value=e.response.data.errors||{}; else err.value=e.response?.data?.message||e.message }
  finally{ saving.value=false }
}
</script>

<template>
  <div style="padding:20px;max-width:520px">
    <h1 style="font-weight:700;font-size:22px">Create Employee</h1>
    <p v-if="err" style="color:red">{{ err }}</p>

    <div style="display:flex;flex-direction:column;gap:10px;margin-top:12px">
      <label>Employee ID<br><input v-model="form.employee_id" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px"><small v-if="fieldErrors.employee_id" style="color:red">{{ fieldErrors.employee_id[0] }}</small></label>
      <label>Name<br><input v-model="form.name" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px"><small v-if="fieldErrors.name" style="color:red">{{ fieldErrors.name[0] }}</small></label>
      <label>Email<br><input v-model="form.email" type="email" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px"><small v-if="fieldErrors.email" style="color:red">{{ fieldErrors.email[0] }}</small></label>
      <label>Address<br><textarea v-model="form.address" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px"></textarea></label>
      <label>Departement<br>
        <select v-model="form.departement_id" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
          <option value="" disabled>Select departement</option>
          <option v-for="d in deps" :key="d.id" :value="d.id">{{ d.departement_name }}</option>
        </select>
        <small v-if="fieldErrors.departement_id" style="color:red">{{ fieldErrors.departement_id[0] }}</small>
      </label>

      <div style="display:flex;gap:8px;margin-top:8px">
        <button @click="submit" :disabled="saving" style="padding:8px 12px;border:1px solid #000;border-radius:6px">{{ saving?'Saving...':'Save' }}</button>
        <a href="/employees" style="padding:8px 12px;border:1px solid #666;border-radius:6px">Cancel</a>
      </div>
    </div>
  </div>
</template>
