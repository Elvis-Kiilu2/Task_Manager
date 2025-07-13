# 🗂️ Task Management System – Cytonn Coding Challenge

A lightweight Task Management System developed using **PHP**, **MySQL**, and **Vanilla JavaScript**, in fulfillment of the Software Engineering Internship coding challenge issued by **Cytonn Investments**.

## 🚀 Features

### 👨‍💼 Administrator Functions
- Add, edit, and delete users
- Assign tasks to users with deadlines
- View and manage all tasks
- Send email notifications upon task assignment

### 👤 User Functions
- View tasks assigned to them
- Update task status: `Pending`, `In Progress`, or `Completed`

---

## 🛠️ Tech Stack

| Layer       | Technology           |
|-------------|----------------------|
| Backend     | PHP (No framework used) |
| Frontend    | HTML, CSS, **Vanilla JavaScript** |
| Database    | MySQL |
| Email       | PHP `mail()` function (SMTP-capable) |
| Hosting     | [Optional: Insert live link if hosted] |

> ⚠️ Note: No JavaScript frameworks were used as per the challenge rules. Vue.js is permitted but was not used.

---

## 🧱 Database Schema

### `users` Table
```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  is_admin BOOLEAN DEFAULT 0
);
