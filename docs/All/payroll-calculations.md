# Payroll Calculations

## Overview

The HRM application implements a comprehensive payroll calculation system that computes employee salaries based on base salary, attendance, earnings, deductions, and tax obligations. The system handles complex scenarios including overtime, undertime, various leave types, and state-specific tax calculations.

## Calculation Components

### 1. Base Salary Calculation

#### Working Days Computation
The system calculates required working days for each month based on:
- **Working Days Per Week**: Configurable setting (default: 5 days)
- **Calendar Days**: Total days in the payroll month
- **Holidays**: Excluded from required working days
- **Not Hired Days**: Days before employee hire date

```php
// Calculate required work days in the month
$requiredDays = getWorkdaysInMonth($month, $workDays);
$requiredDays -= $attendanceInfo['not_hired_days'] - $attendanceInfo['holidays'];
```

#### Daily and Hourly Rates
- **Salary Per Day**: `base_salary / required_days`
- **Salary Per Hour**: `salary_per_day / working_hours_per_day`

### 2. Attendance-Based Adjustments

#### Attendance Status Types
- **P (Present)**: Full day attendance
- **PL (Paid Leave)**: Paid leave days
- **S (Sick)**: Sick leave days
- **UL (Unpaid Leave)**: Unpaid leave days
- **H (Holiday)**: Public holidays
- **NH (Not Hired)**: Days before employment start
- **N (No Show)**: Absent without leave

#### Unpaid Days Calculation
```php
$unpaidDaysCost = ($attendanceInfo['unpaid_leave_days'] + $attendanceInfo['no_show_days']) * $salaryPerDay;
```

#### Effective Days Worked
```php
$daysWorked = $requiredDays - $attendanceInfo['unpaid_leave_days'] - $attendanceInfo['no_show_days'] 
              - $attendanceInfo['paid_leave_days'] - $attendanceInfo['sick_days'];
```

### 3. Overtime and Undertime Calculations

#### Overtime Processing
When overtime is enabled in system settings:
- Calculate net hours worked vs. required hours
- **Positive Net Hours**: Overtime (additional payment)
- **Negative Net Hours**: Undertime (deduction)

```php
if (return_setting('overtime') === 'Yes') {
    $timeSheetInfo = calculateTimeSheetHours($employeeId, $month, $workHours);
    $netHours = $timeSheetInfo['net_hours'];
    
    if ($netHours > 0) {
        $extraHours = $netHours * $salaryPerHour;
    } elseif ($netHours < 0) {
        $underHours = abs($netHours) * $salaryPerHour;
    }
}
```

#### Timesheet Hours Calculation
```php
function calculateTimeSheetHours($employeeId, $payrollMonth, $workHoursPerDay) {
    // Query timesheet_details for actual worked hours
    // Calculate net hours: (actual_hours - required_hours) per day
    // Return positive for overtime, negative for undertime
}
```

### 4. Earnings Calculation

#### Earnings Types
- **Allowance**: Regular allowances and benefits
- **Bonus**: Performance or special bonuses
- **Commission**: Sales or performance-based commissions

#### Earnings Retrieval
```php
function calculateEmployeeEarnings($employeeId, $payrollMonth) {
    $earningTypes = ['Commission', 'Bonus', 'Allowance'];
    
    // Query employee_transactions for approved earnings
    $query = "SELECT transaction_type, SUM(amount) AS total
              FROM employee_transactions
              WHERE emp_id = '$employeeId'
              AND status = 'Approved'
              AND DATE_FORMAT(date, '%Y-%m') = '$payrollMonth'
              AND transaction_type IN ('Commission', 'Bonus', 'Allowance')
              GROUP BY transaction_type";
}
```

### 5. Deductions Calculation

#### Deduction Types
- **Loan**: Employee loan repayments
- **Advance**: Salary advances to be recovered
- **Deduction**: Other miscellaneous deductions

#### Deductions Retrieval
```php
function calculateEmployeeDeductions($employeeId, $payrollMonth) {
    $deductionTypes = ['Loan', 'Advance', 'Deduction'];
    
    // Query employee_transactions for approved deductions
    $query = "SELECT transaction_type, SUM(amount) AS total
              FROM employee_transactions
              WHERE emp_id = '$employeeId'
              AND status = 'Approved'
              AND DATE_FORMAT(date, '%Y-%m') = '$payrollMonth'
              AND transaction_type IN ('Loan', 'Advance', 'Deduction')
              GROUP BY transaction_type";
}
```

### 6. Tax Calculations

#### Tax System Overview
The system implements a progressive tax system based on state-specific tax grids stored in the `states` table.

#### Tax Grid Structure
Each state has a JSON tax grid defining tax brackets:
```json
[
    {"min": 0, "max": 10000, "rate": 0},
    {"min": 10001, "max": 50000, "rate": 10},
    {"min": 50001, "max": 100000, "rate": 20},
    {"min": 100001, "max": 999999999, "rate": 30}
]
```

#### Tax Calculation Process
```php
function getTaxRate(float $amount, int $stateId): float {
    // 1. Fetch state tax grid and stamp duty rate
    $stateInfo = get_data('states', ['id' => $stateId]);
    $taxGrid = json_decode($stateInfo['tax_grid'], true);
    $stampDutyRate = $stateInfo['stamp_duty'];
    
    // 2. Determine applicable tax bracket
    foreach ($taxGrid as $taxBracket) {
        if ($amount >= $taxBracket['min'] && $amount <= $taxBracket['max']) {
            // 3. Calculate tax on taxable amount
            $taxableAmount = max(0, $amount - $nonTaxableAmount);
            $calculatedTax = $taxableAmount * ($taxBracket['rate'] / 100);
            
            // 4. Add stamp duty
            $stampDuty = $calculatedTax * ($stampDutyRate / 100);
            
            return $calculatedTax + $stampDuty;
        }
    }
}
```

#### Non-Taxable Amount
- Tax brackets with 0% rate define non-taxable income thresholds
- Only income above the non-taxable threshold is subject to tax

#### Stamp Duty
- Additional percentage applied to calculated tax
- State-specific stamp duty rates
- Added to final tax amount

### 7. Net Salary Calculation

#### Final Calculation Formula
```php
$total_earnings = $salary + $allowance + $bonus + $commission + $extraHours 
                  - $loan - $advance - $deduction - $underHours - $unpaidDaysCost;

// Calculate tax on total earnings
$taxRate = getTaxRate($total_earnings, $state_id);
$net_salary = $total_earnings - $taxRate;
```

#### Calculation Sequence
1. **Base Salary**: Employee's monthly base salary
2. **Add Earnings**: + allowances + bonuses + commissions + overtime
3. **Subtract Deductions**: - loans - advances - other deductions - undertime - unpaid days
4. **Calculate Tax**: Apply progressive tax on gross amount
5. **Final Net Salary**: Gross earnings - tax amount

## Payroll Detail Record Structure

### Database Fields
The calculated values are stored in the `payroll_details` table:

```sql
CREATE TABLE payroll_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payroll_id INT,
    emp_id INT,
    staff_no VARCHAR(50),
    full_name VARCHAR(100),
    month VARCHAR(20),
    required_days INT,
    days_worked INT,
    base_salary DECIMAL(10,2),
    allowance DECIMAL(10,2),
    bonus DECIMAL(10,2),
    extra_hours DECIMAL(10,2),
    commission DECIMAL(10,2),
    tax DECIMAL(10,2),
    advance DECIMAL(10,2),
    loan DECIMAL(10,2),
    deductions DECIMAL(10,2),
    unpaid_days DECIMAL(10,2),
    unpaid_hours DECIMAL(10,2),
    bank_name VARCHAR(100),
    bank_number VARCHAR(50),
    status ENUM('Draft', 'Approved', 'Paid'),
    pay_date DATETIME,
    paid_by INT,
    paid_through VARCHAR(200),
    bank_id INT
);
```

## Configuration Settings

### System Settings
The payroll calculations depend on several configurable settings:

- **working_hours**: Daily working hours (default: 8)
- **working_days**: Weekly working days (default: 5)
- **overtime**: Enable/disable overtime calculations (Yes/No)
- **time_in**: Standard work start time
- **time_out**: Standard work end time

### Employee-Specific Settings
- **salary**: Monthly base salary
- **work_days**: Individual working days per week
- **work_hours**: Individual working hours per day
- **state_id**: Employee's tax jurisdiction
- **payment_bank**: Designated payment bank account
- **contract_type**: Employment contract type

## Error Handling in Calculations

### Division by Zero Prevention
```php
$salaryPerDay = $salary / max($requiredDays, 1); // Avoid division by zero
$salaryPerHour = $salaryPerDay / max($workHours, 1);
```

### Data Validation
- Validate employee existence before calculations
- Ensure positive salary amounts
- Verify attendance data completeness
- Validate tax grid JSON structure

### Calculation Boundaries
- Skip employees with zero required days
- Handle missing timesheet data gracefully
- Default to zero for missing transaction data
- Validate date ranges for payroll periods

## Performance Optimization

### Batch Processing
- Process multiple employees in single database transaction
- Cache frequently accessed settings and tax grids
- Optimize database queries with proper indexing
- Use prepared statements for repeated operations

### Memory Management
- Process payroll in batches to manage memory usage
- Clear temporary calculation variables
- Efficient data structure usage for large payrolls
- Garbage collection optimization for long-running processes