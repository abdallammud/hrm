# Financial Database Schema

## Overview

This document provides detailed information about the database tables and relationships that support the financial management system in the HRM application. The financial system uses a relational database design with proper foreign key relationships and data integrity constraints.

## Core Financial Tables

### 1. bank_accounts

**Purpose**: Stores company bank account information and current balances

**Table Structure**:
```sql
CREATE TABLE bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(255) NOT NULL,
    account VARCHAR(255),
    balance DECIMAL(12,2) DEFAULT 0.00,
    status ENUM('active', 'inactive') DEFAULT 'active',
    added_by INT,
    updated_by INT,
    updated_date TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_bank_name (bank_name),
    INDEX idx_status (status),
    INDEX idx_balance (balance)
);
```

**Field Descriptions**:

| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| id | INT AUTO_INCREMENT | Primary key identifier | NOT NULL, PRIMARY KEY |
| bank_name | VARCHAR(255) | Name of the bank | NOT NULL |
| account | VARCHAR(255) | Bank account number | NULL allowed |
| balance | DECIMAL(12,2) | Current account balance | DEFAULT 0.00 |
| status | ENUM | Account status | 'active', 'inactive' |
| added_by | INT | User ID who created the account | Foreign key to users table |
| updated_by | INT | User ID who last updated | Foreign key to users table |
| updated_date | TIMESTAMP | Last update timestamp | NULL allowed |
| created_at | TIMESTAMP | Account creation timestamp | DEFAULT CURRENT_TIMESTAMP |

**Relationships**:
- **Referenced by**: `fn_transactions.bank_id`
- **Referenced by**: `payroll_details.bank_id`
- **References**: `users.user_id` (added_by, updated_by)

**Business Rules**:
- Bank names must be unique within the system
- Balance can be negative (overdraft scenarios)
- Status changes preserve historical data
- All balance updates must be logged through transactions

**Sample Data**:
```sql
INSERT INTO bank_accounts (bank_name, account, balance, status, added_by) VALUES
('First National Bank', '1234567890', 50000.00, 'active', 1),
('City Bank', '0987654321', 25000.00, 'active', 1),
('Regional Credit Union', '5555666677', 10000.00, 'inactive', 2);
```

### 2. fn_transactions

**Purpose**: Records all financial transactions including income and expenses

**Table Structure**:
```sql
CREATE TABLE fn_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('Income', 'Expense') NOT NULL,
    bank_id INT NOT NULL,
    bank_name VARCHAR(255),
    bank_account VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    fn_account_id INT,
    fn_account_name VARCHAR(255),
    payee_payer VARCHAR(255),
    description TEXT,
    ref_number VARCHAR(100),
    added_by INT NOT NULL,
    added_date TIMESTAMP NOT NULL,
    updated_by INT,
    updated_date TIMESTAMP,
    status ENUM('Active', 'Cancelled') DEFAULT 'Active',
    
    INDEX idx_type (type),
    INDEX idx_bank_id (bank_id),
    INDEX idx_fn_account_id (fn_account_id),
    INDEX idx_added_date (added_date),
    INDEX idx_status (status),
    INDEX idx_amount (amount),
    
    FOREIGN KEY (bank_id) REFERENCES bank_accounts(id),
    FOREIGN KEY (fn_account_id) REFERENCES financial_accounts(id),
    FOREIGN KEY (added_by) REFERENCES users(user_id),
    FOREIGN KEY (updated_by) REFERENCES users(user_id)
);
```

**Field Descriptions**:

| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| id | INT AUTO_INCREMENT | Primary key identifier | NOT NULL, PRIMARY KEY |
| type | ENUM | Transaction type | 'Income', 'Expense' |
| bank_id | INT | Reference to bank account | NOT NULL, FOREIGN KEY |
| bank_name | VARCHAR(255) | Bank name (denormalized) | Copied from bank_accounts |
| bank_account | VARCHAR(255) | Account number (denormalized) | Copied from bank_accounts |
| amount | DECIMAL(10,2) | Transaction amount | NOT NULL, > 0 |
| fn_account_id | INT | Financial account category | FOREIGN KEY |
| fn_account_name | VARCHAR(255) | Account name (denormalized) | Copied from financial_accounts |
| payee_payer | VARCHAR(255) | Payee or payer name | NULL allowed |
| description | TEXT | Transaction description | NULL allowed |
| ref_number | VARCHAR(100) | Reference number | NULL allowed |
| added_by | INT | User who created transaction | NOT NULL, FOREIGN KEY |
| added_date | TIMESTAMP | Transaction date | NOT NULL |
| updated_by | INT | User who last updated | FOREIGN KEY |
| updated_date | TIMESTAMP | Last update timestamp | NULL allowed |
| status | ENUM | Transaction status | 'Active', 'Cancelled' |

**Relationships**:
- **References**: `bank_accounts.id` (bank_id)
- **References**: `financial_accounts.id` (fn_account_id)
- **References**: `users.user_id` (added_by, updated_by)

**Business Rules**:
- Amount must be positive (direction determined by type)
- Bank balance automatically updated on transaction creation/modification
- Cancelled transactions do not affect bank balances
- Reference numbers should be unique within a bank account
- Transaction dates can be different from creation dates

**Sample Data**:
```sql
INSERT INTO fn_transactions (type, bank_id, bank_name, amount, fn_account_id, fn_account_name, payee_payer, description, added_by, added_date) VALUES
('Expense', 1, 'First National Bank', 1500.00, 5, 'Office Supplies', 'ABC Office Supply', 'Monthly office supplies purchase', 1, '2024-01-15 10:30:00'),
('Income', 1, 'First National Bank', 5000.00, 2, 'Service Revenue', 'XYZ Corporation', 'Consulting services payment', 1, '2024-01-16 14:20:00'),
('Expense', 2, 'City Bank', 800.00, 6, 'Utilities', 'Electric Company', 'Monthly electricity bill', 2, '2024-01-17 09:15:00');
```

### 3. financial_accounts

**Purpose**: Categorizes transactions into financial account types for reporting and analysis

**Table Structure**:
```sql
CREATE TABLE financial_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('Asset', 'Liability', 'Income', 'Expense') NOT NULL,
    description TEXT,
    parent_id INT,
    account_code VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_name (name),
    INDEX idx_type (type),
    INDEX idx_parent_id (parent_id),
    INDEX idx_account_code (account_code),
    INDEX idx_status (status),
    
    FOREIGN KEY (parent_id) REFERENCES financial_accounts(id)
);
```

**Field Descriptions**:

| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| id | INT AUTO_INCREMENT | Primary key identifier | NOT NULL, PRIMARY KEY |
| name | VARCHAR(255) | Account name | NOT NULL, UNIQUE |
| type | ENUM | Account type | 'Asset', 'Liability', 'Income', 'Expense' |
| description | TEXT | Account description | NULL allowed |
| parent_id | INT | Parent account for hierarchy | FOREIGN KEY, NULL allowed |
| account_code | VARCHAR(50) | Accounting code | NULL allowed |
| status | ENUM | Account status | 'active', 'inactive' |
| created_at | TIMESTAMP | Creation timestamp | DEFAULT CURRENT_TIMESTAMP |
| updated_at | TIMESTAMP | Update timestamp | ON UPDATE CURRENT_TIMESTAMP |

**Relationships**:
- **Referenced by**: `fn_transactions.fn_account_id`
- **Self-referencing**: `parent_id` references `id` for hierarchical structure

**Business Rules**:
- Account names must be unique
- Parent accounts must exist before child accounts
- Account hierarchy supports multiple levels
- Inactive accounts cannot be used for new transactions
- Account codes should follow standard accounting practices

**Sample Data**:
```sql
INSERT INTO financial_accounts (name, type, description, account_code) VALUES
('Current Assets', 'Asset', 'Short-term assets', '1000'),
('Cash and Cash Equivalents', 'Asset', 'Bank accounts and petty cash', '1100'),
('Operating Expenses', 'Expense', 'Day-to-day operational costs', '5000'),
('Office Supplies', 'Expense', 'Office materials and supplies', '5100'),
('Utilities', 'Expense', 'Electricity, water, internet', '5200'),
('Service Revenue', 'Income', 'Revenue from services provided', '4000');
```

## Integration Tables

### 4. banks

**Purpose**: Master list of banks for reference and validation

**Table Structure**:
```sql
CREATE TABLE banks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20),
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(100),
    website VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_name (name),
    INDEX idx_code (code),
    INDEX idx_status (status)
);
```

**Field Descriptions**:

| Field | Type | Description | Constraints |
|-------|------|-------------|-------------|
| id | INT AUTO_INCREMENT | Primary key identifier | NOT NULL, PRIMARY KEY |
| name | VARCHAR(255) | Bank name | NOT NULL |
| code | VARCHAR(20) | Bank code/routing number | NULL allowed |
| address | TEXT | Bank address | NULL allowed |
| phone | VARCHAR(50) | Contact phone number | NULL allowed |
| email | VARCHAR(100) | Contact email | NULL allowed |
| website | VARCHAR(255) | Bank website URL | NULL allowed |
| status | ENUM | Bank status | 'active', 'inactive' |
| created_at | TIMESTAMP | Creation timestamp | DEFAULT CURRENT_TIMESTAMP |

**Relationships**:
- **Referenced by**: Bank selection dropdowns in UI
- **Informational**: Not directly linked via foreign keys

## Payroll Integration Tables

### 5. payroll_details (Financial Aspects)

**Purpose**: Links payroll processing with financial transactions

**Relevant Financial Fields**:
```sql
-- Financial-related fields in payroll_details table
bank_id INT,                    -- Bank account for payment
paid_through VARCHAR(255),      -- Payment method description
pay_date TIMESTAMP,            -- Payment date
paid_by INT,                   -- User who processed payment
base_salary DECIMAL(10,2),     -- Base salary amount
allowance DECIMAL(10,2),       -- Allowances total
bonus DECIMAL(10,2),           -- Bonus amount
commission DECIMAL(10,2),      -- Commission amount
loan DECIMAL(10,2),            -- Loan deductions
advance DECIMAL(10,2),         -- Advance deductions
deductions DECIMAL(10,2),      -- Other deductions
tax DECIMAL(10,2),             -- Tax amount

FOREIGN KEY (bank_id) REFERENCES bank_accounts(id),
FOREIGN KEY (paid_by) REFERENCES users(user_id)
```

**Financial Integration**:
- Bank balance automatically reduced when payroll is paid
- Payment details recorded for audit trail
- Net salary calculation: `(base_salary + allowance + bonus + commission) - (loan + advance + deductions + tax)`

## Database Relationships Diagram

```
bank_accounts (1) ----< fn_transactions (M)
    |
    |
    +----< payroll_details (M)

financial_accounts (1) ----< fn_transactions (M)
    |
    +---- financial_accounts (self-referencing for hierarchy)

users (1) ----< fn_transactions (M) [added_by, updated_by]
    |
    +----< bank_accounts (M) [added_by, updated_by]
    |
    +----< payroll_details (M) [paid_by]
```

## Indexes and Performance

### Primary Indexes

**bank_accounts**:
- PRIMARY KEY: `id`
- INDEX: `bank_name` (for searches and uniqueness)
- INDEX: `status` (for filtering active accounts)
- INDEX: `balance` (for balance-based queries)

**fn_transactions**:
- PRIMARY KEY: `id`
- INDEX: `type` (for income/expense filtering)
- INDEX: `bank_id` (for bank-specific transactions)
- INDEX: `added_date` (for date-based queries)
- INDEX: `status` (for active transaction filtering)
- COMPOSITE INDEX: `(type, added_date)` (for transaction reports)

**financial_accounts**:
- PRIMARY KEY: `id`
- UNIQUE INDEX: `name` (for account name uniqueness)
- INDEX: `type` (for account type filtering)
- INDEX: `parent_id` (for hierarchical queries)

### Query Optimization

**Common Query Patterns**:
```sql
-- Bank account balance lookup
SELECT balance FROM bank_accounts WHERE id = ? AND status = 'active';

-- Transaction history by bank
SELECT * FROM fn_transactions 
WHERE bank_id = ? AND status = 'Active' 
ORDER BY added_date DESC 
LIMIT 50;

-- Monthly expense summary
SELECT fn_account_name, SUM(amount) as total
FROM fn_transactions 
WHERE type = 'Expense' 
  AND added_date >= '2024-01-01' 
  AND added_date < '2024-02-01'
  AND status = 'Active'
GROUP BY fn_account_id, fn_account_name;

-- Bank balance validation for payroll
SELECT balance FROM bank_accounts 
WHERE id = ? AND status = 'active' AND balance >= ?;
```

## Data Integrity Constraints

### Foreign Key Constraints

```sql
-- fn_transactions constraints
ALTER TABLE fn_transactions 
ADD CONSTRAINT fk_fn_trans_bank 
FOREIGN KEY (bank_id) REFERENCES bank_accounts(id);

ALTER TABLE fn_transactions 
ADD CONSTRAINT fk_fn_trans_account 
FOREIGN KEY (fn_account_id) REFERENCES financial_accounts(id);

ALTER TABLE fn_transactions 
ADD CONSTRAINT fk_fn_trans_user 
FOREIGN KEY (added_by) REFERENCES users(user_id);

-- payroll_details constraints
ALTER TABLE payroll_details 
ADD CONSTRAINT fk_payroll_bank 
FOREIGN KEY (bank_id) REFERENCES bank_accounts(id);
```

### Check Constraints

```sql
-- Ensure positive amounts
ALTER TABLE fn_transactions 
ADD CONSTRAINT chk_amount_positive 
CHECK (amount > 0);

-- Ensure valid transaction types
ALTER TABLE fn_transactions 
ADD CONSTRAINT chk_valid_type 
CHECK (type IN ('Income', 'Expense'));

-- Ensure valid account types
ALTER TABLE financial_accounts 
ADD CONSTRAINT chk_valid_account_type 
CHECK (type IN ('Asset', 'Liability', 'Income', 'Expense'));
```

## Backup and Recovery Considerations

### Critical Data Protection

**High Priority Tables**:
1. `bank_accounts` - Contains current balance information
2. `fn_transactions` - Complete transaction history
3. `payroll_details` - Payroll payment records

**Backup Strategy**:
- Daily full backups of financial tables
- Transaction log backups every 15 minutes
- Point-in-time recovery capability for financial data
- Separate backup verification for balance integrity

### Data Archival

**Transaction Archival**:
```sql
-- Archive old transactions (example: older than 7 years)
CREATE TABLE fn_transactions_archive LIKE fn_transactions;

INSERT INTO fn_transactions_archive 
SELECT * FROM fn_transactions 
WHERE added_date < DATE_SUB(NOW(), INTERVAL 7 YEAR);

DELETE FROM fn_transactions 
WHERE added_date < DATE_SUB(NOW(), INTERVAL 7 YEAR);
```

## Security Considerations

### Sensitive Data Protection

**Financial Data Encryption**:
- Bank account numbers should be encrypted at rest
- Transaction amounts may require encryption for sensitive operations
- Reference numbers and descriptions should be protected

**Access Control**:
- Financial tables require elevated permissions
- Balance modifications logged with user identification
- Read-only access for reporting users
- Administrative access for financial managers only

### Audit Trail Requirements

**Required Audit Fields**:
- `added_by` - User who created the record
- `added_date` - When the record was created
- `updated_by` - User who last modified the record
- `updated_date` - When the record was last modified

**Audit Log Triggers**:
```sql
-- Example trigger for bank account changes
CREATE TRIGGER bank_account_audit 
AFTER UPDATE ON bank_accounts
FOR EACH ROW
INSERT INTO audit_log (table_name, record_id, old_values, new_values, changed_by, changed_at)
VALUES ('bank_accounts', NEW.id, JSON_OBJECT('balance', OLD.balance), JSON_OBJECT('balance', NEW.balance), NEW.updated_by, NOW());
```