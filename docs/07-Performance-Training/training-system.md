# Training Management System

## Overview

The Training Management System is a comprehensive module within the HRM application that facilitates the organization, scheduling, and tracking of employee training programs. The system manages trainer information, training types and options, employee assignments, and training progress tracking to ensure effective workforce development and skill enhancement.

## User Stories

### Trainer Management
**As an HR Administrator**, I want to register and manage trainer profiles, so that I can maintain a database of qualified instructors for various training programs.

**As a Training Coordinator**, I want to view trainer contact information and availability, so that I can efficiently schedule training sessions.

### Training Program Management
**As an HR Manager**, I want to create and schedule training programs with specific types and options, so that I can provide structured learning opportunities for employees.

**As a Department Head**, I want to assign employees to relevant training programs, so that I can ensure my team receives necessary skill development.

### Training Tracking
**As an HR Administrator**, I want to track training costs, schedules, and completion status, so that I can monitor training effectiveness and budget utilization.

**As an Employee**, I want to view my assigned training programs and schedules, so that I can plan my participation and track my professional development.

## Key Features

### 1. Trainer Management System

#### Trainer Registration
The system maintains comprehensive trainer profiles including:
- **Personal Information**: Full name, contact details (phone, email)
- **Status Management**: Active/Inactive status for availability tracking
- **Audit Trail**: Creation and modification tracking with user attribution

#### Trainer Operations
- **Create**: Register new trainers with validation
- **Read**: Search and retrieve trainer information
- **Update**: Modify trainer details and status
- **Delete**: Remove trainer records (with proper authorization)

### 2. Training Program Structure

#### Training Types
Training programs are organized into hierarchical categories:
- **Training Types**: High-level categories (Technical, Soft Skills, Compliance, etc.)
- **Training Options**: Specific training programs within each type

#### Training Assignment
Each training assignment includes:
- **Program Details**: Type, option, and trainer assignment
- **Employee Assignment**: Single or multiple employee participation
- **Schedule**: Start and end dates for training duration
- **Cost Management**: Training cost tracking and budgeting
- **Status Tracking**: Progress monitoring from assignment to completion

### 3. Employee Training Management

#### Bulk Assignment
The system supports efficient bulk operations:
- **Multi-Employee Selection**: Assign multiple employees to the same training
- **Batch Processing**: Create individual training records for each participant
- **Data Consistency**: Ensure consistent training details across all assignments

#### Training Records
Each training record maintains:
- **Employee Information**: Denormalized employee data for historical accuracy
- **Trainer Information**: Denormalized trainer data for record completeness
- **Program Details**: Training type, option, and description
- **Schedule and Cost**: Training timeline and financial information
- **Status Tracking**: Current training status and progress

## Technical Implementation

### Database Schema

#### Trainers Table
```sql
CREATE TABLE trainers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255) UNIQUE,
    status VARCHAR(20) DEFAULT 'Active',
    added_by INT,
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by INT,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Training List Table
```sql
CREATE TABLE training_list (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type_id INT,
    type_name VARCHAR(255),
    option_id INT,
    option_name VARCHAR(255),
    trainer_id INT,
    trainer_name VARCHAR(255),
    trainer_phone VARCHAR(20),
    trainer_email VARCHAR(255),
    emp_id INT,
    full_name VARCHAR(255),
    phone_number VARCHAR(20),
    email VARCHAR(255),
    staff_no VARCHAR(50),
    cost DECIMAL(10,2),
    start_date DATE,
    end_date DATE,
    description TEXT,
    status VARCHAR(50) DEFAULT 'Active',
    added_by INT,
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by INT,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Training Types Table
```sql
CREATE TABLE training_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'active'
);
```

#### Training Options Table
```sql
CREATE TABLE training_options (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'active'
);
```

### PHP Implementation

#### Training Classes
```php
class Trainers extends Model {
    public function __construct() {
        parent::__construct('trainers');
    }
    
    // Inherits standard CRUD operations from Model base class
}

class TrainingList extends Model {
    public function __construct() {
        parent::__construct('training_list');
    }
    
    public function get($where = []) {
        return get_data('training_list', $where)[0];
    }
}
```

#### Controller Endpoints
The training_controller.php provides the following main endpoints:

- **trainers**: Complete CRUD operations for trainer management
- **training**: Training program management and assignment
- **employee4Training**: Employee search functionality for training assignment

### Data Denormalization Strategy

The training system employs extensive data denormalization for:

#### Benefits
- **Performance**: Faster query execution for reporting and analytics
- **Historical Accuracy**: Preserved data even when source records change
- **Simplified Queries**: Reduced need for complex joins in data retrieval

#### Implementation
- **Employee Data**: Name, phone, email, staff number stored in training records
- **Trainer Data**: Name, phone, email stored in training assignments
- **Type/Option Data**: Names stored alongside IDs for quick reference

## Workflows

### Trainer Registration Workflow

1. **Access Control**: Verify user has trainer management permissions
2. **Data Validation**: Validate required fields (name, email)
3. **Duplicate Check**: Ensure email uniqueness in trainer database
4. **Data Sanitization**: Clean and escape input data for security
5. **Database Storage**: Insert trainer record with audit information
6. **Response Handling**: Return success/error status with appropriate message

```php
// Example trainer creation code
$data = [
    'full_name' => escapePostData($_POST['full_name']),
    'phone' => escapePostData($_POST['phone']),
    'email' => escapePostData($_POST['email']),
    'status' => 'Active',
    'added_by' => $_SESSION['user_id'],
    'added_date' => date('Y-m-d H:i:s')
];

$trainer = new Trainers();
$result = $trainer->create($data);
```

### Training Program Creation Workflow

1. **Prerequisite Selection**: Choose training type, option, and trainer
2. **Employee Selection**: Search and select target employees
3. **Schedule Definition**: Set training start and end dates
4. **Cost Assignment**: Define training cost and budget allocation
5. **Bulk Processing**: Create individual records for each assigned employee
6. **Data Denormalization**: Store related entity details in training records
7. **Status Initialization**: Set initial training status
8. **Confirmation**: Provide feedback on successful training creation

```php
// Example training assignment code
foreach ($selected_employees as $employee) {
    $training_data = [
        'type_id' => $type_id,
        'type_name' => $type_name,
        'option_id' => $option_id,
        'option_name' => $option_name,
        'trainer_id' => $trainer_id,
        'trainer_name' => $trainer_name,
        'trainer_phone' => $trainer_phone,
        'trainer_email' => $trainer_email,
        'emp_id' => $employee['id'],
        'full_name' => $employee['full_name'],
        'phone_number' => $employee['phone_number'],
        'email' => $employee['email'],
        'staff_no' => $employee['staff_no'],
        'cost' => $cost,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'description' => $description,
        'status' => 'Active',
        'added_by' => $_SESSION['user_id'],
        'added_date' => date('Y-m-d H:i:s')
    ];
    
    $training = new TrainingList();
    $training->create($training_data);
}
```

### Training Progress Management Workflow

1. **Status Monitoring**: Track training progress from assignment to completion
2. **Schedule Management**: Monitor training dates and deadlines
3. **Cost Tracking**: Track actual vs. budgeted training costs
4. **Completion Recording**: Update status when training is completed
5. **Reporting**: Generate training completion and effectiveness reports

## Integration Points

### Employee Management Integration
- **Employee Search**: Integration with employee database for training assignment
- **Employee Data**: Denormalized employee information in training records
- **Department Mapping**: Training assignments can be department-specific

### Organizational Structure Integration
- **Department-Based Training**: Training programs organized by departments
- **Role-Specific Training**: Training options mapped to specific designations
- **Hierarchical Reporting**: Training reports organized by organizational structure

### Financial Management Integration
- **Cost Tracking**: Training costs integrated with financial management
- **Budget Management**: Training budget allocation and monitoring
- **Expense Reporting**: Training expenses included in financial reports

### Performance Management Integration
- **Skill Development**: Training programs linked to performance improvement
- **Goal Achievement**: Training assignments support goal tracking
- **Competency Building**: Training programs address competency gaps

## Security and Permissions

### Required Permissions
- **manage_trainers**: Create, edit, and delete trainer records
- **create_training**: Assign employees to training programs
- **edit_training**: Modify training assignments and details
- **delete_training**: Remove training records
- **view_training**: Access training data and reports

### Data Security Measures
- **Input Sanitization**: All input data sanitized using `escapePostData()`
- **SQL Injection Prevention**: Parameterized queries where implemented
- **Session Management**: Secure session handling for user authentication
- **Audit Trail**: Complete tracking of all training-related actions

### Error Handling
```php
// Consistent error response format
$result = [
    'status' => 201,
    'error' => false,
    'msg' => 'Training created successfully',
    'data' => $training_data
];

// Error response example
$result = [
    'status' => 400,
    'error' => true,
    'msg' => 'Failed to create training: ' . $error_message,
    'data' => null
];
```

## Reporting and Analytics

### Training Reports
- **Employee Training History**: Complete training record for each employee
- **Trainer Utilization**: Training load and availability by trainer
- **Training Costs**: Cost analysis and budget utilization reports
- **Completion Rates**: Training completion statistics and trends

### Training Analytics
- **Popular Training Programs**: Most requested training types and options
- **Department Training Needs**: Training requirements by organizational unit
- **Training Effectiveness**: Success rates and feedback analysis
- **Resource Utilization**: Trainer and facility utilization metrics

## Best Practices

### Training Program Management
- **Needs Assessment**: Regular evaluation of training requirements
- **Trainer Qualification**: Ensure trainer expertise matches training content
- **Schedule Optimization**: Efficient scheduling to minimize business disruption
- **Cost Management**: Regular monitoring of training budgets and expenses

### Employee Development
- **Individual Development Plans**: Align training with career development goals
- **Skill Gap Analysis**: Identify and address competency gaps through training
- **Continuous Learning**: Promote ongoing professional development
- **Feedback Collection**: Gather participant feedback for program improvement

## Common SQL Queries

### Trainer Management Queries
```sql
-- Get all active trainers
SELECT * FROM trainers WHERE status = 'Active' ORDER BY full_name;

-- Find trainer by email
SELECT * FROM trainers WHERE email = ? AND status = 'Active';

-- Get trainer training load
SELECT t.full_name, COUNT(tl.id) as training_count
FROM trainers t
LEFT JOIN training_list tl ON t.id = tl.trainer_id
WHERE t.status = 'Active'
GROUP BY t.id, t.full_name;
```

### Training Assignment Queries
```sql
-- Get employee training history
SELECT tl.*, tt.name as type_name, to.name as option_name
FROM training_list tl
JOIN training_types tt ON tl.type_id = tt.id
JOIN training_options to ON tl.option_id = to.id
WHERE tl.emp_id = ? ORDER BY tl.start_date DESC;

-- Get upcoming trainings
SELECT * FROM training_list 
WHERE start_date >= CURDATE() AND status = 'Active'
ORDER BY start_date;

-- Training cost summary by department
SELECT department, SUM(cost) as total_cost, COUNT(*) as training_count
FROM training_list tl
JOIN employees e ON tl.emp_id = e.id
WHERE tl.status = 'Active'
GROUP BY department;
```

### Training Analytics Queries
```sql
-- Most popular training types
SELECT type_name, COUNT(*) as assignment_count
FROM training_list
WHERE status = 'Active'
GROUP BY type_id, type_name
ORDER BY assignment_count DESC;

-- Training completion rates
SELECT 
    type_name,
    COUNT(*) as total_assignments,
    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
    ROUND(SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as completion_rate
FROM training_list
GROUP BY type_id, type_name;
```

## Troubleshooting

### Common Issues
- **Trainer Not Available**: Check trainer status and existing assignments
- **Training Assignment Failed**: Verify employee exists and is active
- **Bulk Assignment Errors**: Check data consistency and validation rules
- **Cost Calculation Issues**: Verify numeric data types and calculations

### Performance Optimization
- **Index Management**: Ensure proper indexing on frequently queried fields
- **Query Optimization**: Use appropriate WHERE clauses and LIMIT statements
- **Data Archiving**: Archive completed training records for better performance
- **Caching**: Implement caching for frequently accessed training data

## Future Enhancements

### Potential Improvements
- **Training Calendar Integration**: Visual calendar for training schedules
- **Certification Tracking**: Track training certifications and renewals
- **E-Learning Integration**: Support for online training platforms
- **Mobile Application**: Mobile access for training schedules and materials
- **Advanced Analytics**: Predictive analytics for training needs
- **Feedback System**: Integrated training evaluation and feedback collection
- **Resource Management**: Training room and equipment scheduling
- **Automated Notifications**: Email/SMS reminders for upcoming trainings