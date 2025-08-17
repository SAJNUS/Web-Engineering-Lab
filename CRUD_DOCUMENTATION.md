# Biodata CRUD Operations Documentation

## Overview
This system provides comprehensive CRUD (Create, Read, Update, Delete) operations for a biodata management system. The system includes both web interfaces and REST API endpoints.

## System Components

### 1. Database Structure
- **Database**: `biodata_system`
- **Tables**: 
  - `users` - User authentication and account information
  - `biodata` - Personal biodata information linked to users

### 2. Web Interfaces

#### A. User Interface (`biodata_crud.php`)
**Features:**
- ✅ **Create**: Add new biodata record
- ✅ **Read**: View current user's biodata
- ✅ **Update**: Edit existing biodata
- ✅ **Delete**: Remove biodata record

**URL**: `biodata_crud.php`

**Navigation:**
- `?action=list` - View biodata
- `?action=new` - Create new biodata form
- `?action=edit` - Edit existing biodata form
- `?action=delete` - Delete biodata (with confirmation)

#### B. Admin Interface (`admin_biodata.php`)
**Features:**
- View all biodata records across all users
- User statistics and analytics
- Admin-level delete operations
- Detailed biodata viewing

**URL**: `admin_biodata.php`

#### C. API Demo Interface (`api_demo.html`)
**Features:**
- Interactive API testing interface
- Real-time CRUD operations
- Form-based data entry
- API response visualization

### 3. REST API (`api/biodata_api.php`)

#### Authentication
- Simple Bearer token authentication (demo purposes)
- In production, implement proper JWT or session-based auth

#### Endpoints

##### GET /api/biodata_api.php/biodata
**Description**: Get current user's biodata
**Headers**: `Authorization: Bearer {user_id}`
**Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "full_name": "John Doe",
    "father_name": "Robert Doe",
    // ... other fields
    "created_at": "2024-01-01 12:00:00",
    "updated_at": "2024-01-01 12:00:00"
  }
}
```

##### GET /api/biodata_api.php/users
**Description**: Get all biodata records (admin function)
**Headers**: `Authorization: Bearer {user_id}`
**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "username": "john_doe",
      "full_name": "John Doe",
      // ... other fields
    }
  ],
  "count": 1
}
```

##### POST /api/biodata_api.php/biodata
**Description**: Create new biodata
**Headers**: 
- `Content-Type: application/json`
- `Authorization: Bearer {user_id}`
**Body**:
```json
{
  "full_name": "John Doe",
  "father_name": "Robert Doe",
  "mother_name": "Jane Doe",
  "date_of_birth": "1990-01-01",
  "gender": "Male",
  "address": "123 Main St",
  "phone": "+1234567890",
  "email": "john@example.com",
  "linkedin": "https://linkedin.com/in/johndoe",
  "github": "https://github.com/johndoe",
  "education": "Bachelor's in Computer Science",
  "skills": "PHP, JavaScript, MySQL",
  "languages": "English, Spanish",
  "marital_status": "Single",
  "hobbies": "Reading, Programming",
  "blood_group": "O+",
  "website": "https://johndoe.com"
}
```

##### PUT /api/biodata_api.php/biodata
**Description**: Update existing biodata
**Headers**: Same as POST
**Body**: Same as POST (only include fields to update)

##### DELETE /api/biodata_api.php/biodata
**Description**: Delete user's biodata
**Headers**: `Authorization: Bearer {user_id}`

## CRUD Operations Breakdown

### CREATE Operations
1. **Web Form**: Use `biodata_crud.php?action=new`
2. **API**: POST to `/api/biodata_api.php/biodata`
3. **Validation**: Required fields validation
4. **File Upload**: Profile picture support (web form only)

### READ Operations
1. **Single Record**: View specific user's biodata
2. **All Records**: Admin view of all biodata (admin_biodata.php)
3. **API Fetch**: GET endpoints for JSON responses
4. **Display Options**: 
   - Formatted web display
   - JSON API responses
   - Printable format

### UPDATE Operations
1. **Web Form**: Pre-populated edit form
2. **API**: PUT method with JSON payload
3. **Partial Updates**: Only specified fields are updated
4. **File Handling**: Profile picture updates

### DELETE Operations
1. **Soft Delete**: Currently implements hard delete
2. **Confirmation**: JavaScript confirmation dialogs
3. **Cascade**: Related data cleanup
4. **Audit**: Deletion logging (can be implemented)

## Security Features

### Input Sanitization
```php
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
```

### SQL Injection Prevention
- Prepared statements for all database queries
- Parameter binding for user inputs

### Authentication
- Session-based authentication for web interface
- Token-based authentication for API
- Login requirement for all operations

### File Upload Security
- Image file type validation
- File size restrictions
- Secure file naming
- Upload directory protection

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Biodata Table
```sql
CREATE TABLE biodata (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100),
    mother_name VARCHAR(100),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    linkedin VARCHAR(255),
    github VARCHAR(255),
    education TEXT,
    skills TEXT,
    languages VARCHAR(255),
    marital_status VARCHAR(20),
    hobbies TEXT,
    blood_group VARCHAR(5),
    website VARCHAR(255),
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Usage Examples

### Web Interface Usage
1. Login to the system
2. Navigate to `biodata_crud.php`
3. Use the navigation tabs:
   - "View Biodata" - See current data
   - "Create Biodata" - Add new data
   - "Edit Biodata" - Modify existing data

### API Usage Examples

#### JavaScript Fetch API
```javascript
// Get biodata
const response = await fetch('api/biodata_api.php/biodata', {
    headers: {
        'Authorization': 'Bearer 1',
        'Content-Type': 'application/json'
    }
});
const data = await response.json();

// Create biodata
const createResponse = await fetch('api/biodata_api.php/biodata', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer 1',
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        full_name: 'John Doe',
        email: 'john@example.com'
        // ... other fields
    })
});
```

#### cURL Examples
```bash
# Get biodata
curl -H "Authorization: Bearer 1" \
     http://localhost/your-project/api/biodata_api.php/biodata

# Create biodata
curl -X POST \
     -H "Authorization: Bearer 1" \
     -H "Content-Type: application/json" \
     -d '{"full_name":"John Doe","email":"john@example.com"}' \
     http://localhost/your-project/api/biodata_api.php/biodata

# Update biodata
curl -X PUT \
     -H "Authorization: Bearer 1" \
     -H "Content-Type: application/json" \
     -d '{"full_name":"John Smith"}' \
     http://localhost/your-project/api/biodata_api.php/biodata

# Delete biodata
curl -X DELETE \
     -H "Authorization: Bearer 1" \
     http://localhost/your-project/api/biodata_api.php/biodata
```

## File Structure
```
Web-Engineering-Lab/
├── biodata_crud.php          # Main CRUD interface
├── admin_biodata.php         # Admin interface
├── api_demo.html            # API testing interface
├── api/
│   └── biodata_api.php      # REST API endpoints
├── config/
│   └── database.php         # Database configuration
├── database/
│   └── biodata_system.sql   # Database schema
├── css/
│   └── biodata.css         # Styling
└── uploads/                # Profile picture uploads
```

## Setup Instructions

1. **Database Setup**:
   ```bash
   # Import the database schema
   mysql -u root -p < database/biodata_system.sql
   ```

2. **Web Server Configuration**:
   - Ensure XAMPP/LAMP/WAMP is running
   - Place files in web root directory
   - Configure database credentials in `config/database.php`

3. **API Configuration**:
   - Enable mod_rewrite for clean URLs (optional)
   - Set proper file permissions for uploads directory
   - Configure CORS headers if needed

4. **Testing**:
   - Access `biodata_crud.php` for web interface
   - Access `api_demo.html` for API testing
   - Use `admin_biodata.php` for admin functions

## Error Handling

### Common Error Responses
```json
{
  "error": "Authentication required"
}

{
  "error": "Biodata already exists for this user. Use PUT to update."
}

{
  "error": "Full name is required"
}
```

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `405` - Method Not Allowed
- `500` - Internal Server Error

## Future Enhancements

1. **Advanced Features**:
   - Image resizing and optimization
   - Data export (PDF, CSV)
   - Advanced search and filtering
   - Bulk operations

2. **Security Improvements**:
   - JWT token implementation
   - Rate limiting
   - Input validation enhancement
   - File upload security hardening

3. **Performance Optimizations**:
   - Database indexing
   - Caching implementation
   - API pagination
   - Image optimization

4. **Additional Functionality**:
   - Audit logging
   - Version history
   - Backup and restore
   - Multi-language support

## Troubleshooting

### Common Issues
1. **Database Connection Error**: Check database credentials in `config/database.php`
2. **File Upload Issues**: Verify uploads directory permissions
3. **API CORS Errors**: Check CORS headers in API file
4. **Authentication Failures**: Verify token format and user existence

### Debug Mode
Enable debug mode by adding to `config/database.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```
