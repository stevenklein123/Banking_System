<?php
require_once 'config/auth.php';
require_once 'controller/function.php';

requireRole('teller');

$error = '';
$success = '';
$customer = null;
$account = null;

// Search for customer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'search') {
    $search_term = sanitizeInput($_POST['search_term']);
    
    $query = "SELECT * FROM customers WHERE (customer_id = '$search_term' OR email = '$search_term' OR phone = '$search_term') AND status = 'active'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    } else {
        $error = 'Customer not found';
    }
}

// Get account if customer found
if ($customer) {
    $accounts_result = getCustomerAccounts($customer['id']);
    if ($accounts_result->num_rows > 0) {
        $account = $accounts_result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Customer - Banking System</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-dark bg-dark">
            <div class="navbar-container">
                <div class="navbar-brand">Banking Management System</div>
                <div class="navbar-user">
                    <span><?php echo htmlspecialchars($_SESSION['full_name']); ?> (Teller)</span>
                    <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </div>
        </nav>
        
        <div class="main-content">
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="customers_search.php" class="active">Search Customer</a></li>
                        <li><a href="deposit.php">Process Deposit</a></li>
                        <li><a href="withdrawal.php">Process Withdrawal</a></li>
                        <li><a href="check_clearing.php">Check Clearing</a></li>
                        <li><a href="transactions.php">Transactions</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="content">
                <header class="content-header">
                    <h1>Search Customer</h1>
                </header>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div class="form-section">
                    <form method="POST" class="search-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="search_term">Search by Customer ID, Email, or Phone *</label>
                                <input type="text" id="search_term" name="search_term" class="form-control" placeholder="Enter customer ID or email" required>
                            </div>
                            <button type="submit" name="action" value="search" class="btn btn-primary search-btn">Search</button>
                        </div>
                    </form>
                </div>
                
                <?php if ($customer): ?>
                    <div class="customer-section">
                        <h2>Customer Information</h2>
                        <div class="info-box">
                            <div class="info-row">
                                <div class="info-item">
                                    <label>Customer ID:</label>
                                    <span><?php echo htmlspecialchars($customer['customer_id']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Name:</label>
                                    <span><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></span>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-item">
                                    <label>Email:</label>
                                    <span><?php echo htmlspecialchars($customer['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Phone:</label>
                                    <span><?php echo htmlspecialchars($customer['phone']); ?></span>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-item">
                                    <label>Address:</label>
                                    <span><?php echo htmlspecialchars($customer['address'] . ', ' . $customer['city'] . ', ' . $customer['state'] . ' ' . $customer['zip_code']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>ID Type:</label>
                                    <span><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $customer['id_type']))); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($account): ?>
                        <div class="account-section">
                            <h2>Account Information</h2>
                            <div class="info-box">
                                <div class="info-row">
                                    <div class="info-item">
                                        <label>Account Number:</label>
                                        <span><?php echo htmlspecialchars($account['account_number']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label>Account Type:</label>
                                        <span><?php echo htmlspecialchars(ucfirst($account['account_type'])); ?></span>
                                    </div>
                                </div>
                                <div class="info-row">
                                    <div class="info-item">
                                        <label>Current Balance:</label>
                                        <span class="balance-highlight"><?php echo formatCurrency($account['balance']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label>Status:</label>
                                        <span class="badge badge-success"><?php echo ucfirst($account['status']); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="action-buttons" style="margin-top: 20px;">
                                <a href="deposit.php?account_id=<?php echo $account['id']; ?>&account_number=<?php echo $account['account_number']; ?>" class="btn btn-success">Process Deposit</a>
                                <a href="withdrawal.php?account_id=<?php echo $account['id']; ?>&account_number=<?php echo $account['account_number']; ?>" class="btn btn-warning">Process Withdrawal</a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>
