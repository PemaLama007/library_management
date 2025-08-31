# 📚 Library Management System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-8.x-red?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.0+-blue?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-5.7+-orange?style=for-the-badge&logo=mysql" alt="MySQL">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

<p align="center">
  A comprehensive Library Management System built with Laravel, featuring inventory tracking, fine calculation, reporting, and automated notifications.
</p>

## ✨ Features

- 📖 **Book Management**: Complete CRUD operations for books with inventory tracking
- 👥 **Student Management**: Student registration with unique ID generation
- 📋 **Issue/Return System**: Streamlined book issuing and returning process
- 💰 **Fine Calculation**: Automated fine calculation for overdue books
- 📊 **Inventory Tracking**: Real-time stock management and availability
- 🔍 **Global Search**: Comprehensive search across all entities
- 📈 **Analytics & Reporting**: Detailed reports with trend analysis
- 🔔 **Notification System**: Automated reminders and overdue notices
- 🏷️ **Category & Publisher Management**: Organized content classification

## 🚀 Quick Start 
### 📥 Installation

1. **Clone the repository**
```bash
git clone https://github.com/PemaLama007/library_management.git
cd library_management
```

2. **Install dependencies**
```bash
composer install
npm install && npm run dev
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database setup**
```bash
php artisan migrate:fresh --seed
```

5. **Start the server**
```bash
php artisan serve
```

### 🔐 Default Credentials
```
Username: pemawoser
Password: admin
```