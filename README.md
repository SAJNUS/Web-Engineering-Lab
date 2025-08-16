# Biodata Management System

A complete PHP and MySQL-based biodata management system with user authentication and CRUD operations.

## Features

### 🔐 **Authentication System**
- User registration with validation
- Secure login with password hashing
- Session management
- Logout functionality

### 📝 **Biodata Management (CRUD Operations)**
- **Create:** Add new biodata entries
- **Read:** View existing biodata
- **Update:** Modify biodata information
- **Delete:** Remove biodata entries

### 🎨 **Modern UI/UX**
- Clean, responsive design
- Consistent color scheme (#1266F1 blue theme)
- Form validation and user feedback
- File upload for profile pictures

## Setup Instructions

### Prerequisites
- XAMPP/WAMP/LAMP server
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Installation Steps

1. **Start your local server (XAMPP/WAMP)**
   - Start Apache and MySQL services

2. **Place files in web directory**
   ```
   Copy all files to: xampp/htdocs/biodata-system/
   ```

3. **Database Setup**
   - Visit: `http://localhost/biodata-system/setup.php`
   - This will automatically create the database and tables

4. **Configure Database (if needed)**
   - Edit `config/database.php` if you need different database credentials
   - Default settings:
     - Host: localhost
     - Username: root
     - Password: (empty)
     - Database: biodata_system

### Usage

1. **Access the application:**
   - Portfolio: `http://localhost/biodata-system/portfolio.html`
   - Direct login: `http://localhost/biodata-system/login.php`

2. **Create an account:**
   - Click "Sign up" from login page
   - Fill in registration details
   - Account will be created and you'll be redirected to login

3. **Login and manage biodata:**
   - Login with your credentials
   - Fill out the biodata form
   - Update/delete as needed

## File Structure

```
biodata-system/
├── config/
│   └── database.php          # Database configuration
├── css/
│   ├── login.css            # Login page styles
│   ├── signup.css           # Signup page styles
│   └── biodata.css          # Biodata form styles
├── uploads/                 # Profile picture uploads
├── login.php               # PHP login page
├── signup.php              # PHP signup page
├── biodata.php             # PHP biodata form with CRUD
├── logout.php              # Logout functionality
├── setup.php               # Database setup
└── portfolio.html          # Main portfolio page
```

## Database Schema

### Users Table
- id (Primary Key)
- first_name
- last_name
- email (Unique)
- username (Unique)
- password (Hashed)
- phone
- created_at
- updated_at

### Biodata Table
- id (Primary Key)
- user_id (Foreign Key)
- full_name
- father_name
- mother_name
- date_of_birth
- gender
- address
- phone
- email
- linkedin
- github
- education
- skills
- languages
- marital_status
- hobbies
- blood_group
- website
- profile_picture
- created_at
- updated_at

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Input sanitization
- Session-based authentication
- File upload validation for images

## CRUD Operations

### Create
- New users can register accounts
- Users can create their biodata

### Read
- Display existing biodata in the form
- Show user information in header

### Update
- Modify existing biodata entries
- Update profile pictures

### Delete
- Remove biodata entries
- Confirmation dialog for safety

## Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **File Upload:** PHP file handling
- **Security:** Password hashing, prepared statements

## Demo Credentials

After setup, you can create your own account or use any account you register.

## Support

If you encounter any issues:
1. Check that Apache and MySQL are running
2. Verify database credentials in `config/database.php`
3. Run `setup.php` to ensure database is properly initialized
4. Check PHP error logs for debugging
