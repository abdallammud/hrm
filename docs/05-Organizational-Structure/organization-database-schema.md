# Organizational Database Schema

## Overview

This document provides comprehensive documentation of the database schema for organizational structure management in the HRM application. The schema supports multi-level organizational hierarchies, geographical organization, and various organizational entities required for comprehensive HR management.

## Core Organizational Tables

### 1. company
**Purpose**: Central company information and configuration  
**Primary Key**: `id`  
**Engine**: InnoDB  
**Character Set**: UTF-8

| Field | Type | Null | Default | Description |
|-------|------|------|---------|-------------|
| id | INT AUTO_INCREMENT | NO | NULL | Primary key |
| name | VARCHAR(255) | NO | NULL | Company name |
| address | TEXT | YES | NULL | Company physical address |
| contact_phone | VARCHAR(255) | YES | NULL | Contact phone numbers (pipe-separated) |
| contact_email | VARCHAR(255) | YES | NULL | Contact email addresses (pipe-separated) |
| website | VARCHAR(255) | YES | NULL | Company website URL |
| logo | VARCHAR(255) | YES | NULL | Logo file path |
| status | ENUM('active', 'inactive') | NO | 'active' | Company status |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Last update timestamp |

#### Indexes
- PRIMARY KEY (`id`)
- UNIQUE KEY `name` (`name`)
- INDEX `status` (`status`)

#### Sample Data
```sql
INSERT INTO company (name, address, contact_phone, contact_email) VALUES
('Hawlkar Tech Solutions', 'Bondhere Mogadishu Somalia', '0610000000 | 0614444444', 'info@hawlkar.com | sales@hawlkar.com');
```

### 2. branches
**Purpose**: Company branches/departments management  
**Primary Key**: `id`  
**Engine**: InnoDB  
**Character Set**: UTF-8

| Field | Type | Null | Default | Description |
|-------|------|------|---------|-------------|
| id | INT AUTO_INCREMENT | NO | NULL | Primary key |
| name | VARCHAR(255) | NO | NULL | Branch/department name |
| address | TEXT | YES | NULL | Branch physical address |
| contact_phone | VARCHAR(255) | YES | NULL | Branch contact phone |
| contact_email | VARCHAR(255) | YES | NULL | Branch contact email |
| status | ENUM('active', 'inactive') | NO | 'active' | Branch status |
| added_by | INT | YES | NULL | User who created the branch |
| added_date | TIMESTAMP | NO | CURRENT_TIMESTAMP | Creation timestamp |
| updated_by | INT | YES | NULL | User who last updated |
| updated_date | TIMESTAMP | YES | NULL | Last update timestamp |

#### Indexes
- PRIMARY KEY (`id`)
- UNIQUE KEY `name` (`name`)
- INDEX `status` (`status`)
- INDEX `added_by` (`added_by`)

#### Foreign Key Relationships
```sql
ALTER TABLE branches 
ADD CONSTRAINT fk_branches_added_by 
FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL;

ALTER TABLE branches 
ADD CONSTRAINT fk_branches_updated_by 
FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL;
```

### 3. countries
**Purpose**: International country management  
**Primary Key**: `country_id`  
**Engine**: InnoDB  
**Character Set**: UTF-8

| Field | Type | Null | Default | Description |
|-------|------|------|---------|-------------|
| country_id | INT AUTO_INCREMENT | NO | NULL | Primary key |
| country_name | VARCHAR(255) | NO | NULL | Country name |
| country_code | VARCHAR(10) | YES | NULL | ISO country code |
| is_default | ENUM('Yes', 'No') | NO | 'No' | Default country flag |
| status | ENUM('active', 'inactive') | NO | 'active' | Country status |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | Record creation timestamp |

#### Indexes
- PRIMARY KEY (`country_id`)
- UNIQUE KEY `country_name` (`country_name`)
- UNIQUE KEY `country_code` (`country_code`)
- INDEX `is_default` (`is_default`)
- INDEX `status` (`status`)

#### Sample Data
```sql
INSERT INTO countries (country_name, country_code, is_default) VALUES
('Somalia', 'SO', 'Yes'),
('United States', 'US', 'No'),
('United Kingdom', 'UK', 'No'),
('Canada', 'CA', 'No');
```

### 4. states
**Purpose**: State/province management with tax configuration  
**Primary Key**: `id`  
**Engine**: InnoDB  
**Character Set**: UTF-8

| Field | Type | Null | Default | Description |
|-------|------|------|---------|-------------|
| id | INT AUTO_INCREMENT | NO | NULL | Primary key |
| name | VARCHAR(255) | NO | NULL | State/province name |
| country_id | INT | YES | NULL | Foreign key to countries table |
| country_name | VARCHAR(255) | YES | NULL | Country name (denormalized) |
| tax_grid | JSON | YES | NULL | Tax bracket configuration |
| stamp_duty | DECIMAL(10,2) | NO | 0.00 | State stamp duty amount |
| status | ENUM('active', 'inactive') | NO | 'active' | State status |
| added_by | INT | YES | NULL | User who created the state |
| added_date | TIMESTAMP | NO | CURRENT_TIMESTAMP | Creation timestamp |
| updated_by | INT | YES | NULL | User who last updated |
| updated_date | TIMESTAMP | YES | NULL | Last update timestamp |

#### Indexes
- PRIMARY KEY (`id`)
- UNIQUE KEY `name_country` (`name`, `country_id`)
- INDEX `country_id` (`country_id`)
- INDEX `status` (`status`)
- INDEX `added_by` (`added_by`)

#### Foreign Key Relationships
```sql
ALTER TABLE states 
ADD CONSTRAINT fk_states_country 
FOREIGN KEY (country_id) REFERENCES countries(country_id) ON DELETE SET NULL;

ALTER TABLE states 
ADD CONSTRAINT fk_states_added_by 
FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL;
```

#### Tax Grid JSON Structure
```json
[
    {
        "min_amount": 0,
        "max_amount": 1000,
        "rate": 5
    },
    {
        "min_amount": 1001,
        "max_amount": 5000,
        "rate": 10
    },
    {
        "min_amount": 5001,
        "max_amount": 10000,
        "rate": 15
    }
]
```

### 5. locations
**Purpose**: Physical duty location management  
**Primary Key**: `id`  
**Engine**: InnoDB  
**Character Set**: UTF-8

| Field | Type | Null | Default | Description |
|-------|------|------|---------|-------------|
| id | INT AUTO_INCREMENT | NO | NULL | Primary key |
| name | VARCHAR(255) | NO | NULL | Location name |
| state_id | INT | YES | NULL | Foreign key to states table |
| state_name | VARCHAR(255) | YES | NULL | State name (denormalized) |
| city_name | VARCHAR(255) | YES | NULL | City name |
| address | TEXT | YES | NULL | Location address |
| status | ENUM('active', 'inactive') | NO | 'active' | Location status |
| added_by | INT | YES | NULL | User who created the location |
| added_date | TIMESTAMP | NO | CURRENT_TIMESTAMP | Creation timestamp |
| updated_by | INT | YES | NULL | User who last updated |
| updated_date | TIMESTAMP | YES | NULL | Last update timestamp |

#### Indexes
- PRIMARY KEY (`id`)
- UNIQUE KEY `name_state` (`name`, `state_id`)
- INDEX `state_id` (`state_id`)
- INDEX `city_name` (`city_name`)
- INDEX `status` (`status`)
- INDEX `added_by` (`added_by`)

#### Foreign Key Relationships
```sql
ALTER TABLE locations 
ADD CONSTRAINT fk_locations_state 
FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE SET NULL;

ALTER TABLE locations 
ADD CONSTRAINT fk_locations_added_by 
FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL;
```

## Supporting Organizational Tables

### 6. designations
**Purpose**: Job titles and position management  
**Primary Key**: `id`  
**Engine**: InnoDB  
**Character Set**: UTF-8

| Field | Type | Null | Default | Description |
|-------|------|------|---------|-------------|
| id | INT AUTO_INCREMENT | NO | NULL | Primary key |
| name | VARCHAR(255) | NO | NULL | Designation name |
| status | ENUM('active', 'inactive') | NO | 'active' | Designation status |
| added_by | INT | YES | NULL | User who created the designation |
| added_date | TIMESTAMP | NO | CURRENT_TIMESTAMP | Creation timestamp |
| updated_by | INT | YES | NULL | User who last updated |
| updated_date | TIMESTAMP | YES | NULL | Last update timestamp |

#### Indexes
- PRIMARY KEY (`id`)
- UNIQUE KEY `name` (`name`)
- INDEX `status` (`status`)

### 7. projects
**Purpose**: Company project management  
**Primary Key**: `id`  
**Engine**: InnoDB  
**Character Set**: UTF-8

| Field | Type | Null | Default | Description |
|-------|------|------|---------|-------------|
| id | INT AUTO_INCREMENT | NO | NULL | Primary key |
| name | VARCHAR(255) | NO | NULL | Project name |
| comments | TEXT | YES | NULL | Project description/comments |
| status | ENUM('active', 'inactive', 'completed') | NO | 'active' | Project status |
| added_by | INT | YES | NULL | User who created the project |
| added_date | TIMESTAMP | NO | CURRENT_TIMESTAMP | Creation timestamp |
| updated_by | INT | YES | NULL | User who last updated |
| updated_date | TIMESTAMP | YES | NULL | Last update timestamp |

#### Indexes
- PRIMARY KEY (`id`)
- UNIQUE KEY `name` (`name`)
- INDEX `status` (`status`)

### 8. contract_types
**Purpose**: Employment contract type management  
**Primary Key**: `id`  
**Engine**: InnoDB  
**Character Set**: UTF-8

| Field | Type | Null | Default | Description |
|-------|------|------|---------|-------------|
| id | INT AUTO_INCREMENT | NO | NULL | Primary key |
| name | VARCHAR(255) | NO | NULL | Contract type name |
| status | ENUM('active', 'inactive') | NO | 'active' | Contract type status |
| added_by | INT | YES | NULL | User who created the contract type |
| added_date | TIMESTAMP | NO | CURRENT_TIMESTAMP | Creation timestamp |

#### Sample Data
```sql
INSERT INTO contract_types (name) VALUES
('Full-time Permanent'),
('Part-time Permanent'),
('Fixed-term Contract'),
('Temporary'),
('Consultant'),
('Intern');
```

### 9. budget_codes
**Purpose**: Budget tracking and financial categorization  
**Primary Key**: `id`  
**Engine**: InnoDB  
**Character Set**: UTF-8

| Field | Type | Null | Default | Description |
|-------|------|------|---------|-------------|
| id | INT AUTO_INCREMENT | NO | NULL | Primary key |
| name | VARCHAR(255) | NO | NULL | Budget code name |
| comments | TEXT | YES | NULL | Budget code description |
| status | ENUM('active', 'inactive') | NO | 'active' | Budget code status |
| added_by | INT | YES | NULL | User who created the budget code |
| added_date | TIMESTAMP | NO | CURRENT_TIMESTAMP | Creation timestamp |

## Employee Integration Tables

### Employee Organizational Assignments
The `employees` table contains foreign key references to organizational entities:

```sql
-- Employee organizational assignments
ALTER TABLE employees 
ADD COLUMN branch_id INT,
ADD COLUMN location_id INT,
ADD COLUMN state_id INT,
ADD COLUMN designation VARCHAR(255),
ADD COLUMN project_id VARCHAR(250), -- Comma-separated project IDs
ADD COLUMN contract_type VARCHAR(255);

-- Foreign key constraints
ALTER TABLE employees 
ADD CONSTRAINT fk_employees_branch 
FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL;

ALTER TABLE employees 
ADD CONSTRAINT fk_employees_location 
FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL;

ALTER TABLE employees 
ADD CONSTRAINT fk_employees_state 
FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE SET NULL;
```

## Database Relationships

### Hierarchical Relationships

#### Geographical Hierarchy
```sql
-- Country → State → Location hierarchy
countries (country_id) 
    ← states (country_id)
        ← locations (state_id)
```

#### Organizational Hierarchy
```sql
-- Company → Branch → Employee hierarchy
company (id)
    ← branches (company_id) -- Implied relationship
        ← employees (branch_id)
```

#### Employee Assignment Relationships
```sql
-- Employee multi-dimensional assignments
employees (employee_id)
    → branches (branch_id)
    → locations (location_id)
    → states (state_id)
    → designations (designation)
    → projects (project_id) -- Many-to-many via comma-separated values
    → contract_types (contract_type)
```

### Referential Integrity Constraints

#### Cascade Rules
- **ON DELETE SET NULL**: Preserve employee records when organizational entities are deleted
- **ON UPDATE CASCADE**: Automatically update references when primary keys change
- **Foreign Key Constraints**: Ensure data integrity across organizational relationships

#### Data Consistency Rules
```sql
-- Ensure location belongs to correct state
DELIMITER //
CREATE TRIGGER check_location_state_consistency
BEFORE INSERT ON employees
FOR EACH ROW
BEGIN
    DECLARE location_state_id INT;
    
    IF NEW.location_id IS NOT NULL AND NEW.state_id IS NOT NULL THEN
        SELECT state_id INTO location_state_id 
        FROM locations 
        WHERE id = NEW.location_id;
        
        IF location_state_id != NEW.state_id THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Employee state must match location state';
        END IF;
    END IF;
END//
DELIMITER ;
```

## Query Examples

### Basic Organizational Queries

#### Get Complete Company Structure
```sql
SELECT 
    c.name AS company_name,
    b.name AS branch_name,
    COUNT(e.employee_id) AS employee_count
FROM company c
LEFT JOIN branches b ON 1=1  -- Implied relationship
LEFT JOIN employees e ON e.branch_id = b.id
WHERE c.status = 'active' AND b.status = 'active'
GROUP BY c.id, b.id
ORDER BY c.name, b.name;
```

#### Get Geographical Distribution
```sql
SELECT 
    co.country_name,
    s.name AS state_name,
    l.name AS location_name,
    l.city_name,
    COUNT(e.employee_id) AS employee_count
FROM countries co
LEFT JOIN states s ON s.country_id = co.country_id
LEFT JOIN locations l ON l.state_id = s.id
LEFT JOIN employees e ON e.location_id = l.id
WHERE co.status = 'active' 
  AND s.status = 'active' 
  AND l.status = 'active'
GROUP BY co.country_id, s.id, l.id
ORDER BY co.country_name, s.name, l.name;
```

### Employee Assignment Queries

#### Get Employee Organizational Details
```sql
SELECT 
    e.employee_id,
    e.full_name,
    b.name AS branch_name,
    l.name AS location_name,
    l.city_name,
    s.name AS state_name,
    co.country_name,
    e.designation,
    e.contract_type
FROM employees e
LEFT JOIN branches b ON e.branch_id = b.id
LEFT JOIN locations l ON e.location_id = l.id
LEFT JOIN states s ON e.state_id = s.id
LEFT JOIN countries co ON s.country_id = co.country_id
WHERE e.status = 'active'
ORDER BY e.full_name;
```

#### Get Project Assignments
```sql
SELECT 
    e.employee_id,
    e.full_name,
    p.name AS project_name,
    p.status AS project_status
FROM employees e
JOIN projects p ON FIND_IN_SET(p.id, e.project_id) > 0
WHERE e.status = 'active' AND p.status = 'active'
ORDER BY e.full_name, p.name;
```

### Tax Calculation Queries

#### Get State Tax Information
```sql
SELECT 
    s.name AS state_name,
    s.tax_grid,
    s.stamp_duty,
    COUNT(e.employee_id) AS affected_employees
FROM states s
LEFT JOIN employees e ON e.state_id = s.id AND e.status = 'active'
WHERE s.status = 'active'
GROUP BY s.id
ORDER BY s.name;
```

#### Calculate Tax for Salary Range
```sql
-- Function to calculate tax based on state tax grid
DELIMITER //
CREATE FUNCTION calculate_state_tax(state_id INT, gross_salary DECIMAL(10,2))
RETURNS DECIMAL(10,2)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE tax_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE tax_grid_json JSON;
    
    SELECT tax_grid INTO tax_grid_json 
    FROM states 
    WHERE id = state_id;
    
    -- Tax calculation logic would be implemented here
    -- This is a simplified example
    
    RETURN tax_amount;
END//
DELIMITER ;
```

## Performance Optimization

### Indexing Strategy

#### Primary Indexes
- All tables have primary key indexes for optimal performance
- Unique constraints on name fields prevent duplicates
- Status indexes for efficient filtering

#### Composite Indexes
```sql
-- Optimize employee organizational queries
CREATE INDEX idx_employee_org ON employees (branch_id, location_id, state_id, status);

-- Optimize geographical hierarchy queries
CREATE INDEX idx_location_hierarchy ON locations (state_id, city_name, status);

-- Optimize state tax queries
CREATE INDEX idx_state_tax ON states (country_id, status);
```

#### Query Optimization
```sql
-- Optimize organizational reporting queries
CREATE INDEX idx_org_reporting ON employees (status, branch_id, location_id);

-- Optimize project assignment queries
CREATE INDEX idx_project_assignments ON employees (project_id, status);
```

### Data Archiving Strategy

#### Inactive Entity Management
```sql
-- Archive inactive organizational entities
CREATE TABLE archived_branches AS 
SELECT * FROM branches WHERE status = 'inactive';

CREATE TABLE archived_locations AS 
SELECT * FROM locations WHERE status = 'inactive';

-- Maintain referential integrity during archiving
UPDATE employees 
SET branch_id = NULL 
WHERE branch_id IN (SELECT id FROM branches WHERE status = 'inactive');
```

## Backup and Recovery

### Backup Strategy
```sql
-- Complete organizational structure backup
mysqldump --single-transaction --routines --triggers \
  --databases hrm_db \
  --tables company branches countries states locations \
           designations projects contract_types budget_codes \
  > organizational_backup.sql
```

### Recovery Procedures
```sql
-- Restore organizational structure
mysql hrm_db < organizational_backup.sql

-- Verify data integrity after restore
SELECT 
    'company' AS table_name, COUNT(*) AS record_count FROM company
UNION ALL
SELECT 'branches', COUNT(*) FROM branches
UNION ALL
SELECT 'states', COUNT(*) FROM states
UNION ALL
SELECT 'locations', COUNT(*) FROM locations;
```

## Data Migration Scripts

### Initial Setup Script
```sql
-- Create organizational structure tables
SOURCE create_organizational_tables.sql;

-- Insert default data
INSERT INTO countries (country_name, country_code, is_default) VALUES
('Somalia', 'SO', 'Yes');

INSERT INTO contract_types (name) VALUES
('Full-time Permanent'),
('Part-time Permanent'),
('Fixed-term Contract');

-- Create default company
INSERT INTO company (name, address, contact_phone, contact_email) VALUES
('Default Company', 'Default Address', '000-000-0000', 'admin@company.com');
```

### Data Validation Script
```sql
-- Validate organizational data integrity
SELECT 'Orphaned Employees - Branch' AS issue, COUNT(*) AS count
FROM employees e
LEFT JOIN branches b ON e.branch_id = b.id
WHERE e.branch_id IS NOT NULL AND b.id IS NULL

UNION ALL

SELECT 'Orphaned Employees - Location', COUNT(*)
FROM employees e
LEFT JOIN locations l ON e.location_id = l.id
WHERE e.location_id IS NOT NULL AND l.id IS NULL

UNION ALL

SELECT 'Orphaned Employees - State', COUNT(*)
FROM employees e
LEFT JOIN states s ON e.state_id = s.id
WHERE e.state_id IS NOT NULL AND s.id IS NULL;
```