# Complete Database Schema

## Overview

This document provides a comprehensive overview of all database tables in the HRM application. The system uses MySQL as the database engine and follows a relational database design pattern with proper foreign key relationships between entities.

## Database Configuration

- **Database Engine**: MySQL
- **Database Name**: test_edurdur (development) / u138037914_hrm (production)
- **Connection**: MySQLi with prepared statements
- **Character Set**: UTF-8

## Core Entity Tables

### 1. employees
**Primary Key**: employee_id  
**Description**: Central table storing all employee information

| Field | Type | Description |
|-------|------|-------------|
| employee_id | INT AUTO_INCREMENT | Primary key |
| staff_no | VARCHAR | Unique staff number |
| full_name | VARCHAR | Employee full name |
| email | VARCHAR | Employee email address |
| phone_number | VARCHAR | Contact phone number |
| address | TEXT | Physical address |
| date_of_birth | DATE | Birth date |
| hire_date | DATE | Employment start date |
| status | ENUM('active', 'inactive') | Employment status |
| branch_id | INT | Foreign key to branches table |
| location_id | INT | Foreign key to locations table |
| state_id | INT | Foreign key to states table |
| contract_type | VARCHAR | Employment contract type |
| designation | VARCHAR | Job title/position |
| project_id | VARCHAR(250) | Comma-separated project IDs |
| created_at | TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### 2. users
**Primary Key**: user_id  
**Description**: User accounts for system access

| Field | Type | Description |
|-------|------|-------------|
| user_id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| username | VARCHAR | Login username |
| password | VARCHAR | Encrypted password |
| role | INT | Foreign key to sys_roles table |
| status | ENUM('active', 'inactive') | Account status |
| last_login | TIMESTAMP | Last login timestamp |
| created_at | TIMESTAMP | Account creation timestamp |
### 3. company
**Primary Key**: id  
**Description**: Company information and settings

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Company name |
| address | TEXT | Company address |
| phone | VARCHAR | Contact phone |
| email | VARCHAR | Contact email |
| website | VARCHAR | Company website |
| logo | VARCHAR | Logo file path |
| created_at | TIMESTAMP | Record creation timestamp |

## Organizational Structure Tables

### 4. branches
**Primary Key**: id  
**Description**: Company branches/departments

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Branch name |
| address | TEXT | Branch address |
| contact_email | VARCHAR | Branch contact email |
| contact_phone | VARCHAR | Branch contact phone |
| status | ENUM('active', 'inactive') | Branch status |
| created_at | TIMESTAMP | Record creation timestamp |

### 5. locations
**Primary Key**: id  
**Description**: Physical locations/offices

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Location name |
| city_name | VARCHAR | City name |
| address | TEXT | Location address |
| status | ENUM('active', 'inactive') | Location status |
| created_at | TIMESTAMP | Record creation timestamp |

### 6. states
**Primary Key**: id  
**Description**: States/provinces for geographical organization

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | State/province name |
| country_name | VARCHAR | Country name |
| status | ENUM('active', 'inactive') | State status |
| created_at | TIMESTAMP | Record creation timestamp |

### 7. countries
**Primary Key**: country_id  
**Description**: Countries for international operations

| Field | Type | Description |
|-------|------|-------------|
| country_id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Country name |
| code | VARCHAR | Country code |
| status | ENUM('active', 'inactive') | Country status |## Payroll
 System Tables

### 8. payroll
**Primary Key**: id  
**Description**: Payroll processing records

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| ref | VARCHAR | Payroll reference number |
| ref_name | VARCHAR | Payroll period name |
| month | VARCHAR | Payroll month (YYYY-MM) |
| added_date | TIMESTAMP | Creation date |
| added_by | INT | User who created payroll |
| status | ENUM('Draft', 'Approved', 'Paid') | Payroll status |

### 9. payroll_details
**Primary Key**: id  
**Description**: Individual employee payroll details

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| payroll_id | INT | Foreign key to payroll table |
| emp_id | INT | Foreign key to employees table |
| staff_no | VARCHAR | Employee staff number |
| full_name | VARCHAR | Employee name |
| month | VARCHAR | Payroll month |
| base_salary | DECIMAL(10,2) | Base salary amount |
| allowance | DECIMAL(10,2) | Allowances total |
| bonus | DECIMAL(10,2) | Bonus amount |
| commission | DECIMAL(10,2) | Commission amount |
| loan | DECIMAL(10,2) | Loan deductions |
| advance | DECIMAL(10,2) | Advance deductions |
| deductions | DECIMAL(10,2) | Other deductions |
| tax | DECIMAL(10,2) | Tax amount |
| bank_id | INT | Foreign key to bank_accounts |
| status | ENUM('Draft', 'Approved', 'Paid') | Payment status |

### 10. employee_salaries
**Primary Key**: salary_id  
**Description**: Employee salary configurations

| Field | Type | Description |
|-------|------|-------------|
| salary_id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| base_salary | DECIMAL(10,2) | Base salary amount |
| allowances | DECIMAL(10,2) | Regular allowances |
| effective_date | DATE | Salary effective date |
| status | ENUM('active', 'inactive') | Salary status |

### 11. employee_transactions
**Primary Key**: transaction_id  
**Description**: Employee financial transactions

| Field | Type | Description |
|-------|------|-------------|
| transaction_id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| staff_no | VARCHAR | Employee staff number |
| full_name | VARCHAR | Employee name |
| phone_number | VARCHAR | Employee phone |
| email | VARCHAR | Employee email |
| transaction_type | VARCHAR | Transaction type |
| transaction_subtype | VARCHAR | Transaction subtype |
| amount | DECIMAL(10,2) | Transaction amount |
| description | TEXT | Transaction description |
| date | DATE | Transaction date |
| payroll_id | INT | Associated payroll ID |
| added_by | INT | User who added transaction |
| status | ENUM('Active', 'Cancelled') | Transaction status |## Attenda
nce and Time Tracking Tables

### 12. attendance
**Primary Key**: id  
**Description**: Daily attendance records

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| atten_date | DATE | Attendance date |
| time_in | TIME | Check-in time |
| time_out | TIME | Check-out time |
| hours_worked | DECIMAL(4,2) | Total hours worked |
| status | ENUM('Present', 'Absent', 'Late') | Attendance status |
| payroll_id | INT | Associated payroll ID |
| created_at | TIMESTAMP | Record creation timestamp |

### 13. atten_details
**Primary Key**: id  
**Description**: Detailed attendance information

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| atten_date | DATE | Attendance date |
| details | TEXT | Additional attendance details |
| created_at | TIMESTAMP | Record creation timestamp |

### 14. timesheet
**Primary Key**: id  
**Description**: Employee timesheet records

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| ts_date | DATE | Timesheet date |
| hours | DECIMAL(4,2) | Hours logged |
| description | TEXT | Work description |
| payroll_id | INT | Associated payroll ID |
| status | ENUM('Draft', 'Submitted', 'Approved') | Timesheet status |

### 15. timesheet_details
**Primary Key**: id  
**Description**: Detailed timesheet entries

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| timesheet_id | INT | Foreign key to timesheet table |
| project_id | INT | Associated project |
| hours | DECIMAL(4,2) | Hours for this entry |
| description | TEXT | Work description |

### 16. leave_types
**Primary Key**: id  
**Description**: Types of leave available

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Leave type name |
| days_allowed | INT | Maximum days allowed |
| paid_type | ENUM('Paid', 'Unpaid') | Payment type |
| status | ENUM('active', 'inactive') | Leave type status |

### 17. employee_leave
**Primary Key**: id  
**Description**: Employee leave requests and records

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| leave_id | INT | Foreign key to leave_types table |
| date_from | DATE | Leave start date |
| date_to | DATE | Leave end date |
| days_num | INT | Number of leave days |
| reason | TEXT | Leave reason |
| paid_type | ENUM('Paid', 'Unpaid') | Payment type |
| status | ENUM('Pending', 'Approved', 'Rejected') | Leave status |
| applied_date | TIMESTAMP | Application date |## Emp
loyee Relationship Tables

### 18. employee_projects
**Primary Key**: id  
**Description**: Many-to-many relationship between employees and projects

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| project_id | INT | Foreign key to projects table |
| assigned_date | TIMESTAMP | Assignment date |

### 19. employee_budget_codes
**Primary Key**: id  
**Description**: Many-to-many relationship between employees and budget codes

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| code_id | INT | Foreign key to budget_codes table |
| assigned_date | TIMESTAMP | Assignment date |

### 20. employee_education
**Primary Key**: id  
**Description**: Employee education history

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| employee_id | INT | Foreign key to employees table |
| institution | VARCHAR | Educational institution |
| degree | VARCHAR | Degree/qualification |
| field_of_study | VARCHAR | Field of study |
| start_year | YEAR | Start year |
| end_year | YEAR | End year |
| grade | VARCHAR | Grade/GPA |
| created_at | TIMESTAMP | Record creation timestamp |

### 21. employee_docs
**Primary Key**: id  
**Description**: Employee document storage

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| employee_id | INT | Foreign key to employees table |
| document_name | VARCHAR | Document name |
| file_path | VARCHAR | File storage path |
| document_type | VARCHAR | Type of document |
| upload_date | TIMESTAMP | Upload timestamp |
| created_at | TIMESTAMP | Record creation timestamp |

## Project and Budget Management Tables

### 22. projects
**Primary Key**: id  
**Description**: Company projects

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Project name |
| description | TEXT | Project description |
| start_date | DATE | Project start date |
| end_date | DATE | Project end date |
| status | ENUM('Active', 'Completed', 'On Hold') | Project status |
| created_at | TIMESTAMP | Record creation timestamp |

### 23. budget_codes
**Primary Key**: id  
**Description**: Budget codes for financial tracking

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Budget code name |
| description | TEXT | Budget code description |
| budget_amount | DECIMAL(12,2) | Allocated budget |
| status | ENUM('Active', 'Inactive') | Budget code status |
| created_at | TIMESTAMP | Record creation timestamp |###
 24. res_allocation
**Primary Key**: id  
**Description**: Resource allocation tracking

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| allocation_data | JSON | JSON data for budget/project allocations |
| total_time | DECIMAL(4,2) | Total allocated time |
| date | DATE | Allocation date |
| status | ENUM('Active', 'Inactive') | Allocation status |

## Financial Management Tables

### 25. bank_accounts
**Primary Key**: id  
**Description**: Company bank accounts

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| bank_name | VARCHAR | Bank name |
| account | VARCHAR | Account number |
| balance | DECIMAL(12,2) | Current balance |
| status | ENUM('active', 'inactive') | Account status |
| updated_by | INT | Last updated by user |
| updated_date | TIMESTAMP | Last update timestamp |
| created_at | TIMESTAMP | Account creation timestamp |

### 26. banks
**Primary Key**: id  
**Description**: Bank information

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Bank name |
| code | VARCHAR | Bank code |
| address | TEXT | Bank address |
| status | ENUM('active', 'inactive') | Bank status |

### 27. fn_transactions
**Primary Key**: id  
**Description**: Financial transactions

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| transaction_type | ENUM('Income', 'Expense') | Transaction type |
| amount | DECIMAL(10,2) | Transaction amount |
| description | TEXT | Transaction description |
| date | DATE | Transaction date |
| account_id | INT | Foreign key to financial_accounts |
| added_by | INT | User who added transaction |
| status | ENUM('Active', 'Cancelled') | Transaction status |

### 28. financial_accounts
**Primary Key**: id  
**Description**: Financial account categories

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Account name |
| type | ENUM('Asset', 'Liability', 'Income', 'Expense') | Account type |
| description | TEXT | Account description |
| status | ENUM('active', 'inactive') | Account status |## 
Performance and Training Tables

### 29. performance
**Primary Key**: id  
**Description**: Employee performance records

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| review_period | VARCHAR | Performance review period |
| rating | DECIMAL(3,2) | Performance rating |
| comments | TEXT | Performance comments |
| reviewer_id | INT | Foreign key to users table |
| review_date | DATE | Review date |
| status | ENUM('Draft', 'Completed') | Review status |

### 30. employee_performance
**Primary Key**: id  
**Description**: Detailed employee performance metrics

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| metric_name | VARCHAR | Performance metric name |
| score | DECIMAL(5,2) | Metric score |
| max_score | DECIMAL(5,2) | Maximum possible score |
| evaluation_date | DATE | Evaluation date |

### 31. trainers
**Primary Key**: id  
**Description**: Training instructors/trainers

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Trainer name |
| email | VARCHAR | Trainer email |
| phone | VARCHAR | Trainer phone |
| specialization | VARCHAR | Training specialization |
| status | ENUM('active', 'inactive') | Trainer status |

### 32. training_list
**Primary Key**: id  
**Description**: Training programs and sessions

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| training_name | VARCHAR | Training program name |
| trainer_id | INT | Foreign key to trainers table |
| start_date | DATE | Training start date |
| end_date | DATE | Training end date |
| status | ENUM('Scheduled', 'In Progress', 'Completed') | Training status |
| cost | DECIMAL(10,2) | Training cost |

### 33. training_options
**Primary Key**: id  
**Description**: Available training options

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Training option name |
| description | TEXT | Training description |
| duration | INT | Duration in hours |
| cost | DECIMAL(10,2) | Training cost |
| status | ENUM('active', 'inactive') | Option status |

### 34. training_types
**Primary Key**: id  
**Description**: Categories of training

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Training type name |
| description | TEXT | Type description |
| status | ENUM('active', 'inactive') | Type status |##
 Management and HR Tables

### 35. promotions
**Primary Key**: promotion_id  
**Description**: Employee promotion records

| Field | Type | Description |
|-------|------|-------------|
| promotion_id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| from_position | VARCHAR | Previous position |
| to_position | VARCHAR | New position |
| promotion_date | DATE | Promotion effective date |
| salary_change | DECIMAL(10,2) | Salary change amount |
| reason | TEXT | Promotion reason |
| approved_by | INT | Approving manager |
| status | ENUM('Pending', 'Approved', 'Rejected') | Promotion status |

### 36. transfers
**Primary Key**: transfer_id  
**Description**: Employee transfer records

| Field | Type | Description |
|-------|------|-------------|
| transfer_id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| from_branch | INT | Previous branch ID |
| to_branch | INT | New branch ID |
| transfer_date | DATE | Transfer effective date |
| reason | TEXT | Transfer reason |
| approved_by | INT | Approving manager |
| status | ENUM('Pending', 'Approved', 'Rejected') | Transfer status |

### 37. resignations
**Primary Key**: resignation_id  
**Description**: Employee resignation records

| Field | Type | Description |
|-------|------|-------------|
| resignation_id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| resignation_date | DATE | Resignation date |
| last_working_date | DATE | Last working date |
| reason | TEXT | Resignation reason |
| notice_period | INT | Notice period in days |
| status | ENUM('Submitted', 'Approved', 'Rejected') | Resignation status |

### 38. terminations
**Primary Key**: termination_id  
**Description**: Employee termination records

| Field | Type | Description |
|-------|------|-------------|
| termination_id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| termination_date | DATE | Termination date |
| reason | TEXT | Termination reason |
| terminated_by | INT | Terminating manager |
| notice_given | BOOLEAN | Whether notice was given |
| status | ENUM('Active', 'Completed') | Termination status |

### 39. warnings
**Primary Key**: warning_id  
**Description**: Employee warning records

| Field | Type | Description |
|-------|------|-------------|
| warning_id | INT AUTO_INCREMENT | Primary key |
| emp_id | INT | Foreign key to employees table |
| warning_type | VARCHAR | Type of warning |
| description | TEXT | Warning description |
| warning_date | DATE | Warning date |
| issued_by | INT | Manager who issued warning |
| status | ENUM('Active', 'Resolved') | Warning status |## System
 and Configuration Tables

### 40. sys_permissions
**Primary Key**: id  
**Description**: System permissions

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Permission name |
| actions | JSON | Available actions for permission |
| description | TEXT | Permission description |
| status | ENUM('active', 'inactive') | Permission status |

### 41. sys_roles
**Primary Key**: id  
**Description**: System user roles

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Role name |
| description | TEXT | Role description |
| status | ENUM('active', 'inactive') | Role status |

### 42. sys_role_permissions
**Primary Key**: id  
**Description**: Role-permission relationships

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| role_id | INT | Foreign key to sys_roles table |
| permission | VARCHAR | Permission code |
| granted | BOOLEAN | Whether permission is granted |

### 43. user_permissions
**Primary Key**: id  
**Description**: Individual user permissions

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| user_id | INT | Foreign key to users table |
| permission_id | INT | Foreign key to sys_permissions table |
| granted | BOOLEAN | Whether permission is granted |

### 44. permissions
**Primary Key**: id  
**Description**: Legacy permissions table

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Permission name |
| description | TEXT | Permission description |

### 45. sys_settings
**Primary Key**: type  
**Description**: System configuration settings

| Field | Type | Description |
|-------|------|-------------|
| type | VARCHAR | Setting type/key |
| value | TEXT | Setting value |
| description | TEXT | Setting description |
| updated_at | TIMESTAMP | Last update timestamp |## Lookup
 and Reference Tables

### 46. designations
**Primary Key**: id  
**Description**: Job designations/positions

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Designation name |
| description | TEXT | Designation description |
| status | ENUM('active', 'inactive') | Designation status |

### 47. contract_types
**Primary Key**: id  
**Description**: Employment contract types

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Contract type name |
| description | TEXT | Contract description |
| status | ENUM('active', 'inactive') | Contract type status |

### 48. trans_subtypes
**Primary Key**: id  
**Description**: Transaction subtypes for categorization

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| type | VARCHAR | Main transaction type |
| name | VARCHAR | Subtype name |
| description | TEXT | Subtype description |
| status | ENUM('active', 'inactive') | Subtype status |

### 49. goal_types
**Primary Key**: id  
**Description**: Performance goal types

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Goal type name |
| description | TEXT | Goal type description |
| status | ENUM('active', 'inactive') | Goal type status |

### 50. award_types
**Primary Key**: id  
**Description**: Employee award types

| Field | Type | Description |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR | Award type name |
| description | TEXT | Award description |
| status | ENUM('active', 'inactive') | Award type status |

## Database Statistics

- **Total Tables**: 50
- **Core Entity Tables**: 7
- **Relationship Tables**: 8
- **Lookup/Reference Tables**: 12
- **System Tables**: 8
- **Financial Tables**: 6
- **HR Management Tables**: 9

## Naming Conventions

- **Table Names**: Lowercase with underscores (snake_case)
- **Primary Keys**: Usually 'id' or '{table_name}_id'
- **Foreign Keys**: '{referenced_table}_id' format
- **Status Fields**: ENUM with consistent values ('active', 'inactive')
- **Timestamps**: 'created_at', 'updated_at' for audit trails
- **Boolean Fields**: BOOLEAN or TINYINT(1) for true/false values