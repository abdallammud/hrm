# Technology Stack

## Overview

The HRM application is built using a traditional LAMP stack architecture with modern PHP practices. This document provides detailed information about all technologies, frameworks, libraries, and tools used in the system.

## Backend Technologies

### 1. PHP (Server-Side Language)

**Version**: PHP 7.4+ (recommended PHP 8.0+)

**Key Features Used**:
- Object-Oriented Programming (OOP)
- MySQLi extension for database connectivity
- Session management
- File handling and uploads
- Password hashing functions
- Exception handling
- Prepared statements

**PHP Extensions Required**:
- `mysqli` - MySQL database connectivity
- `session` - Session management
- `json` - JSON encoding/decoding
- `fileinfo` - File type detection
- `gd` or `imagick` - Image processing (for avatars)

**Code Example**:
```php
// Database connection using MySQLi
$GLOBALS['conn'] = $conn = new mysqli($servername, $username, $password, $db);

// Object-oriented model structure
class Employee extends Model {
    public function __construct() {
        parent::__construct('employees', 'employee_id');
    }
}
```

### 2. MySQL (Database Management System)

**Version**: MySQL 5.7+ or MySQL 8.0+

**Database Features Used**:
- InnoDB storage engine
- Foreign key constraints
- Indexes for performance optimization
- Transactions for data integrity
- Prepared statements for security

**Key Database Objects**:
- **Tables**: 50+ tables for comprehensive HR data management
- **Relationships**: Foreign key relationships between entities
- **Indexes**: Performance indexes on frequently queried columns
- **Constraints**: Data integrity constraints

**Connection Configuration**:
```php
$servername = "localhost";
$username   = "root";
$password   = "";
$db = "test_edurdur";

$GLOBALS['conn'] = $conn = new mysqli($servername, $username, $password, $db);
```

## Frontend Technologies

### 1. HTML5

**Features Used**:
- Semantic HTML elements
- Form validation attributes
- File upload inputs
- Data attributes for JavaScript interaction

### 2. CSS3 and Styling Framework

**CSS Framework**: Bootstrap 5.x

**Key Features**:
- Responsive grid system
- Component library (buttons, forms, modals, tables)
- Utility classes for spacing and layout
- Custom CSS for application-specific styling

**File Structure**:
```
assets/css/
├── bootstrap.min.css
├── custom.css
└── modules/
    ├── dashboard.css
    ├── employees.css
    └── [other module styles]
```

### 3. JavaScript

**Core JavaScript Features**:
- ES6+ syntax where supported
- AJAX requests for dynamic content
- DOM manipulation
- Form validation
- Event handling

**JavaScript Libraries**:
- **jQuery 3.x**: DOM manipulation and AJAX
- **Bootstrap JS**: Component interactions
- **ApexCharts**: Data visualization and charts
- **DataTables**: Advanced table functionality

**Module Structure**:
```
assets/js/
├── jquery.min.js
├── bootstrap.bundle.min.js
├── modules/
│   ├── hrm.js
│   ├── payroll.js
│   ├── attendance.js
│   └── [other modules]
└── plugins/
    ├── apexchart/
    └── datatables/
```

## Development Framework and Patterns

### 1. Custom MVC-Inspired Framework

The application uses a custom-built framework following MVC principles:

**Model Layer**:
- Base `Model` class with CRUD operations
- Domain-specific model classes extending base model
- Database abstraction through prepared statements

**Controller Layer**:
- Action-based controllers handling HTTP requests
- Endpoint routing for different entity operations
- Business logic coordination

**View Layer**:
- PHP template files for HTML rendering
- Shared components (header, footer, sidebar)
- AJAX responses in JSON format

### 2. Autoloading System

Custom autoloading mechanism for classes and dependencies:

```php
// From app/init.php
require('db.php');
require('utilities.php');
require('config.php');
require('helpers.php');
require('auth.php');
require('autoload.php');

// Domain classes
require('EmployeeClass.php');
require('UserClass.php');
require('PayrollClass.php');
// ... other classes
```

## Database Technology Details

### 1. MySQL Configuration

**Storage Engine**: InnoDB (default)
- ACID compliance
- Foreign key support
- Row-level locking
- Crash recovery

**Character Set**: UTF-8 (utf8mb4)
- Full Unicode support
- Emoji support
- International character support

### 2. Database Connection Management

**Connection Pattern**: Single global connection
```php
$GLOBALS['conn'] = $conn = new mysqli($servername, $username, $password, $db);

// Error handling
if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

**Connection Features**:
- Persistent connection reuse
- Error handling and logging
- Transaction support
- Prepared statement support

## Security Technologies

### 1. Authentication and Authorization

**Password Security**:
- PHP `password_hash()` function with default algorithm (bcrypt)
- Salt generation handled automatically
- Password verification using `password_verify()`

```php
$password = password_hash($post['phone_number'], PASSWORD_DEFAULT);
```

**Session Management**:
- PHP native sessions
- Session-based authentication
- Role-based access control (RBAC)
- Session timeout handling

### 2. Input Security

**SQL Injection Prevention**:
- MySQLi prepared statements
- Parameter binding
- Input sanitization functions

```php
function escapeStr($str) {
    return $GLOBALS['conn']->real_escape_string($str);
}
```

**XSS Prevention**:
- Output escaping
- Input validation
- Content Security Policy headers (where applicable)

## File and Asset Management

### 1. File Upload Handling

**Supported File Types**:
- Images: JPG, JPEG, PNG, GIF, WebP
- Documents: PDF, DOC, DOCX
- CSV files for bulk imports

**Upload Security**:
- File type validation
- File size limits
- Unique filename generation
- Secure upload directory structure

```php
$target_dir = "../assets/images/avatars/";
$newfilename = round(microtime(true)) . '.' . end($temp);
```

### 2. Asset Organization

**Directory Structure**:
```
assets/
├── css/           # Stylesheets
├── js/            # JavaScript files
├── images/        # Static images
├── docs/          # Document uploads
├── fonts/         # Web fonts
├── plugins/       # Third-party libraries
└── tcpdf/         # PDF generation library
```

## Third-Party Libraries and Plugins

### 1. PDF Generation

**Library**: TCPDF
- Server-side PDF generation
- Report generation
- Payslip and document creation

### 2. Data Visualization

**Library**: ApexCharts
- Interactive charts and graphs
- Dashboard analytics
- Performance metrics visualization

### 3. Data Tables

**Library**: DataTables (jQuery plugin)
- Advanced table functionality
- Sorting, filtering, pagination
- AJAX data loading
- Export capabilities

## Development Tools and Environment

### 1. Recommended Development Environment

**Web Server**: Apache 2.4+ or Nginx
**PHP Version**: 7.4+ (recommended 8.0+)
**Database**: MySQL 5.7+ or MariaDB 10.3+
**Development Tools**:
- Code editor with PHP support (VS Code, PhpStorm)
- Database management tool (phpMyAdmin, MySQL Workbench)
- Version control (Git)

### 2. Server Requirements

**Minimum Requirements**:
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server
- 512MB RAM minimum (2GB+ recommended)
- 1GB disk space minimum

**PHP Extensions Required**:
```
- mysqli
- session
- json
- fileinfo
- gd (for image processing)
- mbstring (for string handling)
- openssl (for security)
```

## Configuration Management

### 1. Application Configuration

**Configuration Files**:
- `app/config.php`: Application-specific settings
- `app/db.php`: Database connection settings
- `asset_config.php`: Asset path configuration

**Environment-Specific Settings**:
```php
// Development
$servername = "localhost";
$username   = "root";
$password   = "";
$db = "test_edurdur";

// Production (commented out)
// $servername = "localhost";
// $username   = "u138037914_hrm";
// $password   = "|8nJaj9eU";
// $db = "u138037914_hrm";
```

### 2. Global Configuration

**Global Variables**:
- Database connection: `$GLOBALS['conn']`
- Base URI: `$GLOBALS['baseUri']`
- Application classes: `$GLOBALS['employeeClass']`, etc.

## Performance and Optimization

### 1. Database Optimization

**Query Optimization**:
- Prepared statements for query reuse
- Proper indexing strategy
- Efficient JOIN operations
- Batch processing for bulk operations

### 2. Frontend Optimization

**Asset Optimization**:
- Minified CSS and JavaScript files
- Image optimization
- Browser caching headers
- CDN usage for external libraries

### 3. Caching Strategies

**Application-Level Caching**:
- Session-based permission caching
- Menu configuration caching
- Entity relationship caching during bulk operations

## Deployment Considerations

### 1. Production Environment

**Web Server Configuration**:
- Apache with mod_rewrite enabled
- PHP-FPM for better performance
- SSL/TLS certificate for HTTPS
- Proper file permissions and security

### 2. Database Deployment

**Production Database Setup**:
- Regular backups
- Performance monitoring
- Index optimization
- Connection pooling

### 3. Security Hardening

**Production Security**:
- Disable PHP error display
- Secure file upload directories
- Regular security updates
- Database user with minimal privileges
- Web application firewall (WAF)

This technology stack provides a robust foundation for the HRM application while maintaining compatibility with standard web hosting environments and allowing for future scalability and enhancements.