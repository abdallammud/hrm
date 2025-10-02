# Organization Hierarchy Management

## Overview

The HRM application implements a comprehensive organizational hierarchy system that supports multi-level company structures, geographical organization, and various organizational entities. This system enables efficient management of company structure, employee assignments, and administrative organization.

## User Stories

### Company Management
**As a system administrator, I want to manage company information, so that I can maintain accurate organizational details and branding.**

The system allows administrators to:
- Create and edit company profiles with contact information
- Manage company addresses and communication details
- Configure company-wide settings and branding
- Maintain multiple company entities if needed

### Branch/Department Management
**As an HR manager, I want to manage company branches and departments, so that I can organize employees into logical business units.**

The system enables:
- Creation of branches or departments based on business needs
- Assignment of employees to specific branches
- Management of branch-specific contact information
- Hierarchical organization of business units

### Geographical Organization
**As an administrator, I want to manage geographical locations, so that I can organize employees by their physical work locations and apply location-specific policies.**

The system supports:
- Country-level organization for international operations
- State/province management with tax configuration
- Duty location management for physical offices
- Geographical assignment of employees

## Organizational Hierarchy Structure

### Primary Hierarchy Levels

#### 1. Company Level
- **Entity**: Company
- **Purpose**: Top-level organizational entity
- **Key Attributes**: Name, address, contact information, branding
- **Relationships**: Parent to all other organizational entities

#### 2. Branch/Department Level
- **Entity**: Branches
- **Purpose**: Business unit organization
- **Key Attributes**: Name, contact information, status
- **Relationships**: Contains employees, reports to company

#### 3. Geographical Levels
```
Country → State/Province → Duty Location
```
- **Countries**: International presence management
- **States**: Regional organization with tax configuration
- **Locations**: Physical office/duty locations

### Supporting Organizational Entities

#### Designations
- **Purpose**: Job titles and positions
- **Usage**: Employee role definition
- **Management**: Create, edit, activate/deactivate

#### Projects
- **Purpose**: Project-based employee assignments
- **Usage**: Multi-project employee allocation
- **Management**: Project creation and employee assignment

#### Contract Types
- **Purpose**: Employment contract categorization
- **Usage**: Employee contract classification
- **Management**: Contract type definition and assignment

#### Budget Codes
- **Purpose**: Financial tracking and budgeting
- **Usage**: Expense categorization and budget allocation
- **Management**: Budget code creation and assignment

## Company Management Features

### Company Profile Management

#### Creating a Company
1. **Access**: Navigate to Organization → Organization
2. **Action**: Click "Add Organization" button
3. **Required Information**:
   - Organization Name (required)
   - Phone Numbers (multiple, pipe-separated)
   - Email Addresses (multiple, pipe-separated)
   - Physical Address (required)

#### Company Information Fields
- **Name**: Primary company identifier
- **Address**: Physical headquarters address
- **Contact Phone**: Multiple phone numbers (format: 000000000 | 0000000000)
- **Contact Email**: Multiple email addresses
- **Website**: Company website URL (optional)
- **Logo**: Company logo file (optional)

#### Company Management Workflow
```
User Access → Organization Menu → Add Organization → 
Form Completion → Validation → Database Storage → 
Confirmation → Company Profile Active
```

### Company Data Structure
```php
// Company creation data
$data = array(
    'name' => $_POST['name'], 
    'address' => $_POST['address'], 
    'contact_phone' => $_POST['phones'], 
    'contact_email' => $_POST['emails']
);
```

## Branch/Department Management Features

### Branch Structure Management

#### Creating Branches
1. **Access**: Navigate to Organization → Branches/Departments
2. **Action**: Click "Add Branch/Department" button
3. **Required Information**:
   - Branch/Department Name (required)
   - Contact Information (optional)

#### Branch Management Capabilities
- **Flexible Naming**: System supports both "Branch" and "Department" terminology
- **Contact Management**: Optional contact information per branch
- **Status Management**: Active/inactive branch status
- **Employee Assignment**: Direct employee-to-branch relationships

#### Branch Assignment Impact
- **Payroll Processing**: Branch information included in payroll reports
- **Reporting**: Branch-based employee reporting and analytics
- **Access Control**: Branch-based permission and access management
- **Communication**: Branch-specific notifications and communications

### Branch Data Structure
```php
// Branch creation data
$data = array(
    'name' => $_POST['name'], 
    'address' => isset($_POST['address']) ? $_POST['address'] : '', 
    'contact_phone' => isset($_POST['contact_phone']) ? $_POST['contact_phone'] : '', 
    'contact_email' => isset($_POST['contact_email']) ? $_POST['contact_email'] : ''
);
```

## Geographical Organization Features

### Country Management
- **Purpose**: International operations support
- **Features**: Country code management, default country selection
- **Integration**: State/province parent entity

### State/Province Management

#### State Creation Process
1. **Access**: Navigate to Organization → Locations (States section)
2. **Action**: Click "Add State" button
3. **Required Information**:
   - State Name (required)
   - Country Selection (required)
   - Stamp Duty Amount
   - Tax Grid Configuration

#### Tax Grid Configuration
States support complex tax bracket configuration:
- **Tax Brackets**: Multiple income ranges with different tax rates
- **Dynamic Configuration**: Add/remove tax brackets as needed
- **Payroll Integration**: Tax grids used for automatic payroll calculations

#### Tax Grid Structure
```javascript
// Tax grid example
[
    {min_amount: 0, max_amount: 1000, rate: 5},
    {min_amount: 1001, max_amount: 5000, rate: 10},
    {min_amount: 5001, max_amount: 10000, rate: 15}
]
```

### Duty Location Management

#### Location Creation Process
1. **Access**: Navigate to Organization → Locations
2. **Action**: Click "Add Location" button
3. **Required Information**:
   - Location Name (required)
   - State Selection (required)
   - City Name
   - Status (Active/Inactive)

#### Location Features
- **State Relationship**: Each location belongs to a specific state
- **City Organization**: City-level geographical organization
- **Employee Assignment**: Direct employee-to-location assignment
- **Status Management**: Active/inactive location control

## Employee Assignment Integration

### Multi-Level Assignment
Employees can be assigned to multiple organizational levels simultaneously:

```php
// Employee organizational assignments
$employee_data = [
    'branch_id' => $branch_id,        // Branch assignment
    'location_id' => $location_id,    // Physical location
    'state_id' => $state_id,          // State for tax purposes
    'designation' => $designation,     // Job title
    'project_id' => $project_ids,     // Comma-separated project IDs
    'contract_type' => $contract_type  // Employment contract type
];
```

### Assignment Impact on Operations

#### Payroll Processing
- **State Tax**: Automatic tax calculation based on state tax grid
- **Location Allowances**: Location-specific allowances and benefits
- **Branch Reporting**: Branch-based payroll reporting and analytics

#### Reporting and Analytics
- **Hierarchical Reporting**: Reports organized by organizational hierarchy
- **Cross-Functional Analysis**: Multi-dimensional employee analysis
- **Geographical Insights**: Location-based workforce analytics

#### Access Control
- **Branch-Based Permissions**: Access control based on branch assignment
- **Location-Based Access**: Physical location access management
- **Hierarchical Permissions**: Cascading permission inheritance

## Administrative Features

### Bulk Management Operations
- **Mass Assignment**: Bulk employee assignment to organizational entities
- **Status Updates**: Bulk activation/deactivation of organizational entities
- **Data Import**: CSV-based organizational data import
- **Reporting**: Comprehensive organizational structure reports

### Data Validation and Integrity
- **Duplicate Prevention**: Automatic duplicate name checking
- **Referential Integrity**: Proper foreign key relationships
- **Data Consistency**: Cross-entity data validation
- **Audit Trail**: Change tracking and history maintenance

### Search and Filtering
- **Entity Search**: Quick search across all organizational entities
- **Hierarchical Filtering**: Filter by organizational hierarchy levels
- **Status Filtering**: Active/inactive entity filtering
- **Assignment Filtering**: Filter by employee assignments

## Integration with Other Systems

### HR Management Integration
- **Employee Profiles**: Organizational assignments in employee records
- **Performance Management**: Organization-based performance tracking
- **Training Management**: Organization-specific training programs

### Financial Integration
- **Budget Tracking**: Budget code integration with financial systems
- **Cost Center Management**: Branch-based cost center organization
- **Expense Allocation**: Organizational expense distribution

### Reporting Integration
- **Organizational Reports**: Comprehensive organizational structure reports
- **Employee Distribution**: Workforce distribution across organizational entities
- **Performance Analytics**: Organization-based performance metrics

## Best Practices

### Organizational Design
1. **Clear Hierarchy**: Maintain clear organizational hierarchy levels
2. **Consistent Naming**: Use consistent naming conventions across entities
3. **Regular Review**: Periodically review and update organizational structure
4. **Documentation**: Maintain documentation of organizational changes

### Data Management
1. **Data Quality**: Ensure accurate and complete organizational data
2. **Regular Cleanup**: Remove inactive or obsolete organizational entities
3. **Backup Strategy**: Maintain regular backups of organizational data
4. **Access Control**: Implement proper access controls for organizational management

### Change Management
1. **Impact Assessment**: Assess impact of organizational changes on employees
2. **Communication**: Communicate organizational changes to affected stakeholders
3. **Training**: Provide training on new organizational structures
4. **Monitoring**: Monitor the impact of organizational changes on operations