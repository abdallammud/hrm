# Employee Management Database Schema

## Overview

This document provides detailed information about the database tables and relationships that support the Employee Management system. The schema is designed to handle comprehensive employee information, organizational relationships, and integration with other HR modules.

## Core Employee Tables

### 1. employees (Primary Entity)

**Purpose**: Central table storing all employee information and serving as the primary entity for HR operations.

**Table Structure**:
```sql
CREATE TABLE employees (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_no VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone_number VARCHAR(20),
    national_id VARCHAR(50),
    gender ENUM('Male', 'Female'),
    date_of_birth DATE,
    address TEXT,
    city VARCHAR(100),
    state_id INT,
    branch_id INT NOT NULL,
    location_id INT NOT NULL,
    position VARCHAR(255),
    designation VARCHAR(255),
    hire_date DATE NOT NULL,
    contract_start DATE,
    contract_end DATE,
    contract_type VARCHAR(100),
    work_days INT DEFAULT 5,
    work_hours INT DEFAULT 8,
    salary DECIMAL(10,2),
    tax_exempt ENUM('Yes', 'No') DEFAULT 'No',
    grade VARCHAR(50),
    seniority VARCHAR(50),
    moh_contract ENUM('Yes', 'No') DEFAULT 'No',
    payment_bank VARCHAR(255),
    payment_account VARCHAR(100),
    status ENUM('Active', 'Inactive', 'Suspended', 'Deleted') DEFAULT 'Active',
    avatar VARCHAR(255),
    added_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (state_id) REFERENCES states(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (location_id) REFERENCES locations(id),
    FOREIGN KEY (added_by) REFERENCES users(user_id),
    FOREIGN KEY (updated_by) REFERENCES users(user_id),
    
    INDEX idx_staff_no (staff_no),
    INDEX idx_email (email),
    INDEX idx_full_name (full_name),
    INDEX idx_status (status),
    INDEX idx_branch_id (branch_id),
    INDEX idx_location_id (location_id),
    INDEX idx_state_id (state_id)
);
```

**Field Descriptions**:

| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| employee_id | INT | Primary key, auto-increment | NOT NULL, AUTO_INCREMENT |
| staff_no | VARCHAR(50) | Unique staff identifier | UNIQUE, NOT NULL |
| full_name | VARCHAR(255) | Employee's complete name | NOT NULL |
| email | VARCHAR(255) | Employee's email address | UNIQUE, NOT NULL |
| phone_number | VARCHAR(20) | Contact phone number | Optional |
| national_id | VARCHAR(50) | National identification number | Optional |
| gender | ENUM | Employee gender | 'Male', 'Female' |
| date_of_birth | DATE | Birth date | Optional |
| address | TEXT | Physical address | Optional |
| city | VARCHAR(100) | City of residence | Optional |
| state_id | INT | Foreign key to states table | References states(id) |
| branch_id | INT | Foreign key to branches table | NOT NULL, References branches(id) |
| location_id | INT | Foreign key to locations table | NOT NULL, References locations(id) |
| position | VARCHAR(255) | Job title/position | Optional |
| designation | VARCHAR(255) | Official designation | Optional |
| hire_date | DATE | Employment start date | NOT NULL |
| contract_start | DATE | Current contract start date | Optional |
| contract_end | DATE | Current contract end date | Optional |
| contract_type | VARCHAR(100) | Type of employment contract | Optional |
| work_days | INT | Working days per week | Default: 5 |
| work_hours | INT | Working hours per day | Default: 8 |
| salary | DECIMAL(10,2) | Base salary amount | Optional |
| tax_exempt | ENUM | Tax exemption status | 'Yes', 'No', Default: 'No' |
| grade | VARCHAR(50) | Employee grade/level | Optional |
| seniority | VARCHAR(50) | Seniority level | Optional |
| moh_contract | ENUM | Ministry of Health contract | 'Yes', 'No', Default: 'No' |
| payment_bank | VARCHAR(255) | Bank for salary payments | Optional |
| payment_account | VARCHAR(100) | Bank account number | Optional |
| status | ENUM | Employee status | 'Active', 'Inactive', 'Suspended', 'Deleted' |
| avatar | VARCHAR(255) | Profile picture filename | Optional |
| added_by | INT | User who created record | References users(user_id) |
| updated_by | INT | User who last updated record | References users(user_id) |
| created_at | TIMESTAMP | Record creation timestamp | Default: CURRENT_TIMESTAMP |
| updated_date | TIMESTAMP | Last update timestamp | Auto-update on change |

**Business Rules**:
- Staff numbers are auto-generated using system prefix + employee_id
- Email addresses must be unique across all employees
- Branch and location assignments are mandatory
- Salary information is optional but required for payroll processing
- Status changes are tracked through audit fields

## Employee Relationship Tables

### 2. employee_projects (Many-to-Many)

**Purpose**: Links employees to projects they are assigned to work on.

**Table Structure**:
```sql
CREATE TABLE employee_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    project_id INT NOT NULL,
    assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (emp_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_assignment (emp_id, project_id),
    INDEX idx_emp_id (emp_id),
    INDEX idx_project_id (project_id)
);
```

**Key Operations**:
```php
// Assign projects to employee (replaces all existing assignments)
public function assignProjects(int $employeeId, array $projectIds): void
{
    $GLOBALS['conn']->begin_transaction();
    
    // Remove existing assignments
    $stmt = $GLOBALS['conn']->prepare('DELETE FROM employee_projects WHERE emp_id = ?');
    $stmt->bind_param('i', $employeeId);
    $stmt->execute();
    
    // Add new assignments
    $stmt = $GLOBALS['conn']->prepare('INSERT INTO employee_projects (emp_id, project_id) VALUES (?, ?)');
    foreach ($projectIds as $pid) {
        $stmt->bind_param('ii', $employeeId, $pid);
        $stmt->execute();
    }
    
    $GLOBALS['conn']->commit();
}
```

### 3. employee_budget_codes (Many-to-Many)

**Purpose**: Links employees to budget codes for financial tracking and reporting.

**Table Structure**:
```sql
CREATE TABLE employee_budget_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    code_id INT NOT NULL,
    assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (emp_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    FOREIGN KEY (code_id) REFERENCES budget_codes(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_budget_assignment (emp_id, code_id),
    INDEX idx_emp_id (emp_id),
    INDEX idx_code_id (code_id)
);
```

### 4. employee_education (One-to-Many)

**Purpose**: Stores educational qualifications and history for each employee.

**Table Structure**:
```sql
CREATE TABLE employee_education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    degree VARCHAR(255) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    field_of_study VARCHAR(255),
    start_year YEAR,
    graduation_year YEAR,
    grade VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    
    INDEX idx_employee_id (employee_id),
    INDEX idx_graduation_year (graduation_year)
);
```

**Usage Example**:
```php
// Get employee education history
public function getEducation(int $employeeId): array
{
    return $this->runQuery(
        'SELECT * FROM employee_education WHERE employee_id = ? ORDER BY start_year DESC',
        [$employeeId],
        'i'
    );
}
```

### 5. employee_docs (One-to-Many)

**Purpose**: Manages document storage and organization for employees.

**Table Structure**:
```sql
CREATE TABLE employee_docs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    folder_id INT,
    folder_name VARCHAR(255),
    type_id INT,
    type_name VARCHAR(255),
    emp_id INT NOT NULL,
    employee_id INT NOT NULL,
    full_name VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    expiration_date DATE,
    document VARCHAR(255) NOT NULL,
    created_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (emp_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    FOREIGN KEY (folder_id) REFERENCES folders(id),
    FOREIGN KEY (type_id) REFERENCES document_types(id),
    FOREIGN KEY (created_by) REFERENCES users(user_id),
    FOREIGN KEY (updated_by) REFERENCES users(user_id),
    
    INDEX idx_emp_id (emp_id),
    INDEX idx_folder_id (folder_id),
    INDEX idx_type_id (type_id),
    INDEX idx_expiration_date (expiration_date)
);
```

## Supporting Tables

### 6. users (Employee System Access)

**Purpose**: Manages system user accounts linked to employee records.

**Relevant Fields**:
```sql
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT UNIQUE,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    phone VARCHAR(20),
    role INT,
    branch_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (emp_id) REFERENCES employees(employee_id) ON DELETE SET NULL,
    FOREIGN KEY (role) REFERENCES sys_roles(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id)
);
```

**Employee-User Relationship**:
```php
// Get user account for employee
public function getUser(int $employeeId): ?array
{
    $result = $this->runQuery(
        'SELECT * FROM users WHERE emp_id = ? LIMIT 1',
        [$employeeId],
        'i'
    );
    return $result[0] ?? null;
}
```

### 7. Organizational Structure Tables

#### branches
```sql
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    contact_email VARCHAR(255),
    contact_phone VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### locations
```sql
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    city_name VARCHAR(255),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### states
```sql
CREATE TABLE states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    country_name VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Complex Queries and Operations

### Employee Profile Retrieval

**Complete Employee Profile Query**:
```sql
SELECT 
    e.*,
    s.name AS state_name,
    s.country_name,
    l.name AS location_name,
    l.city_name AS location_city,
    b.name AS branch_name,
    b.address AS branch_address,
    b.contact_email AS branch_email,
    b.contact_phone AS branch_phone,
    GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ", ") AS projects,
    GROUP_CONCAT(DISTINCT bc.name ORDER BY bc.name SEPARATOR ", ") AS budget_codes
FROM employees e
LEFT JOIN states s ON s.id = e.state_id
LEFT JOIN locations l ON l.id = e.location_id
LEFT JOIN branches b ON b.id = e.branch_id
LEFT JOIN employee_projects ep ON ep.emp_id = e.employee_id
LEFT JOIN projects p ON p.id = ep.project_id
LEFT JOIN employee_budget_codes ebc ON ebc.emp_id = e.employee_id
LEFT JOIN budget_codes bc ON bc.id = ebc.code_id
WHERE e.employee_id = ?
GROUP BY e.employee_id;
```

### Employee Search and Filtering

**Multi-criteria Employee Search**:
```sql
SELECT 
    e.employee_id,
    e.staff_no,
    e.full_name,
    e.email,
    e.phone_number,
    e.position,
    e.hire_date,
    e.salary,
    e.status,
    b.name AS branch_name,
    l.name AS location_name,
    s.name AS state_name
FROM employees e
LEFT JOIN branches b ON b.id = e.branch_id
LEFT JOIN locations l ON l.id = e.location_id
LEFT JOIN states s ON s.id = e.state_id
WHERE 1=1
    AND (? = '' OR b.id = ?)
    AND (? = '' OR s.id = ?)
    AND (? = '' OR l.id = ?)
    AND (? = '' OR e.status = ?)
    AND (? = '' OR e.full_name LIKE CONCAT('%', ?, '%'))
ORDER BY e.full_name ASC
LIMIT ? OFFSET ?;
```

### Bulk Operations

**Employee Deletion with Cascade**:
```sql
-- Delete from related tables first
DELETE FROM atten_details WHERE emp_id = ?;
DELETE FROM employee_budget_codes WHERE emp_id = ?;
DELETE FROM employee_docs WHERE emp_id = ?;
DELETE FROM employee_education WHERE emp_id = ?;
DELETE FROM employee_leave WHERE emp_id = ?;
DELETE FROM employee_performance WHERE emp_id = ?;
DELETE FROM employee_projects WHERE emp_id = ?;
DELETE FROM employee_transactions WHERE emp_id = ?;
DELETE FROM payroll_details WHERE emp_id = ?;
DELETE FROM training_list WHERE emp_id = ?;

-- Finally delete the employee record
DELETE FROM employees WHERE employee_id = ?;
```

## Data Integrity and Constraints

### Primary Key Constraints
- All tables have auto-incrementing primary keys
- Composite unique constraints for relationship tables

### Foreign Key Constraints
- Cascading deletes for dependent records
- SET NULL for optional relationships
- Referential integrity enforcement

### Business Logic Constraints
- Unique staff numbers and email addresses
- Required organizational assignments
- Status validation and workflow rules
- Date range validations for contracts

### Indexing Strategy

**Performance Indexes**:
```sql
-- Employee search optimization
CREATE INDEX idx_employee_search ON employees(full_name, staff_no, email);

-- Organizational filtering
CREATE INDEX idx_employee_org ON employees(branch_id, location_id, state_id);

-- Status and date filtering
CREATE INDEX idx_employee_status_date ON employees(status, hire_date);

-- Relationship table optimization
CREATE INDEX idx_emp_projects_lookup ON employee_projects(emp_id, project_id);
CREATE INDEX idx_emp_budget_lookup ON employee_budget_codes(emp_id, code_id);
```

## Integration Points

### Payroll System Integration
- Employee salary and bank account information
- Work schedule data for payroll calculations
- Status changes affecting payroll eligibility

### Attendance System Integration
- Work schedule validation
- Location-based attendance tracking
- Project time allocation

### Performance Management Integration
- Employee profile data for performance reviews
- Organizational hierarchy for reporting relationships
- Historical data for performance trending

### Security and Access Control
- User account synchronization
- Role-based data access permissions
- Audit trail maintenance

## Data Migration and Maintenance

### Bulk Import Structure
```csv
staff_no,full_name,phone_number,email,gender,national_id,date_of_birth,city,address,payment_bank,payment_account,branch,designation,state,location,hire_date,contract_start,contract_end,contract_type,salary,tax_exempt,budget_codes,projects,moh_contract,work_days,work_hours,grade,seniority
```

### Maintenance Queries

**Clean up orphaned records**:
```sql
-- Find employees without user accounts
SELECT e.employee_id, e.full_name, e.email 
FROM employees e 
LEFT JOIN users u ON u.emp_id = e.employee_id 
WHERE u.emp_id IS NULL AND e.status = 'Active';

-- Find inactive employees with active user accounts
SELECT e.employee_id, e.full_name, u.username, u.status 
FROM employees e 
JOIN users u ON u.emp_id = e.employee_id 
WHERE e.status != 'Active' AND u.status = 'active';
```

**Data consistency checks**:
```sql
-- Verify all employees have required organizational assignments
SELECT employee_id, full_name 
FROM employees 
WHERE branch_id IS NULL OR location_id IS NULL OR state_id IS NULL;

-- Check for duplicate staff numbers
SELECT staff_no, COUNT(*) as count 
FROM employees 
GROUP BY staff_no 
HAVING count > 1;
```