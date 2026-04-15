<?php
require_once 'config/auth.php';
require_once 'controller/function.php';

requireLogin();

$user = getUserByID($_SESSION['user_id']);
$error = '';
$success = '';

// Get statistics for admin/teller
$stats = [];
if (checkRole('admin')) {
    $result = $conn->query("SELECT COUNT(*) as count FROM customers WHERE status = 'active'");
    $stats['total_customers'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM loans WHERE status = 'pending'");
    $stats['pending_loans'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT SUM(balance) as total FROM accounts WHERE status = 'active'");
    $stats['total_balance'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM transactions WHERE DATE(created_at) = CURDATE()");
    $stats['daily_transactions'] = $result->fetch_assoc()['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Banking System</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
</head>
<body>
    <div class="container-fluid">
        <!-- Navigation -->
        <nav class="navbar navbar-dark bg-dark">
            <div class="navbar-container">
                <div class="navbar-brand">Banking Management System</div>
                <div class="navbar-menu">
                    <div class="navbar-user">
                        <span><?php echo htmlspecialchars($user['full_name']); ?> (<?php echo ucfirst($user['role']); ?>)</span>
                        <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
        
        <div class="main-content">
            <!-- Sidebar -->
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="dashboard.php" class="active">Dashboard</a></li>
                        
                        <?php if (checkRole('admin')): ?>
                            <li><a href="customers.php">Customers</a></li>
                            <li><a href="accounts.php">Accounts</a></li>
                            <li><a href="loans.php">Loans</a></li>
                            <li><a href="reports.php">Reports</a></li>
                        <?php elseif (checkRole('teller')): ?>
                            <li><a href="customers_search.php">Search Customer</a></li>
                            <li><a href="deposit.php">Process Deposit</a></li>
                            <li><a href="withdrawal.php">Process Withdrawal</a></li>
                            <li><a href="check_clearing.php">Check Clearing</a></li>
                            <li><a href="transactions.php">Transactions</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </aside>
            
            <!-- Main Content -->
            <main class="content">
                <header class="content-header">
                    <h1>Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
                </header>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <?php if (checkRole('admin')): ?>
                    <!-- Admin Dashboard -->
                    <div class="dashboard-grid">
                        <div class="stat-card">
                            <div class="stat-icon">👥</div>
                            <div class="stat-content">
                                <h3>Total Customers</h3>
                                <p class="stat-value"><?php echo $stats['total_customers']; ?></p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">📋</div>
                            <div class="stat-content">
                                <h3>Pending Loans</h3>
                                <p class="stat-value"><?php echo $stats['pending_loans']; ?></p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">💰</div>
                            <div class="stat-content">
                                <h3>Total Balance</h3>
                                <p class="stat-value"><?php echo formatCurrency($stats['total_balance'] ?? 0); ?></p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">📊</div>
                            <div class="stat-content">
                                <h3>Daily Transactions</h3>
                                <p class="stat-value"><?php echo $stats['daily_transactions']; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-section">
                        <h2>Quick Actions</h2>
                        <div class="action-buttons">
                            <a href="customers.php" class="btn btn-primary">Add New Customer</a>
                            <a href="loans.php" class="btn btn-secondary">Manage Loans</a>
                            <a href="reports.php" class="btn btn-info">View Reports</a>
                        </div>
                    </div>
                    
                <?php elseif (checkRole('teller')): ?>
                    <!-- Teller Dashboard -->
                    <div class="dashboard-section">
                        <h2>Quick Actions</h2>
                        <div class="action-buttons">
                            <a href="customers_search.php" class="btn btn-primary">Search Customer</a>
                            <a href="deposit.php" class="btn btn-success">Process Deposit</a>
                            <a href="withdrawal.php" class="btn btn-warning">Process Withdrawal</a>
                            <a href="check_clearing.php" class="btn btn-info">Check Clearing</a>
                        </div>
                    </div>
                    
                    <div class="dashboard-section">
                        <h2>Recent Transactions</h2>
                        <?php
                        $result = $conn->query("SELECT t.*, acc.account_number, c.first_name, c.last_name, u.full_name as teller_name 
                                              FROM transactions t 
                                              JOIN accounts acc ON t.account_id = acc.id 
                                              JOIN customers c ON acc.customer_id = c.id 
                                              LEFT JOIN users u ON t.teller_id = u.id 
                                              ORDER BY t.created_at DESC LIMIT 10");
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Balance</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($trans = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($trans['account_number']); ?></td>
                                        <td><?php echo htmlspecialchars($trans['first_name'] . ' ' . $trans['last_name']); ?></td>
                                        <td><span class="badge badge-<?php echo $trans['transaction_type'] == 'deposit' ? 'success' : 'warning'; ?>"><?php echo ucfirst($trans['transaction_type']); ?></span></td>
                                        <td><?php echo formatCurrency($trans['amount']); ?></td>
                                        <td><?php echo formatCurrency($trans['balance_after']); ?></td>
                                        <td><?php echo formatDateTime($trans['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>
