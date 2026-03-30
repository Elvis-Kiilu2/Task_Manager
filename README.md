# Task Manager — Monorepo

A full-stack Task Management application with a Vue.js frontend and Laravel REST API backend.

---

## Project Structure

```
Task_Manager/
├── README.md                  ← This file
├── .gitignore
│
├── backend/                   ← Laravel 13.2.0 REST API (PHP 8.4.1 / MySQL 8.0)
│   ├── app/
│   │   ├── Enums/             ← Priority, Status (PHP 8.1 backed enums)
│   │   ├── Http/
│   │   │   ├── Controllers/   ← TaskController (5 endpoints)
│   │   │   ├── Requests/      ← StoreTaskRequest, UpdateStatusRequest
│   │   │   └── Resources/     ← TaskResource
│   │   └── Models/            ← Task (Eloquent)
│   ├── database/
│   │   ├── migrations/        ← tasks table schema
│   │   └── seeders/           ← Sample data
│   ├── routes/api.php         ← API routes (/api/v1/*)
│   └── ...
├── frontend/                  ← Vue 3.5.30 + Vite 8.0.3 SPA
│   ├── src/
│   │   ├── services/api.js    ← Axios API client
│   │   ├── router/index.js    ← Vue Router
│   │   ├── views/
│   │   │   ├── TaskList.vue   ← Task list with filters & pagination
│   │   │   ├── TaskForm.vue   ← Create task form
│   │   │   └── DailyReport.vue ← Priority/status report
│   │   ├── App.vue            ← Root component with nav
│   │   ├── main.js            ← Entry point
│   │   └── style.css          ← Global styles
│   └── ...
│
└── legacy/                    ← Original PHP app (preserved for reference)
```

## Quick Start

### 1. Start Backend

```bash
# Start MySQL (Docker)
docker run -d --name task-manager-db \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=task_manager \
  -p 3306:3306 mysql:8.0

# Start Laravel API
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve --port=8000
```

### 2. Start Frontend

```bash
cd frontend
npm install
npm run dev
# App at http://localhost:5173
```

### Or: Docker (Full Stack)

```bash
cd backend
docker compose up -d --build
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

| GET | `/api/v1/tasks/report?date=YYYY-MM-DD` | Daily report |

### Example API Requests

#### 1. Create Task
`POST /api/v1/tasks`
```json
{
  "title": "Complete Laravel Assignment",
  "due_date": "2026-04-01",
  "priority": "high"
}
```

#### 2. Update Status
`PATCH /api/v1/tasks/{id}/status`
```json
{
  "status": "in_progress"
}
```

#### 3. Daily Report
`GET /api/v1/tasks/report?date=2026-04-01`
Response:
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

## Frontend Features

- Task list with status filter (All / Pending / In Progress / Done)
- Create new tasks with title, due date, priority
- Inline status update via dropdown 
- Delete done tasks
- Daily report with priority × status breakdown
- Pagination support
- Responsive design

## Tech Stack

| Component | Technology |
|-----------|------------|
| Frontend | Vue 3.5.30 + Vite 8.0.3 + Vue Router + Axios 1.14 |
| Backend | PHP 8.4.1 / Laravel 13.2.0 |
| Database | MySQL 8.0 |
| ORM | Eloquent |
| Linting | Laravel Pint (PSR-12) |
| Testing | PHPUnit |
| Deployment | Docker + Render/Railway |


