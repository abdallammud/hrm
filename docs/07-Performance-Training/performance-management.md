# Performance Management System

## Overview

The Performance Management System is a comprehensive module within the HRM application that enables organizations to define performance indicators, conduct employee appraisals, and track organizational goals. The system provides a structured approach to performance evaluation through competency-based assessments and goal-oriented tracking mechanisms.

## User Stories

### Performance Indicators Management
**As an HR Manager**, I want to create and manage performance indicators for different departments and designations, so that I can establish standardized evaluation criteria across the organization.

**As a Department Head**, I want to view performance indicators specific to my department, so that I can understand the evaluation criteria for my team members.

### Employee Appraisals
**As an HR Administrator**, I want to conduct employee performance appraisals using predefined indicators, so that I can provide consistent and objective performance evaluations.

**As a Manager**, I want to rate employees based on behavioral, organizational, and technical competencies, so that I can provide comprehensive performance feedback.

### Goal Tracking
**As an Executive**, I want to set and track departmental goals with specific targets and timelines, so that I can monitor organizational progress and performance.

**As a Department Manager**, I want to update goal progress and status, so that I can keep stakeholders informed about achievement levels.

## Key Features

### 1. Performance Indicators System

#### Competency Categories
The system organizes performance evaluation into three main competency areas:

- **Behavioral Competencies**: Communication, teamwork, and interpersonal skills
- **Organizational Competencies**: Leadership, project management, and strategic thinking  
- **Technical Competencies**: Job-specific skills and technical expertise

#### Rating Scale
Each competency is rated on a scale of 0-5:
- **0**: Not Applicable/Not Observed
- **1**: Below Expectations
- **2**: Partially Meets Expectations
- **3**: Meets Expectations
- **4**: Exceeds Expectations
- **5**: Outstanding Performance

#### Department and Designation Mapping
Performance indicators are mapped to specific:
- **Departments**: Sales, HR, IT, Finance, etc.
- **Designations**: Manager, Senior Developer, Analyst, etc.

### 2. Employee Appraisal System

#### Appraisal Process
1. **Employee Selection**: Search and select employee for evaluation
2. **Indicator Application**: Apply relevant indicators based on employee's department and designation
3. **Rating Assignment**: Rate employee on each competency area
4. **Overall Assessment**: Provide overall appraisal rating and remarks
5. **Record Storage**: Save complete appraisal with historical tracking

#### Data Capture
The system captures comprehensive employee information during appraisal:
- Employee demographics (name, email, phone, staff number)
- Organizational details (department, designation)
- Performance ratings (indicator-based and overall)
- Appraisal period and remarks
- Audit trail (created by, updated by, timestamps)

### 3. Goal Tracking System

#### Goal Definition
Goals are defined with the following attributes:
- **Department**: Organizational unit responsible for the goal
- **Type**: Category of goal (Strategic, Operational, Development, etc.)
- **Subject**: Brief goal title or summary
- **Target**: Specific measurable target or outcome
- **Description**: Detailed goal description and context
- **Timeline**: Start and end dates for goal achievement
- **Progress**: Current progress status or percentage
- **Status**: Goal status (Active, In Progress, Completed, Cancelled)

#### Progress Monitoring
The system provides mechanisms to:
- Track progress against defined targets
- Update goal status as work progresses
- Monitor goal timelines and deadlines
- Generate progress reports and analytics

## Technical Implementation

### Database Schema

#### Performance Indicators Table
```sql
CREATE TABLE indicators (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department_id INT,
    designation_id INT,
    department VARCHAR(255),
    designation VARCHAR(255),
    attributes JSON,
    added_by INT,
    added_date TIMESTAMP,
    updated_by INT,
    updated_date TIMESTAMP
);
```

#### Employee Performance Table
```sql
CREATE TABLE employee_performance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    emp_id INT,
    full_name VARCHAR(255),
    phone_number VARCHAR(20),
    email VARCHAR(255),
    staff_no VARCHAR(50),
    department_id INT,
    designation_id INT,
    department VARCHAR(255),
    desgination VARCHAR(255), -- Note: typo in original schema
    indicator_rating DECIMAL(3,2),
    appraisal_rating DECIMAL(3,2),
    month VARCHAR(20),
    remarks TEXT,
    status VARCHAR(20),
    added_by INT,
    added_date TIMESTAMP,
    updated_by INT,
    updated_date TIMESTAMP
);
```

#### Goal Tracking Table
```sql
CREATE TABLE goal_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department_id INT,
    type_id INT,
    department VARCHAR(255),
    type VARCHAR(255),
    subject VARCHAR(255),
    target TEXT,
    description TEXT,
    start_date DATE,
    end_date DATE,
    progress VARCHAR(255),
    status VARCHAR(50),
    added_by INT,
    added_date TIMESTAMP,
    updated_by INT,
    updated_date TIMESTAMP
);
```

### PHP Implementation

#### Performance Class
```php
class Performance extends Model {
    public function __construct() {
        parent::__construct('performance', 'id');
    }
    
    // Inherits standard CRUD operations from Model base class
    // - create($data)
    // - read($where)
    // - update($data, $where)
    // - delete($where)
}
```

#### Controller Endpoints
The performance_controller.php provides the following main endpoints:

- **indicators**: Manage performance indicators (CRUD operations)
- **appraisal**: Conduct employee appraisals
- **goal_tracking**: Track and manage organizational goals
- **employee4Select**: Search employees for performance assignment

### JSON Attributes Structure

Performance indicators use JSON storage for competency ratings:

```json
{
  "Behavioural Competencies": [
    {"name": "Business Process", "rating": 4},
    {"name": "Oral Communication", "rating": 3},
    {"name": "Written Communication", "rating": 4},
    {"name": "Teamwork", "rating": 5}
  ],
  "Organizational Competencies": [
    {"name": "Leadership", "rating": 3},
    {"name": "Project Management", "rating": 4},
    {"name": "Strategic Thinking", "rating": 3}
  ],
  "Technical Competencies": [
    {"name": "Allocating Resources", "rating": 4},
    {"name": "Technical Expertise", "rating": 5},
    {"name": "Problem Solving", "rating": 4}
  ]
}
```

## Workflows

### Performance Indicator Creation Workflow

1. **Access Control**: Verify user has `manage_indicators` permission
2. **Data Collection**: Gather department, designation, and competency information
3. **Attribute Building**: Construct JSON structure with three competency categories
4. **Validation**: Ensure required fields are present and valid
5. **Database Storage**: Insert indicator record with metadata
6. **Response**: Return success/error status with appropriate message

### Employee Appraisal Workflow

1. **Employee Search**: Use employee4Select endpoint to find target employee
2. **Indicator Retrieval**: Fetch relevant indicators for employee's department/designation
3. **Appraisal Form**: Present rating interface with competency categories
4. **Rating Collection**: Capture ratings for each competency area
5. **Overall Assessment**: Collect overall rating and remarks
6. **Data Denormalization**: Store employee details in appraisal record
7. **Database Storage**: Insert complete appraisal with audit trail
8. **Confirmation**: Provide feedback on successful appraisal completion

### Goal Tracking Workflow

1. **Goal Definition**: Define department, type, subject, and target
2. **Timeline Setting**: Set start and end dates for goal achievement
3. **Initial Status**: Set goal status to "Active" or "In Progress"
4. **Progress Updates**: Regular updates to progress field
5. **Status Management**: Update status as goal progresses (Active → In Progress → Completed)
6. **Completion**: Mark goal as completed when target is achieved

## Integration Points

### Employee Management Integration
- Employee search functionality for appraisal assignment
- Employee data denormalization in performance records
- Department and designation mapping from organizational structure

### Organizational Structure Integration
- Department-based indicator definition
- Designation-specific competency requirements
- Hierarchical goal tracking by organizational units

### User Management Integration
- Permission-based access control for performance functions
- Audit trail tracking for all performance-related actions
- Session management for secure access to performance data

## Security and Permissions

### Required Permissions
- **manage_indicators**: Create, edit, and delete performance indicators
- **manage_goal_tracking**: Create, edit, and delete organizational goals
- **view_performance**: View performance data and reports
- **conduct_appraisals**: Perform employee performance appraisals

### Data Security
- Input sanitization using `escapePostData()` function
- SQL injection prevention through parameterized queries
- Session-based authentication for all performance operations
- Audit trail logging for accountability and compliance

## Reporting and Analytics

### Performance Reports
- Individual employee performance history
- Department-wise performance analytics
- Competency-based performance trends
- Appraisal rating distributions

### Goal Tracking Reports
- Goal achievement rates by department
- Timeline adherence and deadline tracking
- Progress monitoring and status reports
- Goal completion analytics

## Best Practices

### Performance Evaluation
- Regular appraisal cycles (quarterly, semi-annual, annual)
- Consistent application of rating criteria
- Documentation of performance improvement plans
- Integration with training and development programs

### Goal Management
- SMART goal setting (Specific, Measurable, Achievable, Relevant, Time-bound)
- Regular progress reviews and updates
- Alignment with organizational objectives
- Clear accountability and ownership

## Troubleshooting

### Common Issues
- **Indicator Not Loading**: Check department and designation mapping
- **Appraisal Save Failure**: Verify all required fields are completed
- **Goal Progress Not Updating**: Ensure proper permissions and data validation
- **Search Not Working**: Check employee status and database connectivity

### Error Handling
The system provides consistent error responses:
```php
$result = [
    'status' => 201,
    'error' => false,
    'msg' => 'Operation completed successfully',
    'data' => $responseData
];
```

## Future Enhancements

### Potential Improvements
- 360-degree feedback integration
- Performance dashboard with visual analytics
- Automated goal progress tracking
- Integration with learning management systems
- Mobile application for performance reviews
- Advanced reporting with data visualization 