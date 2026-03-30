# Task Manager — Monorepo

A full-stack Task Management application with a Vue.js frontend and Laravel REST API backend.

---

## Project Structure

```
Task_Manager/
├── README.md                  ← This file
├── BRD.md                     ← Business Requirements Document
├── .gitignore
│
├── backend/                   ← Laravel 11 REST API (PHP 8.2+ / MySQL 8.x)
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
│   ├── docker-compose.yml     ← app + mysql containers
│   ├── Dockerfile
│   ├── pint.json              ← Laravel Pint (PSR-12)
│   └── ...
│
├── frontend/                  ← Vue 3 + Vite SPA
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

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/health` | Health check |
| GET | `/api/v1/tasks` | List tasks (paginated, filterable by status) |
| POST | `/api/v1/tasks` | Create task |
| PATCH | `/api/v1/tasks/{id}/status` | Update task status (forward-only) |
| DELETE | `/api/v1/tasks/{id}` | Delete task (done only) |
| GET | `/api/v1/tasks/report?date=YYYY-MM-DD` | Daily report |

## Frontend Features

- Task list with status filter (All / Pending / In Progress / Done)
- Create new tasks with title, due date, priority
- Inline status update via dropdown (only allowed transitions shown)
- Delete done tasks
- Daily report with priority × status breakdown
- Pagination support
- Responsive design

## Tech Stack

| Component | Technology |
|-----------|------------|
| Frontend | Vue 3 + Vite + Vue Router + Axios |
| Backend | PHP 8.2+ / Laravel 11 |
| Database | MySQL 8.x |
| ORM | Eloquent |
| Linting | Laravel Pint (PSR-12) |
| Testing | PHPUnit |
| Deployment | Docker + Render/Railway |

## Documentation

- [BRD.md](BRD.md) — Full business requirements, API specs, architecture decisions
