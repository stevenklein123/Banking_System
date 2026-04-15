<?php
require_once 'config/auth.php';
require_once 'controller/function.php';

requireRole('admin');

$accounts_result = $conn->query("SELECT a.*, c.first_name, c.last_name, c.customer_id FROM accounts a JOIN customers c ON a.customer_id = c.id ORDER BY a.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts - Banking System</title>
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
                        <li><a href="accounts.php" class="active">Accounts</a></li>
                        <li><a href="loans.php">Loans</a></li>
                        <li><a href="reports.php">Reports</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="content">
                <header class="content-header">
                    <h1>Account Management</h1>
                </header>
                
                <div class="table-section">
                    <h2>All Bank Accounts</h2>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Account Number</th>
                                    <th>Customer</th>
                                    <th>Customer ID</th>
                                    <th>Account Type</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($account = $accounts_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($account['account_number']); ?></td>
                                        <td><?php echo htmlspecialchars($account['first_name'] . ' ' . $account['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($account['customer_id']); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($account['account_type'])); ?></td>
                                        <td><?php echo formatCurrency($account['balance']); ?></td>
                                        <td><span class="badge badge-<?php echo $account['status'] == 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($account['status']); ?></span></td>
                                        <td><?php echo formatDate($account['created_at']); ?></td>
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
