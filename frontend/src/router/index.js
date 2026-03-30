import { createRouter, createWebHistory } from 'vue-router'
import TaskList from '../views/TaskList.vue'
import TaskForm from '../views/TaskForm.vue'
import DailyReport from '../views/DailyReport.vue'

const routes = [
  { path: '/', name: 'tasks', component: TaskList },
  { path: '/tasks/new', name: 'create-task', component: TaskForm },
  { path: '/tasks/:id/edit', name: 'edit-task', component: TaskForm, props: true },
  { path: '/report', name: 'report', component: DailyReport },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
