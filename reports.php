<?php
require_once 'config/auth.php';
require_once 'controller/function.php';

requireRole('admin');

$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get transactions for date range
$transactions_result = getTransactionsByDateRange($start_date, $end_date);

// Get daily summary
$daily_summary = getDailyTransactionSummary(date('Y-m-d'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Banking System</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-dark bg-dark">
            <div class="navbar-container">
                <div class="navbar-brand">Banking Management System</div>
                <div class="navbar-user">
                    <span><?php echo htmlspecialchars($_SESSION['full_name']); ?> (Admin)</span>
                    <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </div>
        </nav>
        
        <div class="main-content">
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="customers.php">Customers</a></li>
                        <li><a href="accounts.php">Accounts</a></li>
                        <li><a href="loans.php">Loans</a></li>
                        <li><a href="reports.php" class="active">Reports</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="content">
                <header class="content-header">
                    <h1>Reports & Analytics</h1>
                </header>
                
                <div class="filter-section">
                    <form method="GET" class="filter-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">
                            </div>
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary filter-btn">Generate Report</button>
                        </div>
                    </form>
                </div>
                
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-icon">💵</div>
                        <div class="stat-content">
                            <h3>Total Deposits</h3>
                            <p class="stat-value"><?php echo formatCurrency($daily_summary['total_deposits'] ?? 0); ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">💸</div>
                        <div class="stat-content">
                            <h3>Total Withdrawals</h3>
                            <p class="stat-value"><?php echo formatCurrency($daily_summary['total_withdrawals'] ?? 0); ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">📊</div>
                        <div class="stat-content">
                            <h3>Total Transactions</h3>
                            <p class="stat-value"><?php echo $daily_summary['total_transactions'] ?? 0; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-content">
                            <h3>Accounts Affected</h3>
                            <p class="stat-value"><?php echo $daily_summary['accounts_affected'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="table-section">
                    <h2>Transaction Report (<?php echo formatDate($start_date); ?> to <?php echo formatDate($end_date); ?>)</h2>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Account</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Balance After</th>
                                    <th>Payment Method</th>
                                    <th>Teller</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($trans = $transactions_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($trans['id']); ?></td>
                                        <td><?php echo htmlspecialchars($trans['account_number']); ?></td>
                                        <td><?php echo htmlspecialchars($trans['first_name'] . ' ' . $trans['last_name']); ?></td>
                                        <td><span class="badge badge-<?php echo $trans['transaction_type'] == 'deposit' ? 'success' : 'warning'; ?>"><?php echo ucfirst($trans['transaction_type']); ?></span></td>
                                        <td><?php echo formatCurrency($trans['amount']); ?></td>
                                        <td><?php echo formatCurrency($trans['balance_after']); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($trans['payment_method'])); ?></td>
                                        <td><?php echo htmlspecialchars($trans['teller_name'] ?? 'System'); ?></td>
                                        <td><?php echo formatDateTime($trans['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
