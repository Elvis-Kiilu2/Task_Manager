# Task Manager — Monorepo

A Task Management application with a **Vue js** frontend and **Laravel** REST API backend.

---

## Project Structure

```
Task_Manager/
├── backend/                   ← Laravel 13.2.0 REST API (PHP 8.4.1)
├── frontend/                  ← Vue 3.5.30 + Vite 8.0.1 SPA
├── database_dump.sql          ← MySQL SQL dump file
├── Dockerfile                 ← Production Build (Single-container)
├── docker-compose.yml         ← Local Dev (Multi-container)
└── README.md                  ← This file
```

---

## Quick Start (Local)

### 1. Using Docker Compose
```bash
# Start the full stack
docker compose up -d --build

# Run initial setup
docker compose exec backend php artisan migrate --seed

# Access: http://localhost:8080 (Frontend) | http://localhost:8000 (API)
```

---

## How to Deploy (Render)

This project is optimized for **Single-Container** deployment using the root `Dockerfile`.

1. **Connect Repository**: Link your GitHub repo to Render.
2. **Setup Environment**: Add required `.env` variables (APP_KEY, etc.).
3. **Build Command**: The system will automatically use the root `Dockerfile`.

---

## Health & Verification (Docker)

To verify the system is running correctly:
```bash
# Check container status
docker compose ps

# Test API health from host
curl -I http://localhost:8000/api/v1/tasks

# View real-time logs
docker compose logs -f backend
```

---

## API Examples

### 1. Create Task
`POST /api/v1/tasks`
```json
{
  "title": "Assignment Review",
  "due_date": "2026-04-01",
  "priority": "high"
}
```

### 2. Daily Report
`GET /api/v1/tasks/report?date=2026-04-01`
```json
{
  "date": "2026-04-01",
  "summary": {
    "high": {"pending": 0, "in_progress": 1, "done": 0},
    "medium": {"pending": 1, "in_progress": 0, "done": 0},
    "low": {"pending": 0, "in_progress": 0, "done": 0}
  }
}
```

---

## Tech Stack
- **Frontend**: Vue 3.5.30, Vite 8.0.1, Axios.
- **Backend**: Laravel 13.2.0, PHP 8.4.1, Eloquent ORM.
- **Infra**: Docker Compose, MySQL 8.0.
- **Standards**: PSR-12 (Laravel Pint).
