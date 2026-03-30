<template>
  <div>
    <div class="page-header">
      <h2>Tasks</h2>
      <router-link to="/tasks/new" class="btn btn-primary">+ New Task</router-link>
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
                  <select
                    v-if="task.allowed_next_status.length > 0"
                    class="form-control"
                    style="width: auto; padding: 4px 8px; font-size: 12px"
                    @change="updateStatus(task.id, $event.target.value)"
                  >
                    <option value="" disabled selected>Move to...</option>
                    <option
                      v-for="s in task.allowed_next_status"
                      :key="s"
                      :value="s"
                    >
                      {{ formatStatus(s) }}
                    </option>
                  </select>
                  <span v-else style="font-size: 12px; color: var(--gray-400)">Terminal</span>
                  <router-link
                    :to="'/tasks/' + task.id + '/edit'"
                    class="btn btn-outline btn-sm"
                  >
                    Edit
                  </router-link>
                  <button
                    v-if="task.status === 'done'"
                    class="btn btn-danger btn-sm"
                    @click="deleteTask(task.id)"
                  >
                    Delete
                  </button>
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
