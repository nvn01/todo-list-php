# 📝 TODO LIST APP

Modern todo list application with PHP and MySQL.

## 🚀 Installation Instructions

### 1. Import Database to phpMyAdmin

1. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Click **"New"** in the left sidebar
3. Create database with name: **`todolist_db`**
4. Click on the new database
5. Go to **"Import"** tab
6. Click **"Choose File"** and select `database.sql`
7. Click **"Go"** to import

### 2. Start XAMPP

- Start **Apache** (for PHP)
- Start **MySQL** (for database)

### 3. Access the App

Open browser and go to: **`http://localhost/todo-list/`**

## 🔑 Login Credentials

**Admin Account:**
- Username: `admin`
- Password: `TomatoPotato01!`

**Regular Users:**
- Register from the landing page (username + password only)

## ✨ Features

- ✅ User registration & login
- ✅ Add, complete, and delete todos
- ✅ **Board View** - Card layout (To Do / Doing / Done)
- ✅ **Table View** - Spreadsheet layout
- ✅ Secure password hashing
- ✅ Dark theme with purple accents

## 📁 Database Structure

**Database Name:** `todolist_db`

**Tables:**
- `users` - Stores user accounts (id, username, password, role)
- `todos` - Stores todo items (id, user_id, title, status, priority, is_completed)

## 🎨 Design

- Black background (#000000)
- Purple accent color (#7C3AED)
- Notion-inspired card and table layout
- Clean, modern interface

## 🐛 Troubleshooting

**Connection Error:**
- Make sure MySQL is running in XAMPP
- Check database name is `todolist_db`

**Page Not Found:**
- Folder must be named `todo-list` inside `xampp/htdocs/`
