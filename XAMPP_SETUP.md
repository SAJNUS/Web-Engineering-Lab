# XAMPP Setup Guide for Biodata System

## Method 1: Automatic Setup (Recommended)

### Step 1: Place Files in XAMPP
1. Copy your entire project folder to: `C:\xampp\htdocs\Web-Engineering-Lab\`
2. Start XAMPP Control Panel
3. Start **Apache** and **MySQL** services

### Step 2: Auto-Create Database
1. Open browser and visit: `http://localhost/Web-Engineering-Lab/setup.php`
2. The database and tables will be created automatically
3. You'll see confirmation messages

### Step 3: Test the System
1. Visit: `http://localhost/Web-Engineering-Lab/portfolio.html`
2. Click on "Biodata Form"
3. Create a new account and test the system

---

## Method 2: Manual Database Import

### Step 1: Access phpMyAdmin
1. Open browser and go to: `http://localhost/phpmyadmin`
2. Login with:
   - Username: `root`
   - Password: (leave empty)

### Step 2: Import Database
1. Click on **"Import"** tab in phpMyAdmin
2. Click **"Choose File"** button
3. Select the file: `database/biodata_system.sql`
4. Click **"Go"** button
5. Database will be created with sample admin user

### Step 3: Test with Sample User
- Username: `admin`
- Password: `admin123`

---

## Database Configuration

### Default Settings (config/database.php):
```php
DB_HOST = 'localhost'
DB_USERNAME = 'root'
DB_PASSWORD = ''
DB_NAME = 'biodata_system'
```

### If you need different settings:
1. Edit `config/database.php`
2. Change the database credentials to match your XAMPP setup

---

## Folder Structure in XAMPP:
```
C:\xampp\htdocs\Web-Engineering-Lab\
├── config/
│   └── database.php
├── css/
├── database/
│   └── biodata_system.sql
├── uploads/                 (will be created automatically)
├── *.php files
└── *.html files
```

---

## Troubleshooting

### If you get "Access Denied" error:
1. Check if MySQL is running in XAMPP
2. Verify database credentials in `config/database.php`
3. Make sure you're using the correct database name

### If uploads don't work:
1. Create `uploads/` folder manually
2. Set folder permissions to allow writing

### If PHP errors appear:
1. Check XAMPP PHP error logs
2. Make sure all PHP files are in the correct location

---

## URLs to Access:

- **Portfolio:** `http://localhost/Web-Engineering-Lab/portfolio.html`
- **Login:** `http://localhost/Web-Engineering-Lab/login.php`
- **Signup:** `http://localhost/Web-Engineering-Lab/signup.php`
- **Setup:** `http://localhost/Web-Engineering-Lab/setup.php`
- **phpMyAdmin:** `http://localhost/phpmyadmin`

---

## Sample Admin Account (after manual import):
- **Username:** admin
- **Password:** admin123
- **Email:** admin@example.com
