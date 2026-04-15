<?php
session_start();

require_once 'database.php';

// Login function
function login($username, $password) {
    global $conn;
    
    $username = $conn->real_escape_string($username);
    $query = "SELECT id, username, password, role, full_name, employee_id, status FROM users WHERE username = '$username'";
    $result = $conn->query($query);
    
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

?>
