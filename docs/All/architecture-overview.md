# System Architecture Overview

## Introduction

The HRM application is a comprehensive Human Resource Management system built using PHP and MySQL. It follows a modular, MVC-inspired architecture that separates concerns between data access, business logic, and presentation layers. The system is designed to handle complex HR workflows including employee management, payroll processing, attendance tracking, and organizational structure management.

## Architectural Patterns

### 1. Model-View-Controller (MVC) Inspired Structure

The application follows an MVC-inspired pattern with the following components:

- **Models**: PHP classes extending the base `Model` class for database operations
- **Controllers**: PHP controller files handling HTTP requests and business logic
- **Views**: PHP template files for rendering HTML responses

### 2. Base Model Pattern

All data access is centralized through a base `Model` class that provides:

```php
class Model {
    protected $table;
    protected $primaryKey;
    protected $db;
    
    // CRUD operations
    public function create($data)
    public function read($id)
    public function update($id, $data)
    public function delete($id)
    public function read_all($limit = null, $orderBy = null)
}
```

### 3. Global Connection Pattern

The application uses a global database connection pattern:

```php
$GLOBALS['conn'] = $conn = new mysqli($servername, $username, $password, $db);
```

This connection is shared across all models and controllers for database operations.

## Core Components

### 1. Application Initialization (`app/init.php`)

The initialization system loads all required components in a specific order:

1. **Database Connection** (`db.php`)
2. **Utilities and Helpers** (`utilities.php`, `helpers.php`)
3. **Configuration** (`config.php`)
4. **Authentication** (`auth.php`)
5. **Base Model** (`Model.php`)
6. **Autoloader** (`autoload.php`)
7. **Domain Classes** (Employee, User, Payroll, etc.)

### 2. Request Routing System (`app/autoload.php`)

The application uses a custom routing system based on URL parameters:

- `menu`: Determines the main module (hrm, payroll, attendance, etc.)
- `action`: Specifies the action within a module (add, edit, show, etc.)
- `tab`: Handles sub-menu navigation

```php
function load_files() {
    $menu = $_GET['menu'] ?? null;
    $action = $_GET['action'] ?? null;
    $tab = $_GET['tab'] ?? null;
    
    // Route to appropriate controller/view
}
```

### 3. Authentication and Authorization System

The system implements role-based access control (RBAC):

- **Session Management**: User sessions with role-based permissions
- **Permission Checking**: `check_auth()` function validates user permissions
- **Role-Based Routing**: Landing pages determined by user permissions

```php
function check_auth($authKey, $msg = "You are not authorized to perform this action.") {
    if(!check_session($authKey)) {
        // Return unauthorized response
    }
}
```

## Data Layer Architecture

### 1. Database Connection Management

- **Single Global Connection**: `$GLOBALS['conn']` used throughout the application
- **MySQLi Extension**: Uses MySQLi for database operations
- **Prepared Statements**: Model class uses prepared statements for security

### 2. Model Inheritance Hierarchy

```
Model (Base Class)
├── Employee
├── Users
├── PayrollClass
├── AttendanceClass
├── BranchClass
├── CompanyClass
└── [Other Domain Models]
```

### 3. Data Access Patterns

- **Active Record Pattern**: Models represent database tables
- **Query Builder**: Base model provides query building methods
- **Relationship Handling**: Manual relationship management in domain classes

## Business Logic Layer

### 1. Controller Architecture

Controllers handle HTTP requests and coordinate between models and views:

- **Action-Based Routing**: Controllers respond to specific actions (save, update, delete, load)
- **Endpoint Handling**: Different endpoints for different entity types
- **Transaction Management**: Database transactions for complex operations

### 2. Service Layer Pattern

Business logic is encapsulated in controller methods:

```php
// Example from hrm_controller.php
if($_GET['action'] == 'save' && $_GET['endpoint'] == 'employee') {
    // Begin transaction
    $GLOBALS['conn']->begin_transaction();
    
    // Process employee data
    // Handle related entities (education, projects, etc.)
    // Create user account
    
    // Commit or rollback
}
```

### 3. Validation and Security

- **Input Sanitization**: `escapePostData()` function sanitizes all input
- **SQL Injection Prevention**: Prepared statements and parameter binding
- **Authorization Checks**: Permission validation before operations

## Presentation Layer

### 1. Template System

- **PHP Templates**: Direct PHP rendering for views
- **Shared Components**: Common header, footer, and sidebar components
- **Asset Management**: CSS, JavaScript, and image assets organized by type

### 2. AJAX Integration

- **JSON Responses**: Controllers return JSON for AJAX requests
- **Client-Side Processing**: JavaScript modules handle UI interactions
- **Real-Time Updates**: Dynamic content updates without page refresh

## Integration Patterns

### 1. Menu Configuration System

Dynamic menu generation based on user permissions:

```php
function get_menu_config() {
    return [
        'hrm' => [
            'folder' => 'hrm',
            'default' => 'employees',
            'auth' => ['manage_employees', 'manage_employee_docs'],
            'sub' => [
                'employees' => [...],
                'documents' => [...]
            ]
        ]
    ];
}
```

### 2. Permission System Integration

- **Role-Based Permissions**: Permissions stored in database and cached in session
- **Menu Filtering**: Menus filtered based on user permissions
- **Action Authorization**: Each action validates required permissions

### 3. Cross-Module Communication

- **Global Classes**: Domain classes available globally via `$GLOBALS`
- **Shared Utilities**: Common functions available across modules
- **Data Sharing**: Session-based data sharing between requests

## Error Handling and Logging

### 1. Exception Handling

- **Try-Catch Blocks**: Database operations wrapped in exception handling
- **Transaction Rollback**: Automatic rollback on errors
- **Error Responses**: Structured JSON error responses

### 2. Validation Framework

- **Server-Side Validation**: Input validation in controllers
- **Client-Side Validation**: JavaScript validation for user experience
- **Error Messaging**: User-friendly error messages

## Performance Considerations

### 1. Database Optimization

- **Prepared Statements**: Reusable query execution
- **Connection Reuse**: Single connection shared across requests
- **Batch Processing**: Bulk operations for large datasets

### 2. Caching Strategies

- **Session Caching**: User permissions cached in session
- **Entity Caching**: Frequently accessed entities cached during bulk operations
- **Menu Caching**: Menu configuration cached for performance

## Security Architecture

### 1. Authentication Security

- **Password Hashing**: `password_hash()` with default algorithm
- **Session Management**: Secure session handling
- **Login Tracking**: User login/logout tracking

### 2. Authorization Security

- **Permission-Based Access**: Every action requires specific permissions
- **SQL Injection Prevention**: Prepared statements and input sanitization
- **XSS Prevention**: Output escaping and validation

### 3. Data Security

- **Input Sanitization**: All user input sanitized before processing
- **File Upload Security**: File type and size validation
- **Database Security**: Parameterized queries prevent injection attacks

## Scalability Considerations

### 1. Modular Design

- **Separation of Concerns**: Clear separation between data, business, and presentation layers
- **Module Independence**: Each module can be developed and maintained independently
- **Extensibility**: New modules can be added following existing patterns

### 2. Database Design

- **Normalized Schema**: Proper database normalization for data integrity
- **Relationship Management**: Clear foreign key relationships
- **Index Strategy**: Database indexes for performance optimization

This architecture provides a solid foundation for a comprehensive HRM system while maintaining flexibility for future enhancements and modifications.