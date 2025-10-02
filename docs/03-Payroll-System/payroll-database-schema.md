# Payroll Database Schema

## Overview

The payroll system uses several interconnected database tables to manage payroll generation, employee transactions, payment processing, and audit trails. This document provides detailed schema information for all payroll-related tables and their relationships.

## Core Payroll Tables

### 1. payroll
**Primary Key**: id  
**Description**: Main payroll batch records containing payroll generation metadata

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique payroll batch identifier |
| ref | VARCHAR(100) | NOT NULL | Payroll reference type (All, Department, Location, Employee) |
| ref_id | VARCHAR(50) | NULL | Reference entity ID (branch_id, location_id, employee_id) |
| ref_name | VARCHAR(200) | NOT NULL | Human-readable reference name |
| month | VARCHAR(500) | NOT NULL | Comma-separated list of payroll months (YYYY-MM format) |
| workflow | JSON | NULL | JSON array of workflow status changes and approvals |
| status | ENUM | DEFAULT 'Draft' | Payroll status: 'Draft', 'Approved', 'Paid' |
| added_by | INT | NOT NULL | Foreign key to users table (creator) |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Payroll creation timestamp |
| updated_by | INT | NULL | Foreign key to users table (last modifier) |
| updated_date | TIMESTAMP | NULL | Last modification timestamp |

#### Indexes
- PRIMARY KEY (id)
- INDEX idx_payroll_ref (ref, ref_id)
- INDEX idx_payroll_month (month)
- INDEX idx_payroll_status (status)
- INDEX idx_payroll_added_by (added_by)

#### Sample Data
```sql
INSERT INTO payroll (ref, ref_id, ref_name, month, workflow, added_by) VALUES
('All', '', 'All employees', '2024-01,2024-02', 
 '[{"action":"Created by Admin","date":"2024-01-15 10:30:00","status":"Created","user_id":1}]', 1);
```

### 2. payroll_details
**Primary Key**: id  
**Description**: Individual employee payroll calculations and payment details

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique payroll detail record identifier |
| payroll_id | INT | NOT NULL | Foreign key to payroll table |
| emp_id | INT | NOT NULL | Foreign key to employees table |
| staff_no | VARCHAR(50) | NOT NULL | Employee staff number (for reference) |
| full_name | VARCHAR(200) | NOT NULL | Employee full name (for reference) |
| email | VARCHAR(100) | NULL | Employee email (for reference) |
| contract_type | VARCHAR(50) | NULL | Employment contract type |
| job_title | VARCHAR(100) | NULL | Employee job title/position |
| month | VARCHAR(20) | NOT NULL | Specific payroll month (YYYY-MM) |
| required_days | INT | DEFAULT 0 | Required working days in the month |
| days_worked | INT | DEFAULT 0 | Actual days worked by employee |
| base_salary | DECIMAL(12,2) | DEFAULT 0.00 | Employee base monthly salary |
| allowance | DECIMAL(12,2) | DEFAULT 0.00 | Total allowances for the month |
| bonus | DECIMAL(12,2) | DEFAULT 0.00 | Total bonuses for the month |
| extra_hours | DECIMAL(12,2) | DEFAULT 0.00 | Overtime payment amount |
| commission | DECIMAL(12,2) | DEFAULT 0.00 | Total commissions for the month |
| tax | DECIMAL(12,2) | DEFAULT 0.00 | Calculated tax amount |
| advance | DECIMAL(12,2) | DEFAULT 0.00 | Salary advances to be deducted |
| loan | DECIMAL(12,2) | DEFAULT 0.00 | Loan repayments to be deducted |
| deductions | DECIMAL(12,2) | DEFAULT 0.00 | Other miscellaneous deductions |
| unpaid_days | DECIMAL(12,2) | DEFAULT 0.00 | Deduction for unpaid leave days |
| unpaid_hours | DECIMAL(12,2) | DEFAULT 0.00 | Deduction for undertime hours |
| bank_name | VARCHAR(100) | NULL | Employee's designated bank name |
| bank_number | VARCHAR(50) | NULL | Employee's bank account number |
| bank_id | INT | NULL | Foreign key to bank_accounts (payment source) |
| status | ENUM | DEFAULT 'Draft' | Payment status: 'Draft', 'Approved', 'Paid' |
| pay_date | DATETIME | NULL | Actual payment processing date |
| paid_by | INT | NULL | Foreign key to users table (payment processor) |
| paid_through | VARCHAR(200) | NULL | Payment method description |
| added_by | INT | NOT NULL | Foreign key to users table (creator) |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |

#### Calculated Fields (Virtual)
```sql
-- Net Salary Calculation
SELECT (base_salary + allowance + bonus + commission + extra_hours) 
       - (loan + advance + deductions + unpaid_days + unpaid_hours + tax) AS net_salary
FROM payroll_details;

-- Total Earnings
SELECT (base_salary + allowance + bonus + commission + extra_hours) AS total_earnings
FROM payroll_details;

-- Total Deductions  
SELECT (loan + advance + deductions + unpaid_days + unpaid_hours + tax) AS total_deductions
FROM payroll_details;
```

#### Indexes
- PRIMARY KEY (id)
- INDEX idx_payroll_details_payroll_id (payroll_id)
- INDEX idx_payroll_details_emp_id (emp_id)
- INDEX idx_payroll_details_month (month)
- INDEX idx_payroll_details_status (status)
- UNIQUE KEY uk_payroll_emp_month (payroll_id, emp_id, month)

### 3. employee_transactions
**Primary Key**: transaction_id  
**Description**: Employee financial transactions (earnings and deductions)

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| transaction_id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique transaction identifier |
| emp_id | INT | NOT NULL | Foreign key to employees table |
| staff_no | VARCHAR(50) | NOT NULL | Employee staff number (for reference) |
| full_name | VARCHAR(200) | NOT NULL | Employee full name (for reference) |
| phone_number | VARCHAR(20) | NULL | Employee phone (for reference) |
| email | VARCHAR(100) | NULL | Employee email (for reference) |
| transaction_type | VARCHAR(50) | NOT NULL | Transaction category (Commission, Bonus, Allowance, Loan, Advance, Deduction) |
| transaction_subtype | VARCHAR(100) | NULL | Specific transaction subtype |
| amount | DECIMAL(12,2) | NOT NULL | Transaction amount |
| description | TEXT | NULL | Transaction description or notes |
| date | DATE | NOT NULL | Transaction effective date |
| payroll_id | INT | NULL | Associated payroll batch (set during payroll generation) |
| status | ENUM | DEFAULT 'Active' | Transaction status: 'Active', 'Cancelled', 'Approved' |
| added_by | INT | NOT NULL | Foreign key to users table (creator) |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Transaction creation timestamp |
| updated_by | INT | NULL | Foreign key to users table (last modifier) |
| updated_date | TIMESTAMP | NULL | Last modification timestamp |

#### Transaction Types
- **Earnings**: Commission, Bonus, Allowance
- **Deductions**: Loan, Advance, Deduction

#### Indexes
- PRIMARY KEY (transaction_id)
- INDEX idx_employee_transactions_emp_id (emp_id)
- INDEX idx_employee_transactions_type (transaction_type)
- INDEX idx_employee_transactions_date (date)
- INDEX idx_employee_transactions_payroll_id (payroll_id)
- INDEX idx_employee_transactions_status (status)

## Supporting Tables

### 4. bank_accounts
**Primary Key**: id  
**Description**: Company bank accounts used for payroll payments

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique bank account identifier |
| bank_name | VARCHAR(100) | NOT NULL | Bank institution name |
| account | VARCHAR(50) | NOT NULL | Bank account number |
| balance | DECIMAL(15,2) | DEFAULT 0.00 | Current account balance |
| status | ENUM | DEFAULT 'active' | Account status: 'active', 'inactive' |
| added_by | INT | NOT NULL | Foreign key to users table (creator) |
| added_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Account creation timestamp |
| updated_by | INT | NULL | Foreign key to users table (last modifier) |
| updated_date | TIMESTAMP | NULL | Last modification timestamp |

### 5. states
**Primary Key**: id  
**Description**: State/province information with tax configuration

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | Unique state identifier |
| name | VARCHAR(100) | NOT NULL | State/province name |
| country_name | VARCHAR(100) | NULL | Country name |
| tax_grid | JSON | NULL | Progressive tax brackets configuration |
| stamp_duty | DECIMAL(5,2) | DEFAULT 0.00 | Stamp duty percentage on tax |
| status | ENUM | DEFAULT 'active' | State status: 'active', 'inactive' |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |

#### Tax Grid JSON Structure
```json
[
    {"min": 0, "max": 10000, "rate": 0},
    {"min": 10001, "max": 50000, "rate": 10},
    {"min": 50001, "max": 100000, "rate": 20},
    {"min": 100001, "max": 999999999, "rate": 30}
]
```

## Table Relationships

### Primary Relationships
```sql
-- Payroll to Payroll Details (One-to-Many)
ALTER TABLE payroll_details 
ADD CONSTRAINT fk_payroll_details_payroll 
FOREIGN KEY (payroll_id) REFERENCES payroll(id) ON DELETE CASCADE;

-- Payroll Details to Employee (Many-to-One)
ALTER TABLE payroll_details 
ADD CONSTRAINT fk_payroll_details_employee 
FOREIGN KEY (emp_id) REFERENCES employees(employee_id) ON DELETE CASCADE;

-- Employee Transactions to Employee (Many-to-One)
ALTER TABLE employee_transactions 
ADD CONSTRAINT fk_employee_transactions_employee 
FOREIGN KEY (emp_id) REFERENCES employees(employee_id) ON DELETE CASCADE;

-- Employee Transactions to Payroll (Many-to-One)
ALTER TABLE employee_transactions 
ADD CONSTRAINT fk_employee_transactions_payroll 
FOREIGN KEY (payroll_id) REFERENCES payroll(id) ON DELETE SET NULL;

-- Payroll Details to Bank Account (Many-to-One)
ALTER TABLE payroll_details 
ADD CONSTRAINT fk_payroll_details_bank 
FOREIGN KEY (bank_id) REFERENCES bank_accounts(id) ON DELETE SET NULL;

-- Employee to State (Many-to-One) - for tax calculations
ALTER TABLE employees 
ADD CONSTRAINT fk_employees_state 
FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE SET NULL;
```

### Relationship Diagram
```
employees (1) ----< (M) payroll_details
    |                       |
    |                       |
    v                       v
states (1)              payroll (1)
    |                       |
    |                       |
    v                       v
tax_grid              workflow_history

employees (1) ----< (M) employee_transactions
                            |
                            v
                        payroll (1)

bank_accounts (1) ----< (M) payroll_details
```

## Common SQL Queries

### 1. Generate Payroll Summary
```sql
SELECT 
    p.id,
    p.ref_name,
    p.month,
    p.status,
    COUNT(DISTINCT pd.emp_id) as employee_count,
    SUM(pd.base_salary + pd.allowance + pd.bonus + pd.commission + pd.extra_hours) as total_earnings,
    SUM(pd.loan + pd.advance + pd.deductions + pd.unpaid_days + pd.unpaid_hours + pd.tax) as total_deductions,
    SUM((pd.base_salary + pd.allowance + pd.bonus + pd.commission + pd.extra_hours) - 
        (pd.loan + pd.advance + pd.deductions + pd.unpaid_days + pd.unpaid_hours + pd.tax)) as net_payroll
FROM payroll p
LEFT JOIN payroll_details pd ON p.id = pd.payroll_id
GROUP BY p.id, p.ref_name, p.month, p.status;
```

### 2. Employee Payroll Details
```sql
SELECT 
    pd.*,
    e.full_name,
    e.staff_no,
    (pd.base_salary + pd.allowance + pd.bonus + pd.commission + pd.extra_hours) as gross_salary,
    (pd.loan + pd.advance + pd.deductions + pd.unpaid_days + pd.unpaid_hours + pd.tax) as total_deductions,
    ((pd.base_salary + pd.allowance + pd.bonus + pd.commission + pd.extra_hours) - 
     (pd.loan + pd.advance + pd.deductions + pd.unpaid_days + pd.unpaid_hours + pd.tax)) as net_salary
FROM payroll_details pd
JOIN employees e ON pd.emp_id = e.employee_id
WHERE pd.payroll_id = ? AND pd.month = ?;
```

### 3. Employee Transactions for Payroll Month
```sql
SELECT 
    transaction_type,
    SUM(amount) as total_amount
FROM employee_transactions
WHERE emp_id = ? 
    AND DATE_FORMAT(date, '%Y-%m') = ?
    AND status = 'Approved'
GROUP BY transaction_type;
```

### 4. Bank Account Balance After Payroll Payment
```sql
SELECT 
    ba.bank_name,
    ba.account,
    ba.balance as current_balance,
    SUM(((pd.base_salary + pd.allowance + pd.bonus + pd.commission + pd.extra_hours) - 
         (pd.loan + pd.advance + pd.deductions + pd.unpaid_days + pd.unpaid_hours + pd.tax))) as payroll_amount,
    (ba.balance - SUM(((pd.base_salary + pd.allowance + pd.bonus + pd.commission + pd.extra_hours) - 
                      (pd.loan + pd.advance + pd.deductions + pd.unpaid_days + pd.unpaid_hours + pd.tax)))) as balance_after_payment
FROM bank_accounts ba
JOIN payroll_details pd ON ba.id = pd.bank_id
WHERE pd.payroll_id = ? AND pd.status = 'Approved'
GROUP BY ba.id, ba.bank_name, ba.account, ba.balance;
```

### 5. Payroll Audit Trail
```sql
SELECT 
    p.id as payroll_id,
    p.ref_name,
    p.month,
    JSON_EXTRACT(p.workflow, '$[*].action') as workflow_actions,
    JSON_EXTRACT(p.workflow, '$[*].date') as workflow_dates,
    JSON_EXTRACT(p.workflow, '$[*].user_id') as workflow_users
FROM payroll p
WHERE p.id = ?;
```

## Data Integrity Constraints

### Business Rules
1. **Unique Payroll per Employee per Month**: Prevent duplicate payroll generation
2. **Positive Amounts**: Ensure all monetary amounts are non-negative
3. **Valid Date Ranges**: Payroll months must be valid calendar months
4. **Employee Existence**: All payroll records must reference valid employees
5. **Bank Account Validation**: Payment bank accounts must be active

### Triggers and Constraints
```sql
-- Prevent negative amounts
ALTER TABLE payroll_details 
ADD CONSTRAINT chk_positive_amounts 
CHECK (base_salary >= 0 AND allowance >= 0 AND bonus >= 0 AND commission >= 0 
       AND extra_hours >= 0 AND tax >= 0 AND advance >= 0 AND loan >= 0 
       AND deductions >= 0 AND unpaid_days >= 0 AND unpaid_hours >= 0);

-- Ensure valid payroll month format
ALTER TABLE payroll_details 
ADD CONSTRAINT chk_valid_month 
CHECK (month REGEXP '^[0-9]{4}-(0[1-9]|1[0-2])$');

-- Prevent duplicate payroll for same employee and month
ALTER TABLE payroll_details 
ADD CONSTRAINT uk_emp_month_payroll 
UNIQUE (emp_id, month, payroll_id);
```

## Performance Optimization

### Indexing Strategy
- **Composite Indexes**: For frequently queried combinations (emp_id + month, payroll_id + status)
- **Covering Indexes**: Include commonly selected columns in indexes
- **Partial Indexes**: For status-based queries (active transactions, approved payrolls)

### Query Optimization
- Use prepared statements for repeated queries
- Implement pagination for large payroll datasets
- Cache frequently accessed configuration data
- Optimize JOIN operations with proper indexing

### Storage Considerations
- Archive old payroll data to separate tables
- Implement data retention policies
- Use appropriate data types for monetary amounts (DECIMAL)
- Consider partitioning large tables by date ranges