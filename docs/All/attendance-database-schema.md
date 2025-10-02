# Attendance Database Schema

## Overview

The attendance tracking system uses several interconnected database tables to manage employee attendance, leave requests, timesheet data, and resource allocation. This document provides detailed information about each table structure, relationships, and usage patterns.

## Core Attendance Tables

### 1. attendance
**Primary Key**: id  
**Description**: Master table for attendance records, groups attendance entries by reference (Employee, Department, Location)

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique attendance record identifier |
| ref | VARCHAR(50) | NOT NULL | Reference type (Employee, Department, Location) |
| ref_id | INT | NOT NULL | ID of the referenced entity |
| ref_name | VARCHAR(255) | NOT NULL | Name of the referenced entity |
| atten_date | DATE | NOT NULL | Date of attendance record |
| added_by | INT | NOT NULL | User ID who created the record |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_by | INT | NULL | User ID who last updated the record |
| updated_date | TIMESTAMP | NULL | Last update timestamp |

#### Relationships
- **added_by** → users.user_id (Foreign Key)
- **updated_by** → users.user_id (Foreign Key)
- **ref_id** → employees.employee_id, branches.id, or locations.id (depending on ref type)

#### Usage Examples
```sql
-- Create attendance record for a department
INSERT INTO attendance (ref, ref_id, ref_name, atten_date, added_by) 
VALUES ('Department', 1, 'IT Department', '2024-01-15', 1);

-- Get all attendance records for a specific date
SELECT * FROM attendance WHERE atten_date = '2024-01-15';

-- Count employees in each attendance record
SELECT a.*, COUNT(ad.emp_id) as employee_count 
FROM attendance a 
LEFT JOIN atten_details ad ON a.id = ad.atten_id 
GROUP BY a.id;
```

### 2. atten_details
**Primary Key**: id  
**Description**: Detailed attendance information for individual employees within an attendance record

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique detail record identifier |
| atten_id | INT | NOT NULL | Foreign key to attendance table |
| emp_id | INT | NOT NULL | Employee ID |
| full_name | VARCHAR(255) | NOT NULL | Employee full name |
| phone_number | VARCHAR(20) | NULL | Employee phone number |
| email | VARCHAR(255) | NULL | Employee email address |
| staff_no | VARCHAR(50) | NULL | Employee staff number |
| status | VARCHAR(10) | NOT NULL | Attendance status (P, A, L, PL, UL) |
| atten_date | DATE | NOT NULL | Attendance date |
| added_by | INT | NOT NULL | User ID who created the record |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_by | INT | NULL | User ID who last updated the record |
| updated_date | TIMESTAMP | NULL | Last update timestamp |

#### Relationships
- **atten_id** → attendance.id (Foreign Key)
- **emp_id** → employees.employee_id (Foreign Key)
- **added_by** → users.user_id (Foreign Key)
- **updated_by** → users.user_id (Foreign Key)

#### Status Codes
- **P**: Present - Employee was present for the full day
- **A**: Absent - Employee was absent without leave
- **L**: Late - Employee arrived late
- **PL**: Paid Leave - Employee on approved paid leave
- **UL**: Unpaid Leave - Employee on approved unpaid leave

#### Usage Examples
```sql
-- Get attendance details for a specific attendance record
SELECT * FROM atten_details WHERE atten_id = 1;

-- Get employee attendance history
SELECT ad.*, a.atten_date 
FROM atten_details ad 
JOIN attendance a ON ad.atten_id = a.id 
WHERE ad.emp_id = 123 
ORDER BY a.atten_date DESC;

-- Count attendance by status
SELECT status, COUNT(*) as count 
FROM atten_details 
WHERE atten_date BETWEEN '2024-01-01' AND '2024-01-31' 
GROUP BY status;
```

## Timesheet Tables

### 3. timesheet
**Primary Key**: id  
**Description**: Master table for timesheet records, groups time entries by date

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique timesheet record identifier |
| ts_date | DATE | NOT NULL | Timesheet date |
| added_by | INT | NOT NULL | User ID who created the record |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_by | INT | NULL | User ID who last updated the record |
| updated_date | TIMESTAMP | NULL | Last update timestamp |

#### Relationships
- **added_by** → users.user_id (Foreign Key)
- **updated_by** → users.user_id (Foreign Key)

#### Usage Examples
```sql
-- Create timesheet for a specific date
INSERT INTO timesheet (ts_date, added_by) VALUES ('2024-01-15', 1);

-- Check if timesheet exists for a date
SELECT * FROM timesheet WHERE ts_date = '2024-01-15';

-- Get timesheet with employee count
SELECT t.*, COUNT(td.emp_id) as employee_count 
FROM timesheet t 
LEFT JOIN timesheet_details td ON t.id = td.ts_id 
GROUP BY t.id;
```

### 4. timesheet_details
**Primary Key**: id  
**Description**: Detailed time tracking information for individual employees

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique detail record identifier |
| ts_id | INT | NOT NULL | Foreign key to timesheet table |
| emp_id | INT | NOT NULL | Employee ID |
| full_name | VARCHAR(255) | NOT NULL | Employee full name |
| phone_number | VARCHAR(20) | NULL | Employee phone number |
| email | VARCHAR(255) | NULL | Employee email address |
| staff_no | VARCHAR(50) | NULL | Employee staff number |
| ts_date | DATE | NOT NULL | Timesheet date |
| time_in | TIME | NULL | Employee check-in time |
| time_out | TIME | NULL | Employee check-out time |
| status | VARCHAR(10) | NULL | Time status (P for Present) |
| added_by | INT | NOT NULL | User ID who created the record |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_by | INT | NULL | User ID who last updated the record |
| updated_date | TIMESTAMP | NULL | Last update timestamp |

#### Relationships
- **ts_id** → timesheet.id (Foreign Key)
- **emp_id** → employees.employee_id (Foreign Key)
- **added_by** → users.user_id (Foreign Key)
- **updated_by** → users.user_id (Foreign Key)

#### Time Calculation Logic
```sql
-- Calculate hours worked
SELECT 
    emp_id,
    full_name,
    time_in,
    time_out,
    TIMEDIFF(time_out, time_in) as hours_worked,
    CASE 
        WHEN TIMEDIFF(time_out, time_in) > '08:00:00' 
        THEN TIMEDIFF(TIMEDIFF(time_out, time_in), '08:00:00') 
        ELSE '00:00:00' 
    END as overtime_hours
FROM timesheet_details 
WHERE ts_date = '2024-01-15' AND status = 'P';
```

## Leave Management Tables

### 5. leave_types
**Primary Key**: id  
**Description**: Configuration table for different types of leave available in the system

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique leave type identifier |
| name | VARCHAR(100) | NOT NULL, UNIQUE | Leave type name |
| paid_type | ENUM('Paid', 'Unpaid') | NOT NULL | Whether leave is paid or unpaid |
| added_by | INT | NOT NULL | User ID who created the record |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_by | INT | NULL | User ID who last updated the record |
| updated_date | TIMESTAMP | NULL | Last update timestamp |

#### Usage Examples
```sql
-- Create leave types
INSERT INTO leave_types (name, paid_type, added_by) VALUES 
('Annual Leave', 'Paid', 1),
('Sick Leave', 'Paid', 1),
('Personal Leave', 'Unpaid', 1);

-- Get all active leave types
SELECT * FROM leave_types ORDER BY name;
```

### 6. employee_leave
**Primary Key**: id  
**Description**: Employee leave requests and approved leave records

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique leave record identifier |
| emp_id | INT | NOT NULL | Employee ID |
| leave_id | INT | NOT NULL | Foreign key to leave_types table |
| paid_type | ENUM('Paid', 'Unpaid') | NOT NULL | Payment type (copied from leave_types) |
| date_from | DATE | NOT NULL | Leave start date |
| date_to | DATE | NOT NULL | Leave end date |
| days_num | INT | NOT NULL | Number of leave days |
| status | ENUM('Pending', 'Approved', 'Rejected', 'Cancelled') | DEFAULT 'Pending' | Leave request status |
| added_by | INT | NOT NULL | User ID who created the record |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_by | INT | NULL | User ID who last updated the record |
| updated_date | TIMESTAMP | NULL | Last update timestamp |

#### Relationships
- **emp_id** → employees.employee_id (Foreign Key)
- **leave_id** → leave_types.id (Foreign Key)
- **added_by** → users.user_id (Foreign Key)
- **updated_by** → users.user_id (Foreign Key)

#### Leave Integration with Attendance
```sql
-- Check for leave during attendance recording
SELECT el.*, lt.name as leave_type_name 
FROM employee_leave el 
JOIN leave_types lt ON el.leave_id = lt.id 
WHERE el.emp_id = 123 
AND el.status = 'Approved' 
AND '2024-01-15' BETWEEN el.date_from AND el.date_to;

-- Get leave summary for an employee
SELECT 
    e.full_name,
    lt.name as leave_type,
    el.date_from,
    el.date_to,
    el.days_num,
    el.paid_type,
    el.status
FROM employee_leave el
JOIN employees e ON el.emp_id = e.employee_id
JOIN leave_types lt ON el.leave_id = lt.id
WHERE el.emp_id = 123
ORDER BY el.date_from DESC;
```

## Resource Allocation Table

### 7. res_allocation
**Primary Key**: id  
**Description**: Resource allocation tracking for project and budget code assignments

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique allocation record identifier |
| emp_id | INT | NOT NULL | Employee ID |
| full_name | VARCHAR(255) | NOT NULL | Employee full name |
| phone_number | VARCHAR(20) | NULL | Employee phone number |
| email | VARCHAR(255) | NULL | Employee email address |
| staff_no | VARCHAR(50) | NULL | Employee staff number |
| sup_id | INT | NOT NULL | Supervisor employee ID |
| sup_name | VARCHAR(255) | NOT NULL | Supervisor full name |
| sup_phone | VARCHAR(20) | NULL | Supervisor phone number |
| sup_email | VARCHAR(255) | NULL | Supervisor email address |
| sup_staff_no | VARCHAR(50) | NULL | Supervisor staff number |
| allocation | JSON | NOT NULL | JSON data containing budget/project allocations |
| month | VARCHAR(7) | NOT NULL | Allocation month (YYYY-MM format) |
| added_by | INT | NOT NULL | User ID who created the record |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |

#### Relationships
- **emp_id** → employees.employee_id (Foreign Key)
- **sup_id** → employees.employee_id (Foreign Key)
- **added_by** → users.user_id (Foreign Key)

#### JSON Allocation Structure
```json
{
  "budgets": [
    {
      "budget_id": 1,
      "budget_name": "Development Budget",
      "time": 40
    }
  ],
  "projects": [
    {
      "project_id": 1,
      "project_name": "Website Redesign",
      "time": 20
    }
  ]
}
```

#### Usage Examples
```sql
-- Get allocation for an employee and month
SELECT * FROM res_allocation 
WHERE emp_id = 123 AND month = '2024-01';

-- Calculate total allocated time using JSON functions
SELECT 
    emp_id,
    full_name,
    month,
    JSON_EXTRACT(allocation, '$.budgets[*].time') as budget_times,
    JSON_EXTRACT(allocation, '$.projects[*].time') as project_times
FROM res_allocation 
WHERE month = '2024-01';
```

## Table Relationships and Constraints

### Foreign Key Relationships
```sql
-- Attendance system relationships
ALTER TABLE atten_details 
ADD CONSTRAINT fk_atten_details_attendance 
FOREIGN KEY (atten_id) REFERENCES attendance(id) ON DELETE CASCADE;

ALTER TABLE atten_details 
ADD CONSTRAINT fk_atten_details_employee 
FOREIGN KEY (emp_id) REFERENCES employees(employee_id) ON DELETE CASCADE;

-- Timesheet system relationships
ALTER TABLE timesheet_details 
ADD CONSTRAINT fk_timesheet_details_timesheet 
FOREIGN KEY (ts_id) REFERENCES timesheet(id) ON DELETE CASCADE;

ALTER TABLE timesheet_details 
ADD CONSTRAINT fk_timesheet_details_employee 
FOREIGN KEY (emp_id) REFERENCES employees(employee_id) ON DELETE CASCADE;

-- Leave system relationships
ALTER TABLE employee_leave 
ADD CONSTRAINT fk_employee_leave_employee 
FOREIGN KEY (emp_id) REFERENCES employees(employee_id) ON DELETE CASCADE;

ALTER TABLE employee_leave 
ADD CONSTRAINT fk_employee_leave_type 
FOREIGN KEY (leave_id) REFERENCES leave_types(id) ON DELETE RESTRICT;
```

### Indexes for Performance
```sql
-- Attendance indexes
CREATE INDEX idx_attendance_date ON attendance(atten_date);
CREATE INDEX idx_attendance_ref ON attendance(ref, ref_id);
CREATE INDEX idx_atten_details_emp_date ON atten_details(emp_id, atten_date);
CREATE INDEX idx_atten_details_date ON atten_details(atten_date);

-- Timesheet indexes
CREATE INDEX idx_timesheet_date ON timesheet(ts_date);
CREATE INDEX idx_timesheet_details_emp_date ON timesheet_details(emp_id, ts_date);
CREATE INDEX idx_timesheet_details_date ON timesheet_details(ts_date);

-- Leave indexes
CREATE INDEX idx_employee_leave_emp ON employee_leave(emp_id);
CREATE INDEX idx_employee_leave_dates ON employee_leave(date_from, date_to);
CREATE INDEX idx_employee_leave_status ON employee_leave(status);

-- Resource allocation indexes
CREATE INDEX idx_res_allocation_emp_month ON res_allocation(emp_id, month);
CREATE INDEX idx_res_allocation_sup ON res_allocation(sup_id);
```

## Data Integrity and Constraints

### Business Rules Implemented in Database
1. **Unique Constraints**: Prevent duplicate attendance/timesheet entries for same employee/date
2. **Date Validation**: Ensure end dates are after start dates for leave requests
3. **Status Validation**: Restrict status values to predefined enums
4. **Referential Integrity**: Maintain relationships between employees and their records

### Triggers and Stored Procedures
```sql
-- Example trigger to validate leave dates
DELIMITER //
CREATE TRIGGER validate_leave_dates 
BEFORE INSERT ON employee_leave
FOR EACH ROW
BEGIN
    IF NEW.date_to < NEW.date_from THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Leave end date must be after start date';
    END IF;
    
    -- Calculate days_num automatically
    SET NEW.days_num = DATEDIFF(NEW.date_to, NEW.date_from) + 1;
END//
DELIMITER ;
```

## Common Queries and Reports

### Attendance Reports
```sql
-- Daily attendance summary
SELECT 
    a.atten_date,
    a.ref_name,
    COUNT(ad.emp_id) as total_employees,
    SUM(CASE WHEN ad.status = 'P' THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN ad.status = 'A' THEN 1 ELSE 0 END) as absent,
    SUM(CASE WHEN ad.status = 'L' THEN 1 ELSE 0 END) as late,
    SUM(CASE WHEN ad.status IN ('PL', 'UL') THEN 1 ELSE 0 END) as on_leave
FROM attendance a
LEFT JOIN atten_details ad ON a.id = ad.atten_id
WHERE a.atten_date BETWEEN '2024-01-01' AND '2024-01-31'
GROUP BY a.id, a.atten_date, a.ref_name
ORDER BY a.atten_date DESC;
```

### Timesheet Reports
```sql
-- Employee hours summary
SELECT 
    td.emp_id,
    td.full_name,
    COUNT(*) as days_worked,
    SUM(CASE WHEN td.time_in IS NOT NULL AND td.time_out IS NOT NULL 
        THEN TIME_TO_SEC(TIMEDIFF(td.time_out, td.time_in))/3600 
        ELSE 0 END) as total_hours
FROM timesheet_details td
WHERE td.ts_date BETWEEN '2024-01-01' AND '2024-01-31'
AND td.status = 'P'
GROUP BY td.emp_id, td.full_name
ORDER BY total_hours DESC;
```

### Leave Reports
```sql
-- Leave balance and usage
SELECT 
    e.employee_id,
    e.full_name,
    lt.name as leave_type,
    COUNT(el.id) as leave_requests,
    SUM(CASE WHEN el.status = 'Approved' THEN el.days_num ELSE 0 END) as approved_days,
    SUM(CASE WHEN el.status = 'Pending' THEN el.days_num ELSE 0 END) as pending_days
FROM employees e
LEFT JOIN employee_leave el ON e.employee_id = el.emp_id
LEFT JOIN leave_types lt ON el.leave_id = lt.id
WHERE e.status = 'active'
GROUP BY e.employee_id, e.full_name, lt.id, lt.name
ORDER BY e.full_name, lt.name;
```