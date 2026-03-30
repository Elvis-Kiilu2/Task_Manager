<template>
  <div>
    <div class="page-header">
      <h2>Daily Report</h2>
      <router-link to="/" class="btn btn-outline">Back to Tasks</router-link>
    </div>

    <div class="card" style="margin-bottom: 24px">
      <div class="card-body">
        <div class="form-group" style="margin-bottom: 0">
          <label for="date">Select Date</label>
          <input
            id="date"
            v-model="selectedDate"
            type="date"
            class="form-control"
            style="max-width: 250px"
            @change="fetchReport"
          />
        </div>
      </div>
    </div>

    <div v-if="loading" class="empty-state">
      <p>Loading report...</p>
    </div>

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else>
      <p style="margin-bottom: 20px; color: var(--gray-500); font-size: 14px">
        Task summary for <strong>{{ selectedDate }}</strong>
      </p>

      <div class="report-grid">
        <div v-for="(counts, priority) in report.summary" :key="priority" class="report-card">
          <h3>
            <span class="badge" :class="'badge-' + priority">{{ priority }}</span>
          </h3>
          <div class="report-row">
            <span>Pending</span>
            <span>{{ counts.pending }}</span>
          </div>
          <div class="report-row">
            <span>In Progress</span>
            <span>{{ counts.in_progress }}</span>
          </div>
          <div class="report-row">
            <span>Done</span>
            <span>{{ counts.done }}</span>
          </div>
          <div class="report-row" style="border-top: 1px solid var(--gray-200); padding-top: 8px; margin-top: 4px">
            <span><strong>Total</strong></span>
            <span><strong>{{ counts.pending + counts.in_progress + counts.done }}</strong></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const selectedDate = ref(new Date().toISOString().split('T')[0])
const report = ref({ date: '', summary: {} })
const loading = ref(true)
const error = ref('')

async function fetchReport() {
  loading.value = true
  error.value = ''
  try {
    const res = await api.getReport(selectedDate.value)
    report.value = res.data
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to load report'
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchReport())
</script>
