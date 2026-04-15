<?php
require_once 'config/auth.php';
require_once 'controller/function.php';

requireRole('teller');

$transactions_result = $conn->query("SELECT t.*, acc.account_number, c.first_name, c.last_name, u.full_name as teller_name 
                                   FROM transactions t 
                                   JOIN accounts acc ON t.account_id = acc.id 
                                   JOIN customers c ON acc.customer_id = c.id 
                                   LEFT JOIN users u ON t.teller_id = u.id 
                                   ORDER BY t.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Banking System</title>
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
                        <li><a href="customers_search.php">Search Customer</a></li>
                        <li><a href="deposit.php">Process Deposit</a></li>
                        <li><a href="withdrawal.php">Process Withdrawal</a></li>
                        <li><a href="check_clearing.php">Check Clearing</a></li>
                        <li><a href="transactions.php" class="active">Transactions</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="content">
                <header class="content-header">
                    <h1>Transaction History</h1>
                </header>
                
                <div class="table-section">
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
                                    <th>Date & Time</th>
                                    <th>Reference</th>
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
                                        <td><?php echo htmlspecialchars($trans['reference_number']); ?></td>
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
