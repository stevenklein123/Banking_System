<?php
require_once __DIR__ . '/../config/database.php';

// ==================== ACCOUNT FUNCTIONS ====================

// Create new customer account
function createCustomerAccount($first_name, $middle_name, $last_name, $email, $phone, $address, $city, $state, $zip_code, $dob, $id_type, $id_number, $occupation) {
    global $conn;
    
    $first_name = sanitizeInput($first_name);
    $middle_name = sanitizeInput($middle_name);
    $last_name = sanitizeInput($last_name);
    $email = sanitizeInput($email);
    $phone = sanitizeInput($phone);
    $address = sanitizeInput($address);
    $city = sanitizeInput($city);
    $state = sanitizeInput($state);
    $zip_code = sanitizeInput($zip_code);
    $dob = sanitizeInput($dob);
    $id_type = sanitizeInput($id_type);
    $id_number = sanitizeInput($id_number);
    $occupation = sanitizeInput($occupation);
    
    // Check if email already exists
    $check = $conn->query("SELECT id FROM customers WHERE email = '$email'");
    if ($check->num_rows > 0) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    $customer_id = generateUniqueID('CUST', 'customers', 'customer_id');
    
    $query = "INSERT INTO customers (customer_id, first_name, middle_name, last_name, email, phone, address, city, state, zip_code, date_of_birth, id_type, id_number, occupation, status) 
              VALUES ('$customer_id', '$first_name', '$middle_name', '$last_name', '$email', '$phone', '$address', '$city', '$state', '$zip_code', '$dob', '$id_type', '$id_number', '$occupation', 'active')";
    
    if ($conn->query($query)) {
        $customer_id_db = $conn->insert_id;
        
        // Create savings account for new customer
        $account_number = generateUniqueID('ACC', 'accounts', 'account_number');
        $acc_query = "INSERT INTO accounts (account_number, customer_id, account_type, balance, status) 
                      VALUES ('$account_number', $customer_id_db, 'savings', 0, 'active')";
        
        if ($conn->query($acc_query)) {
            return ['success' => true, 'message' => 'Customer account created successfully', 'customer_id' => $customer_id, 'account_number' => $account_number];
        } else {
            return ['success' => false, 'message' => 'Error creating account: ' . $conn->error];
        }
    } else {
        return ['success' => false, 'message' => 'Error creating customer: ' . $conn->error];
    }
}

// Get customer by customer ID
function getCustomerByID($customer_id) {
    global $conn;
    $customer_id = sanitizeInput($customer_id);
    $query = "SELECT * FROM customers WHERE customer_id = '$customer_id'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Get all customers
function getAllCustomers() {
    global $conn;
    $query = "SELECT * FROM customers WHERE status = 'active' ORDER BY created_at DESC";
    return $conn->query($query);
}

// Get account by account number
function getAccountByNumber($account_number) {
    global $conn;
    $account_number = sanitizeInput($account_number);
    $query = "SELECT * FROM accounts WHERE account_number = '$account_number' AND status = 'active'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Get account by ID
function getAccountByID($account_id) {
    global $conn;
    $query = "SELECT * FROM accounts WHERE id = $account_id";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Get customer accounts
function getCustomerAccounts($customer_id) {
    global $conn;
    $query = "SELECT * FROM accounts WHERE customer_id = $customer_id AND status = 'active'";
    return $conn->query($query);
}

// ==================== DEPOSIT FUNCTIONS ====================

// Process deposit
function processDeposit($account_id, $amount, $payment_method, $teller_id, $description = '') {
    global $conn;
    
    $amount = floatval($amount);
    $payment_method = sanitizeInput($payment_method);
    $description = sanitizeInput($description);
    
    if ($amount <= 0) {
        return ['success' => false, 'message' => 'Invalid amount'];
    }
    
    $account = getAccountByID($account_id);
    if (!$account) {
        return ['success' => false, 'message' => 'Account not found'];
    }
    
    $new_balance = $account['balance'] + $amount;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        $reference_number = 'DEP' . time();
        $transaction_query = "INSERT INTO transactions (account_id, transaction_type, amount, balance_after, description, teller_id, payment_method, reference_number) 
                             VALUES ($account_id, 'deposit', $amount, $new_balance, '$description', $teller_id, '$payment_method', '$reference_number')";
        
        if (!$conn->query($transaction_query)) {
            throw new Exception("Error recording transaction: " . $conn->error);
        }
        
        $update_query = "UPDATE accounts SET balance = $new_balance WHERE id = $account_id";
        if (!$conn->query($update_query)) {
            throw new Exception("Error updating balance: " . $conn->error);
        }
        
        $conn->commit();
        return ['success' => true, 'message' => 'Deposit processed successfully', 'reference_number' => $reference_number, 'new_balance' => $new_balance];
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// ==================== WITHDRAWAL FUNCTIONS ====================

// Process withdrawal
function processWithdrawal($account_id, $amount, $payment_method, $teller_id, $description = '') {
    global $conn;
    
    $amount = floatval($amount);
    $payment_method = sanitizeInput($payment_method);
    $description = sanitizeInput($description);
    
    if ($amount <= 0) {
        return ['success' => false, 'message' => 'Invalid amount'];
    }
    
    $account = getAccountByID($account_id);
    if (!$account) {
        return ['success' => false, 'message' => 'Account not found'];
    }
    
    if ($account['balance'] < $amount) {
        return ['success' => false, 'message' => 'Insufficient balance'];
    }
    
    $new_balance = $account['balance'] - $amount;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        $reference_number = 'WIT' . time();
        $transaction_query = "INSERT INTO transactions (account_id, transaction_type, amount, balance_after, description, teller_id, payment_method, reference_number) 
                             VALUES ($account_id, 'withdrawal', $amount, $new_balance, '$description', $teller_id, '$payment_method', '$reference_number')";
        
        if (!$conn->query($transaction_query)) {
            throw new Exception("Error recording transaction: " . $conn->error);
        }
        
        $update_query = "UPDATE accounts SET balance = $new_balance WHERE id = $account_id";
        if (!$conn->query($update_query)) {
            throw new Exception("Error updating balance: " . $conn->error);
        }
        
        $conn->commit();
        return ['success' => true, 'message' => 'Withdrawal processed successfully', 'reference_number' => $reference_number, 'new_balance' => $new_balance];
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// ==================== LOAN FUNCTIONS ====================

// Create loan application
function createLoanApplication($customer_id, $principal_amount, $interest_rate, $loan_term_months, $collateral_description, $collateral_value, $purpose) {
    global $conn;
    
    $principal_amount = floatval($principal_amount);
    $interest_rate = floatval($interest_rate);
    $loan_term_months = intval($loan_term_months);
    $collateral_description = sanitizeInput($collateral_description);
    $collateral_value = floatval($collateral_value);
    $purpose = sanitizeInput($purpose);
    
    if ($principal_amount <= 0) {
        return ['success' => false, 'message' => 'Invalid loan amount'];
    }
    
    $loan_number = generateUniqueID('LOAN', 'loans', 'loan_number');
    
    $query = "INSERT INTO loans (loan_number, customer_id, principal_amount, interest_rate, loan_term_months, collateral_description, collateral_value, purpose, status) 
              VALUES ('$loan_number', $customer_id, $principal_amount, $interest_rate, $loan_term_months, '$collateral_description', $collateral_value, '$purpose', 'pending')";
    
    if ($conn->query($query)) {
        return ['success' => true, 'message' => 'Loan application submitted successfully', 'loan_number' => $loan_number];
    } else {
        return ['success' => false, 'message' => 'Error creating loan: ' . $conn->error];
    }
}

// Approve loan
function approveLoan($loan_id, $admin_id) {
    global $conn;
    
    $start_date = date('Y-m-d');
    $query = "SELECT loan_term_months FROM loans WHERE id = $loan_id";
    $result = $conn->query($query);
    $loan = $result->fetch_assoc();
    $end_date = date('Y-m-d', strtotime("+{$loan['loan_term_months']} months"));
    
    $update_query = "UPDATE loans SET status = 'approved', approved_by = $admin_id, approval_date = NOW(), start_date = '$start_date', end_date = '$end_date' WHERE id = $loan_id";
    
    if ($conn->query($update_query)) {
        return ['success' => true, 'message' => 'Loan approved successfully'];
    } else {
        return ['success' => false, 'message' => 'Error approving loan: ' . $conn->error];
    }
}

// Reject loan
function rejectLoan($loan_id, $admin_id) {
    global $conn;
    
    $update_query = "UPDATE loans SET status = 'rejected', approved_by = $admin_id, approval_date = NOW() WHERE id = $loan_id";
    
    if ($conn->query($update_query)) {
        return ['success' => true, 'message' => 'Loan rejected successfully'];
    } else {
        return ['success' => false, 'message' => 'Error rejecting loan: ' . $conn->error];
    }
}

// Get pending loans
function getPendingLoans() {
    global $conn;
    $query = "SELECT l.*, c.first_name, c.last_name, c.customer_id FROM loans l 
              JOIN customers c ON l.customer_id = c.id 
              WHERE l.status = 'pending' 
              ORDER BY l.created_at ASC";
    return $conn->query($query);
}

// Get all loans
function getAllLoans() {
    global $conn;
    $query = "SELECT l.*, c.first_name, c.last_name, c.customer_id FROM loans l 
              JOIN customers c ON l.customer_id = c.id 
              ORDER BY l.created_at DESC";
    return $conn->query($query);
}

// Get customer loans
function getCustomerLoans($customer_id) {
    global $conn;
    $query = "SELECT * FROM loans WHERE customer_id = $customer_id ORDER BY created_at DESC";
    return $conn->query($query);
}

// Record loan payment
function recordLoanPayment($loan_id, $amount, $payment_method) {
    global $conn;
    
    $amount = floatval($amount);
    $payment_method = sanitizeInput($payment_method);
    
    if ($amount <= 0) {
        return ['success' => false, 'message' => 'Invalid amount'];
    }
    
    $reference_number = 'LPN' . time();
    
    $query = "INSERT INTO loan_payments (loan_id, amount, payment_date, payment_method, reference_number) 
              VALUES ($loan_id, $amount, NOW(), '$payment_method', '$reference_number')";
    
    if ($conn->query($query)) {
        return ['success' => true, 'message' => 'Loan payment recorded successfully', 'reference_number' => $reference_number];
    } else {
        return ['success' => false, 'message' => 'Error recording payment: ' . $conn->error];
    }
}

// ==================== TRANSACTION FUNCTIONS ====================

// Get account transactions
function getAccountTransactions($account_id, $limit = 50) {
    global $conn;
    $query = "SELECT t.*, u.full_name as teller_name FROM transactions t 
              LEFT JOIN users u ON t.teller_id = u.id 
              WHERE t.account_id = $account_id 
              ORDER BY t.created_at DESC 
              LIMIT $limit";
    return $conn->query($query);
}

// Get transactions by date range
function getTransactionsByDateRange($start_date, $end_date) {
    global $conn;
    $query = "SELECT t.*, acc.account_number, c.first_name, c.last_name, u.full_name as teller_name 
              FROM transactions t 
              JOIN accounts acc ON t.account_id = acc.id 
              JOIN customers c ON acc.customer_id = c.id 
              LEFT JOIN users u ON t.teller_id = u.id 
              WHERE DATE(t.created_at) BETWEEN '$start_date' AND '$end_date' 
              ORDER BY t.created_at DESC";
    return $conn->query($query);
}

// Get daily transaction summary
function getDailyTransactionSummary($date) {
    global $conn;
    $query = "SELECT 
              SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE 0 END) as total_deposits,
              SUM(CASE WHEN transaction_type = 'withdrawal' THEN amount ELSE 0 END) as total_withdrawals,
              COUNT(*) as total_transactions,
              COUNT(DISTINCT account_id) as accounts_affected
              FROM transactions 
              WHERE DATE(created_at) = '$date'";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

// ==================== HELPER FUNCTIONS ====================

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

// Sanitize input
function sanitizeInput($input) {
    global $conn;
    return $conn->real_escape_string(trim($input));
}

// Format currency
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format datetime
function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

?>
