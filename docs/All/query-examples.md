# Database Query Examples

## Overview

This document provides practical SQL query examples for common operations in the HRM application database. These queries demonstrate how to retrieve, manipulate, and analyze data across the various tables and relationships.

## Employee Management Queries

### 1. Basic Employee Information

#### Get All Active Employees
```sql
SELECT 
    employee_id,
    staff_no,
    full_name,
    email,
    phone_number,
    hire_date,
    status
FROM employees 
WHERE status = 'active'
ORDER BY full_name;
```

#### Get Employee with Full Profile Information
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
    b.contact_phone AS branch_phone
FROM employees e
LEFT JOIN states s ON s.id = e.state_id
LEFT JOIN locations l ON l.id = e.location_id
LEFT JOIN branches b ON b.id = e.branch_id
WHERE e.employee_id = ?;
```

#### Search Employees by Multiple Criteria
```sql
SELECT 
    employee_id,
    staff_no,
    full_name,
    email,
    phone_number
FROM employees 
WHERE status = 'active' 
    AND (
        full_name LIKE '%search_term%' 
        OR phone_number LIKE '%search_term%' 
        OR email LIKE '%search_term%'
        OR staff_no LIKE '%search_term%'
    )
ORDER BY full_name ASC 
LIMIT 10;
```

### 2. Employee Relationships

#### Get Employee Projects
```sql
SELECT 
    e.full_name,
    p.name AS project_name,
    p.description,
    p.status AS project_status,
    ep.assigned_date
FROM employees e
JOIN employee_projects ep ON ep.emp_id = e.employee_id
JOIN projects p ON p.id = ep.project_id
WHERE e.employee_id = ?
ORDER BY ep.assigned_date DESC;
```

#### Get Employee Budget Codes
```sql
SELECT 
    e.full_name,
    bc.name AS budget_code_name,
    bc.description,
    bc.budget_amount,
    ebc.assigned_date
FROM employees e
JOIN employee_budget_codes ebc ON ebc.emp_id = e.employee_id
JOIN budget_codes bc ON bc.id = ebc.code_id
WHERE e.employee_id = ?
ORDER BY ebc.assigned_date DESC;
```

#### Get Employee Education History
```sql
SELECT 
    institution,
    degree,
    field_of_study,
    start_year,
    end_year,
    grade
FROM employee_education 
WHERE employee_id = ?
ORDER BY start_year DESC;
```

## Payroll System Queries

### 3. Payroll Processing

#### Get Payroll Summary
```sql
SELECT 
    p.id,
    p.ref,
    p.ref_name,
    p.month,
    p.status,
    COUNT(DISTINCT pd.emp_id) AS employee_count,
    SUM(pd.base_salary + pd.allowance + pd.bonus + pd.commission) AS gross_total,
    SUM(pd.loan + pd.advance + pd.deductions + pd.tax) AS deductions_total,
    SUM(pd.base_salary + pd.allowance + pd.bonus + pd.commission - pd.loan - pd.advance - pd.deductions - pd.tax) AS net_total
FROM payroll p
LEFT JOIN payroll_details pd ON pd.payroll_id = p.id
WHERE p.id = ?
GROUP BY p.id;
```

#### Get Employee Payroll Details
```sql
SELECT 
    pd.*,
    (pd.base_salary + pd.allowance + pd.bonus + pd.commission) AS gross_salary,
    (pd.loan + pd.advance + pd.deductions + pd.tax) AS total_deductions,
    (pd.base_salary + pd.allowance + pd.bonus + pd.commission - pd.loan - pd.advance - pd.deductions - pd.tax) AS net_salary
FROM payroll_details pd
WHERE pd.payroll_id = ? AND pd.month LIKE ?
ORDER BY pd.full_name;
```

#### Get Employee Transaction History
```sql
SELECT 
    transaction_id,
    transaction_type,
    transaction_subtype,
    amount,
    description,
    date,
    status
FROM employee_transactions 
WHERE emp_id = ?
    AND status <> 'Cancelled'
ORDER BY date DESC;
```

### 4. Salary Management

#### Get Current Employee Salaries
```sql
SELECT 
    e.full_name,
    e.staff_no,
    es.base_salary,
    es.allowances,
    es.effective_date,
    es.status
FROM employee_salaries es
JOIN employees e ON e.employee_id = es.emp_id
WHERE es.status = 'active'
    AND es.effective_date = (
        SELECT MAX(effective_date) 
        FROM employee_salaries es2 
        WHERE es2.emp_id = es.emp_id 
            AND es2.status = 'active'
    )
ORDER BY e.full_name;
```## Atten
dance and Time Tracking Queries

### 5. Attendance Management

#### Get Employee Attendance for a Month
```sql
SELECT 
    a.atten_date,
    a.time_in,
    a.time_out,
    a.hours_worked,
    a.status
FROM attendance a
WHERE a.emp_id = ?
    AND a.atten_date BETWEEN ? AND ?
ORDER BY a.atten_date;
```

#### Get Attendance Summary by Employee
```sql
SELECT 
    e.full_name,
    e.staff_no,
    COUNT(a.id) AS total_days,
    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present_days,
    SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent_days,
    SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) AS late_days,
    SUM(a.hours_worked) AS total_hours
FROM employees e
LEFT JOIN attendance a ON a.emp_id = e.employee_id 
    AND a.atten_date BETWEEN ? AND ?
WHERE e.status = 'active'
GROUP BY e.employee_id, e.full_name, e.staff_no
ORDER BY e.full_name;
```

#### Get Timesheet Details with Project Breakdown
```sql
SELECT 
    t.ts_date,
    t.hours AS total_hours,
    t.description,
    td.project_id,
    p.name AS project_name,
    td.hours AS project_hours,
    td.description AS project_description
FROM timesheet t
LEFT JOIN timesheet_details td ON td.timesheet_id = t.id
LEFT JOIN projects p ON p.id = td.project_id
WHERE t.emp_id = ?
    AND t.ts_date BETWEEN ? AND ?
ORDER BY t.ts_date DESC, td.project_id;
```

### 6. Leave Management

#### Get Employee Leave Balance
```sql
SELECT 
    lt.name AS leave_type,
    lt.days_allowed,
    COALESCE(SUM(el.days_num), 0) AS days_taken,
    (lt.days_allowed - COALESCE(SUM(el.days_num), 0)) AS days_remaining
FROM leave_types lt
LEFT JOIN employee_leave el ON el.leave_id = lt.id 
    AND el.emp_id = ?
    AND el.status = 'Approved'
    AND YEAR(el.date_from) = YEAR(CURDATE())
WHERE lt.status = 'active'
GROUP BY lt.id, lt.name, lt.days_allowed
ORDER BY lt.name;
```

#### Get Pending Leave Requests
```sql
SELECT 
    el.id,
    e.full_name,
    e.staff_no,
    lt.name AS leave_type,
    el.date_from,
    el.date_to,
    el.days_num,
    el.reason,
    el.applied_date
FROM employee_leave el
JOIN employees e ON e.employee_id = el.emp_id
JOIN leave_types lt ON lt.id = el.leave_id
WHERE el.status = 'Pending'
ORDER BY el.applied_date;
```

## Financial and Banking Queries

### 7. Financial Transactions

#### Get Bank Account Balances
```sql
SELECT 
    ba.id,
    ba.bank_name,
    ba.account,
    ba.balance,
    ba.status,
    ba.updated_date
FROM bank_accounts ba
WHERE ba.status = 'active'
ORDER BY ba.bank_name, ba.account;
```

#### Get Financial Transaction Summary
```sql
SELECT 
    ft.transaction_type,
    fa.name AS account_name,
    COUNT(ft.id) AS transaction_count,
    SUM(ft.amount) AS total_amount
FROM fn_transactions ft
JOIN financial_accounts fa ON fa.id = ft.account_id
WHERE ft.date BETWEEN ? AND ?
    AND ft.status = 'Active'
GROUP BY ft.transaction_type, fa.name
ORDER BY ft.transaction_type, fa.name;
```

#### Get Employee Transaction Summary by Type
```sql
SELECT 
    et.transaction_type,
    et.transaction_subtype,
    COUNT(et.transaction_id) AS transaction_count,
    SUM(et.amount) AS total_amount
FROM employee_transactions et
WHERE et.emp_id = ?
    AND et.date BETWEEN ? AND ?
    AND et.status <> 'Cancelled'
GROUP BY et.transaction_type, et.transaction_subtype
ORDER BY et.transaction_type, et.transaction_subtype;
```

## Performance and Training Queries

### 8. Performance Management

#### Get Employee Performance History
```sql
SELECT 
    p.review_period,
    p.rating,
    p.comments,
    u.username AS reviewer,
    p.review_date,
    p.status
FROM performance p
JOIN users u ON u.user_id = p.reviewer_id
WHERE p.emp_id = ?
ORDER BY p.review_date DESC;
```

#### Get Performance Metrics by Employee
```sql
SELECT 
    ep.metric_name,
    ep.score,
    ep.max_score,
    (ep.score / ep.max_score * 100) AS percentage,
    ep.evaluation_date
FROM employee_performance ep
WHERE ep.emp_id = ?
ORDER BY ep.evaluation_date DESC, ep.metric_name;
```

### 9. Training Management

#### Get Employee Training History
```sql
SELECT 
    tl.training_name,
    t.name AS trainer_name,
    tl.start_date,
    tl.end_date,
    tl.status,
    tl.cost
FROM training_list tl
JOIN trainers t ON t.id = tl.trainer_id
WHERE tl.emp_id = ?
ORDER BY tl.start_date DESC;
```

#### Get Training Summary by Status
```sql
SELECT 
    tl.status,
    COUNT(tl.id) AS training_count,
    SUM(tl.cost) AS total_cost
FROM training_list tl
WHERE tl.start_date BETWEEN ? AND ?
GROUP BY tl.status
ORDER BY tl.status;
```## Orga
nizational and HR Management Queries

### 10. Organizational Structure

#### Get Organizational Hierarchy
```sql
SELECT 
    c.name AS company_name,
    b.name AS branch_name,
    l.name AS location_name,
    s.name AS state_name,
    s.country_name,
    COUNT(e.employee_id) AS employee_count
FROM company c
CROSS JOIN branches b
LEFT JOIN locations l ON l.status = 'active'
LEFT JOIN states s ON s.status = 'active'
LEFT JOIN employees e ON e.branch_id = b.id 
    AND e.location_id = l.id 
    AND e.state_id = s.id
    AND e.status = 'active'
WHERE b.status = 'active'
GROUP BY c.name, b.name, l.name, s.name, s.country_name
ORDER BY c.name, b.name, l.name, s.name;
```

#### Get Employees by Branch
```sql
SELECT 
    b.name AS branch_name,
    e.staff_no,
    e.full_name,
    e.email,
    e.designation,
    e.hire_date
FROM branches b
JOIN employees e ON e.branch_id = b.id
WHERE b.id = ? AND e.status = 'active'
ORDER BY e.full_name;
```

### 11. HR Management Records

#### Get Employee Promotion History
```sql
SELECT 
    p.promotion_date,
    p.from_position,
    p.to_position,
    p.salary_change,
    p.reason,
    u.username AS approved_by,
    p.status
FROM promotions p
LEFT JOIN users u ON u.user_id = p.approved_by
WHERE p.emp_id = ?
ORDER BY p.promotion_date DESC;
```

#### Get Employee Transfer History
```sql
SELECT 
    t.transfer_date,
    b1.name AS from_branch,
    b2.name AS to_branch,
    t.reason,
    u.username AS approved_by,
    t.status
FROM transfers t
LEFT JOIN branches b1 ON b1.id = t.from_branch
LEFT JOIN branches b2 ON b2.id = t.to_branch
LEFT JOIN users u ON u.user_id = t.approved_by
WHERE t.emp_id = ?
ORDER BY t.transfer_date DESC;
```

#### Get Employee Warnings
```sql
SELECT 
    w.warning_date,
    w.warning_type,
    w.description,
    u.username AS issued_by,
    w.status
FROM warnings w
LEFT JOIN users u ON u.user_id = w.issued_by
WHERE w.emp_id = ?
ORDER BY w.warning_date DESC;
```

## System Administration Queries

### 12. User and Permission Management

#### Get User Permissions
```sql
SELECT 
    u.username,
    sr.name AS role_name,
    srp.permission,
    srp.granted
FROM users u
JOIN sys_roles sr ON sr.id = u.role
LEFT JOIN sys_role_permissions srp ON srp.role_id = sr.id
WHERE u.user_id = ?
    AND srp.granted = 1
ORDER BY srp.permission;
```

#### Get Users by Role
```sql
SELECT 
    u.user_id,
    u.username,
    e.full_name,
    u.status,
    u.last_login
FROM users u
JOIN sys_roles sr ON sr.id = u.role
LEFT JOIN employees e ON e.employee_id = u.emp_id
WHERE sr.name = ?
    AND u.status = 'active'
ORDER BY u.username;
```

## Reporting and Analytics Queries

### 13. Dashboard Statistics

#### Get Employee Count by Contract Type
```sql
SELECT 
    ct.name AS contract_type,
    COUNT(e.employee_id) AS employee_count
FROM contract_types ct
LEFT JOIN employees e ON e.contract_type = ct.name AND e.status = 'active'
GROUP BY ct.id, ct.name
ORDER BY employee_count DESC;
```

#### Get Monthly Payroll Summary
```sql
SELECT 
    p.month,
    COUNT(DISTINCT pd.emp_id) AS employees_paid,
    SUM(pd.base_salary + pd.allowance + pd.bonus + pd.commission) AS gross_total,
    SUM(pd.loan + pd.advance + pd.deductions + pd.tax) AS deductions_total,
    SUM(pd.base_salary + pd.allowance + pd.bonus + pd.commission - pd.loan - pd.advance - pd.deductions - pd.tax) AS net_total
FROM payroll p
JOIN payroll_details pd ON pd.payroll_id = p.id
WHERE p.status = 'Paid'
    AND p.month BETWEEN ? AND ?
GROUP BY p.month
ORDER BY p.month;
```

#### Get Attendance Statistics
```sql
SELECT 
    DATE_FORMAT(a.atten_date, '%Y-%m') AS month,
    COUNT(DISTINCT a.emp_id) AS employees_tracked,
    COUNT(a.id) AS total_records,
    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present_count,
    SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent_count,
    SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) AS late_count,
    AVG(a.hours_worked) AS avg_hours_worked
FROM attendance a
WHERE a.atten_date BETWEEN ? AND ?
GROUP BY DATE_FORMAT(a.atten_date, '%Y-%m')
ORDER BY month;
```

## Data Maintenance Queries

### 14. Data Cleanup and Maintenance

#### Find Orphaned Records
```sql
-- Find employee_projects without valid employees
SELECT ep.* 
FROM employee_projects ep
LEFT JOIN employees e ON e.employee_id = ep.emp_id
WHERE e.employee_id IS NULL;

-- Find payroll_details without valid employees
SELECT pd.* 
FROM payroll_details pd
LEFT JOIN employees e ON e.employee_id = pd.emp_id
WHERE e.employee_id IS NULL;

-- Find attendance records without valid employees
SELECT a.* 
FROM attendance a
LEFT JOIN employees e ON e.employee_id = a.emp_id
WHERE e.employee_id IS NULL;
```

#### Update Employee Status
```sql
-- Deactivate employees who have resigned
UPDATE employees 
SET status = 'inactive' 
WHERE employee_id IN (
    SELECT emp_id 
    FROM resignations 
    WHERE status = 'Approved' 
        AND last_working_date < CURDATE()
);
```

#### Archive Old Records
```sql
-- Archive attendance records older than 2 years
CREATE TABLE attendance_archive AS 
SELECT * FROM attendance 
WHERE atten_date < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);

-- Delete archived records from main table
DELETE FROM attendance 
WHERE atten_date < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);
```

## Performance Optimization Queries

### 15. Index Usage and Performance

#### Check Query Performance
```sql
-- Use EXPLAIN to analyze query performance
EXPLAIN SELECT 
    e.full_name,
    COUNT(a.id) as attendance_count
FROM employees e
LEFT JOIN attendance a ON a.emp_id = e.employee_id
WHERE e.status = 'active'
    AND a.atten_date BETWEEN '2024-01-01' AND '2024-12-31'
GROUP BY e.employee_id, e.full_name;
```

#### Create Useful Indexes
```sql
-- Indexes for common queries
CREATE INDEX idx_employees_status ON employees(status);
CREATE INDEX idx_attendance_emp_date ON attendance(emp_id, atten_date);
CREATE INDEX idx_payroll_details_payroll_emp ON payroll_details(payroll_id, emp_id);
CREATE INDEX idx_employee_transactions_emp_date ON employee_transactions(emp_id, date);
CREATE INDEX idx_timesheet_emp_date ON timesheet(emp_id, ts_date);
```

These query examples demonstrate the most common database operations in the HRM system and can be used as templates for building application features or generating reports.