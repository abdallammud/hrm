# Security Best Practices and Data Protection

## Overview

This document outlines the security measures implemented in the HRM application and provides recommendations for maintaining and improving the system's security posture. It covers data validation, protection mechanisms, and security best practices for ongoing system maintenance.

## User Story

**As a security administrator**, I want to ensure the HRM system follows security best practices and protects sensitive employee data, so that the organization maintains compliance with data protection regulations and prevents security breaches.

## Current Security Implementation

### Password Security

#### Implemented Measures

1. **Secure Password Hashing**
   ```php
   // Password hashing using PHP's secure functions
   $password = password_hash($post['phone_number'], PASSWORD_DEFAULT);
   
   // Password verification
   if (!password_verify($password, $passDB)) {
       // Handle incorrect password
   }
   ```

2. **Password Storage**
   - Uses `PASSWORD_DEFAULT` algorithm (currently bcrypt)
   - Automatic salt generation for each password
   - No plain text password storage

#### Recommendations

1. **Password Policy Implementation**
   ```php
   function validatePassword($password) {
       $errors = [];
       
       if (strlen($password) < 8) {
           $errors[] = "Password must be at least 8 characters long";
       }
       
       if (!preg_match('/[A-Z]/', $password)) {
           $errors[] = "Password must contain at least one uppercase letter";
       }
       
       if (!preg_match('/[a-z]/', $password)) {
           $errors[] = "Password must contain at least one lowercase letter";
       }
       
       if (!preg_match('/[0-9]/', $password)) {
           $errors[] = "Password must contain at least one number";
       }
       
       if (!preg_match('/[^A-Za-z0-9]/', $password)) {
           $errors[] = "Password must contain at least one special character";
       }
       
       return $errors;
   }
   ```

2. **Password Expiration**
   ```php
   function checkPasswordExpiry($user_id) {
       $user = $GLOBALS['userClass']->get($user_id);
       $password_changed = strtotime($user['password_changed_at']);
       $expiry_days = 90; // 90 days policy
       
       if (time() - $password_changed > ($expiry_days * 24 * 60 * 60)) {
           return true; // Password expired
       }
       
       return false;
   }
   ```

### Session Security

#### Current Implementation

1. **Session Management**
   ```php
   session_start(); // Basic session initialization
   
   // Session data storage
   $_SESSION['user_id'] = $user_id;
   $_SESSION['username'] = $username;
   $_SESSION['role'] = $role;
   ```

#### Security Improvements

1. **Session Configuration**
   ```php
   // Secure session configuration
   ini_set('session.cookie_httponly', 1);
   ini_set('session.cookie_secure', 1);
   ini_set('session.use_strict_mode', 1);
   ini_set('session.cookie_samesite', 'Strict');
   
   // Session regeneration after login
   function secure_session_start() {
       session_start();
       session_regenerate_id(true);
   }
   ```

2. **Session Timeout**
   ```php
   function checkSessionTimeout() {
       $timeout = 30 * 60; // 30 minutes
       
       if (isset($_SESSION['last_activity']) && 
           (time() - $_SESSION['last_activity'] > $timeout)) {
           session_unset();
           session_destroy();
           return false;
       }
       
       $_SESSION['last_activity'] = time();
       return true;
   }
   ```

## Data Validation and Sanitization

### Current Input Handling

The system uses basic sanitization functions:

```php
// Example from utilities or helpers
function escapeStr($string) {
    return mysqli_real_escape_string($GLOBALS['conn'], $string);
}

function escapePostData($data) {
    $escaped = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $escaped[$key] = escapePostData($value);
        } else {
            $escaped[$key] = escapeStr($value);
        }
    }
    return $escaped;
}
```

### Enhanced Input Validation

#### Comprehensive Validation Framework

```php
class InputValidator {
    public static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Invalid email format");
        }
        return $email;
    }
    
    public static function validatePhone($phone) {
        $phone = preg_replace('/[^0-9+\-\s\(\)]/', '', $phone);
        if (strlen($phone) < 10) {
            throw new ValidationException("Invalid phone number");
        }
        return $phone;
    }
    
    public static function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            throw new ValidationException("Invalid date format");
        }
        return $date;
    }
    
    public static function sanitizeString($string, $maxLength = 255) {
        $string = trim($string);
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        return substr($string, 0, $maxLength);
    }
    
    public static function validateNumeric($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            throw new ValidationException("Value must be numeric");
        }
        
        if ($min !== null && $value < $min) {
            throw new ValidationException("Value must be at least $min");
        }
        
        if ($max !== null && $value > $max) {
            throw new ValidationException("Value must not exceed $max");
        }
        
        return $value;
    }
}
```

#### File Upload Security

```php
class FileUploadValidator {
    private static $allowedTypes = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'document' => ['pdf', 'doc', 'docx', 'txt'],
        'spreadsheet' => ['xls', 'xlsx', 'csv']
    ];
    
    public static function validateUpload($file, $type = 'image', $maxSize = 5242880) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException("File upload error");
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            throw new ValidationException("File size exceeds limit");
        }
        
        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::$allowedTypes[$type])) {
            throw new ValidationException("Invalid file type");
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf'
        ];
        
        if (!isset($allowedMimes[$extension]) || 
            $mimeType !== $allowedMimes[$extension]) {
            throw new ValidationException("File type mismatch");
        }
        
        return true;
    }
}
```

## SQL Injection Prevention

### Current Implementation Issues

The system has some SQL injection vulnerabilities:

```php
// Vulnerable query example
$getUser = "SELECT * FROM `users` WHERE (`username` = '$username' OR `email` LIKE '$username')";
$userSet = $GLOBALS['conn']->query($getUser);
```

### Secure Database Practices

#### Prepared Statements Implementation

```php
class SecureDatabase {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getUserByCredentials($username) {
        $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND status != 'deleted'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function updateUserLoginInfo($userId, $isLogged, $timestamp) {
        $column = $isLogged ? 'this_time' : 'last_logged';
        $sql = "UPDATE users SET is_logged = ?, $column = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $isLogged, $timestamp, $userId);
        return $stmt->execute();
    }
    
    public function createEmployee($data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = str_repeat('?,', count($data) - 1) . '?';
        $sql = "INSERT INTO employees ($fields) VALUES ($placeholders)";
        
        $stmt = $this->conn->prepare($sql);
        $types = str_repeat('s', count($data));
        $stmt->bind_param($types, ...array_values($data));
        
        return $stmt->execute() ? $this->conn->insert_id : false;
    }
}
```

#### Query Builder Pattern

```php
class QueryBuilder {
    private $conn;
    private $table;
    private $conditions = [];
    private $parameters = [];
    private $types = '';
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function table($table) {
        $this->table = $table;
        return $this;
    }
    
    public function where($field, $operator, $value) {
        $this->conditions[] = "$field $operator ?";
        $this->parameters[] = $value;
        $this->types .= 's';
        return $this;
    }
    
    public function get() {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($this->parameters)) {
            $stmt->bind_param($this->types, ...$this->parameters);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
```

## Cross-Site Scripting (XSS) Prevention

### Output Encoding

```php
class OutputEncoder {
    public static function html($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public static function attribute($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public static function javascript($string) {
        return json_encode($string, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }
    
    public static function url($string) {
        return urlencode($string);
    }
}

// Usage in templates
echo OutputEncoder::html($user['full_name']);
echo '<input value="' . OutputEncoder::attribute($user['email']) . '">';
```

### Content Security Policy

```php
// Add to header files
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
```

## Cross-Site Request Forgery (CSRF) Protection

### CSRF Token Implementation

```php
class CSRFProtection {
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateToken($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            throw new SecurityException("Invalid CSRF token");
        }
        return true;
    }
    
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}

// Usage in forms
echo CSRFProtection::getTokenField();

// Validation in controllers
CSRFProtection::validateToken($_POST['csrf_token']);
```

## Data Encryption

### Sensitive Data Protection

```php
class DataEncryption {
    private static $key;
    
    public static function setKey($key) {
        self::$key = $key;
    }
    
    public static function encrypt($data) {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', self::$key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    public static function decrypt($encryptedData) {
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', self::$key, 0, $iv);
    }
}

// Usage for sensitive fields
$encryptedSSN = DataEncryption::encrypt($employee['national_id']);
$decryptedSSN = DataEncryption::decrypt($encryptedSSN);
```

## Audit Logging

### Security Event Logging

```php
class SecurityLogger {
    public static function logLogin($userId, $success, $ipAddress) {
        $event = [
            'event_type' => 'login_attempt',
            'user_id' => $userId,
            'success' => $success,
            'ip_address' => $ipAddress,
            'timestamp' => date('Y-m-d H:i:s'),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        
        self::writeLog($event);
    }
    
    public static function logPermissionDenied($userId, $permission, $resource) {
        $event = [
            'event_type' => 'permission_denied',
            'user_id' => $userId,
            'permission' => $permission,
            'resource' => $resource,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        self::writeLog($event);
    }
    
    public static function logDataAccess($userId, $table, $recordId, $action) {
        $event = [
            'event_type' => 'data_access',
            'user_id' => $userId,
            'table' => $table,
            'record_id' => $recordId,
            'action' => $action,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        self::writeLog($event);
    }
    
    private static function writeLog($event) {
        $logFile = '../logs/security_' . date('Y-m-d') . '.log';
        $logEntry = json_encode($event) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
```

## Rate Limiting

### Login Attempt Limiting

```php
class RateLimiter {
    public static function checkLoginAttempts($identifier) {
        $key = "login_attempts_" . md5($identifier);
        $attempts = $_SESSION[$key] ?? 0;
        $lastAttempt = $_SESSION[$key . '_time'] ?? 0;
        
        // Reset counter after 15 minutes
        if (time() - $lastAttempt > 900) {
            $attempts = 0;
        }
        
        if ($attempts >= 5) {
            throw new SecurityException("Too many login attempts. Please try again later.");
        }
        
        return true;
    }
    
    public static function recordFailedLogin($identifier) {
        $key = "login_attempts_" . md5($identifier);
        $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
        $_SESSION[$key . '_time'] = time();
    }
    
    public static function clearLoginAttempts($identifier) {
        $key = "login_attempts_" . md5($identifier);
        unset($_SESSION[$key]);
        unset($_SESSION[$key . '_time']);
    }
}
```

## Security Headers

### HTTP Security Headers

```php
class SecurityHeaders {
    public static function setSecurityHeaders() {
        // Prevent clickjacking
        header('X-Frame-Options: DENY');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Strict transport security (HTTPS only)
        if (isset($_SERVER['HTTPS'])) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        
        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content security policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
    }
}

// Call in header files
SecurityHeaders::setSecurityHeaders();
```

## Data Privacy and Compliance

### GDPR Compliance Features

```php
class DataPrivacy {
    public static function anonymizeEmployee($employeeId) {
        $anonymizedData = [
            'full_name' => 'Anonymized User',
            'email' => 'anonymized@example.com',
            'phone_number' => '000-000-0000',
            'address' => 'Anonymized Address',
            'national_id' => 'ANONYMIZED',
            'status' => 'anonymized'
        ];
        
        return $GLOBALS['employeeClass']->update($employeeId, $anonymizedData);
    }
    
    public static function exportUserData($userId) {
        $userData = [];
        
        // Get user information
        $user = $GLOBALS['userClass']->get($userId);
        $userData['user'] = $user;
        
        // Get employee information
        if ($user['emp_id']) {
            $employee = $GLOBALS['employeeClass']->get($user['emp_id']);
            $userData['employee'] = $employee;
        }
        
        // Get related data (payroll, attendance, etc.)
        // ... additional data collection
        
        return json_encode($userData, JSON_PRETTY_PRINT);
    }
    
    public static function deleteUserData($userId) {
        // Mark user as deleted instead of hard delete
        $GLOBALS['userClass']->update($userId, ['status' => 'deleted']);
        
        // Anonymize related employee data
        $user = $GLOBALS['userClass']->get($userId);
        if ($user['emp_id']) {
            self::anonymizeEmployee($user['emp_id']);
        }
    }
}
```

## Security Monitoring

### Intrusion Detection

```php
class SecurityMonitor {
    public static function detectSuspiciousActivity($userId) {
        $suspicious = false;
        
        // Check for multiple failed logins
        $failedLogins = self::getFailedLoginCount($userId, 3600); // Last hour
        if ($failedLogins > 10) {
            $suspicious = true;
        }
        
        // Check for unusual access patterns
        $accessPattern = self::getAccessPattern($userId);
        if (self::isUnusualPattern($accessPattern)) {
            $suspicious = true;
        }
        
        if ($suspicious) {
            self::alertSecurity($userId, 'Suspicious activity detected');
        }
        
        return $suspicious;
    }
    
    private static function alertSecurity($userId, $message) {
        // Send alert to security team
        SecurityLogger::logSecurityAlert($userId, $message);
        
        // Optional: Temporarily lock account
        $GLOBALS['userClass']->update($userId, ['status' => 'locked']);
    }
}
```

## Backup and Recovery

### Data Backup Security

```php
class SecureBackup {
    public static function createEncryptedBackup() {
        $backupData = self::exportAllData();
        $encryptedBackup = DataEncryption::encrypt($backupData);
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.enc';
        $backupPath = '../backups/' . $filename;
        
        file_put_contents($backupPath, $encryptedBackup);
        
        // Log backup creation
        SecurityLogger::logBackupCreated($filename);
        
        return $filename;
    }
    
    public static function restoreFromBackup($filename, $decryptionKey) {
        $backupPath = '../backups/' . $filename;
        
        if (!file_exists($backupPath)) {
            throw new Exception("Backup file not found");
        }
        
        $encryptedData = file_get_contents($backupPath);
        $backupData = DataEncryption::decrypt($encryptedData);
        
        // Restore data with validation
        return self::importData($backupData);
    }
}
```

## Security Checklist

### Implementation Checklist

- [ ] **Authentication Security**
  - [x] Password hashing implemented
  - [ ] Password policy enforcement
  - [ ] Account lockout after failed attempts
  - [ ] Session timeout implementation
  - [ ] Multi-factor authentication

- [ ] **Authorization Security**
  - [x] Role-based access control
  - [x] Permission checking in controllers
  - [ ] Resource-level permissions
  - [ ] Permission auditing

- [ ] **Data Protection**
  - [ ] Input validation framework
  - [ ] Output encoding
  - [ ] SQL injection prevention
  - [ ] XSS protection
  - [ ] CSRF protection

- [ ] **Infrastructure Security**
  - [ ] Security headers implementation
  - [ ] HTTPS enforcement
  - [ ] File upload security
  - [ ] Error handling security

- [ ] **Monitoring and Logging**
  - [ ] Security event logging
  - [ ] Intrusion detection
  - [ ] Audit trails
  - [ ] Backup encryption

### Regular Security Tasks

1. **Weekly**
   - Review security logs
   - Check for failed login attempts
   - Monitor system performance

2. **Monthly**
   - Update dependencies
   - Review user permissions
   - Test backup restoration

3. **Quarterly**
   - Security assessment
   - Penetration testing
   - Policy review and updates

4. **Annually**
   - Comprehensive security audit
   - Compliance review
   - Security training updates