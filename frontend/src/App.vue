<template>
  <div id="app">
    <nav class="navbar">
      <div class="nav-brand">
        <h1>Task Manager</h1>
      </div>
      <div class="nav-links">
        <router-link to="/" class="nav-link">Tasks</router-link>
        <router-link to="/tasks/new" class="nav-link btn-primary">+ New Task</router-link>
        <router-link to="/report" class="nav-link">Report</router-link>
      </div>
    </nav>
    <main class="container">
      <router-view />
    </main>
  </div>
</template>

<script setup>
</script>

<style scoped>
</style>
