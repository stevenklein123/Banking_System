<?php
session_start();

require_once 'database.php';

// Login function
function login($username, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, username, password, role, full_name, employee_id, status FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($user['status'] == 'inactive') {
            return ['success' => false, 'message' => 'Account is inactive'];
        }
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['employee_id'] = $user['employee_id'];
            return ['success' => true, 'message' => 'Login successful'];
        } else {
            return ['success' => false, 'message' => 'Invalid password'];
        }
    } else {
        return ['success' => false, 'message' => 'User not found'];
    }
}

// Logout function
function logout() {
    $_SESSION = [];
    session_destroy();
    return true;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function checkRole($requiredRole) {
    if (!isLoggedIn()) {
        return false;
    }
    if (is_array($requiredRole)) {
        return in_array($_SESSION['role'], $requiredRole);
    }
    return $_SESSION['role'] == $requiredRole;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Redirect if insufficient role
function requireRole($requiredRole) {
    requireLogin();
    if (!checkRole($requiredRole)) {
        header('Location: dashboard.php?error=Unauthorized access');
        exit;
    }
}

// Generate unique ID
function generateUniqueID($prefix, $table, $column) {
    global $conn;
    $count = 1;
    while (true) {
        $id = $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
        $result = $conn->query("SELECT id FROM $table WHERE $column = '$id'");
        if ($result->num_rows == 0) {
            return $id;
        }
        $count++;
    }
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Sanitize input
function sanitizeInput($input) {
    global $conn;
    return $conn->real_escape_string(trim($input));
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Get user by ID
function getUserByID($userID) {
    global $conn;
    $query = "SELECT * FROM users WHERE id = $userID";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Register
function registerUser($username, $password, $fullName, $email) {
    global $conn;

    // Check username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return ['success' => false, 'message' => 'Username already exists'];
    }

    $stmt->close();

    // Generate employee ID
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    $row = $result->fetch_assoc();

    $nextNumber = $row['total'] + 1;
    $employeeId = 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert
    $stmt = $conn->prepare("
        INSERT INTO users (employee_id, username, password, role, full_name, email, status)
        VALUES (?, ?, ?, 'teller', ?, ?, 'active')
    ");

    $stmt->bind_param("sssss", $employeeId, $username, $hashedPassword, $fullName, $email);

    if ($stmt->execute()) {
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => $stmt->error];
    }
}

?>
