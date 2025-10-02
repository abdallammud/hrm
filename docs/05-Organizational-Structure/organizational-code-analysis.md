# Organizational Structure Code Analysis

## Overview

This document provides a comprehensive analysis of the organizational hierarchy code and database structure in the HRM application. The system implements a multi-level organizational structure that supports companies, branches, geographical locations (states and duty locations), and various organizational entities like designations, projects, and budget codes.

## PHP Classes and Files

### Core Organizational Classes

#### 1. CompanyClass.php
```php
class Company extends Model {
    public function __construct() {
        parent::__construct('company');
    }

    public function get($id) {
        return $this->read($id);
    }
}
```
- **Purpose**: Manages company information and settings
- **Table**: `company`
- **Key Methods**: Inherits CRUD operations from Model base class, plus custom `get()` method
- **Global Instance**: `$GLOBALS['companyClass']`

#### 2. BranchClass.php
```php
class Branch extends Model {
    public function __construct() {
        parent::__construct('branches');
    }
}
```
- **Purpose**: Manages company branches/departments
- **Table**: `branches`
- **Key Methods**: Standard CRUD operations via Model inheritance
- **Global Instance**: `$GLOBALS['branchClass']`

#### 3. StatesClass.php
```php
class States extends Model {
    public function __construct() {
        parent::__construct('states');
    }
}
```
- **Purpose**: Manages states/provinces for geographical organization
- **Table**: `states`
- **Key Methods**: Standard CRUD operations via Model inheritance
- **Global Instance**: `$GLOBALS['statesClass']`

#### 4. LocationsClass.php
```php
class Locations extends Model {
    public function __construct() {
        parent::__construct('locations');
    }
}
```
- **Purpose**: Manages physical duty locations/offices
- **Table**: `locations`
- **Key Methods**: Standard CRUD operations via Model inheritance
- **Global Instance**: `$GLOBALS['locationsClass']`

#### 5. CountryClass.php
```php
class Country extends Model {
    public function __construct() {
        parent::__construct('countries', 'country_id');
    }
}
```
- **Purpose**: Manages countries for international operations
- **Table**: `countries`
- **Primary Key**: `country_id` (custom primary key)
- **Global Instance**: `$GLOBALS['countryClass']`

### Supporting Organizational Classes (MiscClass.php)

#### 6. Designations
```php
class Designations extends Model {
    public function __construct() {
        parent::__construct('designations');
    }
}
```
- **Purpose**: Manages job titles/positions
- **Table**: `designations`

#### 7. Projects
```php
class Projects extends Model {
    public function __construct() {
        parent::__construct('projects');
    }
}
```
- **Purpose**: Manages company projects for employee assignments
- **Table**: `projects`

#### 8. ContractTypes
```php
class ContractTypes extends Model {
    public function __construct() {
        parent::__construct('contract_types');
    }
}
```
- **Purpose**: Manages employment contract types
- **Table**: `contract_types`

#### 9. BudgetCodes
```php
class BudgetCodes extends Model {
    public function __construct() {
        parent::__construct('budget_codes');
    }
}
```
- **Purpose**: Manages budget codes for financial tracking
- **Table**: `budget_codes`

## Controller Analysis (org_controller.php)

### Main Controller Structure
The `org_controller.php` file handles all organizational entity management through a REST-like API structure:

```php
if(isset($_GET['action'])) {
    if(isset($_GET['endpoint'])) {
        // Action routing: save, update, delete
        if($_GET['action'] == 'save') {
            // Entity routing: company, branch, state, location, etc.
            if($_GET['endpoint'] == 'company') {
                // Company creation logic
            } else if($_GET['endpoint'] == 'branch') {
                // Branch creation logic
            }
            // ... other endpoints
        }
    }
}
```

### Supported Endpoints and Actions

#### Company Management
- **Endpoint**: `company`
- **Actions**: `save`, `update`
- **Data Fields**: name, address, contact_phone, contact_email
- **Permissions**: `create_organization`, `edit_organization`

#### Branch Management
- **Endpoint**: `branch`
- **Actions**: `save`, `update`
- **Data Fields**: name, address, contact_phone, contact_email
- **Permissions**: `create_departments`, `edit_departments`

#### State Management
- **Endpoint**: `state`
- **Actions**: `save`, `update`
- **Data Fields**: name, country_id, country_name, tax_grid, stamp_duty
- **Permissions**: `create_states`, `edit_states`
- **Special Features**: Tax grid configuration (JSON format)

#### Location Management
- **Endpoint**: `location`
- **Actions**: `save`, `update`
- **Data Fields**: name, state_id, state_name, city_name
- **Permissions**: `create_duty_locations`, `edit_duty_locations`

#### Additional Endpoints
- **designation**: Job titles/positions
- **project**: Company projects
- **contract_type**: Employment contract types
- **budget_code**: Budget tracking codes
- **bank**: Bank information
- **bank_account**: Bank account details

## Database Structure Analysis

### Hierarchical Relationships

#### Geographical Hierarchy
```
Countries (countries)
    └── States (states)
        └── Locations (locations)
```

#### Organizational Hierarchy
```
Company (company)
    └── Branches (branches)
        └── Employees (employees)
```

#### Employee Assignment Structure
```
Employee (employees)
    ├── Branch (branch_id)
    ├── Location (location_id)
    ├── State (state_id)
    ├── Designation (designation)
    ├── Projects (project_id - comma-separated)
    └── Contract Type (contract_type)
```

### Key Database Tables

#### company
- **Primary Key**: id
- **Fields**: name, address, phone, email, website, logo
- **Purpose**: Central company information

#### branches
- **Primary Key**: id
- **Fields**: name, address, contact_email, contact_phone, status
- **Purpose**: Company departments/divisions

#### states
- **Primary Key**: id
- **Fields**: name, country_name, tax_grid (JSON), stamp_duty, status
- **Purpose**: Geographical states with tax configuration

#### locations
- **Primary Key**: id
- **Fields**: name, city_name, state_id, state_name, address, status
- **Purpose**: Physical duty locations

#### countries
- **Primary Key**: country_id
- **Fields**: name, code, status
- **Purpose**: International country management

## Workflow Analysis

### Entity Creation Workflows

#### 1. Company Creation Workflow
```
User Input (org_add.php) 
    → Form Validation 
    → POST to org_controller.php?action=save&endpoint=company
    → Permission Check (create_organization)
    → Duplicate Check (check_exists)
    → CompanyClass->create()
    → JSON Response
```

#### 2. Branch Creation Workflow
```
User Input (branch_add.php)
    → Form Validation
    → POST to org_controller.php?action=save&endpoint=branch
    → Permission Check (create_departments)
    → Duplicate Check (check_exists)
    → BranchClass->create()
    → JSON Response
```

#### 3. State Creation Workflow
```
User Input (state_add.php)
    → Tax Grid Configuration
    → Form Validation
    → POST to org_controller.php?action=save&endpoint=state
    → Permission Check (create_states)
    → Duplicate Check (check_exists)
    → Tax Grid JSON Encoding
    → StatesClass->create()
    → JSON Response
```

#### 4. Location Creation Workflow
```
User Input (location_add.php)
    → State Selection
    → Form Validation
    → POST to org_controller.php?action=save&endpoint=location
    → Permission Check (create_duty_locations)
    → Duplicate Check (check_exists)
    → LocationsClass->create()
    → JSON Response
```

### Entity Update Workflows

All entities follow similar update patterns:
```
User Input (entity_edit.php)
    → Form Pre-population
    → Form Validation
    → POST to org_controller.php?action=update&endpoint=[entity]
    → Permission Check (edit_[entity])
    → Duplicate Check (excluding current record)
    → EntityClass->update()
    → JSON Response
```

## Integration Points

### Employee Management Integration
- Employees are assigned to branches via `branch_id`
- Employees are assigned to locations via `location_id`
- Employees are assigned to states via `state_id`
- Projects are assigned as comma-separated values in `project_id`

### Payroll Integration
- State tax grids are used for payroll tax calculations
- Branch information is included in payroll reports
- Location data affects payroll processing rules

### Financial Integration
- Budget codes are used for expense tracking
- Bank accounts are linked to organizational entities
- Financial accounts are categorized by organizational structure

## Security and Permissions

### Permission System
Each organizational entity has specific permissions:
- **Company**: `create_organization`, `edit_organization`
- **Branches**: `create_departments`, `edit_departments`
- **States**: `create_states`, `edit_states`
- **Locations**: `create_duty_locations`, `edit_duty_locations`
- **Designations**: `create_designations`, `edit_designations`
- **Projects**: `create_projects`, `edit_projects`

### Data Validation
- Duplicate name checking via `check_exists()` function
- Required field validation on both client and server side
- Data sanitization via `escapePostData()` function
- SQL injection prevention through prepared statements (Model class)

## Technical Implementation Details

### Model Inheritance Pattern
All organizational classes extend the base `Model` class, providing:
- Standard CRUD operations (create, read, update, delete)
- Database connection management
- Prepared statement execution
- Error handling and logging

### Global Instance Management
All classes are instantiated as global variables for easy access:
```php
$GLOBALS['companyClass'] = $companyClass = new Company();
$GLOBALS['branchClass'] = $branchClass = new Branch();
// ... etc
```

### JSON Response Format
All controller actions return standardized JSON responses:
```php
$result = [
    'id' => $created_id,           // For successful creation
    'msg' => 'Success message',    // User-friendly message
    'error' => false,              // Boolean error flag
    'sql_error' => $exception      // Technical error details
];
```

### Tax Grid Configuration
States support complex tax grid configuration stored as JSON:
```php
'tax_grid' => json_encode([
    ['min_amount' => 0, 'max_amount' => 1000, 'rate' => 5],
    ['min_amount' => 1001, 'max_amount' => 5000, 'rate' => 10],
    // ... additional tax brackets
])
```

This analysis provides the foundation for understanding how organizational entities are created, managed, and integrated throughout the HRM system.