<template>
  <div>
    <div class="page-header">
      <h2>{{ isEdit ? 'Edit Task' : 'Create Task' }}</h2>
      <router-link to="/" class="btn btn-outline">Back to Tasks</router-link>
    </div>

    <div v-if="error" class="alert alert-error">{{ error }}</div>

    <div class="card">
      <div class="card-body">
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="title">Title</label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              class="form-control"
              :class="{ error: errors.title }"
              placeholder="Enter task title"
              required
            />
            <p v-if="errors.title" class="error-text">{{ errors.title[0] }}</p>
          </div>

          <div class="form-group">
            <label for="due_date">Due Date</label>
            <input
              id="due_date"
              v-model="form.due_date"
              type="date"
              class="form-control"
              :class="{ error: errors.due_date }"
              :min="today"
              required
            />
            <p v-if="errors.due_date" class="error-text">{{ errors.due_date[0] }}</p>
          </div>

          <div class="form-group">
            <label for="priority">Priority</label>
            <select
              id="priority"
              v-model="form.priority"
              class="form-control"
              :class="{ error: errors.priority }"
              required
            >
              <option value="" disabled>Select priority</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
            <p v-if="errors.priority" class="error-text">{{ errors.priority[0] }}</p>
          </div>

          <div v-if="isEdit" class="form-group">
            <label for="status">Status</label>
            <select
              id="status"
              v-model="form.status"
              class="form-control"
            >
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="done">Done</option>
            </select>
          </div>

          <div style="display: flex; gap: 12px; margin-top: 24px">
            <button type="submit" class="btn btn-primary" :disabled="submitting">
              {{ submitting ? 'Saving...' : (isEdit ? 'Update Task' : 'Create Task') }}
            </button>
            <router-link to="/" class="btn btn-outline">Cancel</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const props = defineProps({ id: String })
const route = useRoute()
const router = useRouter()

const isEdit = computed(() => !!props.id)

const form = ref({
  title: '',
  due_date: '',
  priority: '',
  status: 'pending',
})

const errors = ref({})
const error = ref('')
const submitting = ref(false)

const today = new Date().toISOString().split('T')[0]

onMounted(async () => {
  if (isEdit.value) {
    try {
      const res = await api.getTasks()
      const task = res.data.data.find((t) => t.id === parseInt(props.id))
      if (task) {
        form.value = {
          title: task.title,
          due_date: task.due_date,
          priority: task.priority,
          status: task.status,
        }
      } else {
        error.value = 'Task not found'
      }
    } catch (e) {
      error.value = 'Failed to load task'
    }
  }
})

async function submitForm() {
  submitting.value = true
  errors.value = {}
  error.value = ''

  try {
    if (isEdit.value) {
      await api.updateTask(props.id, form.value)
      router.push('/')
      return
    }

    await api.createTask(form.value)
    router.push('/')
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors || {}
      error.value = e.response.data.message || 'Validation failed'
    } else {
      error.value = e.response?.data?.message || 'Failed to save task'
    }
  } finally {
    submitting.value = false
  }
}
</script>
