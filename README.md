# Task Manager – Cytonn Software Engineering Internship Challenge

A simple task management system built in **PHP**, **MySQL** — designed to allow administrators to manage users and tasks while enabling users to update their assigned task statuses.

---

## ✨ Features

### 👨‍💼 Admin Capabilities
- **User Management**: Add, edit, and delete users with role assignments
- **Task Assignment**: Create and assign tasks with deadlines to specific users
- **Task Overview**: View all tasks across the system with filtering options
- **Email Notifications**: Automatically send email notifications when tasks are assigned
- **Dashboard Analytics**: Monitor task completion rates and user activity

### 👤 User Capabilities
- **Task Dashboard**: View all assigned tasks with clear status indicators
- **Status Updates**: Update task status between `Pending`, `In Progress`, and `Completed`
- **Task Details**: Access comprehensive task information including deadlines and descriptions
- **Personal Analytics**: Track personal task completion statistics

### 🔐 Security Features
- **Role-Based Authentication**: Separate login interfaces for Admin and User roles
- **Session Management**: Secure session-based access control
- **Access Control**: Role-restricted dashboard and feature access

### 📬 Communication
- **Email Notifications**: Automated email alerts for task assignments using PHP `mail()`
- **Real-time Updates**: Dynamic status updates without page refreshes

---

## 📁 Project Structure

```
Task_Manager/
├── README.md
└── task-manager/
    ├── index.php                    # Landing page and login interface
    ├── login.php                    # Authentication logic
    ├── logout.php                   # Session termination
    ├── dashboard.php                # Main dashboard (role-specific)
    ├── assign_task.php              # Admin: Task assignment interface
    ├── update_status.php            # User: Task status updates
    ├── edit_task.php                # Task editing functionality
    ├── includes/
    │   ├── auth.php                 # Authentication middleware
    │   ├── db.php                   # Database connection handler
    │   └── functions.php            # Utility functions and helpers
    ├── users/
    │   ├── manage_users.php         # Admin: User management dashboard
    │   ├── add_user.php             # Admin: Add new users
    │   ├── edit_user.php            # Admin: Edit existing users
    │   └── delete_user.php          # Admin: Remove users
    ├── assets/
    │   ├── css/
    │   │   └── style.css            # Application styling
    │   └── js/
    │       └── main.js              # Client-side interactions
    └── sql/
        └── dump.sql                 # Database schema and seed data
```

---

## 🚀 Installation & Setup

### Prerequisites
- **XAMPP/LAMP Stack** (Apache, MySQL, PHP 8.0+)
- **Web Browser** (Chrome, Firefox, Safari, Edge)
- **Database Access** (phpMyAdmin or MySQL CLI)

### Step-by-Step Setup

1. **Install XAMPP/LAMP**
   ```bash
   # For Ubuntu/Debian
   sudo apt install lamp-server^
   
   # Or download XAMPP from https://www.apachefriends.org/
   ```

2. **Deploy Application**
   ```bash
   # Place the task-manager folder in your web root
   cp -r task-manager/ /opt/lampp/htdocs/
   # or for XAMPP on Windows: C:\xampp\htdocs\
   ```

3. **Database Setup**
   ```bash
   # Access MySQL
   mysql -u root -p
   
   # Create database
   CREATE DATABASE task_manager;
   USE task_manager;
   
   # Import schema
   SOURCE /full/path/to/sql/dump.sql;
   ```

4. **Configuration**
   - Update database credentials in `includes/db.php`
   - Configure email settings for notifications (optional)

5. **Access Application**
   - Navigate to: `http://localhost/task-manager/`
   - Use sample credentials below to test functionality

---

## 👥 Default Credentials

| Role  | Email                 | Password    | Access Level        |
|-------|-----------------------|-------------|---------------------|
| Admin | admin@example.com     | admin23 | Full system access  |
| User  | user@example.com      | user123 | Task management only |

> **Note**: Change these credentials immediately after setup for security.

---

## 🔧 Configuration

### Email Setup
To enable email notifications:

1. **Basic PHP Mail** (Default)
   ```php
   // Ensure PHP mail() is configured on your server
   // Check php.ini for SMTP settings
   ```

2. **Enhanced Email (Recommended)**
   ```php
   // Consider implementing PHPMailer with Gmail SMTP
   // For production-grade email delivery
   ```

### Database Configuration
Update `includes/db.php` with your database settings:
```php
$host = 'localhost';
$dbname = 'task_manager';
$username = 'your_username';
$password = 'your_password';
```

---

## 🛠 Technology Stack

| Component | Technology | Version |
|-----------|------------|---------|
| **Backend** | PHP | 8.0+ |
| **Database** | MySQL/MariaDB | 5.7+ |
| **Web Server** | Apache | 2.4+ |
| **Frontend** | HTML5, CSS3, JavaScript | ES6+ |
| **Architecture** | MVC Pattern | Custom |

---

## 📱 Usage Guide

### For Administrators
1. **Login** with admin credentials
2. **Manage Users**: Add, edit, or remove users from the system
3. **Assign Tasks**: Create tasks with deadlines and assign to users
4. **Monitor Progress**: Track task completion across all users
5. **View Reports**: Access system-wide analytics and reports

### For Users
1. **Login** with user credentials
2. **View Tasks**: See all assigned tasks on the dashboard
3. **Update Status**: Change task status as work progresses
4. **Track Progress**: Monitor personal task completion rates

---


## 🤝 Contributing

This project was developed as part of the Cytonn Software Engineering Internship Challenge. For improvements or bug fixes:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Submit a pull request

---

## 🆘 Support

For technical issues or questions:
- Check the troubleshooting section in the documentation
- Review the code comments for implementation details
- Ensure all prerequisites are properly installed

---

