<template>
  <div>
    <div class="page-header">
      <h2>Tasks</h2>
    </div>

    <div v-if="error" class="alert alert-error">{{ error }}</div>
    <div v-if="success" class="alert alert-success">{{ success }}</div>

    <div class="filter-bar">
      <button
        class="filter-btn"
        :class="{ active: !statusFilter }"
        @click="filterByStatus(null)"
      >
        All
      </button>
      <button
        class="filter-btn"
        :class="{ active: statusFilter === 'pending' }"
        @click="filterByStatus('pending')"
      >
        Pending
      </button>
      <button
        class="filter-btn"
        :class="{ active: statusFilter === 'in_progress' }"
        @click="filterByStatus('in_progress')"
      >
        In Progress
      </button>
      <button
        class="filter-btn"
        :class="{ active: statusFilter === 'done' }"
        @click="filterByStatus('done')"
      >
        Done
      </button>
    </div>

    <div v-if="loading" class="empty-state">
      <p>Loading tasks...</p>
    </div>

    <div v-else-if="tasks.length === 0" class="empty-state">
      <h3>No tasks found</h3>
      <p>Create your first task to get started.</p>
    </div>

    <div v-else class="card">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Title</th>
              <th>Priority</th>
              <th>Status</th>
              <th>Due Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in tasks" :key="task.id">
              <td>
                <strong>{{ task.title }}</strong>
              </td>
              <td>
                <span class="badge" :class="'badge-' + task.priority">
                  {{ task.priority }}
                </span>
              </td>
              <td>
                <span class="badge" :class="'badge-' + task.status">
                  {{ formatStatus(task.status) }}
                </span>
              </td>
              <td>{{ task.due_date }}</td>
              <td>
                <div class="actions">
                  <div v-if="task.allowed_next_status.length > 0" class="actions">
                    <button
                      v-for="s in task.allowed_next_status"
                      :key="s"
                      class="btn-icon"
                      :class="{ 'btn-success-icon': s === 'done', 'btn-info-icon': s === 'in_progress' }"
                      :title="'Mark as ' + formatStatus(s)"
                      @click="updateStatus(task.id, s)"
                    >
                      <!-- Play Icon for In Progress -->
                      <svg v-if="s === 'in_progress'" style="width:16px;height:16px" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M8,5.14V19.14L19,12.14L8,5.14Z" />
                      </svg>
                      <!-- Check Icon for Done -->
                      <svg v-else-if="s === 'done'" style="width:16px;height:16px" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" />
                      </svg>
                    </button>
                  </div>
                  <span v-else style="font-size: 11px; color: var(--gray-400); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em">Finished</span>
                  
                  <div class="actions">
                    <router-link
                      :to="'/tasks/' + task.id + '/edit'"
                      class="btn-icon"
                      title="Edit Task"
                    >
                      <svg style="width:16px;height:16px" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.88,8.88M3,17.25V21H6.75L17.81,9.93L14.07,6.19L3,17.25Z" />
                      </svg>
                    </router-link>
                    
                    <button
                      v-if="task.status === 'done'"
                      class="btn-icon btn-danger-icon"
                      title="Delete Task"
                      @click="deleteTask(task.id)"
                    >
                      <svg style="width:18px;height:18px" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M7,6H17V19H7V6M9,8V17H11V8H9M13,8V17H15V8H13Z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="meta.last_page > 1" class="pagination">
      <button :disabled="meta.current_page <= 1" @click="loadPage(meta.current_page - 1)">
        Previous
      </button>
      <span>Page {{ meta.current_page }} of {{ meta.last_page }}</span>
      <button :disabled="meta.current_page >= meta.last_page" @click="loadPage(meta.current_page + 1)">
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const tasks = ref([])
const meta = ref({ current_page: 1, per_page: 15, total: 0, last_page: 1 })
const loading = ref(true)
const error = ref('')
const success = ref('')
const statusFilter = ref(null)

function formatStatus(status) {
  return status.replace('_', ' ').replace(/\b\w/g, (c) => c.toUpperCase())
}

async function fetchTasks(page = 1) {
  loading.value = true
  error.value = ''
  try {
    const params = { page }
    if (statusFilter.value) params.status = statusFilter.value
    const res = await api.getTasks(params)
    tasks.value = res.data.data
    meta.value = res.data.meta
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to load tasks'
  } finally {
    loading.value = false
  }
}

function filterByStatus(status) {
  statusFilter.value = status
  fetchTasks(1)
}

function loadPage(page) {
  fetchTasks(page)
}

async function updateStatus(id, status) {
  error.value = ''
  success.value = ''
  try {
    await api.updateStatus(id, status)
    success.value = `Task status updated to ${formatStatus(status)}`
    fetchTasks(meta.value.current_page)
  } catch (e) {
    error.value = e.response?.data?.message || e.response?.data?.error || 'Failed to update status'
  }
}

async function deleteTask(id) {
  if (!confirm('Are you sure you want to delete this task?')) return
  error.value = ''
  success.value = ''
  try {
    await api.deleteTask(id)
    success.value = 'Task deleted successfully'
    fetchTasks(meta.value.current_page)
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to delete task'
  }
}

onMounted(() => fetchTasks())
</script>
